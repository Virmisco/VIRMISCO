<?php
    namespace sednasoft\virmisco\vpmh;

    use sednasoft\virmisco\oai\pmh\repository\Request;
    use sednasoft\virmisco\oai\pmh\repository\request\feature\Resumable;
    use sednasoft\virmisco\oai\pmh\repository\Response;
    use sednasoft\virmisco\opamih\AbstractHandler;
    use sednasoft\virmisco\opamih\ErrorResponse;

    /**
     * Handles requests that contain a resumption token which could not be handled by a previous handler.
     */
    class InvalidResumptionTokenErrorHandler extends AbstractHandler
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
            if (($request instanceof Resumable) && $request->hasResumptionToken()) {
                $response->setError(
                    ErrorResponse::ERR_BAD_RESUMPTION_TOKEN,
                    sprintf('The resumption token \'%s\' is invalid or has expired', $request->getResumptionToken())
                );

                return $response;
            }

            return null;
        }
    }
