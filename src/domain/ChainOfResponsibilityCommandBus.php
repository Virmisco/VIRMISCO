<?php
    namespace sednasoft\virmisco\domain;

    use Exception;
    use sednasoft\virmisco\singiere\AbstractCommand;

    /**
     * Serves as a command bus that only passes commands to its only handler which acts as the head of a chain of
     * responsibility.
     */
    class ChainOfResponsibilityCommandBus implements ICommandBus
    {
        /** @var AbstractChainedCommandHandler */
        private $headOfChain;

        /**
         * Creates an instance and initializes all components.
         * @param AbstractChainedCommandHandler $headOfChain
         */
        public function __construct(AbstractChainedCommandHandler $headOfChain)
        {
            $this->headOfChain = $headOfChain;
        }

        /**
         * @param AbstractCommand $command
         * @throws Exception
         */
        public function dispatch(AbstractCommand $command)
        {
            $this->headOfChain->execute($command);
        }
    }
