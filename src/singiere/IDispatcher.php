<?php
    namespace sednasoft\virmisco\singiere;
    use Exception;
    use Generator;

    /**
     * A dispatcher receiving a command and calling the appropriate method on the designated aggregate.
     */
    interface IDispatcher {

        /**
         * Handles a command yielding all generated events or throwing an exception.
         *
         * @param AbstractCommand $command The command to handle.
         * @return Generator An AbstractEvent for every event generated during command processing.
         * @throws Exception
         */
        public function dispatch(AbstractCommand $command);
    }
