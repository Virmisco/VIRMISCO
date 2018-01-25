<?php
    namespace sednasoft\virmisco\domain\command;

    use sednasoft\virmisco\singiere\AbstractCommand;
    use sednasoft\virmisco\singiere\Uuid;

    /**
     * A command designed to be handled by the LasFileService to breaks it down into several other commands.
     */
    class ImportPhotomicrographFromLasFile extends AbstractCommand
    {
        /** @var string */
        private $relativeUri;
        /** @var string */
        private $sequenceNumber;
        /** @var Uuid */
        private $specimenCarrierId;

        /**
         * @param Uuid $specimenCarrierId
         * @param string $sequenceNumber
         * @param string $relativeUri
         */
        public function __construct(Uuid $specimenCarrierId, $sequenceNumber, $relativeUri)
        {
            parent::__construct(Uuid::createRandom());
            $this->specimenCarrierId = $specimenCarrierId;
            $this->sequenceNumber = $sequenceNumber;
            $this->relativeUri = $relativeUri;
        }

        /**
         * @return string
         */
        public function getRelativeUri()
        {
            return $this->relativeUri;
        }

        /**
         * @return string
         */
        public function getSequenceNumber()
        {
            return $this->sequenceNumber;
        }

        /**
         * @return Uuid
         */
        public function getSpecimenCarrierId()
        {
            return $this->specimenCarrierId;
        }
    }
