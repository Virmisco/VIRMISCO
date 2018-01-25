<?php
    namespace sednasoft\virmisco\vpmh;

    use sednasoft\virmisco\oai\pmh\repository\data\Header;
    use sednasoft\virmisco\oai\pmh\repository\Request;
    use sednasoft\virmisco\oai\pmh\repository\request\ListIdentifiers;
    use sednasoft\virmisco\oai\pmh\repository\request\ListRecords;
    use sednasoft\virmisco\oai\pmh\repository\Response;
    use sednasoft\virmisco\opamih\AbstractHandler;
    use sednasoft\virmisco\opamih\ListIdentifiersResponse;
    use sednasoft\virmisco\opamih\ListRecordsResponse;
    use sednasoft\virmisco\opamih\Record;
    use sednasoft\virmisco\opamih\ResumptionToken;

    /**
     * Handles requests to list the available set hierarchy by querying the respective provider.
     */
    class ListRecordsHandler extends AbstractResumableRequestHandler
    {
        /** @var EarliestRecordTimeProvider */
        private $earliestRecordTimeProvider;
        /** @var IMetadataFormatProvider */
        private $metadataFormatProvider;
        /** @var int */
        private $pageSize;
        /** @var IRecordProvider */
        private $recordProvider;
        /** @var ISetProvider */
        private $setProvider;

        /**
         * @param AbstractHandler $nextHandler
         * @param EarliestRecordTimeProvider $earliestRecordTimeProvider
         * @param IMetadataFormatProvider $metadataFormatProvider
         * @param IRecordProvider $recordProvider
         * @param ISetProvider $setProvider
         * @param int $pageSize
         */
        public function __construct(
            AbstractHandler $nextHandler,
            EarliestRecordTimeProvider $earliestRecordTimeProvider,
            IMetadataFormatProvider $metadataFormatProvider,
            IRecordProvider $recordProvider,
            ISetProvider $setProvider,
            $pageSize = 256
        ) {
            parent::__construct($nextHandler);
            $this->earliestRecordTimeProvider = $earliestRecordTimeProvider;
            $this->metadataFormatProvider = $metadataFormatProvider;
            $this->setProvider = $setProvider;
            $this->pageSize = $pageSize;
            $this->recordProvider = $recordProvider;
        }

        /**
         * @return EarliestRecordTimeProvider
         */
        protected function getEarliestRecordTimeProvider()
        {
            return $this->earliestRecordTimeProvider;
        }

        /**
         * @return IMetadataFormatProvider
         */
        protected function getMetadataFormatProvider()
        {
            return $this->metadataFormatProvider;
        }

        /**
         * @return ISetProvider
         */
        protected function getSetProvider()
        {
            return $this->setProvider;
        }

        /**
         * @param Request $request The current request to handle if possible.
         * @param string $dateGranularity One of the Response::HG_* constants.
         * @return null|Response A response object, when the request could be handled, null to pass the request on to
         * the next handler.
         */
        protected function handleRequest(Request $request, $dateGranularity)
        {
            $headerOnly = $request instanceof ListIdentifiers;
            if ($headerOnly || ($request instanceof ListRecords)) {
                $recordProvider = $this->recordProvider;
                if ($request->hasResumptionToken()) {
                    $token = $request->getResumptionToken();
                    list($type, $page, $pageSize, $mdPrefix, $setSpec, $from, $until) = $this->decodeToken($token);
                    if (!($request instanceof $type)) {
                        // let another handler throw errors for invalid resumption tokens
                        return null;
                    }
                    $response = $headerOnly
                        ? new ListIdentifiersResponse($request, $dateGranularity)
                        : new ListRecordsResponse($request, $dateGranularity);
                    $k = 0;
                    $iterator = $recordProvider->iterateRecordsInRange(
                        $page * $pageSize, $pageSize + 1, $mdPrefix, $headerOnly, $setSpec, $from, $until
                    );
                    /** @var Record|Header $record */
                    foreach ($iterator as $k => $record) {
                        // we retrieved 1 more than the page size to see whether we need another page after this one
                        if ($k < $pageSize) {
                            if ($headerOnly) {
                                $response->addHeader($record);
                            } else {
                                $response->addRecord($record);
                            }
                        } else {
                            break;
                        }
                    }
                    if (!isset($record)) {
                        // let another handler throw errors for invalid resumption tokens
                        return null;
                    }
                    $response->setResumptionToken(
                        new ResumptionToken(
                            $k < $pageSize
                                ? null
                                : $this->encodeToken($request, $page + 1, $pageSize, $mdPrefix, $setSpec, $from,
                                $until),
                            null,
                            $k < $pageSize
                                ? $page * $pageSize + $k + 1
                                : $recordProvider->countRecords($mdPrefix, $setSpec, $from, $until),
                            $page * $pageSize
                        )
                    );

                    return $response;
                }
                $response = $headerOnly
                    ? new ListIdentifiersResponse($request, $dateGranularity)
                    : new ListRecordsResponse($request, $dateGranularity);
                $mdPrefix = $request->getMetadataPrefix();
                $setSpec = $request->getSet();
                $from = $request->getFrom();
                $until = $request->getUntil();
                $iterator = $recordProvider->iterateRecordsInRange(
                    0, $this->pageSize + 1, $mdPrefix, $headerOnly, $setSpec, $from, $until
                );
                /** @var Record|Header $record */
                foreach ($iterator as $k => $record) {
                    if ($k < $this->pageSize) {
                        if ($headerOnly) {
                            $response->addHeader($record);
                        } else {
                            $response->addRecord($record);
                        }
                    } else {
                        $response->setResumptionToken(
                            new ResumptionToken(
                                $this->encodeToken($request, 1, $this->pageSize, $mdPrefix, $setSpec, $from, $until),
                                null,
                                $recordProvider->countRecords($mdPrefix, $setSpec, $from, $until),
                                0
                            )
                        );
                        break;
                    }
                }
                if (!isset($record)) {
                    // let another handler throw errors for empty results (currently EmptyResultErrorHandler)
                    return null;
                }

                return $response;
            }

            return null;
        }
    }
