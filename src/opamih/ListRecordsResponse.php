<?php
    namespace sednasoft\virmisco\opamih;

    use RuntimeException;
    use sednasoft\virmisco\oai\pmh\repository\data\Record as IRecord;
    use sednasoft\virmisco\oai\pmh\repository\data\ResumptionToken as IResumptionToken;
    use sednasoft\virmisco\oai\pmh\repository\Request as IRequest;
    use sednasoft\virmisco\oai\pmh\repository\response\ListRecords as IListRecords;

    /**
     * A successful response to a ListRecords request.
     */
    class ListRecordsResponse extends AbstractResponse implements IListRecords
    {
        /** @var \DOMElement|null */
        private $resumptionTokenElement = null;

        /**
         * @param IRequest $request
         * @param string $dateGranularity One of the Response::HG_* constants.
         */
        public function __construct(IRequest $request, $dateGranularity)
        {
            parent::__construct($request, 'ListRecords', $dateGranularity);
        }

        /**
         * @param IRecord $record Metadata about a resource expressed in a single format.
         */
        public function addRecord(IRecord $record)
        {
            $this->getContainer()->insertBefore(
                $record->toDomFragment($this->getDocument()),
                $this->resumptionTokenElement
            );
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
    }
