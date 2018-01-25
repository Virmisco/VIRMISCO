<?php
    namespace sednasoft\virmisco\singiere;

    use Exception;
    use Generator;

    /**
     * A dispatcher receiving a command and calling the appropriate method on the designated aggregate. If it cannot
     * ultimately handle the command itself it will call the next dispatcher in the chain of responsibility. Any events
     * generated from aggregates are both forwarded to the configured event store and yielded to the caller dispatching
     * the command.
     */
    abstract class AbstractChainedDispatcher implements IDispatcher
    {
        /** @var IEventStore */
        private $eventStore;

        /**
         * @param IDispatcher|null $nextDispatcher The next dispatcher in the chain of responsibility that
         * will receive commands not ultimately handled by the current instance.
         */
        public function __construct(IDispatcher $nextDispatcher = null)
        {
            $this->nextDispatcher = $nextDispatcher;
        }

        /**
         * Handles the command itself or passes it to the next dispatcher in the chain, yielding all generated events.
         * Also appends all generated events to the configured event store.
         *
         * @param AbstractCommand $command The command to handle.
         * @return Generator An AbstractEvent for every event generated during command processing.
         * @throws Exception when the processing of the command caused an error.
         */
        public function dispatch(AbstractCommand $command)
        {
            $eventPump = $this->createEventPump();
            $ultimatelyHandled = $this->handleIfPossible($command, $eventPump);
            for ($eventPump->next(); $eventPump->valid(); $eventPump->next()) {
                $this->processEventBeforeYielding($eventPump->current());
                yield $eventPump->current();
            }
            if (!$ultimatelyHandled && $this->nextDispatcher) {
                foreach ($this->nextDispatcher->dispatch($command) as $event) {
                    yield $event;
                }
            }
        }

        /**
         * Tries to handle the command and returns true when it ultimately did or false to signal that the command
         * should be passed on to the next dispatcher. This method must not throw any exceptions, instead they have to
         * be caught and forwarded to the $eventSink. Likewise all generated events must be captured and also sent to
         * the event sink, otherwise they will not be stored and are lost forever.
         *
         * @param AbstractCommand $command The command to handle if possible.
         * @param Generator $eventSink An event sink for all the events and exceptions generated during handling.
         * @return bool True when the command has been ultimately handled, false when the next dispatcher should go on.
         */
        protected abstract function handleIfPossible(AbstractCommand $command, Generator $eventSink);

        /**
         * @return Generator An AbstractEvent for every event generated during command processing.
         * @throws Exception when the processing of the command caused an error.
         */
        private function createEventPump()
        {
            $queue = [];
            $error = null;
            do {
                $event = null;
                try {
                    // wait for send() or throw()
                    $event = yield;
                    // ignore all events after the first error has occured
                    if ($event && !$error) {
                        $queue[] = $event;
                    }
                } catch (Exception $e) {
                    // ignore all errors after the first one
                    $error = $error ?: $e;
                    // continue the loop until no more events get sent in
                    $event = true;
                }
            } while ($event);
            // the last yield was due to next() and not send() nor throw()
            foreach ($queue as $event) {
                yield $event;
            }
            if ($error) {
                throw $error;
            }
        }

        /**
         * Subclasses may overwrite this no-op implementation.
         *
         * @param AbstractEvent $event
         */
        protected function processEventBeforeYielding(AbstractEvent $event)
        {
        }
    }
