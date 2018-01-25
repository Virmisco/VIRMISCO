<?php
    namespace sednasoft\virmisco\domain\command;

    use sednasoft\virmisco\singiere\AbstractCommand;
    use sednasoft\virmisco\singiere\Uuid;

    class ScanSpecimenCarrier extends AbstractCommand
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
         * Creates a new instance. Subclasses should add more parameters for payload.
         *
         * @param Uuid $specimenCarrierId The unique identifier of the aggregate to receive this command.
         * @param string $realPath
         * @param string $uri
         * @param string $creationTime
         * @param string $modificationTime
         */
        public function __construct(
            Uuid $specimenCarrierId,
            $realPath,
            $uri,
            $creationTime,
            $modificationTime
        ) {
            parent::__construct($specimenCarrierId);
            $this->realPath = (string)$realPath;
            $this->uri = (string)$uri;
            $this->creationTime = (string)$creationTime;
            $this->modificationTime = (string)$modificationTime;
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
