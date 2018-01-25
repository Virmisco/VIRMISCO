<?php
    namespace sednasoft\virmisco\vpmh;

    use sednasoft\virmisco\oai\pmh\repository\Request;
    use sednasoft\virmisco\oai\pmh\repository\request\GetRecord;
    use sednasoft\virmisco\oai\pmh\repository\Response;
    use sednasoft\virmisco\opamih\AbstractHandler;
    use sednasoft\virmisco\opamih\GetRecordResponse;

    /**
     * Handles the request to get a specific record for an identifier and in a certain metadata format.
     */
    class GetRecordHandler extends AbstractHandler
    {
        /** @var IRecordProvider */
        private $recordProvider;

        /**
         * @param AbstractHandler $nextHandler
         * @param IRecordProvider $recordProvider
         */
        public function __construct(AbstractHandler $nextHandler, IRecordProvider $recordProvider)
        {
            parent::__construct($nextHandler);
            $this->recordProvider = $recordProvider;
        }

        /**
         * @param Request $request The current request to handle if possible.
         * @param string $dateGranularity One of the Response::HG_* constants.
         * @return null|Response A response object, when the request could be handled, null to pass the request on to
         * the next handler.
         */
        protected function handleRequest(Request $request, $dateGranularity)
        {
            if ($request instanceof GetRecord) {
                $record = $this->recordProvider->getRecordByIdentifier(
                    $request->getIdentifier(),
                    $request->getMetadataPrefix(),
                    false
                );
                if ($record) {
                    $response = new GetRecordResponse($request, $dateGranularity);
                    $response->setRecord($record);

                    return $response;
                }
            }

            return null;
        }
    }
