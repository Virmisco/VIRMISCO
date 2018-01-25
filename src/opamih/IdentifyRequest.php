<?php
    namespace sednasoft\virmisco\opamih;

    use sednasoft\virmisco\oai\pmh\repository\request\Identify as IIdentify;

    /**
     * This verb is used to retrieve information about a repository. Some of the information returned is required as
     * part of the OAI-PMH. Repositories may also employ the Identify verb to return additional descriptive information.
     */
    class IdentifyRequest extends AbstractRequest implements IIdentify
    {
        /**
         * @param string $requestUri
         */
        public function __construct($requestUri)
        {
            parent::__construct($requestUri, 'Identify', []);
        }
    }
