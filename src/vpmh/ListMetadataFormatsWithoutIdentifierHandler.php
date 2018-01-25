<?php
    namespace sednasoft\virmisco\vpmh;

    use sednasoft\virmisco\oai\pmh\repository\Request;
    use sednasoft\virmisco\oai\pmh\repository\request\ListMetadataFormats;
    use sednasoft\virmisco\oai\pmh\repository\Response;
    use sednasoft\virmisco\opamih\AbstractHandler;
    use sednasoft\virmisco\opamih\ListMetadataFormatsResponse;
    use sednasoft\virmisco\opamih\MetadataFormat;

    /**
     * Handles the request to list metadata formats that are supported by the repository, not by a certain item within.
     */
    class ListMetadataFormatsWithoutIdentifierHandler extends AbstractHandler
    {
        /** @var IMetadataFormatProvider */
        private $metadataFormatProvider;

        /**
         * @param AbstractHandler $nextHandler
         * @param IMetadataFormatProvider $metadataFormatProvider
         */
        public function __construct(AbstractHandler $nextHandler, IMetadataFormatProvider $metadataFormatProvider)
        {
            parent::__construct($nextHandler);
            $this->metadataFormatProvider = $metadataFormatProvider;
        }

        /**
         * @param Request $request The current request to handle if possible.
         * @param string $dateGranularity One of the Response::HG_* constants.
         * @return null|Response A response object, when the request could be handled, null to pass the request on to
         * the next handler.
         */
        protected function handleRequest(Request $request, $dateGranularity)
        {
            if (($request instanceof ListMetadataFormats) && !$request->hasIdentifier()) {
                $response = new ListMetadataFormatsResponse($request, $dateGranularity);
                /** @var MetadataFormat $format */
                foreach ($this->metadataFormatProvider->iterateMetadataFormatsInRange(0, PHP_INT_MAX) as $format) {
                    $response->addMetadataFormat($format);
                }

                return $response;
            }

            return null;
        }
    }
