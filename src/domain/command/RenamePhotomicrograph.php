<?php
    namespace sednasoft\virmisco\domain\command;

    use sednasoft\virmisco\singiere\AbstractCommand;
    use sednasoft\virmisco\singiere\Uuid;

    class RenamePhotomicrograph extends AbstractCommand
    {
        /** @var string */
        private $title;

        /**
         * RenamePhotomicrograph constructor.
         * @param Uuid $aggregateId
         * @param $title
         */
        public function __construct(Uuid $aggregateId, $title)
        {
            parent::__construct($aggregateId);
            $this->title = $title;
        }

        /**
         * @return string
         */
        public function getTitle()
        {
            return $this->title;
        }
    }
