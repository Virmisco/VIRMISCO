<?php
    namespace sednasoft\virmisco\domain\valueobject;

    class ScientificName
    {
        /** @var null|string */
        private $authorship;
        /** @var string */
        private $genusOrMonomial;
        /** @var null|string */
        private $infraSpecificEpithet;
        /** @var bool|false */
        private $parenthesized;
        /** @var null|string */
        private $specificEpithet;
        /** @var null|string */
        private $subgenus;
        /** @var int|null */
        private $year;

        /**
         * @param string $genusOrMonomial
         * @param string|null $subgenus
         * @param string|null $specificEpithet
         * @param string|null $infraSpecificEpithet
         * @param string|null $authorship
         * @param int|null $year
         * @param bool|false $parenthesized
         */
        public function __construct(
            $genusOrMonomial,
            $subgenus = null,
            $specificEpithet = null,
            $infraSpecificEpithet = null,
            $authorship = null,
            $year = null,
            $parenthesized = false
        ) {
            $this->genusOrMonomial = $genusOrMonomial;
            $this->subgenus = $subgenus;
            $this->specificEpithet = $specificEpithet;
            $this->infraSpecificEpithet = $infraSpecificEpithet;
            $this->authorship = $authorship;
            $this->year = $year;
            $this->parenthesized = $parenthesized;
        }

        /**
         * @param mixed $scientificName
         * @return bool
         */
        public function equals($scientificName)
        {
            return ($scientificName instanceof self)
            && $scientificName->getGenusOrMonomial() === $this->getGenusOrMonomial()
            && $scientificName->getSubgenus() === $this->getSubgenus()
            && $scientificName->getSpecificEpithet() === $this->getSpecificEpithet()
            && $scientificName->getInfraSpecificEpithet() === $this->getInfraSpecificEpithet()
            && $scientificName->getAuthorship() === $this->getAuthorship()
            && $scientificName->getYear() === $this->getYear()
            && $scientificName->isParenthesized() === $this->isParenthesized();
        }

        /**
         * @return null|string
         */
        public function getAuthorship()
        {
            return $this->authorship;
        }

        /**
         * @return string
         */
        public function getGenusOrMonomial()
        {
            return $this->genusOrMonomial;
        }

        /**
         * @return null|string
         */
        public function getInfraSpecificEpithet()
        {
            return $this->infraSpecificEpithet;
        }

        /**
         * @return null|string
         */
        public function getSpecificEpithet()
        {
            return $this->specificEpithet;
        }

        /**
         * @return null|string
         */
        public function getSubgenus()
        {
            return $this->subgenus;
        }

        /**
         * @return int|null
         */
        public function getYear()
        {
            return $this->year;
        }

        /**
         * @return bool
         */
        public function isParenthesized()
        {
            return $this->parenthesized;
        }
    }
