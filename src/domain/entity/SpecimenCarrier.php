<?php
    namespace sednasoft\virmisco\domain\entity;

    use sednasoft\virmisco\domain\event\HigherTaxaForOrganismProvided;
    use sednasoft\virmisco\domain\event\MentionedNameOfOrganismChanged;
    use sednasoft\virmisco\domain\event\NameOriginOfOrganismChanged;
    use sednasoft\virmisco\domain\event\OrganismDescriptionDiscarded;
    use sednasoft\virmisco\domain\event\OrganismDigitized;
    use sednasoft\virmisco\domain\event\OrganismOnSpecimenCarrierDescribed;
    use sednasoft\virmisco\domain\event\OrganismOnSpecimenCarrierDesignatedAsType;
    use sednasoft\virmisco\domain\event\OrganismOnSpecimenCarrierIdentified;
    use sednasoft\virmisco\domain\event\OrganismOnSpecimenCarrierManipulated;
    use sednasoft\virmisco\domain\event\SpecimenCarrierManipulated;
    use sednasoft\virmisco\domain\event\SpecimenCarrierRecorded;
    use sednasoft\virmisco\domain\event\SpecimenCarrierScannedToImage;
    use sednasoft\virmisco\domain\event\SynonymForOrganismProvided;
    use sednasoft\virmisco\domain\event\SynonymsOfOrganismCleared;
    use sednasoft\virmisco\domain\event\ValidNameOfOrganismChanged;
    use sednasoft\virmisco\singiere\AbstractAggregateRoot;
    use sednasoft\virmisco\singiere\Uuid;

    class SpecimenCarrier extends AbstractAggregateRoot
    {
        /**
         * @param string $sequenceNumber
         * @param string $genus
         * @param string|null $subgenus
         * @param string $specificEpithet
         * @param string|null $infraspecificEpithet
         * @param string|null $authorship
         * @param int|null $year
         * @param bool $parenthesized
         */
        public function changeMentionedNameOfOrganism(
            $sequenceNumber,
            $genus,
            $subgenus,
            $specificEpithet,
            $infraspecificEpithet,
            $authorship,
            $year,
            $parenthesized
        ) {
            $this->apply(
                new MentionedNameOfOrganismChanged(
                    $sequenceNumber,
                    $genus,
                    $subgenus,
                    $specificEpithet,
                    $infraspecificEpithet,
                    $authorship,
                    $year,
                    $parenthesized
                )
            );
        }

        /**
         * @param string $sequenceNumber
         * @param string|null $typeStatus
         * @param string|null $identifier
         * @param string|null $qualifier
         */
        public function changeNameOriginOfOrganism($sequenceNumber, $typeStatus, $identifier, $qualifier)
        {
            $this->apply(new NameOriginOfOrganismChanged($sequenceNumber, $typeStatus, $identifier, $qualifier));
        }

        /**
         * @param string $sequenceNumber
         * @param string $genus
         * @param string|null $subgenus
         * @param string $specificEpithet
         * @param string|null $infraspecificEpithet
         * @param string|null $authorship
         * @param int|null $year
         * @param bool $parenthesized
         */
        public function changeValidNameOfOrganism(
            $sequenceNumber,
            $genus,
            $subgenus,
            $specificEpithet,
            $infraspecificEpithet,
            $authorship,
            $year,
            $parenthesized
        ) {
            $this->apply(
                new ValidNameOfOrganismChanged(
                    $sequenceNumber,
                    $genus,
                    $subgenus,
                    $specificEpithet,
                    $infraspecificEpithet,
                    $authorship,
                    $year,
                    $parenthesized
                )
            );
        }

        /**
         * @param string $sequenceNumber
         */
        public function clearSynonymsOfOrganism($sequenceNumber)
        {
            $this->apply(new SynonymsOfOrganismCleared($sequenceNumber));
        }

        /**
         * @param string $sequenceNumber
         * @param string $sex
         * @param string $phaseOrStage
         * @param string $remarks
         */
        public function describeOrganism($sequenceNumber, $sex, $phaseOrStage, $remarks)
        {
            $this->apply(new OrganismOnSpecimenCarrierDescribed($sequenceNumber, $sex, $phaseOrStage, $remarks));
        }

        /**
         * @param string $sequenceNumber
         * @param string $genus
         * @param string $subgenus
         * @param string $specificEpithet
         * @param string $infraspecificEpithet
         * @param string $authorship
         * @param int $year
         * @param bool $parenthesized
         * @param string $typeStatus
         */
        public function designateOrganismAsType(
            $sequenceNumber,
            $genus,
            $subgenus,
            $specificEpithet,
            $infraspecificEpithet,
            $authorship,
            $year,
            $parenthesized,
            $typeStatus
        ) {
            $this->apply(
                new OrganismOnSpecimenCarrierDesignatedAsType(
                    $sequenceNumber,
                    $genus,
                    $subgenus,
                    $specificEpithet,
                    $infraspecificEpithet,
                    $authorship,
                    $year,
                    $parenthesized,
                    $typeStatus
                )
            );
        }

        /**
         * @param string $sequenceNumber
         * @param Uuid $photomicrographId
         */
        public function digitizeOrganism($sequenceNumber, Uuid $photomicrographId)
        {
            $this->apply(new OrganismDigitized($sequenceNumber, $photomicrographId));
        }

        /**
         * @param string $sequenceNumber
         */
        public function discardOrganismDescription($sequenceNumber)
        {
            $this->apply(new OrganismDescriptionDiscarded($sequenceNumber));
        }

        /**
         * @param string $sequenceNumber
         * @param string $genus
         * @param string $subgenus
         * @param string $specificEpithet
         * @param string $infraspecificEpithet
         * @param string $authorship
         * @param int $year
         * @param bool $parenthesized
         * @param string $identifier
         * @param string $qualifier
         */
        public function identifyOrganism(
            $sequenceNumber,
            $genus,
            $subgenus,
            $specificEpithet,
            $infraspecificEpithet,
            $authorship,
            $year,
            $parenthesized,
            $identifier,
            $qualifier
        ) {
            $this->apply(
                new OrganismOnSpecimenCarrierIdentified(
                    $sequenceNumber,
                    $genus,
                    $subgenus,
                    $specificEpithet,
                    $infraspecificEpithet,
                    $authorship,
                    $year,
                    $parenthesized,
                    $identifier,
                    $qualifier
                )
            );
        }

        /**
         * @param string $carrierNumber
         * @param string $preparationType
         * @param string $owner
         * @param string $previousCollection
         * @param string $labelTranscript
         */
        public function manipulate(
            $carrierNumber,
            $preparationType,
            $owner,
            $previousCollection,
            $labelTranscript
        ) {
            // TODO check
            $this->apply(
                new SpecimenCarrierManipulated(
                    $carrierNumber,
                    $preparationType,
                    $owner,
                    $previousCollection,
                    $labelTranscript
                )
            );
        }

        /**
         * @param string $oldSequenceNumber
         * @param string $newSequenceNumber
         * @param string $sex
         * @param string $phaseOrStage
         * @param string $remarks
         */
        public function manipulateOrganism($oldSequenceNumber, $newSequenceNumber, $sex, $phaseOrStage, $remarks)
        {
            $this->apply(
                new OrganismOnSpecimenCarrierManipulated(
                    $oldSequenceNumber,
                    $newSequenceNumber,
                    $sex,
                    $phaseOrStage,
                    $remarks
                )
            );
        }

        /**
         * @param string $sequenceNumber
         * @param string $higherTaxa
         */
        public function provideHigherTaxaForOrganism($sequenceNumber, $higherTaxa)
        {
            $this->apply(new HigherTaxaForOrganismProvided($sequenceNumber, $higherTaxa));
        }

        /**
         * @param string $sequenceNumber
         * @param string $genus
         * @param string|null $subgenus
         * @param string $specificEpithet
         * @param string|null $infraspecificEpithet
         * @param string|null $authorship
         * @param int|null $year
         * @param bool $parenthesized
         */
        public function provideSynonymForOrganism(
            $sequenceNumber,
            $genus,
            $subgenus,
            $specificEpithet,
            $infraspecificEpithet,
            $authorship,
            $year,
            $parenthesized
        ) {
            $this->apply(
                new SynonymForOrganismProvided(
                    $sequenceNumber,
                    $genus,
                    $subgenus,
                    $specificEpithet,
                    $infraspecificEpithet,
                    $authorship,
                    $year,
                    $parenthesized
                )
            );
        }

        /**
         * @param Uuid $gatheringId
         * @param string $carrierNumber
         * @param string $preparationType
         * @param string $owner
         * @param string $previousCollection
         * @param string $labelTranscript
         */
        public function record(
            Uuid $gatheringId,
            $carrierNumber,
            $preparationType,
            $owner,
            $previousCollection,
            $labelTranscript
        ) {
            // TODO check
            $this->apply(
                new SpecimenCarrierRecorded(
                    $gatheringId,
                    $carrierNumber,
                    $preparationType,
                    $owner,
                    $previousCollection,
                    $labelTranscript
                )
            );
        }

        /**
         * @param string $realPath
         * @param string $uri
         * @param string $creationTime
         * @param string $modificationTime
         */
        public function scanToImageFile($realPath, $uri, $creationTime, $modificationTime)
        {
            $this->apply(new SpecimenCarrierScannedToImage($realPath, $uri, $creationTime, $modificationTime));
        }

        /**
         * @param HigherTaxaForOrganismProvided $event
         */
        protected function applyHigherTaxaForOrganismProvided(HigherTaxaForOrganismProvided $event)
        {
            // TODO: Implement applyHigherTaxaForOrganismProvided() method.
        }

        /**
         * @param MentionedNameOfOrganismChanged $event
         */
        protected function applyMentionedNameOfOrganismChanged(MentionedNameOfOrganismChanged $event)
        {
            // TODO: Implement applyMentionedNameOfOrganismChanged() method.
        }

        /**
         * @param NameOriginOfOrganismChanged $event
         */
        protected function applyNameOriginOfOrganismChanged(NameOriginOfOrganismChanged $event)
        {
            // TODO: Implement applyNameOriginOfOrganismChanged() method.
        }

        /**
         * @param OrganismDigitized $event
         */
        protected function applyOrganismDigitized(OrganismDigitized $event)
        {
            // TODO: Implement applyOrganismDigitized() method.
        }

        /**
         * @param OrganismOnSpecimenCarrierDescribed $event
         */
        protected function applyOrganismOnSpecimenCarrierDescribed(OrganismOnSpecimenCarrierDescribed $event)
        {
            // TODO: Implement applyOrganismOnSpecimenCarrierDescribed() method.
        }

        /**
         * @param OrganismOnSpecimenCarrierDesignatedAsType $event
         */
        protected function applyOrganismOnSpecimenCarrierDesignatedAsType(
            OrganismOnSpecimenCarrierDesignatedAsType $event
        ) {
            // TODO: Implement applyOrganismOnSpecimenCarrierDesignatedAsType() method.
        }

        /**
         * @param OrganismOnSpecimenCarrierIdentified $event
         */
        protected function applyOrganismOnSpecimenCarrierIdentified(OrganismOnSpecimenCarrierIdentified $event)
        {
            // TODO: Implement applyOrganismOnSpecimenCarrierIdentified() method.
        }

        /**
         * @param OrganismDescriptionDiscarded $event
         */
        protected function applyOrganismDescriptionDiscarded(OrganismDescriptionDiscarded $event)
        {
            // TODO: Implement applyOrganismDescriptionDiscarded() method.
        }

        /**
         * @param OrganismOnSpecimenCarrierManipulated $event
         */
        protected function applyOrganismOnSpecimenCarrierManipulated(OrganismOnSpecimenCarrierManipulated $event)
        {
            // TODO: Implement applyOrganismOnSpecimenCarrierManipulated() method.
        }

        /**
         * @param SpecimenCarrierManipulated $event
         */
        protected function applySpecimenCarrierManipulated(SpecimenCarrierManipulated $event)
        {
            // TODO: Implement applySpecimenCarrierManipulated() method.
        }

        /**
         * @param SpecimenCarrierRecorded $event
         */
        protected function applySpecimenCarrierRecorded(SpecimenCarrierRecorded $event)
        {
            // TODO: Implement applySpecimenCarrierRecorded() method.
        }

        /**
         * @param SpecimenCarrierScannedToImage $event
         */
        protected function applySpecimenCarrierScannedToImage(SpecimenCarrierScannedToImage $event)
        {
            // TODO: Implement applySpecimenCarrierScannedToImage() method.
        }

        /**
         * @param SynonymForOrganismProvided $event
         */
        protected function applySynonymForOrganismProvided(SynonymForOrganismProvided $event)
        {
            // TODO: Implement applySynonymForOrganismProvided() method.
        }

        /**
         * @param SynonymsOfOrganismCleared $event
         */
        protected function applySynonymsOfOrganismCleared(SynonymsOfOrganismCleared $event)
        {
            // TODO: Implement applySynonymsOfOrganismCleared() method.
        }

        /**
         * @param ValidNameOfOrganismChanged $event
         */
        protected function applyValidNameOfOrganismChanged(ValidNameOfOrganismChanged $event)
        {
            // TODO: Implement applyValidNameOfOrganismChanged() method.
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
