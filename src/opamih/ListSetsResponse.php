<?php
    namespace sednasoft\virmisco\opamih;

    use RuntimeException;
    use sednasoft\virmisco\oai\pmh\repository\data\ResumptionToken as IResumptionToken;
    use sednasoft\virmisco\oai\pmh\repository\data\Set as ISet;
    use sednasoft\virmisco\oai\pmh\repository\Request as IRequest;
    use sednasoft\virmisco\oai\pmh\repository\response\ListSets as IListSets;

    /**
     * A successful response to a ListSets request.
     */
    class ListSetsResponse extends AbstractResponse implements IListSets
    {
        /** @var \DOMElement|null */
        private $resumptionTokenElement = null;

        /**
         * @param IRequest $request
         * @param string $dateGranularity One of the Response::HG_* constants.
         */
        public function __construct(IRequest $request, $dateGranularity)
        {
            parent::__construct($request, 'ListSets', $dateGranularity);
        }

        /**
         * @param IResumptionToken $resumptionToken A flow control token that marks the result as incomplete and allows
         * the client to continue from that point.
         */
        public function setResumptionToken(IResumptionToken $resumptionToken)
        {
            if ($this->resumptionTokenElement) {
                throw new RuntimeException('resumptionToken already present');
            }
            $this->resumptionTokenElement = $this->getContainer()->appendChild(
                $resumptionToken->toDomFragment($this->getDocument())
            );
        }

        /**
         * @param ISet $set
         */
        public function addSet(ISet $set)
        {
            $this->getContainer()->insertBefore(
                $set->toDomFragment($this->getDocument()),
                $this->resumptionTokenElement
            );
        }
    }
