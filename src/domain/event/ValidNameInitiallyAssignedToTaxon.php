<?php
    namespace sednasoft\virmisco\domain\event;

    use sednasoft\virmisco\domain\valueobject\ScientificName;
    use sednasoft\virmisco\singiere\AbstractEvent;

    class ValidNameInitiallyAssignedToTaxon extends AbstractEvent
    {
        /** @var ScientificName */
        private $validName;

        /**
         * Creates a new instance. Subclasses should add more parameters for payload.
         *
         * @param ScientificName $validName
         */
        public function __construct(ScientificName $validName)
        {
            parent::__construct();
            $this->validName = $validName;
        }

        /**
         * @return ScientificName
         */
        public function getValidName()
        {
            return $this->validName;
        }
    }
