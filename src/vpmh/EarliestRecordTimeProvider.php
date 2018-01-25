<?php
    namespace sednasoft\virmisco\vpmh;

    use DateTime;

    /**
     * Retrieves the earliest datestamp any record in the repository can have as creation or modification date.
     */
    interface EarliestRecordTimeProvider
    {
        /**
         * @return DateTime
         */
        public function getEarliestRecordTime();
    }
