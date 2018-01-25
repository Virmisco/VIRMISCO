<?php
    namespace sednasoft\virmisco\vpmh;

    use sednasoft\virmisco\opamih\MetadataFormat;
    use Traversable;

    class MetadataFormatProvider implements IMetadataFormatProvider
    {
        /** @var MetadataFormat[] */
        private $metadataFormats = [];

        /**
         * Repository constructor.
         */
        public function __construct()
        {
            $this->metadataFormats[] = new MetadataFormat(
                'oai_dc',
                'http://www.openarchives.org/OAI/2.0/oai_dc.xsd',
                'http://www.openarchives.org/OAI/2.0/oai_dc/'
            );
            $this->metadataFormats[] = new MetadataFormat(
                'lido',
                'http://www.lido-schema.org/schema/v1.0/lido-v1.0.xsd',
                'http://www.lido-schema.org'
            );
//            $this->metadataFormats[] = new MetadataFormat(
//                'abcd',
//                'http://www.bgbm.org/TDWG/CODATA/Schema/ABCD_2.06/ABCD_2.06.XSD',
//                'http://www.tdwg.org/schemas/abcd/2.06'
//            );
            $this->metadataFormats[] = new MetadataFormat(
                'sdwc',
                'http://rs.tdwg.org/dwc/xsd/tdwg_dwc_simple.xsd',
                'http://rs.tdwg.org/dwc/xsd/simpledarwincore/'
            );
            $this->metadataFormats[] = new MetadataFormat(
                'vmsc',
                'http://virmisco.org/xmlns/vmsc-1.3/records.xsd',
                'http://virmisco.org/xmlns/vmsc-1.3/'
            );
        }

        /**
         * @return int
         */
        public function countMetadataFormats()
        {
            return count($this->metadataFormats);
        }

        /**
         * @param int $index
         * @return string
         */
        public function getMetadataFormatAt($index)
        {
            return $this->metadataFormats[$index];
        }

        /**
         * @param string $metadataPrefix
         * @return int|null
         */
        public function indexOfMetadataFormat($metadataPrefix)
        {
            /** @var MetadataFormat $format */
            foreach ($this->metadataFormats as $index => $format) {
                if (strval($format) == $metadataPrefix) {
                    return $index;
                }
            }

            return null;
        }

        /**
         * @param int $index
         * @param int $length
         * @return Traversable Each member is an instance of MetadataFormat.
         */
        public function iterateMetadataFormatsInRange($index, $length)
        {
            /** @var MetadataFormat $format */
            foreach (array_slice($this->metadataFormats, $index, $length) as $format) {
                yield $format;
            }
        }
    }
