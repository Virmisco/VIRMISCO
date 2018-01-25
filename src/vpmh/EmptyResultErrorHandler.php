<?php
    namespace sednasoft\virmisco\vpmh;

    use sednasoft\virmisco\oai\pmh\repository\Request;
    use sednasoft\virmisco\oai\pmh\repository\request\GetRecord;
    use sednasoft\virmisco\oai\pmh\repository\request\ListIdentifiers;
    use sednasoft\virmisco\oai\pmh\repository\request\ListMetadataFormats;
    use sednasoft\virmisco\oai\pmh\repository\request\ListRecords;
    use sednasoft\virmisco\oai\pmh\repository\request\ListSets;
    use sednasoft\virmisco\oai\pmh\repository\Response;
    use sednasoft\virmisco\opamih\AbstractHandler;
    use sednasoft\virmisco\opamih\ErrorResponse;

    /**
     * Handles requests that result in an empty list or a non-existing result, which are well-defined error conditions.
     */
    class EmptyResultErrorHandler extends AbstractHandler
    {
        /**
         * @param Request $request The current request to handle if possible.
         * @param string $dateGranularity One of the Response::HG_* constants.
         * @return null|Response A response object, when the request could be handled, null to pass the request on to
         * the next handler.
         */
        protected function handleRequest(Request $request, $dateGranularity)
        {
            $response = new ErrorResponse($request->getRequestUri(), $request->getVerb(), $request->getParameters());
            if ($request instanceof GetRecord) {
                $response->setError(
                    ErrorResponse::ERR_ID_DOES_NOT_EXIST,
                    sprintf('The identifier %s could not be found in this repository', $request->getIdentifier())
                );

                return $response;
            } elseif (($request instanceof ListIdentifiers) || ($request instanceof ListRecords)) {
                $response->setError(
                    ErrorResponse::ERR_NO_RECORDS_MATCH,
                    'There are no records matching the specified filter criteria'
                );

                return $response;
            } elseif ($request instanceof ListMetadataFormats) {
                $response->setError(
                    ErrorResponse::ERR_NO_METADATA_FORMATS,
                    'There are no metadata formats available for the specified item'
                );

                return $response;
            } elseif ($request instanceof ListSets) {
                $response->setError(
                    ErrorResponse::ERR_NO_SET_HIERARCHY,
                    'This repository does not support sets'
                );

                return $response;
            }

            return null;
        }
    }
