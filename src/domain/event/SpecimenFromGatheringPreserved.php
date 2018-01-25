<?php
    namespace sednasoft\virmisco\domain\event;

    use sednasoft\virmisco\singiere\AbstractEvent;
    use sednasoft\virmisco\singiere\Uuid;

    class SpecimenFromGatheringPreserved extends AbstractEvent
    {
        /** @var Uuid */
        private $specimenCarrierId;

        /**
         * SpecimenFromGatheringPreserved constructor.
         * @param Uuid $specimenCarrierId
         */
        public function __construct(UUid $specimenCarrierId)
        {
            parent::__construct();
            $this->specimenCarrierId = $specimenCarrierId;
        }

        /**
         * @return Uuid
         */
        public function getSpecimenCarrierId()
        {
            return $this->specimenCarrierId;
        }
    }
