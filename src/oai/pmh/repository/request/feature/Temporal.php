<?php
    namespace sednasoft\virmisco\oai\pmh\repository\request\feature;

    use DateTimeInterface;

    /**
     * Enables selective harvesting for a request based on an optional date/time range.
     */
    interface Temporal
    {

        /**
         * @return DateTimeInterface|null An optional argument with a UTCdatetime value, which specifies a lower bound
         * for datestamp-based selective harvesting.
         */
        public function getFrom();

        /**
         * @return DateTimeInterface|null An optional argument with a UTCdatetime value, which specifies an upper bound
         * for datestamp-based selective harvesting.
         */
        public function getUntil();

        /**
         * @return bool Whether the optional from argument is present.
         */
        public function hasFrom();

        /**
         * @return bool Whether the optional until argument is present.
         */
        public function hasUntil();
    }
