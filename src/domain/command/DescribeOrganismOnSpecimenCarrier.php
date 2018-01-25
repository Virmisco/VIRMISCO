<?php
    namespace sednasoft\virmisco\domain\command;

    use sednasoft\virmisco\singiere\AbstractCommand;
    use sednasoft\virmisco\singiere\Uuid;

    class DescribeOrganismOnSpecimenCarrier extends AbstractCommand
    {
        /** @var string */
        private $phaseOrStage;
        /** @var string */
        private $remarks;
        /** @var string */
        private $sequenceNumber;
        /** @var string */
        private $sex;

        /**
         * Creates a new instance. Subclasses should add more parameters for payload.
         *
         * @param Uuid $specimenCarrierId The unique identifier of the aggregate to receive this command.
         * @param string $sequenceNumber
         * @param string $phaseOrStage
         * @param string $sex
         * @param string $remarks
         */
        public function __construct(
            Uuid $specimenCarrierId,
            $sequenceNumber,
            $phaseOrStage,
            $sex,
            $remarks
        ) {
            parent::__construct($specimenCarrierId);
            $this->sequenceNumber = (string)$sequenceNumber;
            $this->phaseOrStage = (string)$phaseOrStage;
            $this->sex = (string)$sex;
            $this->remarks = (string)$remarks;
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
