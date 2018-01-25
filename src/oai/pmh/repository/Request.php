<?php
    namespace sednasoft\virmisco\oai\pmh\repository;

    use Traversable;

    /**
     * A request from a client, received over HTTP with a certain verb parameter that specifies one of six predefined
     * queries.
     */
    interface Request
    {
        /**
         * @return Traversable A traversable yielding key-value-pairs of other parameters (apart from verb).
         */
        public function getParameters();

        /**
         * @return string The request URI.
         */
        public function getRequestUri();

        /**
         * @return string The request verb.
         */
        public function getVerb();
    }
