<?php
    namespace sednasoft\virmisco\domain;

    use sednasoft\virmisco\singiere\AbstractEvent;
    use sednasoft\virmisco\singiere\IProjection;

    /**
     * Implements an event publisher as a bus to which projections can subscribe to.
     */
    class ProjectionEventBus implements IEventPublisher
    {
        private $subscribers = [];

        /**
         * Publishes an event to the subscribed projections maintaining current state.
         * @param AbstractEvent $event
         */
        public function publish(AbstractEvent $event)
        {
            /** @var IProjection $subscriber */
            foreach ($this->subscribers as $subscriber) {
                $subscriber->apply($event);
            }
        }

        /**
         * Registers a projection as a subscriber.
         * @param IProjection $subscriber
         */
        public function subscribe(IProjection $subscriber)
        {
            if (!in_array($subscriber, $this->subscribers)) {
                $this->subscribers[] = $subscriber;
            }
        }

        /**
         * Causes the projection to no longer receive events.
         * @param IProjection $subscriber
         */
        public function unsubscribe(IProjection $subscriber)
        {
            foreach ($this->subscribers as $key => $value) {
                if ($value === $subscriber) {
                    array_splice($this->subscribers, $key, 1);
                    break;
                }
            }
        }
    }
