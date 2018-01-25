<?php
    namespace sednasoft\virmisco\domain\command;

    use sednasoft\virmisco\singiere\AbstractCommand;
    use sednasoft\virmisco\singiere\Uuid;

    class DeletePhotomicrograph extends AbstractCommand
    {
        /**
         * ChangeDicPrismPositionOfPhotomicrograph constructor.
         * @param Uuid $aggregateId
         */
        public function __construct(Uuid $aggregateId)
        {
            parent::__construct($aggregateId);
        }
    }
