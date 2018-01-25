<?php
    namespace sednasoft\virmisco\metadata\technical\gateway;

    use DateTime;
    use DateTimeInterface;
    use DOMDocument;
    use DOMElement;
    use DOMNode;
    use DOMXPath;
    use Exception;
    use FilesystemIterator;
    use Generator;
    use RecursiveDirectoryIterator;
    use RecursiveIteratorIterator;
    use RuntimeException;
    use SplFileInfo;

    /**
     * Parses XML files (individually or within whole directory trees) for data chunks that match configured fields in
     * an XML-based configuration file.
     */
    class XmlDataExtractor
    {
        /** @var DOMXPath */
        private $configLocator;
        /** @var int */
        private $fileSystemFlags;
        /** @var string */
        private $functionPcre = '<^\s*function\s*\(\s*(?:array\s+)?(\$\w+)\s*\)\s*\{\s*([\s\S]+)\s*\}\s*$>';
        /** @var int */
        private $iteratorFlags;
        /** @var int */
        private $iteratorMode;
        /** @var string */
        private $quantityPcre = '<^\s*([,.]\d+|\d+(?:[,.]\d*)?)([eE][+\-]?\d+)?\s*(?:(da?|[YZEPTGMkhcmµnpfazy])?(\S+))?\s*($)>';
        /** @var array */
        private $siFactors = [
            'Y' => 1e24,
            'Z' => 1e21,
            'E' => 1e18,
            'P' => 1e15,
            'T' => 1e12,
            'G' => 1e9,
            'M' => 1e6,
            'k' => 1e3,
            'h' => 1e2,
            'da' => 1e1,
            'd' => 1e-1,
            'c' => 1e-2,
            'm' => 1e-3,
            'µ' => 1e-6,
            'n' => 1e-9,
            'p' => 1e-12,
            'f' => 1e-15,
            'a' => 1e-18,
            'z' => 1e-21,
            'y' => 1e-24
        ];

        /**
         * @param string $configFileName
         */
        public function __construct($configFileName)
        {
            $dom = new DOMDocument();
            $dom->load($configFileName);
            $this->configLocator = new DOMXPath($dom);
            $this->configLocator->registerNamespace('c', 'urn:uuid:c3848059-8c74-453e-8a9c-ab4f64b37bce');
            $this->fileSystemFlags = FilesystemIterator::CURRENT_AS_FILEINFO
                | FilesystemIterator::SKIP_DOTS
                | FilesystemIterator::UNIX_PATHS;
            $this->iteratorMode = RecursiveIteratorIterator::LEAVES_ONLY;
            $this->iteratorFlags = RecursiveIteratorIterator::CATCH_GET_CHILD;
        }

        /**
         * @param string $pathName
         * @return Generator yielding [filePath:string, fieldName:string, result:mixed] for every field
         * defined in the configuration and found in a file within the specified directory tree.
         */
        public function processDirectory($pathName)
        {
            $rdi = new RecursiveDirectoryIterator($pathName, $this->fileSystemFlags);
            $rii = new RecursiveIteratorIterator($rdi, $this->iteratorMode, $this->iteratorFlags);
            /** @var SplFileInfo $fileInfo */
            foreach ($rii as $fileInfo) {
                if ($fileInfo->isFile()) {
                    /** @var DOMElement $definition */
                    $filePath = $fileInfo->getRealPath();
                    foreach ($this->processFile($filePath) as $record) {
                        yield $record;
                    }
                }
            }
        }

        /**
         * @param string $pathName
         * @return Generator yielding an array [filePath:string, fieldName:string, result:mixed] for every field
         * defined in the configuration and found in the specified file.
         */
        public function processFile($pathName)
        {
            $dom = null;
            foreach ($this->iterateFieldDefinitionsForFile($pathName) as $definition) {
                if (!$dom) {
                    $dom = new DOMDocument();
                    $dom->load($pathName);
                }
                $fieldName = $definition->getAttribute('name');
                $xpath = $this->initializeXPathProcessor($dom);
                $expr = $this->configLocator->query('c:nodes', $definition)->item(0)->getAttribute('xpath');
                $conversionFunction = null;
                $conversionElement = null;
                $combinationFunction = null;
                $userDefinedCombinator = null;
                /** @var DOMElement $element */
                foreach ($this->configLocator->query('c:convert/c:*', $definition) as $element) {
                    $conversionFunction = $element->localName . 'Converter';
                    $conversionElement = $element;
                    if (!method_exists($this, $conversionFunction)) {
                        throw new ConfigFileException(
                            'Unsupported converter ' . $element->localName . ' for field ' . $fieldName
                        );
                    }
                }
                $values = [];
                /** @var DOMNode $node */
                foreach ($xpath->query($expr) as $node) {
                    $value = $node->nodeType === XML_ELEMENT_NODE ? $node->textContent : $node->nodeValue;
                    try {
                        $values[] = $conversionFunction
                            ? $this->$conversionFunction($value, $conversionElement)
                            : $value;
                    } catch (RuntimeException $e) {
                        switch ($e->getCode()) {
                            case 0x078ba1:
                                throw new DataFormatException(
                                    $pathName,
                                    $fieldName,
                                    $value,
                                    'Value did not match any when branch and an otherwise was missing'
                                );
                            case 0x07b486:
                                throw new DataFormatException(
                                    $pathName,
                                    $fieldName,
                                    $value,
                                    'Value has decimal part that would be truncated'
                                );
                            case 0x1487a2:
                                throw new DataFormatException(
                                    $pathName,
                                    $fieldName,
                                    $value,
                                    'Value was dimensionless but there were units declared. To explicitly allow'
                                    . ' this case add another empty unit like this: '
                                    . '<unit si-prefix="forbid" symbol="strip" />'
                                );
                            case 0x2ac72e:
                                throw new ConfigFileException(
                                    'Cannot keep the SI prefix when the unit should be stripped for field '
                                    . $fieldName
                                );
                            case 0x4587a5:
                                throw new DataFormatException(
                                    $pathName,
                                    $fieldName,
                                    $value,
                                    'Invalid date/time value'
                                );
                            case 0x513410:
                                throw new DataFormatException(
                                    $pathName,
                                    $fieldName,
                                    $value,
                                    'Invalid numeric value'
                                );
                            case 0xa1625e:
                                throw new DataFormatException(
                                    $pathName,
                                    $fieldName,
                                    $value,
                                    'Cannot find a unit declaration matching ' . $e->getMessage()
                                );
                            default:
                                throw new DataFormatException(
                                    $pathName,
                                    $fieldName,
                                    null,
                                    'unexpected internal exception',
                                    0,
                                    $e
                                );
                        }
                    }
                }
                $result = null;
                /** @var DOMElement $element */
                foreach ($this->configLocator->query('c:combine', $definition) as $element) {
                    if ($element->hasAttribute('function')) {
                        $combinationFunction = $element->getAttribute('function') . 'Combinator';
                        if (method_exists($this, $combinationFunction)) {
                            try {
                                $result = $this->$combinationFunction($this->castValuesToSameType($values));
                            } catch (RuntimeException $e) {
                                switch ($e->getCode()) {
                                    case 0xa4df10:
                                        throw new DataFormatException(
                                            $pathName,
                                            $fieldName,
                                            $e->getMessage(),
                                            'Value was not unique'
                                        );
                                    default:
                                        throw new DataFormatException(
                                            $pathName,
                                            $fieldName,
                                            null,
                                            'unexpected internal exception',
                                            0,
                                            $e
                                        );
                                }
                            }
                        } else {
                            throw new ConfigFileException(
                                'Unsupported combinator ' . $element->getAttribute('function')
                                . ' for field ' . $fieldName
                            );
                        }
                    } else {
                        if (preg_match($this->functionPcre, $element->textContent, $matches)) {
                            $combinationFunction = @create_function($matches[1], $matches[2]);
                            if ($error = error_get_last()) {
                                throw new ConfigFileException(
                                    'Error in user-defined combinator for field ' . $fieldName . ': '
                                    . $error['message']
                                );
                            }
                            $result = $combinationFunction($values);
                        } else {
                            throw new ConfigFileException(
                                'Error in user-defined combinator for field ' . $fieldName . ': syntax error'
                            );
                        }
                    }
                }
                yield [$pathName, $fieldName, $result];
            }
        }

        /**
         * @param array $values
         * @return mixed
         */
        protected function avgCombinator(array $values)
        {
            list($value) = $values;
            $count = count($values);
            if ($value instanceof DateTimeInterface) {
                $result = 0;
                /** @var DateTimeInterface $v */
                foreach ($values as $v) {
                    $result += $v->getTimestamp() / $count;
                }
                $result = new DateTime('@' . $result);
                $result->setTimezone($value->getTimezone());

                return $result;
            } elseif (is_string($value)) {
                $length = max(array_map('strlen', $values));
                $result = array_fill(0, $length, 0);
                foreach ($values as $value) {
                    foreach (str_split(sprintf('%-' . $length . 's', $value), 1) as $k => $char) {
                        $result[$k] += ord($char) / $count;
                    }
                }

                return implode('', array_map('chr', array_map('round', $result)));
            }
            $result = array_sum($values) / $count;

            return is_int($result) ? intval(round($result)) : $result;
        }

        /**
         * @param string $value
         * @param DOMElement $element
         * @return bool|float|int|string
         * @throws ConfigFileException
         * @throws RuntimeException
         */
        protected function chooseConverter($value, DOMElement $element)
        {
            /** @var DOMElement $result */
            foreach ($this->configLocator->query('c:when/c:*', $element) as $result) {
                $pcre = sprintf(
                    '/%s/%s',
                    str_replace('/', '\/', $result->parentNode->getAttribute('match')),
                    $result->parentNode->getAttribute('flags')
                );
                if (preg_match($pcre, $value)) {
                    switch ($result->nodeName) {
                        case 'bool':
                            return in_array(trim($result->textContent), ['true', '1']);
                        case 'float':
                            return floatval($result->textContent);
                        case 'int':
                            return intval($result->textContent);
                        case 'string':
                            return $result->textContent;
                        default:
                            throw new ConfigFileException(
                                'Unsupported literal element "' . $result->nodeName
                                . '" encountered while processing "when" element for field '
                                . $element->parentNode->parentNode->getAttribute('name')
                            );
                    }
                }
            }
            /** @var DOMElement $result */
            foreach ($this->configLocator->query('c:otherwise/c:*', $element) as $result) {
                switch ($result->nodeName) {
                    case 'bool':
                        return in_array(trim($result->textContent), ['true', '1']);
                    case 'float':
                        return floatval($result->textContent);
                    case 'int':
                        return intval($result->textContent);
                    case 'string':
                        return $result->textContent;
                    default:
                        throw new ConfigFileException(
                            'Unsupported literal element "' . $result->nodeName
                            . '" encountered while processing "otherwise" element for field '
                            . $element->parentNode->parentNode->getAttribute('name')
                        );
                }
            }
            foreach ($this->configLocator->query('c:when[not(c:*)]|c:otherwise[not(c:*)]', $element) as $result) {
                throw new ConfigFileException(
                    'Empty result branch encountered while processing "' . $result->localName . '" element for field '
                    . $element->parentNode->parentNode->getAttribute('name')
                );
            }
            throw new RuntimeException(json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), 0x078ba1);
        }

        /**
         * @param string $value
         * @param DOMElement $element
         * @return string
         */
        protected function collapseWhitespaceConverter($value, DOMElement $element)
        {
            return trim(preg_replace('<\s+>', ' ', $value));
        }

        /**
         * @param array $values
         * @return mixed
         */
        protected function maxCombinator(array $values)
        {
            $result = null;
            $max = null;
            foreach ($values as $value) {
                $compare = $value;
                if ($value instanceof DateTimeInterface) {
                    $compare = $value->getTimestamp();
                }
                if ($result === null || $compare > $max) {
                    $result = $value;
                    $max = $compare;
                }
            }

            return $result;
        }

        /**
         * @param array $values
         * @return mixed
         */
        protected function minCombinator(array $values)
        {
            $result = null;
            $min = null;
            foreach ($values as $value) {
                $compare = $value;
                if ($value instanceof DateTimeInterface) {
                    $compare = $value->getTimestamp();
                }
                if ($result === null || $compare < $min) {
                    $result = $value;
                    $min = $compare;
                }
            }

            return $result;
        }

        /**
         * @param string $value
         * @param DOMElement $element
         * @return int
         * @throws RuntimeException
         */
        protected function parseDateConverter($value, DOMElement $element)
        {
            try {
                return new DateTime($value);
            } catch (Exception $e) {
                throw new RuntimeException('', 0x4587a5);
            }
        }

        /**
         * @param string $value
         * @param DOMElement $element
         * @return int
         * @throws RuntimeException
         */
        protected function parseFloatConverter($value, DOMElement $element)
        {
            $prefix = null;
            $symbol = null;
            if (preg_match($this->quantityPcre, $value, $matches)) {
                list(, $value, $exponent, $prefix, $symbol) = $matches;
                $value = floatval(str_replace(',', '.', $value)) * (1 . $exponent);
                $prefix = strlen($prefix) ? $prefix : null;
                $symbol = strlen($symbol) ? $symbol : null;
            } else {
                throw new RuntimeException('', 0x513410);
            }
            /** @var DOMElement $unit */
            foreach ($this->configLocator->query('c:unit', $element) as $unit) {
                $prefixAction = $unit->getAttribute('si-prefix');
                $symbolAction = $unit->getAttribute('symbol');
                $symbolName = trim($unit->textContent);
                if (sprintf('%s%s', $prefixAction === 'forbid' ? $prefix : '', $symbol) === $symbolName) {
                    switch ($prefixAction) {
                        case 'forbid':
                            $symbol = $symbolName;
                            $prefix = null;
                            break;
                        case 'multiply':
                            $value *= $prefix ? $this->siFactors[$prefix] : 1;
                            $prefix = null;
                            break;
                    }
                    if ($symbolAction == 'strip') {
                        $symbol = null;
                        $prefix = null;
                        if ($prefixAction == 'keep') {
                            throw new RuntimeException('', 0x2ac72e);
                        }
                    }

                    return $symbol ? sprintf('%G%s%s', $value, $prefix, $symbol) : $value;
                }
            }
            if ($symbol) {
                throw new RuntimeException(
                    json_encode($prefix . $symbol, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                    0xa1625e
                );
            }
            if (isset($symbolName)) {
                throw new RuntimeException('', 0x1487a2);
            }

            return $value;
        }

        /**
         * @param string $value
         * @param DOMElement $element
         * @return int
         * @throws RuntimeException
         */
        protected function parseIntConverter($value, DOMElement $element)
        {
            $result = $this->parseFloatConverter($value, $element);
            if ($result - floor($result) > 1e-10) {
                throw new RuntimeException('', 0x07b486);
            }

            return intval($result);
        }

        /**
         * @param array $values
         * @return mixed
         * @throws RuntimeException
         */
        protected function uniqueCombinator(array $values)
        {
            list($result) = $values;
            foreach ($values as $value) {
                if ($value !== $result) {
                    throw new RuntimeException($value, 0xa4df10);
                }
            }

            return $result;
        }

        /**
         * @param array $values
         * @return array
         */
        private function castValuesToSameType(array $values)
        {
            $typeRanks = ['string' => 0, 'float' => 1, 'int' => 2, 'bool' => 3, 'date' => 3];
            $broadestType = null;
            $broadestRank = null;
            foreach ($values as $value) {
                if ($value instanceof DateTimeInterface) {
                    $type = 'date';
                } elseif (is_bool($value)) {
                    $type = 'bool';
                } elseif (is_int($value)) {
                    $type = 'int';
                } elseif (is_float($value)) {
                    $type = 'float';
                } else {
                    $type = 'string';
                }
                $rank = $typeRanks[$type];
                if (!$broadestType) {
                    $broadestType = $type;
                    $broadestRank = $rank;
                } elseif ($type !== $broadestType && $rank <= $broadestRank) {
                    $broadestRank = min($rank, $broadestRank - 1);
                    $broadestType = array_search($broadestRank, $typeRanks);
                }
                if ($broadestRank === 0) {
                    break;
                }
            }
            if ($broadestRank < 3) {
                foreach ($values as $k => $value) {
                    if ($value instanceof DateTimeInterface) {
                        $values[$k] = $broadestType === 'string'
                            ? $value->format(DateTime::ATOM)
                            : $value->getTimestamp();
                    } elseif (is_bool($value)) {
                        $values[$k] = $broadestType === 'string' ? ($value ? 'true' : 'false') : intval($value);
                    } elseif ($broadestType === 'string') {
                        $values[$k] = strval($value);
                    } else {
                        $function = $broadestType . 'val';
                        $values[$k] = $function($value);
                    }
                }
            }

            return $values;
        }

        /**
         * @param DOMDocument $document
         * @return DOMXPath
         */
        private function initializeXPathProcessor(DOMDocument $document)
        {
            $xpath = new DOMXPath($document);
            /** @var DOMElement $declaration */
            foreach ($this->configLocator->query('/c:metadata/c:namespace') as $declaration) {
                $xpath->registerNamespace($declaration->getAttribute('prefix'), $declaration->textContent);
            }

            return $xpath;
        }

        /**
         * @param $filePath
         * @return Generator yielding a \DOMElement for every field configuration that matches the specified file.
         */
        private function iterateFieldDefinitionsForFile($filePath)
        {
            /** @var DOMElement $elem */
            foreach ($this->configLocator->query('/c:metadata/c:field/c:files') as $elem) {
                $pcre = sprintf(
                    '/%s/%s',
                    str_replace('/', '\/', $elem->getAttribute('match')),
                    $elem->getAttribute('flags')
                );
                if (preg_match($pcre, $filePath)) {
                    yield $elem->parentNode;
                }
            }
        }
    }
