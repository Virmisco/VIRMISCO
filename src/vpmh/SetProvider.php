<?php
    namespace sednasoft\virmisco\vpmh;

    use PDO;
    use sednasoft\virmisco\opamih\Set;
    use sednasoft\virmisco\readlayer\AugmentedAgent;
    use sednasoft\virmisco\readlayer\AugmentedLocation;
    use sednasoft\virmisco\readlayer\AugmentedSamplingDate;
    use sednasoft\virmisco\readlayer\entity\Gathering;
    use Traversable;

    class SetProvider implements ISetProvider
    {
        /** @var PDO */
        private $connection;

        /**
         * @param PDO $connection
         */
        public function __construct(PDO $connection)
        {
            $this->connection = $connection;
        }

        /**
         * @return int
         */
        public function countSets()
        {
            return $this->connection->query('SELECT count(*) FROM `gathering`')->fetchColumn(0);
        }

        /**
         * @param string $setSpec
         * @return int|null
         */
        public function indexOfSet($setSpec)
        {
            $loadGathering = $this->connection->prepare('SELECT * FROM `gathering` WHERE `journal_number` = ?');
            $countGatherings = $this->connection->prepare(
                'SELECT count(*) FROM `gathering` '
                . 'WHERE `sampling_date__after` < ? OR `sampling_date__after` = ? AND `journal_number` < ?'
            );
            Gathering::$agentClass = AugmentedAgent::class;
            Gathering::$locationClass = AugmentedLocation::class;
            Gathering::$samplingDateClass = AugmentedSamplingDate::class;
            $loadGathering->setFetchMode(PDO::FETCH_CLASS, Gathering::class);
            $loadGathering->execute([$setSpec]);
            /** @var Gathering $gathering */
            $gathering = $loadGathering->fetch();
            if ($gathering) {
                $after = $gathering->getSamplingDate()->getAfter();
                $countGatherings->execute([$after, $after, $gathering->getJournalNumber()]);

                return $countGatherings->fetchColumn(0);
            } else {
                return null;
            }
        }

        /**
         * @param int $index
         * @param int $length
         * @return Traversable Each member is an instance of Set.
         */
        public function iterateSetsInRange($index, $length)
        {
            $query = sprintf(
                'SELECT * FROM `gathering` ORDER BY `sampling_date__after`, `journal_number` LIMIT %d OFFSET %d',
                $length,
                $index
            );
            $listGatherings = $this->connection->query($query);
            Gathering::$agentClass = AugmentedAgent::class;
            Gathering::$locationClass = AugmentedLocation::class;
            Gathering::$samplingDateClass = AugmentedSamplingDate::class;
            $listGatherings->setFetchMode(PDO::FETCH_CLASS, Gathering::class);
            /** @var Gathering $gathering */
            foreach ($listGatherings as $gathering) {
                yield new Set(
                    $gathering->getJournalNumber(),
                    sprintf(
                        'the gathering logged with the journal number: %s; performed by: %s; in: %s; on or during: %s',
                        $gathering->getJournalNumber(),
                        $gathering->getAgent(),
                        $gathering->getLocation(),
                        $gathering->getSamplingDate()
                    )
                );
            }
        }
    }
