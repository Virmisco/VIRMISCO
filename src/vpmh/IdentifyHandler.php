<?php
    namespace sednasoft\virmisco\vpmh;

    use sednasoft\virmisco\oai\pmh\repository\Request;
    use sednasoft\virmisco\oai\pmh\repository\request\Identify;
    use sednasoft\virmisco\oai\pmh\repository\Response;
    use sednasoft\virmisco\opamih\AbstractHandler;
    use sednasoft\virmisco\opamih\IdentifyResponse;

    /**
     * Handles the Identify request and provides the stipulated information about the repository itself.
     */
    class IdentifyHandler extends AbstractHandler
    {
        /** @var EarliestRecordTimeProvider */
        private $earliestRecordTimeProvider;
        /** @var IIdentificationProvider */
        private $identificationProvider;

        /**
         * @param AbstractHandler $nextHandler
         * @param EarliestRecordTimeProvider $earliestRecordTimeProvider
         * @param IIdentificationProvider $identificationProvider
         */
        public function __construct(
            AbstractHandler $nextHandler,
            EarliestRecordTimeProvider $earliestRecordTimeProvider,
            IIdentificationProvider $identificationProvider
        ) {
            parent::__construct($nextHandler);
            $this->earliestRecordTimeProvider = $earliestRecordTimeProvider;
            $this->identificationProvider = $identificationProvider;
        }

        /**
         * @param Request $request The current request to handle if possible.
         * @param string $dateGranularity One of the Response::HG_* constants.
         * @return null|Response A response object, when the request could be handled, null to pass the request on to
         * the next handler.
         */
        protected function handleRequest(Request $request, $dateGranularity)
        {
            if ($request instanceof Identify) {
                $response = new IdentifyResponse($request, $dateGranularity);
                $response->setRepositoryName($this->identificationProvider->getRepositoryName());
                $response->setBaseUrl($this->identificationProvider->getBaseUrl());
                $response->addAdminEmail($this->identificationProvider->getAdminEmail());
                $response->setProtocolVersion('2.0');
                $response->setEarliestDatestamp($this->earliestRecordTimeProvider->getEarliestRecordTime());
                $response->setDeletedRecord(IdentifyResponse::SL_PERSISTENT);

                return $response;
            }

            return null;
        }
    }
