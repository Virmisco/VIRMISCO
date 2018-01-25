<?php
    namespace sednasoft\virmisco\domain;

    use sednasoft\virmisco\singiere\AbstractEvent;

    /**
     * Publishes events. Usually a repository will maintain an instance and forward events to it after appending them to
     * the event store when saving an aggregate.
     */
    interface IEventPublisher
    {
        /**
         * @param AbstractEvent $event
         */
        public function publish(AbstractEvent $event);
    }
