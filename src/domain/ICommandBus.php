<?php
    namespace sednasoft\virmisco\domain;

    use Exception;
    use sednasoft\virmisco\singiere\AbstractCommand;

    interface ICommandBus
    {
        /**
         * @param AbstractCommand $command
         * @throws Exception
         */
        public function dispatch(AbstractCommand $command);
    }
