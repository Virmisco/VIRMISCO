<?php
    namespace sednasoft\virmisco\vpmh;

    use sednasoft\virmisco\oai\pmh\repository\data\MetadataFormat;
    use Traversable;

    /**
     * Retrieves MetadataFormat objects and related information.
     */
    interface IMetadataFormatProvider
    {
        /**
         * @return int
         */
        public function countMetadataFormats();

        /**
         * @param int $index
         * @return MetadataFormat
         */
        public function getMetadataFormatAt($index);

        /**
         * @param string $metadataPrefix
         * @return int|null
         */
        public function indexOfMetadataFormat($metadataPrefix);

        /**
         * @param int $index
         * @param int $length
         * @return Traversable Each member is an instance of MetadataFormat.
         */
        public function iterateMetadataFormatsInRange($index, $length);
    }
