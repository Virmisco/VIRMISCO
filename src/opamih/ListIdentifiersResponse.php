<?php
    namespace sednasoft\virmisco\opamih;

    use DOMElement;
    use sednasoft\virmisco\oai\pmh\repository\data\Header as IHeader;
    use sednasoft\virmisco\oai\pmh\repository\data\ResumptionToken as IResumptionToken;
    use sednasoft\virmisco\oai\pmh\repository\Request as IRequest;
    use sednasoft\virmisco\oai\pmh\repository\response\ListIdentifiers as IListIdentifiers;

    /**
     * A successful response to a ListIdentifiers request.
     */
    class ListIdentifiersResponse extends AbstractResponse implements IListIdentifiers
    {
        /** @var DOMElement|null */
        private $token;

        /**
         * @param IRequest $request
         * @param string $dateGranularity One of the Response::HG_* constants.
         */
        public function __construct(IRequest $request, $dateGranularity)
        {
            parent::__construct($request, 'ListIdentifiers', $dateGranularity);
        }

        /**
         * @param IResumptionToken $resumptionToken A flow control token that marks the result as incomplete and allows
         * the client to continue from that point.
         */
        public function setResumptionToken(IResumptionToken $resumptionToken)
        {
            $this->token = $this->getContainer()->appendChild($resumptionToken->toDomFragment($this->getDocument()));
        }

        /**
         * @param IHeader $header A header object identifying a record.
         */
        public function addHeader(IHeader $header)
        {
            $this->getContainer()->insertBefore($header->toDomFragment($this->getDocument()), $this->token);
        }
    }
