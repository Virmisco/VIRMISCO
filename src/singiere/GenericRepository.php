<?php
    namespace sednasoft\virmisco\singiere;

    use sednasoft\virmisco\domain\IEventPublisher;
    use sednasoft\virmisco\singiere\error\AggregateExistsException;
    use sednasoft\virmisco\singiere\error\AggregateMismatchException;
    use sednasoft\virmisco\singiere\error\AggregateNotFoundException;
    use sednasoft\virmisco\singiere\error\NotAnEventException;

    /**
     * Can load existing aggregates (from its cache or a connected event store) and create new ones with the help of a
     * generic aggregate factory.
     */
    class GenericRepository implements IRepository
    {
        /** @var AbstractAggregateRoot[] */
        private $cache;
        /** @var IEventStore */
        private $eventStore;
        /** @var IGenericAggregateRootFactory */
        private $factory;
        /** @var IEventPublisher */
        private $publisher;

        /**
         * @param IEventStore $eventStore
         * @param IGenericAggregateRootFactory $factory
         * @param IEventPublisher $publisher
         */
        public function __construct(IEventStore $eventStore, IGenericAggregateRootFactory $factory, IEventPublisher $publisher)
        {
            $this->eventStore = $eventStore;
            $this->factory = $factory;
            $this->publisher = $publisher;
        }

        /**
         * Creates a new aggregate.
         *
         * @param string $className The name of a subclass of AbstractAggregateRoot.
         * @param Uuid|null $aggregateId The ID to use for the aggregate or null to auto-generate one.
         * @return AbstractAggregateRoot The new aggregate with an empty change history.
         * @throws AggregateExistsException When the specified ID already exists in the cache or the event store.
         */
        public function create($className, Uuid $aggregateId = null)
        {
            $aggregateId = $aggregateId ?: Uuid::createRandom();
            if (isset($this->cache[strval($aggregateId)])) {
                throw new AggregateExistsException('Attempt to create existing aggregate ' . $aggregateId);
            }
            $aggregate = $this->factory->createAggregateByClass($className, $aggregateId);
            foreach ($this->eventStore->iterateEventsForAggregate($aggregateId) as $event) {
                throw new AggregateExistsException('Attempt to create existing aggregate ' . $aggregateId);
            }
            $this->cache[strval($aggregateId)] = $aggregate;

            return $this->cache[strval($aggregateId)];
        }

        /**
         * Reconstitutes an existing aggregate.
         *
         * @param string $className The name of a subclass of AbstractAggregateRoot.
         * @param Uuid $aggregateId The ID of the aggregate to load.
         * @return AbstractAggregateRoot The aggregate completely reconstituted from its event stream.
         * @throws AggregateMismatchException
         * @throws AggregateNotFoundException
         * @throws NotAnEventException
         */
        public function load($className, Uuid $aggregateId)
        {
            if (!isset($this->cache[strval($aggregateId)])) {
                $aggregate = $this->factory->createAggregateByClass($className, $aggregateId);
                $aggregate->replayEventStream($this->eventStore->iterateEventsForAggregate($aggregateId));
                if ($aggregate->getVersion() === 0) {
                    throw new AggregateNotFoundException('Failed to load aggregate ' . $aggregateId);
                }
                $this->cache[strval($aggregateId)] = $aggregate;
            }
            elseif (!($this->cache[strval($aggregateId)] instanceof $className)) {
                throw new AggregateMismatchException(
                    sprintf('Aggregate %s is not compatible with %s', $aggregateId, $className)
                );
            }

            return $this->cache[strval($aggregateId)];
        }

        /**
         * Saves the aggregate's changes in the associated event store.
         *
         * @param AbstractAggregateRoot $aggregate
         */
        public function save(AbstractAggregateRoot $aggregate)
        {
            /** @var AbstractEvent $event */
            foreach ($aggregate->getChanges() as $event) {
                $this->eventStore->append($event);
                $this->publisher->publish($event);
            }
            $aggregate->clearChanges();
        }
    }
