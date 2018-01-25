<?php
    namespace sednasoft\virmisco\domain;

    use Exception;
    use sednasoft\virmisco\singiere\AbstractCommand;
    use sednasoft\virmisco\singiere\IRepository;

    abstract class AbstractCommandHandler
    {
        /** @var IRepository */
        private $repository;

        /**
         * @param IRepository $repository
         */
        public function __construct(IRepository $repository)
        {
            $this->repository = $repository;
        }

        /**
         * Executes the command by loading the required aggregate from the repository and calling methods on the
         * aggregate. In case of exceptions they should be let bubble up. After the modifying method calls, the
         * repository should be instructed to save the aggregate.
         *
         * @param AbstractCommand $command
         * @throws Exception
         */
        abstract public function execute(AbstractCommand $command);

        /**
         * @return IRepository
         */
        protected function getRepository()
        {
            return $this->repository;
        }
    }
