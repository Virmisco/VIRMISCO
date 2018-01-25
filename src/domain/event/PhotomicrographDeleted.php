<?php
    namespace sednasoft\virmisco\domain\event;

    use sednasoft\virmisco\singiere\AbstractEvent;

    class PhotomicrographDeleted extends AbstractEvent
    {
        /**
         * PhotomicrographRenamed constructor.
         */
        public function __construct()
        {
            parent::__construct();
        }
    }
