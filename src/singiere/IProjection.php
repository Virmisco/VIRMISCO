<?php
    namespace sednasoft\virmisco\singiere;

    /**
     * Represents the process of folding (reducing) an event stream into a single resulting state that can then be
     * transformed into a certain format.
     */
    interface IProjection
    {

        /**
         * Applies the event and modifies the current state accordingly.
         *
         * @param AbstractEvent $event
         */
        public function apply(AbstractEvent $event);

        /**
         * Transforms the current state into the desired representation, a DTO, SQL insert statement or anything else.
         *
         * @return mixed A data transfer object.
         */
        public function transform();
    }
