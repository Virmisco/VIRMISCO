<?php
    namespace sednasoft\virmisco\vpmh;

    use Traversable;

    /**
     * Retrieves Set objects and related information.
     */
    interface ISetProvider
    {
        /**
         * @return int
         */
        public function countSets();

        /**
         * @param string $setSpec
         * @return int|null
         */
        public function indexOfSet($setSpec);

        /**
         * @param int $index
         * @param int $length
         * @return Traversable Each member is an instance of Set.
         */
        public function iterateSetsInRange($index, $length);
    }
