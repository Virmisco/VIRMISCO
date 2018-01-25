<?php
    namespace sednasoft\virmisco\domain\entity;

    use sednasoft\virmisco\domain\event\GatheringLogged;
    use sednasoft\virmisco\domain\event\GatheringManipulated;
    use sednasoft\virmisco\domain\event\SpecimenFromGatheringPreserved;
    use sednasoft\virmisco\singiere\AbstractAggregateRoot;
    use sednasoft\virmisco\singiere\Uuid;

    class Gathering extends AbstractAggregateRoot
    {
        /**
         * @param string $journalNumber
         * @param string $samplingDateAfter
         * @param string $samplingDateBefore
         * @param string $agentPerson
         * @param string $agentOrganization
         * @param string $locationCountry
         * @param string $locationProvince
         * @param string $locationRegion
         * @param string $locationPlace
         * @param string $remarks
         */
        public function log(
            $journalNumber,
            $samplingDateAfter,
            $samplingDateBefore,
            $agentPerson,
            $agentOrganization,
            $locationCountry,
            $locationProvince,
            $locationRegion,
            $locationPlace,
            $remarks
        ) {
            // TODO check
            $this->apply(
                new GatheringLogged(
                    $journalNumber,
                    $samplingDateAfter,
                    $samplingDateBefore,
                    $agentPerson,
                    $agentOrganization,
                    $locationCountry,
                    $locationProvince,
                    $locationRegion,
                    $locationPlace,
                    $remarks
                )
            );
        }
        /**
         * @param string $journalNumber
         * @param string $samplingDateAfter
         * @param string $samplingDateBefore
         * @param string $agentPerson
         * @param string $agentOrganization
         * @param string $locationCountry
         * @param string $locationProvince
         * @param string $locationRegion
         * @param string $locationPlace
         * @param string $remarks
         */
        public function manipulate(
            $journalNumber,
            $samplingDateAfter,
            $samplingDateBefore,
            $agentPerson,
            $agentOrganization,
            $locationCountry,
            $locationProvince,
            $locationRegion,
            $locationPlace,
            $remarks
        ) {
            // TODO check
            $this->apply(
                new GatheringManipulated(
                    $journalNumber,
                    $samplingDateAfter,
                    $samplingDateBefore,
                    $agentPerson,
                    $agentOrganization,
                    $locationCountry,
                    $locationProvince,
                    $locationRegion,
                    $locationPlace,
                    $remarks
                )
            );
        }

        /**
         * @param Uuid $specimenCarrierId
         */
        public function preserveSpecimen(Uuid $specimenCarrierId)
        {
            $this->apply(new SpecimenFromGatheringPreserved($specimenCarrierId));
        }

        /**
         * @param GatheringLogged $event
         */
        protected function applyGatheringLogged(GatheringLogged $event)
        {
            // TODO: Implement applyGatheringLogged() method.
        }

        /**
         * @param GatheringManipulated $event
         */
        protected function applyGatheringManipulated(GatheringManipulated $event)
        {
            // TODO: Implement applyGatheringManipulated() method.
        }

        /**
         * @param SpecimenFromGatheringPreserved $event
         */
        protected function applySpecimenFromGatheringPreserved(SpecimenFromGatheringPreserved $event)
        {
            // TODO: Implement applySpecimenFromGatheringPreserved() method.
        }

        /**
         * Initialize all instance variables to default values. This method will be called for instantiation and every
         * time this instance is reconstituted through replayEventStream().
         */
        protected function initializeMembers()
        {
            // TODO: Implement initializeMembers() method.
        }
    }
