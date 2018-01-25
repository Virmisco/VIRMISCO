<?php
    namespace sednasoft\virmisco\domain\projection;

    use DOMAttr;
    use DOMDocument;
    use DOMElement;
    use DOMNode;
    use DOMXPath;
    use RuntimeException;
    use sednasoft\virmisco\singiere\IProjection;

    /**
     * This abstract base class provides XPath based node value setting and can create missing nodes based on a simple
     * subset of XPath expressions.
     */
    abstract class AbstractXmlProjection implements IProjection
    {
        /** @var DOMXPath */
        protected $processor;

        /**
         * Creates a new projection based on a previous projection result given as XML code or from scratch when the
         * argument is omitted or null.
         *
         * @param string|null $xmlCode
         */
        public function __construct($xmlCode = null)
        {
            $document = new DOMDocument();
            if ($xmlCode) {
                $document->loadXML($xmlCode);
            }
            $document->formatOutput = true;
            $this->processor = new DOMXPath($document);
        }

        /**
         * Transforms the current state into an XML representation.
         *
         * @return string The new XML code.
         */
        public function transform()
        {
            return $this->processor->document->documentElement ? $this->processor->document->saveXML() : '';
        }

        /**
         * Example: $this->putValue("/specimenCarriers/specimenCarrier[@id='1234']/sampleProcessing/@index", $index);
         * @param string $xpath
         * @param string $textContent
         * @return DOMNode|null
         */
        protected function putValue($xpath, $textContent)
        {
            /** @var DOMDocument $doc */
            $doc = $this->processor->document;
            /** @var DOMElement|DOMAttr|null $target */
            $target = null;
            foreach ($this->processor->query($xpath) as $node) {
                if ($target) {
                    throw new RuntimeException('Ambiguous XPath expression matches multiple nodes: ' . $xpath);
                }
                $target = $node;
            }
            if (!$target) {
                $path = explode('/', $xpath);
                $last = array_pop($path);
                $path = implode('/', $path);
                /** @var DOMElement|DOMAttr|DOMDocument $node */
                $node = $path ? $this->putValue($path, null) : $doc;
                $pcre = '<^(@?)([\\w.:\\-]+)(\\[(@?)([\\w.:\\-]+)\\s*=\\s*([^\\]]+|"[^"]*"|\'[^\']*\')\\])?($)>';
                if (preg_match($pcre, $last, $matches)) {
                    list(, $isAttr, $name, $hasFilter, $isFilterAttr, $filterName, $filterValue) = $matches;
                    if ($isAttr) {
                        $target = $node->setAttribute($name, $textContent);
                    } else {
                        if (($node instanceof DOMDocument) && $doc->documentElement) {
                            $doc->removeChild($doc->documentElement);
                        }
                        $target = $node->appendChild($doc->createElement($name));
                        if ($hasFilter) {
                            if (in_array(substr($filterValue, 0, 1), ['"', "'"])) {
                                $filterValue = substr($filterValue, 1, -1);
                            }
                            if ($isFilterAttr) {
                                $target->setAttribute($filterName, $filterValue);
                            } else {
                                $child = $target->appendChild($doc->createElement($filterName));
                                $child->appendChild($doc->createTextNode($filterValue));
                            }
                        }
                    }
                } else {
                    throw new RuntimeException('Unsupported XPath expression for creating missing nodes: ' . $xpath);
                }
            }
            if ($textContent !== null) {
                if ($target instanceof DOMAttr) {
                    $target->nodeValue = $textContent;
                } else {
                    while ($target->lastChild) {
                        $target->removeChild($target->lastChild);
                    }
                    $target->appendChild($doc->createTextNode($textContent));
                }
            }

            return $target;
        }
    }
