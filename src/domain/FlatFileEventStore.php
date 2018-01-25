<?php
    namespace sednasoft\virmisco\domain;

    use Generator;
    use sednasoft\virmisco\singiere\AbstractEvent;
    use sednasoft\virmisco\singiere\AbstractVersionCheckEventStore;
    use sednasoft\virmisco\singiere\Uuid;

    class FlatFileEventStore extends AbstractVersionCheckEventStore
    {
        private $cache = [];
        private $fileName;

        /**
         * @param string $flatFileName
         */
        public function __construct($flatFileName)
        {
            $this->fileName = $flatFileName;
            touch($this->fileName);
            if (is_file($this->fileName . '.cache')) {
                $this->cache = unserialize(file_get_contents($this->fileName . '.cache', FILE_BINARY));
            }
        }

        public function __destruct()
        {
            file_put_contents(
                $this->fileName . '.cache',
                serialize($this->cache),
                FILE_BINARY
            );
        }

        /**
         * @param Uuid $aggregateId
         * @return Generator
         */
        public function iterateEventsForAggregate(Uuid $aggregateId)
        {
            return $this->iterateEvents($aggregateId);
        }

        /**
         * @return Generator
         */
        public function iterateEventsForAllAggregates()
        {
            return $this->iterateEvents(null);
        }

        /**
         * @param AbstractEvent $event
         */
        protected function appendToStream(AbstractEvent $event)
        {
            file_put_contents(
                $this->fileName,
                sprintf(
                    "%s:%x:%s:%s\n",
                    $event->getAggregateId(),
                    $event->getVersion(),
                    array_slice(explode('\\', get_class($event)), -1)[0],
                    base64_encode(gzcompress(serialize($event)))
                ),
                FILE_APPEND | FILE_BINARY
            );
            $this->cache[strval($event->getAggregateId())] = $event;
        }

        /**
         * @param Uuid $aggregateId
         * @return AbstractEvent
         */
        protected function getLastEventForAggregate(Uuid $aggregateId)
        {
            $key = strval($aggregateId);
            if (!array_key_exists($key, $this->cache)) {
                foreach ($this->iterateEventsForAggregate($aggregateId) as $event) {
                    $this->cache[$key] = $event;
                }
            }

            return isset($this->cache[$key]) ? $this->cache[$key] : null;
        }

        /**
         * @param Uuid $aggregateId
         * @return Generator
         */
        protected function iterateEvents(Uuid $aggregateId = null)
        {
            $length = 1024;
            $handle = fopen($this->fileName, 'rb');
            $event = null;
            do {
                $line = '';
                do {
                    $chunk = fgets($handle, $length);
                    $line .= $chunk;
                } while (strlen($chunk) === $length && strpos($chunk, "\n") === false);
                $line = rtrim($line, "\n");
                if ($line) {
                    if ($aggregateId === null || substr($line, 0, 36) === strval($aggregateId)) {
                        list(, $version, $name, $data) = explode(':', $line, 4);
                        $event = unserialize(gzuncompress(base64_decode($data)));
                        yield $event;
                    }
                }
            } while ($line);
            if ($event) {
                $this->cache[strval($aggregateId)] = $event;
            }
            fclose($handle);
        }
    }
