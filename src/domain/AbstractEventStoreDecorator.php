<?php
    namespace sednasoft\virmisco\domain;

    use Generator;
    use sednasoft\virmisco\singiere\AbstractEvent;
    use sednasoft\virmisco\singiere\IEventStore;
    use sednasoft\virmisco\singiere\Uuid;

    abstract class AbstractEventStoreDecorator implements IEventStore
    {
        /** @var IEventStore */
        private $baseEventStore;

        /**
         * @param IEventStore $baseEventStore
         */
        public function __construct(IEventStore $baseEventStore)
        {
            $this->baseEventStore = $baseEventStore;
        }

        /**
         * Appends the event to the data store.
         *
         * @param AbstractEvent $event The event to be stored.
         */
        public function append(AbstractEvent $event)
        {
            $this->baseEventStore->append($event);
        }

        /**
         * Seek through the data store from the oldest to the newest entry and return only those events meant for the
         * aggregate with the given identifier.
         *
         * @param Uuid $aggregateId The aggregate identifier.
         * @return Generator An AbstractEvent for every event on the aggregate.
         */
        public function iterateEventsForAggregate(Uuid $aggregateId)
        {
            return $this->baseEventStore->iterateEventsForAggregate($aggregateId);
        }

        /**
         * Iterate through the data store from the oldest to the newest entry and return all events.
         *
         * @return Generator
         */
        public function iterateEventsForAllAggregates()
        {
            return $this->baseEventStore->iterateEventsForAllAggregates();
        }
    }
