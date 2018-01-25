<?php
    namespace sednasoft\virmisco\opamih;

    use DateTime;
    use DateTimeInterface;
    use DateTimeZone;
    use DOMDocument;
    use DOMElement;
    use DOMText;
    use sednasoft\virmisco\oai\pmh\repository\Request as IRequest;
    use sednasoft\virmisco\oai\pmh\repository\Response as IResponse;
    use Traversable;

    /**
     * A message sent to the client as a reaction to a client's request.
     */
    abstract class AbstractResponse implements IResponse
    {
        const ISO8601UTC = 'Y-m-d\TH:i:s\Z';
        /** @var DOMElement */
        private $container;
        /** @var string */
        private $dateGranularity;

        /**
         * @param IRequest $request
         * @param string $containerName
         * @param string $dateGranularity One of the Response::HG_* constants.
         */
        public function __construct(IRequest $request, $containerName, $dateGranularity)
        {
            $this->dateGranularity = $dateGranularity;
            $this->initialize(
                $request->getRequestUri(),
                $request->getVerb(),
                $request->getParameters(),
                $containerName
            );
        }

        /**
         * @return DOMDocument
         */
        public function getDocument()
        {
            return $this->container->ownerDocument;
        }

        /**
         * @param string $name
         * @return DOMElement
         */
        protected function createElement($name)
        {
            return $this->container->ownerDocument->createElement($name);
        }

        /**
         * @param string $content
         * @return DOMText
         */
        protected function createTextNode($content)
        {
            return $this->container->ownerDocument->createTextNode($content);
        }

        /**
         * @return DOMElement
         */
        protected function getContainer()
        {
            return $this->container;
        }

        /**
         * @param string $requestUri
         * @param string|null $verb
         * @param Traversable $parameters
         * @param string $containerName
         */
        protected function initialize($requestUri, $verb, $parameters, $containerName)
        {
            $document = new DOMDocument('1.0', 'UTF-8');
            $document->formatOutput = true;
            $date = new DateTime('now', new DateTimeZone('UTC'));
            $oaiPmh = $document->createElement('OAI-PMH');
            $responseDate = $document->createElement('responseDate');
            $requestElement = $document->createElement('request');
            $this->container = $document->createElement($containerName);
            $document->appendChild($oaiPmh);
            $oaiPmh->setAttribute('xmlns', 'http://www.openarchives.org/OAI/2.0/');
            $oaiPmh->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
            $oaiPmh->setAttribute(
                'xsi:schemaLocation',
                'http://www.openarchives.org/OAI/2.0/ http://www.openarchives.org/OAI/2.0/OAI-PMH.xsd'
            );
            $oaiPmh->appendChild($responseDate);
            $oaiPmh->appendChild($requestElement);
            $oaiPmh->appendChild($this->container);
            $responseDate->appendChild($document->createTextNode($date->format(self::ISO8601UTC)));
            if ($verb) {
                $requestElement->setAttribute('verb', $verb);
            }
            foreach ($parameters as $key => $value) {
                if ($value instanceof DateTimeInterface) {
                    $value = substr($value->format(self::ISO8601UTC), 0, strlen($this->dateGranularity));
                }
                $requestElement->setAttribute($key, $value);
            }
            $requestElement->appendChild($document->createTextNode($requestUri));
        }
    }
