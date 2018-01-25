<?php
    namespace sednasoft\virmisco\domain\event;

    use sednasoft\virmisco\domain\valueobject\ScientificName;
    use sednasoft\virmisco\singiere\AbstractEvent;

    class SynonymAssignedToTaxon extends AbstractEvent
    {
        /** @var ScientificName */
        private $synonym;

        /**
         * Creates a new instance. Subclasses should add more parameters for payload.
         *
         * @param ScientificName $synonym
         */
        public function __construct(ScientificName $synonym)
        {
            parent::__construct();
            $this->synonym = $synonym;
        }

        /**
         * @return ScientificName
         */
        public function getSynonym()
        {
            return $this->synonym;
        }
    }
