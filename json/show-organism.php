<?php
    use sednasoft\virmisco\readlayer\AugmentedAgent;
    use sednasoft\virmisco\readlayer\AugmentedLocation;
    use sednasoft\virmisco\readlayer\AugmentedSamplingDate;
    use sednasoft\virmisco\readlayer\AugmentedScientificName;
    use sednasoft\virmisco\readlayer\AugmentedTaxon;
    use sednasoft\virmisco\readlayer\entity\CarrierScan;
    use sednasoft\virmisco\readlayer\entity\Gathering;
    use sednasoft\virmisco\readlayer\entity\Organism;
    use sednasoft\virmisco\readlayer\entity\Photomicrograph;
    use sednasoft\virmisco\readlayer\entity\SpecimenCarrier;
    use sednasoft\virmisco\readlayer\valueobject\OrganismIdentification;
    use sednasoft\virmisco\readlayer\valueobject\OrganismTypeDesignation;

    require_once '../vendor/autoload.php';
    require_once '../src/credentials.php';
    //
    $connection = new PDO(MARIA_DSN, MARIA_USER, MARIA_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
    ]);
    $id = isset($_GET['id']) ? $_GET['id'] : null;
    $jsonOptions = JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE;
    // register the names of the subclasses to use when creating instances
    Gathering::$agentClass = AugmentedAgent::class;
    Gathering::$locationClass = AugmentedLocation::class;
    Gathering::$samplingDateClass = AugmentedSamplingDate::class;
    //
    $loadOrganism = $connection->prepare('SELECT * FROM `organism` WHERE `id` = ?');
    $loadOrganism->setFetchMode(PDO::FETCH_CLASS, Organism::class);
    $loadSpecimenCarrier = $connection->prepare('SELECT * FROM `specimen_carrier` WHERE `id` = ?');
    $loadSpecimenCarrier->setFetchMode(PDO::FETCH_CLASS, SpecimenCarrier::class);
    $loadGathering = $connection->prepare('SELECT * FROM `gathering` WHERE `id` = ?');
    $loadGathering->setFetchMode(PDO::FETCH_CLASS, Gathering::class);
    $loadScientificName = $connection->prepare('SELECT * FROM `scientific_name` WHERE `id` = ?');
    $loadScientificName->setFetchMode(PDO::FETCH_CLASS, AugmentedScientificName::class);
    $loadTaxon = $connection->prepare('SELECT * FROM `taxon` WHERE `id` = ? OR `valid_name_id` = ?');
    $loadTaxon->setFetchMode(PDO::FETCH_CLASS, AugmentedTaxon::class);
    $listCarrierScans = $connection->prepare('SELECT * FROM `carrier_scan` WHERE `specimen_carrier_id` = ?');
    $listCarrierScans->setFetchMode(PDO::FETCH_CLASS, CarrierScan::class);
    $listPhotomicrographs = $connection->prepare('SELECT * FROM `photomicrograph` WHERE `organism_id` = ?');
    $listPhotomicrographs->setFetchMode(PDO::FETCH_CLASS, Photomicrograph::class);
    //
    $loadOrganism->execute([$id]);
    /** @var Organism $organism */
    $organism = $loadOrganism->fetch();
    $loadSpecimenCarrier->execute([$organism->getSpecimenCarrierId()]);
    /** @var SpecimenCarrier $specimenCarrier */
    $specimenCarrier = $loadSpecimenCarrier->fetch();
    $loadGathering->execute([$specimenCarrier->getGatheringId()]);
    /** @var Gathering $gathering */
    $gathering = $loadGathering->fetch();
    /** @var OrganismIdentification|OrganismTypeDesignation $scientificNameProvider */
    $scientificNameProvider = $organism->getTypeDesignation() ?: $organism->getIdentification();
    $listPhotomicrographs->execute([$organism->getId()]);
    $listCarrierScans->execute([$specimenCarrier->getId()]);
    /** @var AugmentedScientificName|null $scientificName */
    $scientificName = null;
    /** @var AugmentedTaxon|null $taxon */
    $taxon = null;
    if ($scientificNameProvider) {
        $loadScientificName->execute([$scientificNameProvider->getScientificNameId()]);
        $scientificName = $loadScientificName->fetch();
        $loadTaxon->execute([$scientificName->getTaxonId(), $scientificName->getId()]);
        $taxon = $loadTaxon->fetch();
    }
    /** @var AugmentedSamplingDate $samplingDate */
    $samplingDate = $gathering ? $gathering->getSamplingDate() : null;
    /** @var AugmentedAgent $gatheringAgent */
    $gatheringAgent = $gathering ? $gathering->getAgent() : null;
    /** @var AugmentedLocation $location */
    $location = $gathering ? $gathering->getLocation() : null;
    //
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(
        [
            'organism' => [
                'id' => $organism->getId(),
                'phaseOrStage' => $organism->getPhaseOrStage(),
                'remarks' => $organism->getRemarks(),
                'sequenceNumber' => $organism->getSequenceNumber(),
                'sex' => $organism->getSex() ?: 'unknown'
            ],
            'scientificName' => [
                'id' => $scientificName->getId(),
                'authorship' => $scientificName->getAuthorship(),
                'genus' => $scientificName->getGenus(),
                'infraspecificEpithet' => $scientificName->getInfraspecificEpithet(),
                'isParenthesized' => $scientificName->getIsParenthesized(),
                'specificEpithet' => $scientificName->getSpecificEpithet(),
                'subgenus' => $scientificName->getSubgenus(),
                'year' => $scientificName->getYear(),
                'compact' => $scientificName->format(AugmentedScientificName::F_BINOMIAL),
                'full' => $scientificName->format(AugmentedScientificName::F_FULL_NAME)
            ],
            'taxon' => [
                'id' => $taxon->getId(),
                'regnum' => $taxon->getRegnum(),
                'subRegnum' => $taxon->getSubRegnum(),
                'superPhylum' => $taxon->getSuperPhylum(),
                'phylum' => $taxon->getPhylum(),
                'subPhylum' => $taxon->getSubPhylum(),
                'superClassis' => $taxon->getSuperClassis(),
                'classis' => $taxon->getClassis(),
                'subClassis' => $taxon->getSubClassis(),
                'superOrdo' => $taxon->getSuperOrdo(),
                'ordo' => $taxon->getOrdo(),
                'subOrdo' => $taxon->getSubOrdo(),
                'superFamilia' => $taxon->getSuperFamilia(),
                'familia' => $taxon->getFamilia(),
                'subFamilia' => $taxon->getSubFamilia(),
                'tribus' => $taxon->getTribus(),
                'compact' => $taxon->joinNonEmpty(' » '),
                'reverse' => $taxon->joinNonEmpty(' « ', true)
            ],
            'identification' => $scientificNameProvider instanceof OrganismIdentification
                ? [
                    'identifier' => $scientificNameProvider->getIdentifier(),
                    'qualifier' => $scientificNameProvider->getQualifier()
                ]
                : null,
            'typeDesignation' => $scientificNameProvider instanceof OrganismTypeDesignation
                ? ['typeStatus' => $scientificNameProvider->getTypeStatus()]
                : null,
            'specimenCarrier' => [
                'id' => $specimenCarrier->getId(),
                'carrierNumber' => $specimenCarrier->getCarrierNumber(),
                'labelTranscript' => $specimenCarrier->getLabelTranscript(),
                'owner' => $specimenCarrier->getOwner(),
                'preparationType' => $specimenCarrier->getPreparationType(),
                'previousCollection' => $specimenCarrier->getPreviousCollection()
            ],
            'gathering' => [
                'id' => $gathering->getId(),
                'journalNumber' => $gathering->getJournalNumber(),
                'remarks' => $gathering->getRemarks()
            ],
            'samplingDate' => [
                'after' => $samplingDate->getAfterDate(),
                'before' => $samplingDate->getBeforeDate(),
                'compact' => strval($samplingDate)
            ],
            'agent' => [
                'person' => $gatheringAgent->getPerson(),
                'organization' => $gatheringAgent->getOrganization(),
                'compact' => strval($gatheringAgent)
            ],
            'location' => [
                'country' => $location->getCountry(),
                'province' => $location->getProvince(),
                'region' => $location->getRegion(),
                'place' => $location->getPlace(),
                'compact' => strval($location)
            ],
            'carrierScans' => iterator_to_array(iterateCarrierScans($listCarrierScans)),
            'photomicrographs' => iterator_to_array(iteratePhotomicrographs($listPhotomicrographs))
        ],
        $jsonOptions
    );
    //
    /**
     * @param PDOStatement $statement
     * @return Generator
     */
    function iterateCarrierScans (PDOStatement $statement) {
        /** @var CarrierScan $carrierScan */
        foreach ($statement as $carrierScan) {
            yield [
                'carrierScan' => [
                    'id' => $carrierScan->getId(),
                    'creationTime' => $carrierScan->getCreationTime(),
                    'modificationTime' => $carrierScan->getModificationTime(),
                    'realPath' => $carrierScan->getRealPath(),
                    'uri' => $carrierScan->getUri()
                ]
            ];
        }
    }
    /**
     * @param PDOStatement $statement
     * @return Generator
     */
    function iteratePhotomicrographs (PDOStatement $statement) {
        /** @var Photomicrograph $photomicrograph */
        foreach ($statement as $photomicrograph) {
            $camera = $photomicrograph->getCamera();
            $detailOf = $photomicrograph->getDetailOf();
            $digitizationData = $photomicrograph->getDigitizationData();
            $file = $photomicrograph->getFile();
            $microscope = $photomicrograph->getMicroscope();
            $microscopeSettings = $photomicrograph->getMicroscopeSettings();
            yield [
                'photomicrograph' => [
                    'id' => $photomicrograph->getId(),
                    'presentationUri' => $photomicrograph->getPresentationUri(),
                    'title' => $photomicrograph->getTitle()
                ],
                'camera' => $camera
                    ? [
                        'cameraMaker' => $camera->getCameraMaker(),
                        'cameraName' => $camera->getCameraName(),
                        'cameraArticleOrSerialNumber' => $camera->getCameraArticleOrSerialNumber(),
                        'sensorMaker' => $camera->getSensorMaker(),
                        'sensorName' => $camera->getSensorName(),
                        'sensorArticleOrSerialNumber' => $camera->getSensorArticleOrSerialNumber(),
                        'opticalFormat' => $camera->getOpticalFormat(),
                        'captureFormat' => $camera->getCaptureFormat(),
                        'chipWidth' => $camera->getChipWidth(),
                        'chipHeight' => $camera->getChipHeight(),
                        'pixelWidth' => $camera->getPixelWidth(),
                        'pixelHeight' => $camera->getPixelHeight(),
                        'activePixelsHor' => $camera->getActivePixelsHor(),
                        'activePixelsVer' => $camera->getActivePixelsVer(),
                        'colorFilterArray' => $camera->getColorFilterArray(),
                        'protectiveColorFilter' => $camera->getProtectiveColorFilter(),
                        'adcResolution' => $camera->getAdcResolution(),
                        'dynamicRange' => $camera->getDynamicRange(),
                        'snrMax' => $camera->getSnrMax(),
                        'readoutNoise' => $camera->getReadoutNoise(),
                    ]
                    : null,
                'detailOf' => $detailOf
                    ? [
                        'photomicrographId' => $detailOf->getPhotomicrographId(),
                        'hotspotX' => $detailOf->getHotspotX(),
                        'hotspotY' => $detailOf->getHotspotY()
                    ]
                    : null,
                'digitizationData' => $digitizationData
                    ? [
                        'width' => $digitizationData->getWidth(),
                        'height' => $digitizationData->getHeight(),
                        'colorDepth' => $digitizationData->getColorDepth(),
                        'reproductionScaleHorizontal' => $digitizationData->getReproductionScaleHorizontal(),
                        'reproductionScaleVertical' => $digitizationData->getReproductionScaleVertical()
                    ]
                    : null,
                'file' => $file
                    ? [
                        'realPath' => $file->getRealPath(),
                        'uri' => $file->getUri(),
                        'creationTime' => $file->getCreationTime(),
                        'modificationTime' => $file->getModificationTime()
                    ]
                    : null,
                'microscope' => $microscope
                    ? [
                        'standMaker' => $microscope->getStandMaker(),
                        'standName' => $microscope->getStandName(),
                        'standArticleOrSerialNumber' => $microscope->getStandArticleOrSerialNumber(),
                        'condenserMaker' => $microscope->getCondenserMaker(),
                        'condenserName' => $microscope->getCondenserName(),
                        'condenserArticleOrSerialNumber' => $microscope->getCondenserArticleOrSerialNumber(),
                        'condenserTurretPrismMaker' => $microscope->getCondenserTurretPrismMaker(),
                        'condenserTurretPrismName' => $microscope->getCondenserTurretPrismName(),
                        'condenserTurretPrismArticleOrSerialNumber' => $microscope->getCondenserTurretPrismArticleOrSerialNumber(),
                        'nosepieceObjectiveMaker' => $microscope->getNosepieceObjectiveMaker(),
                        'nosepieceObjectiveName' => $microscope->getNosepieceObjectiveName(),
                        'nosepieceObjectiveArticleOrSerialNumber' => $microscope->getNosepieceObjectiveArticleOrSerialNumber(),
                        'nosepieceObjectiveType' => $microscope->getNosepieceObjectiveType(),
                        'nosepieceObjectiveNumericalAperture' => $microscope->getNosepieceObjectiveNumericalAperture(),
                        'nosepieceObjectiveMagnification' => $microscope->getNosepieceObjectiveMagnification(),
                        'dicTurretPrismMaker' => $microscope->getDicTurretPrismMaker(),
                        'dicTurretPrismName' => $microscope->getDicTurretPrismName(),
                        'dicTurretPrismArticleOrSerialNumber' => $microscope->getDicTurretPrismArticleOrSerialNumber(),
                        'magnificationChangerMaker' => $microscope->getMagnificationChangerMaker(),
                        'magnificationChangerName' => $microscope->getMagnificationChangerName(),
                        'magnificationChangerArticleOrSerialNumber' => $microscope->getMagnificationChangerArticleOrSerialNumber(),
                        'numberOfPorts' => $microscope->getNumberOfPorts(),
                        'portsMaker' => $microscope->getPortsMaker(),
                        'portsName' => $microscope->getPortsName(),
                        'portsArticleOrSerialNumber' => $microscope->getPortsArticleOrSerialNumber(),
                        'cameraMountAdapterMaker' => $microscope->getCameraMountAdapterMaker(),
                        'cameraMountAdapterName' => $microscope->getCameraMountAdapterName(),
                        'cameraMountAdapterMagnification' => $microscope->getCameraMountAdapterMagnification(),
                        'cameraMountAdapterArticleOrSerialNumber' => $microscope->getCameraMountAdapterArticleOrSerialNumber()
                    ]
                    : null,
                'microscopeSettings' => $microscopeSettings
                    ? [
                        'contrastMethod' => $microscopeSettings->getContrastMethod(),
                        'dicPrismPosition' => $microscopeSettings->getDicPrismPosition(),
                        'apertureDiaphragmOpening' => $microscopeSettings->getApertureDiaphragmOpening(),
                        'fieldDiaphragmOpening' => $microscopeSettings->getFieldDiaphragmOpening(),
                        'isPolarizerInLightPath' => $microscopeSettings->getIsPolarizerInLightPath(),
                        'magnificationChangerMagnification' => $microscopeSettings->getMagnificationChangerMagnification()
                    ]
                    : null
            ];
        }
    }
