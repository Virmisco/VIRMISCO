<?php
    namespace sednasoft\virmisco\singiere;

    /**
     * A command sent to the domain. When handled by the domain, either events are generated or exceptions thrown. Its
     * name should by in the form MakeThingHappen or FoobarizeBazWithQuux.
     */
    abstract class AbstractCommand
    {
        /** @var Uuid */
        private $aggregateId;

        /**
         * Creates a new instance. Subclasses should add more parameters for payload.
         *
         * @param Uuid $aggregateId The unique identifier of the aggregate to receive this command.
         */
        public function __construct(Uuid $aggregateId)
        {
            $this->aggregateId = $aggregateId;
        }

        /**
         * Returns the unique identifier of the aggregate to receive this command.
         *
         * @return Uuid The aggregate identifier.
         */
        public function getAggregateId()
        {
            return $this->aggregateId;
        }
    }
