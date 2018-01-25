<?php
    namespace sednasoft\virmisco\vpmh;

    interface IIdentificationProvider
    {
        /**
         * @return string
         */
        public function getAdminEmail();

        /**
         * @return string
         */
        public function getBaseUrl();

        /**
         * @return string
         */
        public function getRepositoryName();
    }
