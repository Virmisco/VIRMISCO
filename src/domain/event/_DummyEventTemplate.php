<?php
    namespace sednasoft\virmisco\domain\event;

    use sednasoft\virmisco\singiere\AbstractEvent;

    class _DummyEventTemplate extends AbstractEvent
    {
        /**
         * Creates a new instance. Subclasses should add more parameters for payload.
         */
        public function __construct()
        {
            parent::__construct();
        }
    }
