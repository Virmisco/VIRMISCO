<?php
    namespace sednasoft\virmisco\singiere;

    use sednasoft\virmisco\singiere\error\ConflictingVersionException;

    /**
     * Enforces version checks of the aggregate when a new event is appended.
     */
    abstract class AbstractVersionCheckEventStore implements IEventStore
    {

        /**
         * Appends the event to the data store after checking the version number for concurreny conflicts.
         *
         * @param AbstractEvent $event The event to be stored.
         * @throws ConflictingVersionException When trying to append an event to the event store that would introduce a
         * version conflict.
         */
        public function append(AbstractEvent $event)
        {
            $last = $this->getLastEventForAggregate($event->getAggregateId());
            if (($last ? $last->getVersion() + 1 : 1) !== $event->getVersion()) {
                throw new ConflictingVersionException();
            }
            $this->appendToStream($event);
        }

        /**
         * Serializes the given event and stores it into the underlying data store. No version checking is necessary at
         * this point.
         *
         * @param AbstractEvent $event The event to store away.
         */
        abstract protected function appendToStream(AbstractEvent $event);

        /**
         * Returns the most recent event for the aggregate identified by the given ID. This is a performance
         * optimization possibility, but may (for the laziest implementation) as well just iterateEventsForAggregate()
         * and return the last one.
         *
         * @param Uuid $aggregateId The aggregate identifier.
         * @return AbstractEvent|null The most recent event for this aggregate or null, if it is virgin.
         */
        abstract protected function getLastEventForAggregate(Uuid $aggregateId);
    }
