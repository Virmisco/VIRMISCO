<?php
    namespace sednasoft\virmisco\domain\event;

    use sednasoft\virmisco\singiere\AbstractEvent;

    class PhotomicrographRenamed extends AbstractEvent
    {
        /** @var string */
        private $title;

        /**
         * PhotomicrographRenamed constructor.
         * @param string $title
         */
        public function __construct($title)
        {
            parent::__construct();
            $this->title = $title;
        }

        /**
         * @return string
         */
        public function getTitle()
        {
            return $this->title;
        }
    }
