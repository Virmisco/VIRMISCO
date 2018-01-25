<?php
    namespace sednasoft\virmisco\domain;

    use Generator;
    use sednasoft\virmisco\singiere\Uuid;

    class UpgradeEventStoreDecorator extends AbstractEventStoreDecorator
    {
        /**
         * Seek through the data store from the oldest to the newest entry and return only those events meant for the
         * aggregate with the given identifier.
         *
         * @param Uuid $aggregateId The aggregate identifier.
         * @return Generator An AbstractEvent for every event on the aggregate.
         */
        public function iterateEventsForAggregate(Uuid $aggregateId)
        {
            foreach (parent::iterateEventsForAggregate($aggregateId) as $event) {
                while ($event instanceof IUpgradable) {
                    $event = $event->upgrade();
                }
                yield $event;
            }
        }

        /**
         * Iterate through the data store from the oldest to the newest entry and return all events.
         *
         * @return Generator
         */
        public function iterateEventsForAllAggregates()
        {
            foreach (parent::iterateEventsForAllAggregates() as $event) {
                while ($event instanceof IUpgradable) {
                    $event = $event->upgrade();
                }
                yield $event;
            }
        }

    }
