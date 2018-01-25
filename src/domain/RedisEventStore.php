<?php
    namespace sednasoft\virmisco\domain;

    use Generator;
    use InvalidArgumentException;
    use Predis\Client;
    use Predis\Collection\Iterator;
    use sednasoft\virmisco\singiere\AbstractEvent;
    use sednasoft\virmisco\singiere\AbstractVersionCheckEventStore;
    use sednasoft\virmisco\singiere\Uuid;

    class RedisEventStore extends AbstractVersionCheckEventStore
    {
        /** @var string */
        private $keyAll = 'events/all';
        /** @var string */
        private $keyPrefixAggregate = 'events/aggregate/';
        /** @var Client */
        private $client;

        /**
         * @param string $redisConnectionUri E. g. 'tcp://redis.example.com:6379'
         * @throws InvalidArgumentException When \Predis\Client::createOptions() or \Predis\Client::createConnection
         * throws it.
         */
        public function __construct($redisConnectionUri)
        {
            $this->client = new Client($redisConnectionUri);
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
            foreach (new Iterator\ListKey($this->client, $this->keyPrefixAggregate . $aggregateId) as $event) {
                list($version, $type, $packed, $json) = explode(':', $event, 4);
                yield unserialize(gzuncompress(base64_decode($packed)));
            }
        }

        /**
         * Iterate through the data store from the oldest to the newest entry and return all events.
         *
         * @return Generator
         */
        public function iterateEventsForAllAggregates()
        {
            foreach (new Iterator\ListKey($this->client, $this->keyAll) as $descriptor) {
                list($id, $version) = explode(':', $descriptor);
                $event = $this->client->lindex($this->keyPrefixAggregate . $id, intval($version));
                list($version, $type, $packed, $json) = explode(':', $event, 4);
                yield unserialize(gzuncompress(base64_decode($packed)));
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
            $value = sprintf(
                "%x:%s:%s:%s",
                $event->getVersion(),
                array_slice(explode('\\', get_class($event)), -1)[0],
                base64_encode(gzcompress(serialize($event))),
                json_encode($event, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
            );
            $length = $this->client->rpush($this->keyPrefixAggregate . $event->getAggregateId(), [$value]);
            $value = sprintf('%s:%d', $event->getAggregateId(), $length - 1);
            $this->client->rpush($this->keyAll, [$value]);
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
            foreach ($this->client->lrange($this->keyPrefixAggregate . $aggregateId, -1, -1) as $event) {
                list($version, $type, $packed, $json) = explode(':', $event, 4);

                return unserialize(gzuncompress(base64_decode($packed)));
            }

            return null;
        }
    }
