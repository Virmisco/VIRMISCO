<?php
    namespace sednasoft\virmisco\singiere;

    use Generator;
    use Iterator;
    use sednasoft\virmisco\singiere\error\MissingApplyMethodForEventException;
    use sednasoft\virmisco\singiere\error\NotAnEventException;
    use sednasoft\virmisco\singiere\error\WrongEventVersionException;

    /**
     * A domain object that serves as the identifiable entry point for changes to itself and its associated, not
     * globally identifiable domain objects.
     *
     * Every public method should validate the arguments and apply an event to perform the modifications:
     *
     *     public function changeFoo ($newFoo) {
     *         if (!is_string($newFoo) || $newFoo === '') throw new InvalidFooException($newFoo);
     *         $this->apply(new OldFooDiscarded($this->getId(), $this->nextVersion(), $this->foo));
     *         $this->apply(new FooChanged($this->getId(), $this->nextVersion(), $newFoo));
     *     }
     *
     * For every event generated there must be a protected method named "apply" followed by the class name of the event,
     * which is supposed to actually perform the changes no matter what. There is no way to object against a change that
     * comes from an event, because events always have happened in the past.
     *
     *     protected function applyFooChanged (FooChanged $event) {
     *         $this->foo = $event->getFoo();
     *     }
     */
    abstract class AbstractAggregateRoot
    {
        /** @var Uuid */
        private $aggregateId;
        /** @var AbstractEvent[] */
        private $changes = [];
        /** @var int */
        private $version;

        /**
         * Creates a new instance based on the given unique identifier.
         *
         * @param Uuid $aggregateId The unique identifier forever bound to this instance.
         */
        public function __construct(Uuid $aggregateId)
        {
            $this->aggregateId = $aggregateId;
            $this->initializeMembers();
            $this->version = 0;
        }

        /**
         * Clears the list of changes, should be called after getChanges().
         */
        public function clearChanges()
        {
            $this->changes = [];
        }

        /**
         * Returns the unique identfier of this instance that must never change during its whole lifetime (and beyond)
         * and must never be reused for another instance regardless of lifetime boundaries.
         *
         * @return Uuid The unique identifier.
         */
        public function getAggregateId()
        {
            return $this->aggregateId;
        }

        /**
         * Iterates over the changes in order of execution. When the caller is done with looping through the returned
         * generator, the changes should be cleared to avoid saving the same events twice.
         *
         * @return Generator A generator yielding an AbstractEvent for every change in order.
         */
        public function getChanges()
        {
            foreach ($this->changes as $event) {
                yield $event;
            }
        }

        /**
         * Return the version of this instance, where 0 is after creation, but before any changes. It can be seen as the
         * number of events applied so far.
         *
         * @return int The current version of this instance.
         */
        public function getVersion()
        {
            return $this->version;
        }

        /**
         * Resets this instance to its default values and then applies the events from the stream.
         *
         * @param Iterator $eventStream The events to apply in the given order.
         * @throws NotAnEventException When a member of the stream is not a valid event.
         */
        public function replayEventStream(Iterator $eventStream)
        {
            $this->initializeMembers();
            $this->version = 0;
            foreach ($eventStream as $event) {
                if ($event instanceof AbstractEvent) {
                    $this->applyEvent($event, false);
                } else {
                    throw new NotAnEventException();
                }
            }
        }

        /**
         * Applies the event as a new one (not replayed) and increments the internal version number. Internally, after
         * the version has been incremented, the respective instance method is called whose name consists of "apply"
         * followed by the class name of the event (and which should have protected visibility).
         *
         * @param AbstractEvent $event The event to apply.
         * @return AbstractEvent
         * @throws MissingApplyMethodForEventException When this aggregate lacks a protected method to apply a certain event.
         * @throws WrongEventVersionException When attempting to apply an event whose version is not one more than the current
         * version of the aggregate.
         */
        protected function apply(AbstractEvent $event)
        {
            $event->bindTo($this->getAggregateId(), $this->version + 1);
            $this->applyEvent($event, true);
            $this->changes[] = $event;

            return $event;
        }

        /**
         * Initialize all instance variables to default values. This method will be called for instantiation and every
         * time this instance is reconstituted through replayEventStream().
         */
        abstract protected function initializeMembers();

        /**
         * @param AbstractEvent $event
         * @param bool $isNew
         * @throws MissingApplyMethodForEventException When this aggregate lacks a protected method to apply a certain event.
         * @throws WrongEventVersionException When attempting to apply an event whose version is not one more than the current
         * version of the aggregate.
         */
        private function applyEvent(AbstractEvent $event, $isNew)
        {
            $simpleName = array_slice(explode('\\', get_class($event)), -1)[0];
            $method = 'apply' . $simpleName;
            if (!method_exists($this, $method)) {
                throw new MissingApplyMethodForEventException($simpleName);
            }
            if ($event->getVersion() !== $this->version + 1) {
                throw new WrongEventVersionException(
                    sprintf('%s expected %d but gut %d', $this->getAggregateId(), $this->version + 1, $event->getVersion())
                );
            }
            $this->version++;
            $this->$method($event);
        }
    }
