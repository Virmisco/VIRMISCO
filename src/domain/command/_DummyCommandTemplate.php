<?php
    namespace sednasoft\virmisco\domain\command;

    use sednasoft\virmisco\singiere\AbstractCommand;
    use sednasoft\virmisco\singiere\Uuid;

    class _DummyCommandTemplate extends AbstractCommand
    {

        /**
         * Creates a new instance. Subclasses should add more parameters for payload.
         *
         * @param Uuid $aggregateId The unique identifier of the aggregate to receive this command.
         */
        public function __construct(
            Uuid $aggregateId
        ) {
            parent::__construct($aggregateId);
        }
    }
