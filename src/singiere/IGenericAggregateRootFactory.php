<?php
    namespace sednasoft\virmisco\singiere;

    /**
     * Generic factory for creating arbitrary aggregates from their class name and their ID.
     */
    interface IGenericAggregateRootFactory {

        /**
         * @param string $className
         * @param Uuid $aggregateId
         * @return AbstractAggregateRoot
         */
        public function createAggregateByClass($className, Uuid $aggregateId);
    }
