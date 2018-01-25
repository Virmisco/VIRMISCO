<?php
    namespace sednasoft\virmisco\vpmh;

    use sednasoft\virmisco\oai\pmh\repository\Request;
    use sednasoft\virmisco\oai\pmh\repository\request\ListSets;
    use sednasoft\virmisco\oai\pmh\repository\Response;
    use sednasoft\virmisco\opamih\AbstractHandler;
    use sednasoft\virmisco\opamih\ListSetsResponse;
    use sednasoft\virmisco\opamih\ResumptionToken;
    use sednasoft\virmisco\opamih\Set;

    /**
     * Handles requests to list the available set hierarchy by querying the respective provider.
     */
    class ListSetsHandler extends AbstractResumableRequestHandler
    {
        /** @var EarliestRecordTimeProvider */
        private $earliestRecordTimeProvider;
        /** @var IMetadataFormatProvider */
        private $metadataFormatProvider;
        /** @var int */
        private $pageSize;
        /** @var ISetProvider */
        private $setProvider;

        /**
         * @param AbstractHandler $nextHandler
         * @param EarliestRecordTimeProvider $earliestRecordTimeProvider
         * @param IMetadataFormatProvider $metadataFormatProvider
         * @param ISetProvider $setProvider
         * @param $pageSize
         */
        public function __construct(
            AbstractHandler $nextHandler,
            EarliestRecordTimeProvider $earliestRecordTimeProvider,
            IMetadataFormatProvider $metadataFormatProvider,
            ISetProvider $setProvider,
            $pageSize = 256
        ) {
            parent::__construct($nextHandler);
            $this->earliestRecordTimeProvider = $earliestRecordTimeProvider;
            $this->metadataFormatProvider = $metadataFormatProvider;
            $this->setProvider = $setProvider;
            $this->pageSize = $pageSize;
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
            if ($request instanceof ListSets) {
                $setProvider = $this->getSetProvider();
                if ($request->hasResumptionToken()) {
                    $token = $request->getResumptionToken();
                    list($type, $page, $pageSize/*, $mdPrefix, $setSpec, $from, $until*/) = $this->decodeToken($token);
                    if (!($request instanceof $type)) {
                        // let another handler throw errors for invalid resumption tokens
                        // (currently InvalidResumptionTokenErrorHandler)
                        return null;
                    }
                    $response = new ListSetsResponse($request, $dateGranularity);
                    $k = 0;
                    /** @var Set $set */
                    foreach ($setProvider->iterateSetsInRange($page * $pageSize, $pageSize + 1) as $k => $set) {
                        // we retrieved 1 more than the page size to see whether we need another page after this one
                        if ($k < $pageSize) {
                            $response->addSet($set);
                        } else {
                            break;
                        }
                    }
                    if (!isset($set)) {
                        // let another handler throw errors for invalid resumption tokens
                        // (currently InvalidResumptionTokenErrorHandler)
                        return null;
                    }
                    $response->setResumptionToken(
                        new ResumptionToken(
                            $k < $pageSize ? null : $this->encodeToken($request, $page + 1, $pageSize),
                            null,
                            $k < $pageSize ? $page * $pageSize + $k + 1 : $setProvider->countSets(),
                            $page * $pageSize
                        )
                    );

                    return $response;
                }
                $response = new ListSetsResponse($request, $dateGranularity);
                /** @var Set $set */
                foreach ($setProvider->iterateSetsInRange(0, $this->pageSize + 1) as $k => $set) {
                    if ($k < $this->pageSize) {
                        $response->addSet($set);
                    } else {
                        $response->setResumptionToken(
                            new ResumptionToken(
                                $this->encodeToken($request, 1, $this->pageSize),
                                null,
                                $setProvider->countSets(),
                                0
                            )
                        );
                        break;
                    }
                }
                if (!isset($set)) {
                    // let another handler throw errors for empty results (currently EmptyResultErrorHandler)
                    return null;
                }

                return $response;
            }

            return null;
        }
    }
