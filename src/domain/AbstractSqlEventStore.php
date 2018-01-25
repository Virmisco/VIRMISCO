<?php
    namespace sednasoft\virmisco\domain;

    use Generator;
    use PDO;
    use PDOStatement;
    use Predis\Client;
    use Predis\Collection\Iterator;
    use sednasoft\virmisco\singiere\AbstractEvent;
    use sednasoft\virmisco\singiere\AbstractVersionCheckEventStore;
    use sednasoft\virmisco\singiere\Uuid;
    use stdClass;

    /**
     * Provides persistence capabilities for domain events in an append-only manner within a SQL-based RDBMS. Subclasses
     * will find plenty of features prepared already and only have to provide implementation-specific details as well as
     * access to the table and column names.
     *
     * Subclasses may be configured after instantiation and this abstract class therefore does not rely on any of those
     * settings to be known at construction time, i. e. it has no constructor arguments.
     */
    abstract class AbstractSqlEventStore extends AbstractVersionCheckEventStore
    {
        /** @var PDOStatement */
        private $aggregateEventsStatement;
        /** @var PDOStatement */
        private $allEventsStatement;
        /** @var PDOStatement */
        private $appendStatement;
        /** @var PDOStatement */
        private $lastEventStatement;

        /**
         * Seek through the data store from the oldest to the newest entry and return only those events meant for the
         * aggregate with the given identifier.
         *
         * @param Uuid $aggregateId The aggregate identifier.
         * @return Generator An AbstractEvent for every event on the aggregate.
         */
        public function iterateEventsForAggregate(Uuid $aggregateId)
        {
            if (!$this->aggregateEventsStatement) {
                $this->aggregateEventsStatement = $this->getPdoInstance()->prepare(
                    sprintf(
                        'SELECT %s FROM %s WHERE %s = ? ORDER BY %s',
                        $this->quoteIdentifier($this->getBinaryDataColumnName()),
                        $this->quoteIdentifier($this->getTableName()),
                        $this->quoteIdentifier($this->getAggregateIdColumnName()),
                        $this->quoteIdentifier($this->getAutoIncrementColumnName())
                    )
                );
            }
            $this->aggregateEventsStatement->execute([strval($aggregateId)]);
            foreach ($this->aggregateEventsStatement as $row) {
                yield $this->reconstituteEventFromFirstFieldInResultRow($row);
            }
        }

        /**
         * Iterate through the data store from the oldest to the newest entry and return all events.
         *
         * @return Generator
         */
        public function iterateEventsForAllAggregates()
        {
            if (!$this->allEventsStatement) {
                $this->allEventsStatement = $this->getPdoInstance()->prepare(
                    sprintf(
                        'SELECT %s FROM %s ORDER BY %s',
                        $this->quoteIdentifier($this->getBinaryDataColumnName()),
                        $this->quoteIdentifier($this->getTableName()),
                        $this->quoteIdentifier($this->getAutoIncrementColumnName())
                    )
                );
            }
            $this->allEventsStatement->execute();
            foreach ($this->allEventsStatement as $row) {
                yield $this->reconstituteEventFromFirstFieldInResultRow($row);
            }
        }

        /**
         * Serializes the given event and stores it into the underlying data store. No version checking is necessary at
         * this point.
         *
         * @param AbstractEvent $event The event to store away.
         */
        protected function appendToStream(AbstractEvent $event)
        {
            if (!$this->appendStatement) {
                $this->appendStatement = $this->getPdoInstance()->prepare(
                    sprintf(
                        'INSERT INTO %s (%s, %s, %s, %s, %s, %s) VALUES (?, ?, ?, ?, ?, ?)',
                        $this->quoteIdentifier($this->getTableName()),
                        // omitting auto-increment column is okay and necessary let RDBMS handle it correctly
                        $this->quoteIdentifier($this->getAggregateIdColumnName()),
                        $this->quoteIdentifier($this->getBinaryDataColumnName()),
                        $this->quoteIdentifier($this->getEventTypeColumnName()),
                        $this->quoteIdentifier($this->getJsonDataColumnName()),
                        $this->quoteIdentifier($this->getTimestampColumnName()),
                        $this->quoteIdentifier($this->getVersionColumnName())
                    )
                );
            }
            $this->appendStatement->execute([
                $event->getAggregateId(),
                $this->serializeEvent($event),
                get_class($event),
                json_encode($event, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                $event->getTimestamp(),
                $event->getVersion()
            ]);
        }

        /**
         * Returns the (unquoted) name of the column holding the aggregate ID of an event.
         *
         * @return string The name of the column holding the aggregate ID.
         */
        abstract protected function getAggregateIdColumnName();

        /**
         * Returns the (unquoted) name of the column holding an internal auto-increment value which is not related to any
         * event property.
         *
         * @return string The name of the column holding the internal serial/auto-increment value.
         */
        abstract protected function getAutoIncrementColumnName();

        /**
         * Returns the (unquoted) name of the column holding the serialized binary data of the event as generated by
         * serializeEvent() and consumed by unserializeBinaryData().
         *
         * @return string The name of the column holding the binary event data.
         */
        abstract protected function getBinaryDataColumnName();

        /**
         * Returns the (unquoted) name of the column holding the class name of an event object.
         *
         * @return string The name of the column holding the event's class name.
         */
        abstract protected function getEventTypeColumnName();

        /**
         * Returns the (unquoted) name of the column holding the JSON representation of an event.
         *
         * @return string The name of the column holding the JSON event data.
         */
        abstract protected function getJsonDataColumnName();

        /**
         * Returns the most recent event for the aggregate identified by the given ID. This is a performance
         * optimization possibility, but may (for the laziest implementation) as well just iterateEventsForAggregate()
         * and return the last one.
         *
         * @param Uuid $aggregateId The aggregate identifier.
         * @return AbstractEvent|null The most recent event for this aggregate or null, if it is virgin.
         */
        protected function getLastEventForAggregate(Uuid $aggregateId)
        {
            if (!$this->lastEventStatement) {
                $this->lastEventStatement = $this->getPdoInstance()->prepare(
                    sprintf(
                        'SELECT %s FROM %s WHERE %s = ? ORDER BY %s DESC LIMIT 1',
                        $this->quoteIdentifier($this->getBinaryDataColumnName()),
                        $this->quoteIdentifier($this->getTableName()),
                        $this->quoteIdentifier($this->getAggregateIdColumnName()),
                        $this->quoteIdentifier($this->getAutoIncrementColumnName())
                    )
                );
            }
            $this->lastEventStatement->execute([strval($aggregateId)]);
            foreach ($this->lastEventStatement as $row) {
                return $this->reconstituteEventFromFirstFieldInResultRow($row);
            }

            return null;
        }

        /**
         * Returns an instance of PDO or a subclass of it, which must be connected and ready to issue SQL queries.
         *
         * @return PDO The PDO (or subclass) instance to use for queries and statements.
         */
        abstract protected function getPdoInstance();

        /**
         * Returns the (unquoted) name of the table holding the individual events. That table has at least 6 columns for
         * an internal auto-increment/serial number, the aggregate ID, version, timestamp, JSON representation and
         * binary serialization of an event object.
         *
         * @return string The name of the table holding all events.
         */
        abstract protected function getTableName();

        /**
         * Returns the (unquoted) name of the column holding the unconverted string representation of the event's
         * timestamp.
         *
         * @return string The name of the column holding the timestamp of an event.
         */
        abstract protected function getTimestampColumnName();

        /**
         * Returns the (unquoted) name of the column holding the version number of an event.
         *
         * @return string The name of the column holding the event's version number.
         */
        abstract protected function getVersionColumnName();

        /**
         * Quotes the identifier according to the syntax of the underlying SQL database and returns a string that can
         * safely be used in a query or statement.
         *
         * @param string $identifier The plain schema/table/column name to be quoted.
         * @return string The query/statement-safe quoted identifier.
         */
        abstract protected function quoteIdentifier($identifier);

        /**
         * Serializes the event into a binary string to be stored in the column nominated by getBinaryDataColumnName()
         * and later to be reconstituted through unserializeBinaryData().
         *
         * @param AbstractEvent $event The event object.
         * @return string The binary representation.
         */
        abstract protected function serializeEvent(AbstractEvent $event);

        /**
         * Reconstitutes an event object from the binary data value stored in the column nominated by
         * getBinaryDataColumnName() where the binary data equals the result of calling serializeEvent() on that very
         * event object.
         *
         * @param string $data The binary data.
         * @return AbstractEvent The reconstituted event object.
         */
        abstract protected function unserializeBinaryData($data);

        /**
         * Reconstitutes an event object from the binary data field in the result set. Depending on the fetch mode, this
         * field has to be accessible by its name (array key or public object property) or it must be the first (or
         * only) column for index-based result arrays.
         *
         * @param array|stdClass $row
         * @return null|AbstractEvent
         */
        private function reconstituteEventFromFirstFieldInResultRow($row)
        {
            $data = null;
            if (is_array($row) && isset($row[$this->getBinaryDataColumnName()])) {
                $data = $row[$this->getBinaryDataColumnName()];
            } elseif (is_array($row) && isset($row[0])) {
                $data = $row[0];
            } elseif (is_object($row)) {
                $data = $row->{$this->getBinaryDataColumnName()};
            } else {
                return null;
            }

            return $this->unserializeBinaryData($data);
        }
    }
