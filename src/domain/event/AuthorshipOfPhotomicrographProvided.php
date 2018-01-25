<?php
    namespace sednasoft\virmisco\domain\event;

    use sednasoft\virmisco\singiere\AbstractEvent;

    class AuthorshipOfPhotomicrographProvided extends AbstractEvent
    {
        /** @var string */
        private $creatorCapturingDigitalMaster;
        /** @var string */
        private $creatorProcessingDerivatives;

        /**
         * Creates a new instance. Subclasses should add more parameters for payload.
         *
         * @param string $creatorCapturingDigitalMaster
         * @param string $creatorProcessingDerivatives
         */
        public function __construct(
            $creatorCapturingDigitalMaster,
            $creatorProcessingDerivatives
        ) {
            parent::__construct();
            $this->creatorCapturingDigitalMaster = $creatorCapturingDigitalMaster;
            $this->creatorProcessingDerivatives = $creatorProcessingDerivatives;
        }

        /**
         * @return string
         */
        public function getCreatorCapturingDigitalMaster()
        {
            return $this->creatorCapturingDigitalMaster;
        }

        /**
         * @return string
         */
        public function getCreatorProcessingDerivatives()
        {
            return $this->creatorProcessingDerivatives;
        }
    }
