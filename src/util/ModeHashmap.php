<?php
    namespace sednasoft\virmisco\util;

    use IteratorAggregate;
    use Traversable;

    /**
     * A hashmap counting the occurrences of added key-value-pairs and returns the mode of added values for each key,
     * which is the value occurring most often. Unlike the mathematical definition of mode, no arithmetic mean will be
     * generated when two values occurred equally frequent. Instead, the value added first takes precedence in this
     * case.
     *
     * Example:
     *     $modeHashmap->add('a', 2);
     *     $modeHashmap->add('b', 3);
     *     $modeHashmap->add('a', 4);
     *     $modeHashmap->add('a', 4);
     *     $modeHashmap->getMode('a'); => 4
     *     $modeHashmap->getMode('b'); => 3
     *     $modeHashmap->add('a', 2);
     *     $modeHashmap->add('a', 2);
     *     $modeHashmap->getMode('a'); => 2
     */
    class ModeHashmap implements IteratorAggregate
    {
        private $highestNumbers = [];
        private $mostFrequentValues = [];
        private $occurrences = [];

        /**
         * Stores another occurrence of the given value for the given key.
         *
         * @param string $key
         * @param mixed $value
         */
        public function add($key, $value)
        {
            $this->occurrences[$key][] = $value;
            if (!isset($this->highestNumbers[$key], $this->mostFrequentValues[$key])) {
                $this->highestNumbers[$key] = 1;
                $this->mostFrequentValues[$key] = $value;
            } else {
                $count = 0;
                foreach ($this->occurrences[$key] as $v) {
                    if ($v === $value) {
                        $count++;
                    }
                }
                if ($count > $this->highestNumbers[$key]) {
                    $this->highestNumbers[$key] = $count;
                    $this->mostFrequentValues[$key] = $value;
                }
            }
        }

        /**
         * Retrieve an external iterator
         * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
         *
         * @return Traversable An instance of an object implementing Iterator or Traversable.
         */
        public function getIterator()
        {
            foreach ($this->occurrences as $key => $values) {
                yield $key => $this->getMode($key);
            }
        }

        /**
         * Returns the value that was most frequently added for the given key.
         *
         * @param string $key
         * @return mixed|null
         */
        public function getMode($key)
        {
            return isset($this->mostFrequentValues[$key]) ? $this->mostFrequentValues[$key] : null;
        }
    }
