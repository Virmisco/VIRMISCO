<?php
    namespace sednasoft\virmisco\opamih;

    use sednasoft\virmisco\oai\pmh\repository\request\ListSets as IListSets;

    /**
     * This verb is used to retrieve the set structure of a repository, useful for selective harvesting.
     */
    class ListSetsRequest extends AbstractResumableRequest implements IListSets
    {
        /**
         * @param string $requestUri
         * @param string|null $resumptionToken
         */
        public function __construct($requestUri, $resumptionToken = null)
        {
            parent::__construct(
                $requestUri,
                'ListSets',
                $resumptionToken ? ['resumptionToken' => $resumptionToken] : [],
                $resumptionToken
            );
        }
    }
