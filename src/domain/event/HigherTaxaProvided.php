<?php
    namespace sednasoft\virmisco\domain\event;

    use sednasoft\virmisco\singiere\AbstractEvent;

    class HigherTaxaProvided extends AbstractEvent
    {
        /** @var null|string */
        private $classis;
        /** @var null|string */
        private $familia;
        /** @var null|string */
        private $ordo;
        /** @var null|string */
        private $phylum;
        /** @var null|string */
        private $regnum;
        /** @var null|string */
        private $subclassis;
        /** @var null|string */
        private $subfamilia;
        /** @var null|string */
        private $subordo;
        /** @var null|string */
        private $subphylum;
        /** @var null|string */
        private $subregnum;
        /** @var null|string */
        private $superclassis;
        /** @var null|string */
        private $superfamilia;
        /** @var null|string */
        private $superordo;
        /** @var null|string */
        private $superphylum;
        /** @var null|string */
        private $tribus;

        /**
         * @param string|null $regnum
         * @param string|null $subregnum
         * @param string|null $superphylum
         * @param string|null $phylum
         * @param string|null $subphylum
         * @param string|null $superclassis
         * @param string|null $classis
         * @param string|null $subclassis
         * @param string|null $superordo
         * @param string|null $ordo
         * @param string|null $subordo
         * @param string|null $superfamilia
         * @param string|null $familia
         * @param string|null $subfamilia
         * @param string|null $tribus
         */
        public function __construct(
            $regnum,
            $subregnum,
            $superphylum,
            $phylum,
            $subphylum,
            $superclassis,
            $classis,
            $subclassis,
            $superordo,
            $ordo,
            $subordo,
            $superfamilia,
            $familia,
            $subfamilia,
            $tribus
        ) {
            parent::__construct();
            $this->regnum = $regnum;
            $this->subregnum = $subregnum;
            $this->superphylum = $superphylum;
            $this->phylum = $phylum;
            $this->subphylum = $subphylum;
            $this->superclassis = $superclassis;
            $this->classis = $classis;
            $this->subclassis = $subclassis;
            $this->superordo = $superordo;
            $this->ordo = $ordo;
            $this->subordo = $subordo;
            $this->superfamilia = $superfamilia;
            $this->familia = $familia;
            $this->subfamilia = $subfamilia;
            $this->tribus = $tribus;
        }

        /**
         * @return null|string
         */
        public function getClassis()
        {
            return $this->classis;
        }

        /**
         * @return null|string
         */
        public function getFamilia()
        {
            return $this->familia;
        }

        /**
         * @return null|string
         */
        public function getOrdo()
        {
            return $this->ordo;
        }

        /**
         * @return null|string
         */
        public function getPhylum()
        {
            return $this->phylum;
        }

        /**
         * @return null|string
         */
        public function getRegnum()
        {
            return $this->regnum;
        }

        /**
         * @return null|string
         */
        public function getSubclassis()
        {
            return $this->subclassis;
        }

        /**
         * @return null|string
         */
        public function getSubfamilia()
        {
            return $this->subfamilia;
        }

        /**
         * @return null|string
         */
        public function getSubordo()
        {
            return $this->subordo;
        }

        /**
         * @return null|string
         */
        public function getSubphylum()
        {
            return $this->subphylum;
        }

        /**
         * @return null|string
         */
        public function getSubregnum()
        {
            return $this->subregnum;
        }

        /**
         * @return null|string
         */
        public function getSuperclassis()
        {
            return $this->superclassis;
        }

        /**
         * @return null|string
         */
        public function getSuperfamilia()
        {
            return $this->superfamilia;
        }

        /**
         * @return null|string
         */
        public function getSuperordo()
        {
            return $this->superordo;
        }

        /**
         * @return null|string
         */
        public function getSuperphylum()
        {
            return $this->superphylum;
        }

        /**
         * @return null|string
         */
        public function getTribus()
        {
            return $this->tribus;
        }

    }
