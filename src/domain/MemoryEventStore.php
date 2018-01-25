<?php
    namespace sednasoft\virmisco\domain;

    use Generator;
    use sednasoft\virmisco\singiere\AbstractEvent;
    use sednasoft\virmisco\singiere\AbstractVersionCheckEventStore;
    use sednasoft\virmisco\singiere\Uuid;

    /**
     * Stores domain events in memory (after version check) instead of providing persistent disk storage.
     */
    class MemoryEventStore extends AbstractVersionCheckEventStore
    {
        private $aggregateEventIndices = [];
        private $events = [];

        /**
         * Seek through the data store from the oldest to the newest entry and return only those events meant for the
         * aggregate with the given identifier.
         *
         * @param Uuid $aggregateId The aggregate identifier.
         * @return Generator An AbstractEvent for every event on the aggregate.
         */
        public function iterateEventsForAggregate(Uuid $aggregateId)
        {
            if (isset($this->aggregateEventIndices[strval($aggregateId)])) {
                foreach ($this->aggregateEventIndices[strval($aggregateId)] as $index) {
                    yield $this->events[$index];
                }
            }
        }

        /**
         * Iterate through the data store from the oldest to the newest entry and return all events.
         *
         * @return Generator
         */
        public function iterateEventsForAllAggregates()
        {
            foreach ($this->events as $event) {
                yield $event;
            }
        }

        /**
         * Serializes the given event and stores it into the underlying data store. No version checking is necessary at
         * this point.
         *
         * @param AbstractEvent $event The event to store away.
         */
        protected function appendToStream(AbstractEvent $event)
        {
            $this->aggregateEventIndices[strval($event->getAggregateId())][] = count($this->events);
            $this->events[] = $event;
        }

        /**
         * Returns the most recent event for the aggregate identified by the given ID. This is a performance
         * optimization possibility, but may (for the laziest implementation) as well just iterateEventsForAggregate()
         * and return the last one.
         *
         * @param Uuid $aggregateId The aggregate identifier.
         * @return AbstractEvent|null The most recent event for this aggregate or null, if it is virgin.
         */
        protected function getLastEventForAggregate(Uuid $aggregateId)
        {
            if (isset($this->aggregateEventIndices[strval($aggregateId)])) {
                $events = $this->aggregateEventIndices[strval($aggregateId)];

                return $this->events[end($events)];
            }

            return null;
        }
    }
