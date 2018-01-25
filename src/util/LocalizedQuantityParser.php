<?php
    namespace sednasoft\virmisco\util;

    use InvalidArgumentException;

    /**
     * Parses localized quantities (consisting of amount and unit) into a regular float value by detecting the decimal
     * separator, possible group separators and SI unit prefixes and multiplying everything into the default base unit.
     */
    class LocalizedQuantityParser
    {
        /** @var bool */
        private $siPrefixesAllowed = false;
        /** @var array */
        private $unitFactors = [];

        /**
         * Control whether SI prefixes should be automatically detected and supported. If enabled and the unit 'in'
         * being registered, a time difference in minutes like 12,34min would be interpreted as milli-inch.
         *
         * @param boolean $siPrefixesAllowed Whether SI prefixes (M, µ etc.) should be allowed.
         * @return self The current instance for method chaining.
         */
        public function allowSiPrefixes($siPrefixesAllowed)
        {
            $this->siPrefixesAllowed = $siPrefixesAllowed;
            return $this;
        }

        /**
         * @return boolean Whether SI prefixes (M, µ etc.) are allowed.
         */
        public function areSiPrefixesAllowed()
        {
            return $this->siPrefixesAllowed;
        }

        /**
         * Clears all unit registrations.
         * @return self The current instance for method chaining.
         */
        public function clearUnitRegistrations()
        {
            $this->unitFactors = [];
            return $this;
        }

        /**
         * Parses the specified string into a single float value by multiplying SI factors (if allowed) and registered
         * base unit factors.
         *
         * @param string $localizedQuantity The raw value, e. g. '234,567 µs'
         * @return float The normalized numeric value, e. g. 0.000234567s or 2.34567E-4
         * @throws InvalidArgumentException When the specified string contains unexpected character sequences.
         */
        public function parse($localizedQuantity)
        {
            $localizedQuantity = preg_replace('<\\s+>', ' ', trim($localizedQuantity));
            $factor = 1;
            $currentSymbol = '';
            $currentLength = 0;
            foreach ($this->unitFactors as $symbol => $baseFactor) {
                $symbolLength = strlen($symbol);
                if (substr($localizedQuantity . $currentSymbol, -$symbolLength) === $symbol
                    && $symbolLength > $currentLength
                ) {
                    $localizedQuantity = substr($localizedQuantity . $currentSymbol, 0, -$symbolLength);
                    $factor = $baseFactor;
                    $currentSymbol = $symbol;
                    $currentLength = $symbolLength;
                }
            }
            if ($this->siPrefixesAllowed) {
                $siFactors = [
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
                foreach ($siFactors as $symbol => $prefixFactor) {
                    $symbolLength = strlen($symbol);
                    if (substr($localizedQuantity, -$symbolLength) === $symbol) {
                        $localizedQuantity = substr($localizedQuantity, 0, -$symbolLength);
                        $factor *= $prefixFactor;
                        break;
                    }
                }
            }
            $localizedQuantity = trim($localizedQuantity);
            if (preg_match('<[^\\d,.+-eE _\'].*>', $localizedQuantity, $matches)) {
                $jsonFlags = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE;
                throw new InvalidArgumentException(
                    sprintf(
                        'Junk detected in localized quantity %s (after removing unit prefixes and symbols): %s',
                        json_encode($localizedQuantity, $jsonFlags),
                        json_encode($matches[0], $jsonFlags)
                    )
                );
            }
            $localizedQuantity = str_replace(["'", '_'], ' ', $localizedQuantity); // seldom group separators to spaces
            $dots = substr_count($localizedQuantity, '.');
            $commas = substr_count($localizedQuantity, ',');
            $others = substr_count($localizedQuantity, ' ');
            $lastDot = strrpos($localizedQuantity, '.');
            $lastComma = strrpos($localizedQuantity, ',');
            if ($dots === 0 && $commas === 0) {
                // integer, grouped or not
                $amount = floatval(str_replace(' ', '', $localizedQuantity));
            } elseif ($dots > 1) {
                // dot-grouped number
                $amount = floatval(str_replace(['.', ' ', ','], ['', '', '.'], $localizedQuantity));
            } elseif ($commas > 1) {
                // comma-grouped number
                $amount = floatval(str_replace([' ', ','], '', $localizedQuantity));
            } elseif ($others > 0) {
                // other-grouped number
                $amount = floatval(str_replace([' ', ','], ['', '.'], $localizedQuantity));
            } elseif ($dots === 1 && $commas === 1 && $lastComma < $lastDot) {
                // comma-grouped float, e. g. 12,345.6789
                $amount = floatval(str_replace(',', '', $localizedQuantity));
            } elseif ($dots === 1 && $commas === 1) {
                // dot-grouped comma-separated float, e. g. 12.345,6789
                $amount = floatval(str_replace(['.', ','], ['', '.'], $localizedQuantity));
            } else {
                $amount = floatval(str_replace(',', '.', $localizedQuantity));
            }

            return $amount * $factor;
        }

        /**
         * Registers (or replaces) the specified symbol with the factor. When the symbol is encountered at the end of
         * the input, the amount will be multiplied by that factor.
         *
         * @param string $unitSymbol The unit symbol to register.
         * @param float $unitFactor The factor for the unit symbol relative to the desired result unit.
         * @return self The current instance for method chaining.
         */
        public function registerUnit($unitSymbol, $unitFactor)
        {
            $this->unitFactors[$unitSymbol] = $unitFactor;
            return $this;
        }
    }
