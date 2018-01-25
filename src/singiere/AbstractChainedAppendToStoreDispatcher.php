<?php
    namespace sednasoft\virmisco\singiere;

    /**
     * A dispatcher receiving a command and calling the appropriate method on the designated aggregate. If it cannot
     * ultimately handle the command itself it will call the next dispatcher in the chain of responsibility. Any events
     * generated from aggregates are both forwarded to the configured event store and yielded to the caller dispatching
     * the command.
     */
    abstract class AbstractChainedAppendToStoreDispatcher extends AbstractChainedDispatcher
    {
        /** @var IEventStore */
        private $eventStore;

        /**
         * @param IDispatcher|null $nextDispatcher The next dispatcher in the chain of responsibility that
         * will receive commands not ultimately handled by the current instance.
         * @param IEventStore $eventStore The event store to send all events generated while processing the
         * command.
         */
        public function __construct(IDispatcher $nextDispatcher = null, IEventStore $eventStore)
        {
            parent::__construct($nextDispatcher);
            $this->eventStore = $eventStore;
        }

        /**
         * Send the event to the event store.
         *
         * @param AbstractEvent $event
         */
        protected function processEventBeforeYielding(AbstractEvent $event)
        {
            $this->eventStore->append($event);
        }
    }
