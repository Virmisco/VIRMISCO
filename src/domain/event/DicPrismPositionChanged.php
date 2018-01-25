<?php
    namespace sednasoft\virmisco\domain\event;

    use sednasoft\virmisco\singiere\AbstractEvent;

    class DicPrismPositionChanged extends AbstractEvent
    {
        /** @var float */
        private $dicPrismPosition;

        /**
         * DicPrismPositionChanged constructor.
         * @param float $dicPrismPosition
         */
        public function __construct($dicPrismPosition)
        {
            parent::__construct();
            $this->dicPrismPosition = $dicPrismPosition;
        }

        /**
         * @return float
         */
        public function getDicPrismPosition()
        {
            return $this->dicPrismPosition;
        }
    }
