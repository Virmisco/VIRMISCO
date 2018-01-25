<?php
    namespace sednasoft\virmisco\opamih;

    use sednasoft\virmisco\oai\pmh\repository\data\MetadataFormat as IMetadataFormat;
    use sednasoft\virmisco\oai\pmh\repository\Request as IRequest;
    use sednasoft\virmisco\oai\pmh\repository\response\ListMetadataFormats as IListMetadataFormats;

    /**
     * A successful response to a ListMetadataFormats request.
     */
    class ListMetadataFormatsResponse extends AbstractResponse implements IListMetadataFormats
    {
        /**
         * @param IRequest $request
         * @param string $dateGranularity One of the Response::HG_* constants.
         */
        public function __construct(IRequest $request, $dateGranularity)
        {
            parent::__construct($request, 'ListMetadataFormats', $dateGranularity);
        }

        /**
         * @param IMetadataFormat $metadataFormat
         */
        public function addMetadataFormat(IMetadataFormat $metadataFormat)
        {
            $this->getContainer()->appendChild($metadataFormat->toDomFragment($this->getDocument()));
        }
    }
