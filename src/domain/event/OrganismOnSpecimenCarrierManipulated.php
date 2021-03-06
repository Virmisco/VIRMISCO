<?php
    namespace sednasoft\virmisco\domain\event;

    use sednasoft\virmisco\singiere\AbstractEvent;

    class OrganismOnSpecimenCarrierManipulated extends AbstractEvent
    {
        /** @var string */
        private $oldSequenceNumber;
        /** @var string */
        private $phaseOrStage;
        /** @var string */
        private $remarks;
        /** @var string */
        private $sequenceNumber;
        /** @var string */
        private $sex;

        /**
         * OrganismOnSpecimenCarrierDescribed constructor.
         * @param $oldSequenceNumber
         * @param string $newSequenceNumber
         * @param string $sex
         * @param string $phaseOrStage
         * @param string $remarks
         */
        public function __construct($oldSequenceNumber, $newSequenceNumber, $sex, $phaseOrStage, $remarks)
        {
            parent::__construct();
            $this->oldSequenceNumber = $oldSequenceNumber;
            $this->sequenceNumber = $newSequenceNumber;
            $this->sex = $sex;
            $this->phaseOrStage = $phaseOrStage;
            $this->remarks = $remarks;
        }

        /**
         * @return string
         */
        public function getOldSequenceNumber()
        {
            return $this->oldSequenceNumber;
        }

        /**
         * @return string
         */
        public function getPhaseOrStage()
        {
            return $this->phaseOrStage;
        }

        /**
         * @return string
         */
        public function getRemarks()
        {
            return $this->remarks;
        }

        /**
         * @return string
         */
        public function getSequenceNumber()
        {
            return $this->sequenceNumber;
        }

        /**
         * @return string
         */
        public function getSex()
        {
            return $this->sex;
        }
    }
