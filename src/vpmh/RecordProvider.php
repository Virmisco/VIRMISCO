<?php
    namespace sednasoft\virmisco\vpmh;

    use DateTime;
    use DateTimeInterface;
    use DOMDocument;
    use DOMElement;
    use PDO;
    use PDOStatement;
    use sednasoft\virmisco\opamih\Header;
    use sednasoft\virmisco\opamih\Record;
    use sednasoft\virmisco\readlayer\AugmentedAgent;
    use sednasoft\virmisco\readlayer\AugmentedLocation;
    use sednasoft\virmisco\readlayer\AugmentedSamplingDate;
    use sednasoft\virmisco\readlayer\AugmentedScientificName;
    use sednasoft\virmisco\readlayer\entity\CarrierScan;
    use sednasoft\virmisco\readlayer\entity\FocalPlaneImage;
    use sednasoft\virmisco\readlayer\entity\Gathering;
    use sednasoft\virmisco\readlayer\entity\Organism;
    use sednasoft\virmisco\readlayer\entity\Photomicrograph;
    use sednasoft\virmisco\readlayer\entity\SpecimenCarrier;
    use sednasoft\virmisco\readlayer\valueobject\OrganismIdentification;
    use sednasoft\virmisco\readlayer\valueobject\OrganismTypeDesignation;
    use Traversable;

    class RecordProvider implements IRecordProvider, EarliestRecordTimeProvider
    {
        /** @var PDO */
        private $connection;
        /** @var PDOStatement */
        private $listCarrierScans;
        /** @var PDOStatement */
        private $listFocalPlaneImages;
        /** @var PDOStatement */
        private $listOrganisms;
        /** @var PDOStatement */
        private $listSynonyms;
        /** @var PDOStatement */
        private $loadGathering;
        /** @var PDOStatement */
        private $loadOrganism;
        /** @var PDOStatement */
        private $loadPhotomicrograph;
        /** @var PDOStatement */
        private $loadSpecimenCarrier;
        /** @var string */
        private $templateDir;

        /**
         * @param PDO $connection
         * @param string $templateDir
         */
        public function __construct(PDO $connection, $templateDir)
        {
            $this->templateDir = $templateDir;
            $this->connection = $connection;
            $this->loadPhotomicrograph = $this->connection->prepare(
                <<<'EOD'
SELECT `p`.*, `g`.`journal_number`
FROM `photomicrograph` `p`
JOIN `organism` `o` ON `o`.`id` = `p`.`organism_id`
JOIN `specimen_carrier` `sc` ON `sc`.`id` = `o`.`specimen_carrier_id`
JOIN `gathering` `g` ON `g`.`id` = `sc`.`gathering_id`
WHERE `p`.`id` = ?
EOD
            );
            $this->loadOrganism = $connection->prepare('SELECT * FROM `organism` WHERE `id` = ?');
            $this->loadSpecimenCarrier = $connection->prepare('SELECT * FROM `specimen_carrier` WHERE `id` = ?');
            $this->loadGathering = $connection->prepare('SELECT * FROM `gathering` WHERE `id` = ?');
            $this->listCarrierScans = $connection->prepare('SELECT * FROM `carrier_scan` WHERE `specimen_carrier_id` = ?');
            $this->listFocalPlaneImages = $connection->prepare('SELECT * FROM `focal_plane_image` WHERE `photomicrograph_id` = ?');
            $this->listOrganisms = $connection->prepare('SELECT * FROM `organism` WHERE `specimen_carrier_id` = ?');
            $this->listSynonyms = $connection->prepare('SELECT * FROM `scientific_name` WHERE `specimen_carrier_id` = ? AND `sequence_number` = ?');
            Gathering::$agentClass = AugmentedAgent::class;
            Gathering::$locationClass = AugmentedLocation::class;
            Gathering::$samplingDateClass = AugmentedSamplingDate::class;
            $this->loadPhotomicrograph->setFetchMode(PDO::FETCH_CLASS, Photomicrograph::class);
            $this->loadOrganism->setFetchMode(PDO::FETCH_CLASS, Organism::class);
            $this->loadSpecimenCarrier->setFetchMode(PDO::FETCH_CLASS, SpecimenCarrier::class);
            $this->loadGathering->setFetchMode(PDO::FETCH_CLASS, Gathering::class);
            $this->listCarrierScans->setFetchMode(PDO::FETCH_CLASS, CarrierScan::class);
            $this->listFocalPlaneImages->setFetchMode(PDO::FETCH_CLASS, FocalPlaneImage::class);
            $this->listOrganisms->setFetchMode(PDO::FETCH_CLASS, Organism::class);
            $this->listSynonyms->setFetchMode(PDO::FETCH_CLASS, AugmentedScientificName::class);
        }

        /**
         * @param string $metadataPrefix
         * @param string|null $setSpec
         * @param DateTimeInterface|null $from inclusive
         * @param DateTimeInterface|null $until inclusive
         * @return int
         */
        public function countRecords(
            $metadataPrefix,
            $setSpec = null,
            DateTimeInterface $from = null,
            DateTimeInterface $until = null
        ) {
            $countPhotomicrographs = $this->connection->prepare(
                <<<'EOD'
SELECT count(`p`.id)
FROM `photomicrograph` `p`
JOIN `organism` `o` ON `o`.`id` = `p`.`organism_id`
JOIN `specimen_carrier` `sc` ON `sc`.`id` = `o`.`specimen_carrier_id`
JOIN `gathering` `g` ON `g`.`id` = `sc`.`gathering_id`
WHERE (? IS NULL OR `g`.`journal_number` = ?)
AND (
    `p`.`_created` >= ? AND `p`.`_created` < ?
    OR `p`.`_modified` >= ? AND `p`.`_modified` < ?
    OR `p`.`_deleted` >= ? AND `p`.`_deleted` < ?
)
EOD
            );
            $from = $from ? $from->getTimestamp() : '0.0';
            // add 1s because OAI-PMH defines until to be inclusive
            $until = $until ? $until->getTimestamp() + 1 : '99999999999.999999';
            $countPhotomicrographs->execute([$setSpec, $setSpec, $from, $until, $from, $until, $from, $until]);

            return $countPhotomicrographs->fetchColumn(0);
        }

        /**
         * @return DateTime
         */
        public function getEarliestRecordTime()
        {
            $statement = $this->connection->query(
            /** @lang MySQL */
                <<<'EOD'
SELECT min(
	least(
		ifnull(`_created`, 99999999999.999999),
		ifnull(`_modified`, 99999999999.999999),
		ifnull(`_deleted`, 99999999999.999999)
	)
)
FROM `photomicrograph`
EOD
            );
            $unixTimestamp = intval($statement->fetchColumn(0));

            return new DateTime('@' . $unixTimestamp);
        }

        /**
         * @param string $identifier
         * @param string $metadataPrefix
         * @param bool $returnHeaderOnly
         * @return Record|null
         */
        public function getRecordByIdentifier($identifier, $metadataPrefix, $returnHeaderOnly = false)
        {
            $this->loadPhotomicrograph->execute([$identifier]);
            /** @var Photomicrograph $photomicrograph */
            $photomicrograph = $this->loadPhotomicrograph->fetch();
            if ($photomicrograph) {
                $timestamp = max($photomicrograph->_deleted, $photomicrograph->_modified, $photomicrograph->_created);
                $timestamp = new DateTime('@' . intval($timestamp));
                $header = new Header($photomicrograph->getId(), $timestamp, boolval($photomicrograph->_deleted));
                $header->addSetSpec($photomicrograph->journal_number);

                return $returnHeaderOnly
                    ? $header
                    : new Record(
                        $header,
                        $this->createMetadataRepresentation($photomicrograph, $metadataPrefix, $timestamp)
                    );
            } else {
                return null;
            }
        }

        /**
         * @param int $offset
         * @param int $length
         * @param string $metadataPrefix
         * @param bool $returnHeaderOnly
         * @param string|null $setSpec
         * @param DateTimeInterface|null $from inclusive
         * @param DateTimeInterface|null $until inclusive
         * @return Traversable Each member is an instance of Record or Header.
         */
        public function iterateRecordsInRange(
            $offset,
            $length,
            $metadataPrefix,
            $returnHeaderOnly = false,
            $setSpec = null,
            DateTimeInterface $from = null,
            DateTimeInterface $until = null
        )
        {
            $listPhotomicrographs = $this->connection->prepare(
                sprintf(
                    <<<'EOD'
SELECT `p`.*, `g`.`journal_number`
FROM `photomicrograph` `p`
JOIN `organism` `o` ON `o`.`id` = `p`.`organism_id`
JOIN `specimen_carrier` `sc` ON `sc`.`id` = `o`.`specimen_carrier_id`
JOIN `gathering` `g` ON `g`.`id` = `sc`.`gathering_id`
WHERE (? IS NULL OR `g`.`journal_number` = ?)
AND (
    `p`.`_created` >= ? AND `p`.`_created` < ?
    OR `p`.`_modified` >= ? AND `p`.`_modified` < ?
    OR `p`.`_deleted` >= ? AND `p`.`_deleted` < ?
)
ORDER BY p.file__creation_time, p.id
LIMIT %d OFFSET %d
EOD
                    ,
                    $length,
                    $offset
                )
            );
            $from = $from ? $from->getTimestamp() : '0.0';
            // add 1s because OAI-PMH defines until to be inclusive
            $until = $until ? $until->getTimestamp() + 1 : '99999999999.999999';
            $listPhotomicrographs->setFetchMode(PDO::FETCH_CLASS, Photomicrograph::class);
            $listPhotomicrographs->execute([$setSpec, $setSpec, $from, $until, $from, $until, $from, $until]);
            /** @var Photomicrograph $photomicrograph */
            foreach ($listPhotomicrographs as $photomicrograph) {
                $timestamp = max($photomicrograph->_deleted, $photomicrograph->_modified, $photomicrograph->_created);
                $timestamp = new DateTime('@' . intval($timestamp));
                $header = new Header($photomicrograph->getId(), $timestamp, boolval($photomicrograph->_deleted));
                $header->addSetSpec($photomicrograph->journal_number);
                if ($returnHeaderOnly) {
                    yield $header;
                } else {
                    $metadata = $this->createMetadataRepresentation($photomicrograph, $metadataPrefix, $timestamp);
                    if ($metadata) {
                        yield new Record($header, $metadata);
                    }
                }
            }
        }

        /**
         * @param Photomicrograph $photomicrograph
         * @param string $metadataPrefix
         * @param DateTimeInterface $timestamp
         * @return DOMElement|null
         */
        private function createMetadataRepresentation(
            Photomicrograph $photomicrograph,
            $metadataPrefix,
            DateTimeInterface $timestamp
        ) {
            $templateFile = sprintf('%s/%s.php', $this->templateDir, $metadataPrefix);
            if (is_file($templateFile)) {
                $this->loadOrganism->execute([$photomicrograph->getOrganismId()]);
                /** @var Organism $organism */
                $organism = $this->loadOrganism->fetch();
                $this->loadSpecimenCarrier->execute([$organism->getSpecimenCarrierId()]);
                /** @var SpecimenCarrier $specimenCarrier */
                $specimenCarrier = $this->loadSpecimenCarrier->fetch();
                $this->loadGathering->execute([$specimenCarrier->getGatheringId()]);
                /** @var Gathering $gathering */
                $gathering = $this->loadGathering->fetch();
                /** @var OrganismIdentification|OrganismTypeDesignation $scientificNameProvider */
                $scientificNameProvider = $organism->getTypeDesignation() ?: $organism->getIdentification();
                $this->listCarrierScans->execute([$specimenCarrier->getId()]);
                /** @var CarrierScan[] $carrierScans */
                $carrierScans = iterator_to_array($this->listCarrierScans);
                $this->listFocalPlaneImages->execute([$photomicrograph->getId()]);
                /** @var FocalPlaneImage[] $focalPlaneImages */
                $focalPlaneImages = iterator_to_array($this->listFocalPlaneImages);
                $this->listOrganisms->execute([$specimenCarrier->getId()]);
                /** @var Organism[] $organisms */
                $carrierOrganisms = iterator_to_array($this->listOrganisms);
                /** @var AugmentedScientificName|null $scientificName */
                $scientificName = null;
                /** @var AugmentedScientificName[]|null $synonyms */
                $synonyms = null;
                /** @var AugmentedScientificName|null $validName */
                $validName = null;
                /** @var AugmentedScientificName|null $mentionedNameId */
                $mentionedNameId = null;
                if ($scientificNameProvider) {
                    $this->listSynonyms->execute([
                        $organism->getSpecimenCarrierId(),
                        $organism->getSequenceNumber()
                    ]);
                    $synonyms = [];;
                    /** @var AugmentedScientificName $synonym */
                    foreach (iterator_to_array($this->listSynonyms) as $synonym) {
                        if ($synonym->getIsMentioned()) {
                            $mentionedNameId = $synonym->getId();
                        }
                        if ($synonym->getIsValid()) {
                            $validName = $synonym;
                        }
                        else {
                            $synonyms[] = $synonym;
                        }
                    }
                }
                ob_start();
                include $templateFile;
                $doc = new DOMDocument();
                $doc->loadXML(ob_get_clean());

                return $doc->documentElement;
            }

            return null;
        }
    }
