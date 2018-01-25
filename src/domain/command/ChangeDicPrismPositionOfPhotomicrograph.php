<?php
    namespace sednasoft\virmisco\domain\command;

    use sednasoft\virmisco\singiere\AbstractCommand;
    use sednasoft\virmisco\singiere\Uuid;

    class ChangeDicPrismPositionOfPhotomicrograph extends AbstractCommand
    {
        /** @var float */
        private $dicPrismPosition;

        /**
         * ChangeDicPrismPositionOfPhotomicrograph constructor.
         * @param Uuid $aggregateId
         * @param float $dicPrismPosition
         */
        public function __construct(Uuid $aggregateId, $dicPrismPosition)
        {
            parent::__construct($aggregateId);
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
