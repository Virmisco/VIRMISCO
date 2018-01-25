<?php
    namespace sednasoft\virmisco\domain\event;

    use sednasoft\virmisco\singiere\AbstractEvent;

    class SpecimenCarrierScannedToImage extends AbstractEvent
    {
        /** @var string */
        private $creationTime;
        /** @var string */
        private $modificationTime;
        /** @var string */
        private $realPath;
        /** @var string */
        private $uri;

        /**
         * SpecimenCarrierScannedToImage constructor.
         * @param string $realPath
         * @param string $uri
         * @param string $creationTime
         * @param string $modificationTime
         */
        public function __construct($realPath, $uri, $creationTime, $modificationTime)
        {
            parent::__construct();
            $this->realPath = $realPath;
            $this->uri = $uri;
            $this->creationTime = $creationTime;
            $this->modificationTime = $modificationTime;
        }

        /**
         * @return string
         */
        public function getCreationTime()
        {
            return $this->creationTime;
        }

        /**
         * @return string
         */
        public function getModificationTime()
        {
            return $this->modificationTime;
        }

        /**
         * @return string
         */
        public function getRealPath()
        {
            return $this->realPath;
        }

        /**
         * @return string
         */
        public function getUri()
        {
            return $this->uri;
        }
    }
