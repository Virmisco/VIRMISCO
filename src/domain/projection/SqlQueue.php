<?php
    namespace sednasoft\virmisco\domain\projection;

    use PDO;
    use sednasoft\virmisco\domain\event\AuthorshipOfPhotomicrographProvided;
    use sednasoft\virmisco\domain\event\DicPrismPositionChanged;
    use sednasoft\virmisco\domain\event\EntireTaxonomyModelRedesigned;
    use sednasoft\virmisco\domain\event\FocalPlaneImageAppended;
    use sednasoft\virmisco\domain\event\GatheringLogged;
    use sednasoft\virmisco\domain\event\GatheringManipulated;
    use sednasoft\virmisco\domain\event\HigherTaxaForOrganismProvided;
    use sednasoft\virmisco\domain\event\HigherTaxaProvided;
    use sednasoft\virmisco\domain\event\MentionedNameOfOrganismChanged;
    use sednasoft\virmisco\domain\event\NameOriginOfOrganismChanged;
    use sednasoft\virmisco\domain\event\OrganismDescriptionDiscarded;
    use sednasoft\virmisco\domain\event\OrganismDigitized;
    use sednasoft\virmisco\domain\event\OrganismOnSpecimenCarrierDescribed;
    use sednasoft\virmisco\domain\event\OrganismOnSpecimenCarrierDesignatedAsType;
    use sednasoft\virmisco\domain\event\OrganismOnSpecimenCarrierIdentified;
    use sednasoft\virmisco\domain\event\OrganismOnSpecimenCarrierManipulated;
    use sednasoft\virmisco\domain\event\PhotomicrographDeleted;
    use sednasoft\virmisco\domain\event\PhotomicrographDigitized;
    use sednasoft\virmisco\domain\event\PhotomicrographDigitizedV2;
    use sednasoft\virmisco\domain\event\PhotomicrographManipulated;
    use sednasoft\virmisco\domain\event\PhotomicrographManipulatedV2;
    use sednasoft\virmisco\domain\event\PhotomicrographRenamed;
    use sednasoft\virmisco\domain\event\SpecimenCarrierManipulated;
    use sednasoft\virmisco\domain\event\SpecimenCarrierRecorded;
    use sednasoft\virmisco\domain\event\SpecimenCarrierScannedToImage;
    use sednasoft\virmisco\domain\event\SpecimenFromGatheringPreserved;
    use sednasoft\virmisco\domain\event\SynonymAssignedToTaxon;
    use sednasoft\virmisco\domain\event\SynonymForOrganismProvided;
    use sednasoft\virmisco\domain\event\SynonymsOfOrganismCleared;
    use sednasoft\virmisco\domain\event\TaxonRegistered;
    use sednasoft\virmisco\domain\event\ValidNameInitiallyAssignedToTaxon;
    use sednasoft\virmisco\domain\event\ValidNameOfOrganismChanged;
    use sednasoft\virmisco\domain\valueobject\ScientificName;
    use sednasoft\virmisco\singiere\AbstractEvent;
    use sednasoft\virmisco\singiere\Uuid;

    /**
     * Represents a Redis list of SQL statements yet to be sent to the report database to update the current state.
     */
    class SqlQueue extends AbstractRedisBasedSqlQueueProjection
    {
        private $higherTaxa = [];
        private $synonyms = [];

        /**
         * The method to be queued for EntireTaxonomyModelRedesigned events.
         *
         * @param PDO $dbConnection
         */
        public static function convertTaxonHierarchy(PDO $dbConnection)
        {
            $insertStatement = $dbConnection->prepare(
                <<<'EOD'
INSERT INTO `higher_taxon` (`key_rank_id`, `relative_index`, `parent_rank_id`, `parent_relative_index`, `rank`, `monomial`, `_created`)
VALUES (?, ?, ?, ?, ?, ?, ?)
EOD
            );
            $selectStatement = $dbConnection->query(
                <<<'EOD'
SELECT DISTINCT
    `regnum`,
    `subregnum`,
    `superphylum`,
    `phylum`,
    `subphylum`,
    `superclassis`,
    `classis`,
    `subclassis`,
    `superordo`,
    `ordo`,
    `subordo`,
    `superfamilia`,
    `familia`,
    `subfamilia`,
    `tribus`
FROM `taxon`
EOD
            );
            $selectStatement->setFetchMode(PDO::FETCH_OBJ);
            $hierarchy = [];
            foreach ($selectStatement as $row) {
                $higherRank = null;
                $higherKeyRank = null;
                foreach ($row as $rank => $monomial) {
                    switch ($rank) {
                        case 'regnum':
                            $hierarchy[$rank][$monomial]['indices'][$monomial] = 0;
                            $hierarchy[$rank][$monomial]['ranks'][$monomial] = $rank;
                            $hierarchy[$rank][$monomial]['id'] = isset($hierarchy[$rank][$monomial]['id'])
                                ? $hierarchy[$rank][$monomial]['id']
                                : strval(Uuid::createRandom());
                            $hierarchy[$rank][$monomial]['parentRankId'] = null;
                            $hierarchy[$rank][$monomial]['parentRelativeIndex'] = 0;
                            break;
                        case 'phylum':
                        case 'classis':
                        case 'ordo':
                        case 'familia':
                        case 'species':
                            $higherKeyRank = [
                                'phylum' => 'regnum',
                                'classis' => 'phylum',
                                'ordo' => 'classis',
                                'familia' => 'ordo'
                            ][$rank];
                            $hierarchy[$rank][$monomial]['indices'][$monomial] = 0;
                            $hierarchy[$rank][$monomial]['ranks'][$monomial] = $rank;
                            $hierarchy[$rank][$monomial]['id'] = isset($hierarchy[$rank][$monomial]['id'])
                                ? $hierarchy[$rank][$monomial]['id']
                                : strval(Uuid::createRandom());
                            $hierarchy[$rank][$monomial]['parentRankId']
                                = $hierarchy[$higherKeyRank][$row->$higherKeyRank]['id'];
                            $hierarchy[$rank][$monomial]['parentRelativeIndex']
                                = $hierarchy[$higherKeyRank][$row->$higherKeyRank]['indices'][$row->$higherRank];
                            break;
                        default:
                            if ($monomial && !isset($hierarchy[$higherRank][$row->$higherRank]['indices'][$monomial])) {
                                $hierarchy[$higherRank][$row->$higherRank]['indices'][$monomial]
                                    = count($hierarchy[$higherRank][$row->$higherRank]['indices']);
                                $hierarchy[$higherRank][$row->$higherRank]['ranks'][$monomial] = $rank;
                            }
                    }
                    if ($monomial) {
                        $higherRank = $rank;
                    }
                }
            }
            foreach ($hierarchy as $groupName => $group) {
                foreach ($group as $key => $taxa) {
                    $keyRankId = $taxa['id'];
                    $parentRankId = $taxa['parentRankId'];
                    $parentRelativeIndex = $taxa['parentRelativeIndex'];
                    foreach ($taxa['indices'] as $monomial => $relativeIndex) {
                        $rank = $taxa['ranks'][$monomial];
                        list($usec, $sec) = explode(' ', microtime());

                        $insertStatement->execute([
                            $keyRankId,
                            $relativeIndex,
                            $parentRankId,
                            $parentRelativeIndex,
                            $rank,
                            $monomial,
                            sprintf('%d.%06d', $sec, $usec * 1e6)
                        ]);
                    }
                }
            }
        }

        /**
         * Applies the event and modifies the current state accordingly.
         *
         * @param AbstractEvent $event
         */
        public function apply(AbstractEvent $event)
        {
            if ($event instanceof FocalPlaneImageAppended) {
                $this->queueInsert(self::TBL_FOCAL_PLANE_IMAGE, [
                    self::COL_FPI_ID => strval(Uuid::createRandom()),
                    self::COL_FPI_PHOTOMICROGRAPH_ID => strval($event->getAggregateId()),
                    self::COL_FPI_EXPOSURE_SETTINGS_DURATION => $event->getExposureSettingsDuration(),
                    self::COL_FPI_EXPOSURE_SETTINGS_GAIN => $event->getExposureSettingsGain(),
                    self::COL_FPI_FILE_CREATION_TIME => $event->getFileCreationTime(),
                    self::COL_FPI_FILE_MODIFICATION_TIME => $event->getFileModificationTime(),
                    self::COL_FPI_FILE_REAL_PATH => $event->getFileRealPath(),
                    self::COL_FPI_FILE_URI => $event->getFileUri(),
                    self::COL_FPI_FOCUS_POSITION => $event->getFocusPosition(),
                    self::COL_FPI_HISTOGRAM_SETTINGS_BLACK_CLIP => $event->getHistogramSettingsBlackClip(),
                    self::COL_FPI_HISTOGRAM_SETTINGS_GAMMA => $event->getHistogramSettingsGamma(),
                    self::COL_FPI_HISTOGRAM_SETTINGS_WHITE_CLIP => $event->getHistogramSettingsWhiteClip(),
                    self::COL_FPI_PRESENTATION_URI => $event->getPresentationUri(),
                    self::COL_FPI_POST_PROCESSING_SETTINGS_SHADING => $event->isPostProcessingSettingsShading(),
                    self::COL_FPI_POST_PROCESSING_SETTINGS_SHARPENING => $event->isPostProcessingSettingsShading()
                ]);
            } elseif ($event instanceof GatheringLogged) {
                $this->queueInsert(self::TBL_GATHERING, [
                    self::COL_G_ID => strval($event->getAggregateId()),
                    self::COL_G_AGENT_ORGANIZATION => $event->getAgentOrganization(),
                    self::COL_G_AGENT_PERSON => $event->getAgentPerson(),
                    self::COL_G_JOURNAL_NUMBER => $event->getJournalNumber(),
                    self::COL_G_LOCATION_COUNTRY => $event->getLocationCountry(),
                    self::COL_G_LOCATION_PLACE => $event->getLocationPlace(),
                    self::COL_G_LOCATION_PROVINCE => $event->getLocationProvince(),
                    self::COL_G_LOCATION_REGION => $event->getLocationRegion(),
                    self::COL_G_REMARKS => $event->getRemarks(),
                    self::COL_G_SAMPLING_DATE_AFTER => $event->getSamplingDateAfter(),
                    self::COL_G_SAMPLING_DATE_BEFORE => $event->getSamplingDateBefore()
                ]);
            } elseif ($event instanceof GatheringManipulated) {
                $this->queueUpdate(
                    self::TBL_GATHERING,
                    [
                        self::COL_G_AGENT_ORGANIZATION => $event->getAgentOrganization(),
                        self::COL_G_AGENT_PERSON => $event->getAgentPerson(),
                        self::COL_G_JOURNAL_NUMBER => $event->getJournalNumber(),
                        self::COL_G_LOCATION_COUNTRY => $event->getLocationCountry(),
                        self::COL_G_LOCATION_PLACE => $event->getLocationPlace(),
                        self::COL_G_LOCATION_PROVINCE => $event->getLocationProvince(),
                        self::COL_G_LOCATION_REGION => $event->getLocationRegion(),
                        self::COL_G_REMARKS => $event->getRemarks(),
                        self::COL_G_SAMPLING_DATE_AFTER => $event->getSamplingDateAfter(),
                        self::COL_G_SAMPLING_DATE_BEFORE => $event->getSamplingDateBefore()
                    ],
                    [self::COL_G_ID => strval($event->getAggregateId())]
                );
            } elseif ($event instanceof OrganismDigitized) {
                // ignore this event, since it is just the reverse of PhotomicrographDigitized
            } elseif ($event instanceof OrganismOnSpecimenCarrierDescribed) {
                $this->queueInsert(self::TBL_ORGANISM, [
                    self::COL_O_ID => strval(Uuid::createRandom()),
                    self::COL_O_SPECIMEN_CARRIER_ID => strval($event->getAggregateId()),
                    self::COL_O_SEQUENCE_NUMBER => $event->getSequenceNumber(),
                    self::COL_O_PHASE_OR_STAGE => $event->getPhaseOrStage(),
                    self::COL_O_REMARKS => $event->getRemarks(),
                    self::COL_O_SEX => $event->getSex()
                ]);
            } elseif ($event instanceof OrganismOnSpecimenCarrierManipulated) {
                $this->queueUpdate(
                    self::TBL_ORGANISM,
                    [
                        self::COL_O_SEQUENCE_NUMBER => $event->getSequenceNumber(),
                        self::COL_O_PHASE_OR_STAGE => $event->getPhaseOrStage(),
                        self::COL_O_REMARKS => $event->getRemarks(),
                        self::COL_O_SEX => $event->getSex()
                    ],
                    [
                        self::COL_O_SPECIMEN_CARRIER_ID => strval($event->getAggregateId()),
                        self::COL_O_SEQUENCE_NUMBER => strval($event->getOldSequenceNumber())
                    ]
                );
                $this->queueUpdate(
                    self::TBL_SCIENTIFIC_NAME,
                    [self::COL_SN_SEQUENCE_NUMBER => $event->getSequenceNumber()],
                    [
                        self::COL_SN_SPECIMEN_CARRIER_ID => strval($event->getAggregateId()),
                        self::COL_SN_SEQUENCE_NUMBER => strval($event->getOldSequenceNumber())
                    ]
                );
            } elseif ($event instanceof OrganismDescriptionDiscarded) {
                $this->queuePreparedStatement(
                /** @lang MySQL */
                    <<<'EOD'
DELETE `p`.*, `fpi`.*
FROM `photomicrograph` `p`
JOIN `organism` `o` ON `o`.`id` = `p`.`organism_id`
LEFT JOIN `focal_plane_image` `fpi` ON `fpi`.`photomicrograph_id` = `p`.`id`
WHERE `o`.`specimen_carrier_id` = ? AND `o`.`sequence_number` = ?
EOD
                    ,
                    [strval($event->getAggregateId()), $event->getSequenceNumber()]
                );
                $this->queueDelete(self::TBL_SCIENTIFIC_NAME, [
                    self::COL_SN_SPECIMEN_CARRIER_ID => strval($event->getAggregateId()),
                    self::COL_SN_SEQUENCE_NUMBER => $event->getSequenceNumber()
                ]);
                $this->queueDelete(self::TBL_ORGANISM, [
                    self::COL_O_SPECIMEN_CARRIER_ID => strval($event->getAggregateId()),
                    self::COL_O_SEQUENCE_NUMBER => $event->getSequenceNumber()
                ]);
            } elseif ($event instanceof PhotomicrographManipulated
                || $event instanceof PhotomicrographManipulatedV2
            ) {
                $this->queueUpdate(
                    self::TBL_PHOTOMICROGRAPH,
                    [
                        self::COL_P_CAM_ACTIVE_PIXELS_HOR => $event->getCameraActivePixelsHor(),
                        self::COL_P_CAM_ACTIVE_PIXELS_VER => $event->getCameraActivePixelsVer(),
                        self::COL_P_CAM_ADC_RESOLUTION => $event->getCameraAdcResolution(),
                        self::COL_P_CAM_CAMERA_ASN => $event->getCameraCameraArticleOrSerialNumber(),
                        self::COL_P_CAM_CAMERA_MAKER => $event->getCameraCameraMaker(),
                        self::COL_P_CAM_CAMERA_NAME => $event->getCameraCameraName(),
                        self::COL_P_CAM_CAPTURE_FORMAT => $event->getCameraCaptureFormat(),
                        self::COL_P_CAM_CHIP_HEIGHT => $event->getCameraChipHeight(),
                        self::COL_P_CAM_CHIP_WIDTH => $event->getCameraChipWidth(),
                        self::COL_P_CAM_COLOR_FILTER_ARRAY => $event->getCameraColorFilterArray(),
                        self::COL_P_CAM_DYNAMIC_RANGE => $event->getCameraDynamicRange(),
                        self::COL_P_CAM_OPTICAL_FORMAT => $event->getCameraOpticalFormat(),
                        self::COL_P_CAM_PIXEL_HEIGHT => $event->getCameraPixelHeight(),
                        self::COL_P_CAM_PIXEL_WIDTH => $event->getCameraPixelWidth(),
                        self::COL_P_CAM_PROTECTIVE_COLOR_FILTER => $event->getCameraProtectiveColorFilter(),
                        self::COL_P_CAM_READOUT_NOISE => $event->getCameraReadoutNoise(),
                        self::COL_P_CAM_SENSOR_ASN => $event->getCameraSensorArticleOrSerialNumber(),
                        self::COL_P_CAM_SENSOR_MAKER => $event->getCameraSensorMaker(),
                        self::COL_P_CAM_SENSOR_NAME => $event->getCameraSensorName(),
                        self::COL_P_CAM_SNR_MAX => $event->getCameraSnrMax(),
                        self::COL_P_CREATOR_CAPTURING => $event->getCreatorCapturing(),
                        self::COL_P_CREATOR_PROCESSING => $event->getCreatorProcessing(),
                        self::COL_P_DET_HOTSPOT_X => $event->getDetailOfHotspotX(),
                        self::COL_P_DET_HOTSPOT_Y => $event->getDetailOfHotspotY(),
                        self::COL_P_DET_PHOTOMICROGRAPH_ID => $event->getDetailOfPhotomicrographId(),
                        self::COL_P_DIG_COLOR_DEPTH => $event->getDigitizationDataColorDepth(),
                        self::COL_P_DIG_HEIGHT => $event->getDigitizationDataHeight(),
                        self::COL_P_DIG_REPRODUCTION_SCALE_HORIZONTAL => $event->getDigitizationDataReproductionScaleHorizontal(),
                        self::COL_P_DIG_REPRODUCTION_SCALE_VERTICAL => $event->getDigitizationDataReproductionScaleVertical(),
                        self::COL_P_DIG_WIDTH => $event->getDigitizationDataWidth(),
                        self::COL_P_FILE_CREATION_TIME => $event->getFileCreationTime(),
                        self::COL_P_FILE_MODIFICATION_TIME => $event->getFileModificationTime(),
                        self::COL_P_FILE_REAL_PATH => $event->getFileRealPath(),
                        self::COL_P_FILE_URI => $event->getFileUri(),
                        self::COL_P_MIC_CAMERA_MOUNT_ADAPTER_ASN => $event->getMicroscopeCameraMountAdapterArticleOrSerialNumber(),
                        self::COL_P_MIC_CAMERA_MOUNT_ADAPTER_MAGNIFICATION => $event->getMicroscopeCameraMountAdapterMagnification(),
                        self::COL_P_MIC_CAMERA_MOUNT_ADAPTER_MAKER => $event->getMicroscopeCameraMountAdapterMaker(),
                        self::COL_P_MIC_CAMERA_MOUNT_ADAPTER_NAME => $event->getMicroscopeCameraMountAdapterName(),
                        self::COL_P_MIC_CONDENSER_ASN => $event->getMicroscopeCondenserArticleOrSerialNumber(),
                        self::COL_P_MIC_CONDENSER_MAKER => $event->getMicroscopeCondenserMaker(),
                        self::COL_P_MIC_CONDENSER_NAME => $event->getMicroscopeCondenserName(),
                        self::COL_P_MIC_CONDENSER_TURRET_PRISM_ASN => $event->getMicroscopeCondenserTurretPrismArticleOrSerialNumber(),
                        self::COL_P_MIC_CONDENSER_TURRET_PRISM_MAKER => $event->getMicroscopeCondenserTurretPrismMaker(),
                        self::COL_P_MIC_CONDENSER_TURRET_PRISM_NAME => $event->getMicroscopeCondenserTurretPrismName(),
                        self::COL_P_MIC_DIC_TURRET_PRISM_ASN => $event->getMicroscopeDicTurretPrismArticleOrSerialNumber(),
                        self::COL_P_MIC_DIC_TURRET_PRISM_MAKER => $event->getMicroscopeDicTurretPrismMaker(),
                        self::COL_P_MIC_DIC_TURRET_PRISM_NAME => $event->getMicroscopeDicTurretPrismName(),
                        self::COL_P_MIC_MAGNIFICATION_CHANGER_ASN => $event->getMicroscopeMagnificationChangerArticleOrSerialNumber(),
                        self::COL_P_MIC_MAGNIFICATION_CHANGER_MAKER => $event->getMicroscopeMagnificationChangerMaker(),
                        self::COL_P_MIC_MAGNIFICATION_CHANGER_NAME => $event->getMicroscopeMagnificationChangerName(),
                        self::COL_P_MIC_NOSEPIECE_OBJECTIVE_ASN => $event->getMicroscopeNosepieceObjectiveArticleOrSerialNumber(),
                        self::COL_P_MIC_NOSEPIECE_OBJECTIVE_MAGNIFICATION => $event->getMicroscopeNosepieceObjectiveMagnification(),
                        self::COL_P_MIC_NOSEPIECE_OBJECTIVE_MAKER => $event->getMicroscopeNosepieceObjectiveMaker(),
                        self::COL_P_MIC_NOSEPIECE_OBJECTIVE_NAME => $event->getMicroscopeNosepieceObjectiveName(),
                        self::COL_P_MIC_NOSEPIECE_OBJECTIVE_NUMERICAL_APERTURE => $event->getMicroscopeNosepieceObjectiveNumericalAperture(),
                        self::COL_P_MIC_NOSEPIECE_OBJECTIVE_TYPE => $event->getMicroscopeNosepieceObjectiveType(),
                        self::COL_P_MIC_PORTS_ASN => $event->getMicroscopePortsArticleOrSerialNumber(),
                        self::COL_P_MIC_PORTS_MAKER => $event->getMicroscopePortsMaker(),
                        self::COL_P_MIC_PORTS_NAME => $event->getMicroscopePortsName(),
                        self::COL_P_MIC_STAND_ASN => $event->getMicroscopeStandArticleOrSerialNumber(),
                        self::COL_P_MIC_STAND_MAKER => $event->getMicroscopeStandMaker(),
                        self::COL_P_MIC_STAND_NAME => $event->getMicroscopeStandName(),
                        self::COL_P_SET_APERTURE_DIAPHRAGM_OPENING => $event->getMicroscopeSettingsApertureDiaphragmOpening(),
                        self::COL_P_SET_CONTRAST_METHOD => $event->getMicroscopeSettingsContrastMethod(),
                        self::COL_P_SET_DIC_PRISM_POSITION => $event->getMicroscopeSettingsDicPrismPosition(),
                        self::COL_P_SET_FIELD_DIAPHRAGM_OPENING => $event->getMicroscopeSettingsFieldDiaphragmOpening(),
                        self::COL_P_SET_MAGCHANGER_MAGNIFICATION => $event->getMicroscopeSettingsMagnificationChangerMagnification(),
                        self::COL_P_PRESENTATION_URI => $event->getPresentationUri(),
                        self::COL_P_TITLE => $event->getTitle()
                    ],
                    [self::COL_P_ID => strval($event->getAggregateId())]
                );
            } elseif ($event instanceof PhotomicrographDigitized
                || $event instanceof PhotomicrographDigitizedV2
            ) {
                $this->queueInsert(self::TBL_ORGANISM, [
                    self::COL_O_ID => strval(Uuid::createRandom()),
                    self::COL_O_SPECIMEN_CARRIER_ID => strval($event->getAggregateId()),
                    self::COL_O_SEQUENCE_NUMBER => $event->getSequenceNumber()
                ]);
                $subselect = new SqlQueueSubselect(self::TBL_ORGANISM, self::COL_O_ID);
                $subselect->addFilter(self::COL_O_SPECIMEN_CARRIER_ID, strval($event->getSpecimenCarrierId()));
                $subselect->addFilter(self::COL_O_SEQUENCE_NUMBER, $event->getSequenceNumber());
                $this->queueInsert(self::TBL_PHOTOMICROGRAPH, [
                    self::COL_P_ID => strval($event->getAggregateId()),
                    self::COL_P_CAM_ACTIVE_PIXELS_HOR => $event->getCameraActivePixelsHor(),
                    self::COL_P_CAM_ACTIVE_PIXELS_VER => $event->getCameraActivePixelsVer(),
                    self::COL_P_CAM_ADC_RESOLUTION => $event->getCameraAdcResolution(),
                    self::COL_P_CAM_CAMERA_ASN => $event->getCameraCameraArticleOrSerialNumber(),
                    self::COL_P_CAM_CAMERA_MAKER => $event->getCameraCameraMaker(),
                    self::COL_P_CAM_CAMERA_NAME => $event->getCameraCameraName(),
                    self::COL_P_CAM_CAPTURE_FORMAT => $event->getCameraCaptureFormat(),
                    self::COL_P_CAM_CHIP_HEIGHT => $event->getCameraChipHeight(),
                    self::COL_P_CAM_CHIP_WIDTH => $event->getCameraChipWidth(),
                    self::COL_P_CAM_COLOR_FILTER_ARRAY => $event->getCameraColorFilterArray(),
                    self::COL_P_CAM_DYNAMIC_RANGE => $event->getCameraDynamicRange(),
                    self::COL_P_CAM_OPTICAL_FORMAT => $event->getCameraOpticalFormat(),
                    self::COL_P_CAM_PIXEL_HEIGHT => $event->getCameraPixelHeight(),
                    self::COL_P_CAM_PIXEL_WIDTH => $event->getCameraPixelWidth(),
                    self::COL_P_CAM_PROTECTIVE_COLOR_FILTER => $event->getCameraProtectiveColorFilter(),
                    self::COL_P_CAM_READOUT_NOISE => $event->getCameraReadoutNoise(),
                    self::COL_P_CAM_SENSOR_ASN => $event->getCameraSensorArticleOrSerialNumber(),
                    self::COL_P_CAM_SENSOR_MAKER => $event->getCameraSensorMaker(),
                    self::COL_P_CAM_SENSOR_NAME => $event->getCameraSensorName(),
                    self::COL_P_CAM_SNR_MAX => $event->getCameraSnrMax(),
                    self::COL_P_DET_HOTSPOT_X => $event->getDetailOfHotspotX(),
                    self::COL_P_DET_HOTSPOT_Y => $event->getDetailOfHotspotY(),
                    self::COL_P_DET_PHOTOMICROGRAPH_ID => $event->getDetailOfPhotomicrographId(),
                    self::COL_P_DIG_COLOR_DEPTH => $event->getDigitizationDataColorDepth(),
                    self::COL_P_DIG_HEIGHT => $event->getDigitizationDataHeight(),
                    self::COL_P_DIG_REPRODUCTION_SCALE_HORIZONTAL => $event->getDigitizationDataReproductionScaleHorizontal(),
                    self::COL_P_DIG_REPRODUCTION_SCALE_VERTICAL => $event->getDigitizationDataReproductionScaleVertical(),
                    self::COL_P_DIG_WIDTH => $event->getDigitizationDataWidth(),
                    self::COL_P_FILE_CREATION_TIME => $event->getFileCreationTime(),
                    self::COL_P_FILE_MODIFICATION_TIME => $event->getFileModificationTime(),
                    self::COL_P_FILE_REAL_PATH => $event->getFileRealPath(),
                    self::COL_P_FILE_URI => $event->getFileUri(),
                    self::COL_P_MIC_CAMERA_MOUNT_ADAPTER_ASN => $event->getMicroscopeCameraMountAdapterArticleOrSerialNumber(),
                    self::COL_P_MIC_CAMERA_MOUNT_ADAPTER_MAGNIFICATION => $event->getMicroscopeCameraMountAdapterMagnification(),
                    self::COL_P_MIC_CAMERA_MOUNT_ADAPTER_MAKER => $event->getMicroscopeCameraMountAdapterMaker(),
                    self::COL_P_MIC_CAMERA_MOUNT_ADAPTER_NAME => $event->getMicroscopeCameraMountAdapterName(),
                    self::COL_P_MIC_CONDENSER_ASN => $event->getMicroscopeCondenserArticleOrSerialNumber(),
                    self::COL_P_MIC_CONDENSER_MAKER => $event->getMicroscopeCondenserMaker(),
                    self::COL_P_MIC_CONDENSER_NAME => $event->getMicroscopeCondenserName(),
                    self::COL_P_MIC_CONDENSER_TURRET_PRISM_ASN => $event->getMicroscopeCondenserTurretPrismArticleOrSerialNumber(),
                    self::COL_P_MIC_CONDENSER_TURRET_PRISM_MAKER => $event->getMicroscopeCondenserTurretPrismMaker(),
                    self::COL_P_MIC_CONDENSER_TURRET_PRISM_NAME => $event->getMicroscopeCondenserTurretPrismName(),
                    self::COL_P_MIC_DIC_TURRET_PRISM_ASN => $event->getMicroscopeDicTurretPrismArticleOrSerialNumber(),
                    self::COL_P_MIC_DIC_TURRET_PRISM_MAKER => $event->getMicroscopeDicTurretPrismMaker(),
                    self::COL_P_MIC_DIC_TURRET_PRISM_NAME => $event->getMicroscopeDicTurretPrismName(),
                    self::COL_P_MIC_MAGNIFICATION_CHANGER_ASN => $event->getMicroscopeMagnificationChangerArticleOrSerialNumber(),
                    self::COL_P_MIC_MAGNIFICATION_CHANGER_MAKER => $event->getMicroscopeMagnificationChangerMaker(),
                    self::COL_P_MIC_MAGNIFICATION_CHANGER_NAME => $event->getMicroscopeMagnificationChangerName(),
                    self::COL_P_MIC_NOSEPIECE_OBJECTIVE_ASN => $event->getMicroscopeNosepieceObjectiveArticleOrSerialNumber(),
                    self::COL_P_MIC_NOSEPIECE_OBJECTIVE_MAGNIFICATION => $event->getMicroscopeNosepieceObjectiveMagnification(),
                    self::COL_P_MIC_NOSEPIECE_OBJECTIVE_MAKER => $event->getMicroscopeNosepieceObjectiveMaker(),
                    self::COL_P_MIC_NOSEPIECE_OBJECTIVE_NAME => $event->getMicroscopeNosepieceObjectiveName(),
                    self::COL_P_MIC_NOSEPIECE_OBJECTIVE_NUMERICAL_APERTURE => $event->getMicroscopeNosepieceObjectiveNumericalAperture(),
                    self::COL_P_MIC_NOSEPIECE_OBJECTIVE_TYPE => $event->getMicroscopeNosepieceObjectiveType(),
                    self::COL_P_MIC_PORTS_ASN => $event->getMicroscopePortsArticleOrSerialNumber(),
                    self::COL_P_MIC_PORTS_MAKER => $event->getMicroscopePortsMaker(),
                    self::COL_P_MIC_PORTS_NAME => $event->getMicroscopePortsName(),
                    self::COL_P_MIC_STAND_ASN => $event->getMicroscopeStandArticleOrSerialNumber(),
                    self::COL_P_MIC_STAND_MAKER => $event->getMicroscopeStandMaker(),
                    self::COL_P_MIC_STAND_NAME => $event->getMicroscopeStandName(),
                    self::COL_P_SET_APERTURE_DIAPHRAGM_OPENING => $event->getMicroscopeSettingsApertureDiaphragmOpening(),
                    self::COL_P_SET_CONTRAST_METHOD => $event->getMicroscopeSettingsContrastMethod(),
                    self::COL_P_SET_DIC_PRISM_POSITION => $event->getMicroscopeSettingsDicPrismPosition(),
                    self::COL_P_SET_FIELD_DIAPHRAGM_OPENING => $event->getMicroscopeSettingsFieldDiaphragmOpening(),
                    self::COL_P_SET_MAGCHANGER_MAGNIFICATION => $event->getMicroscopeSettingsMagnificationChangerMagnification(),
                    self::COL_P_PRESENTATION_URI => $event->getPresentationUri(),
                    self::COL_P_ORGANISM_ID => $subselect,
                    self::COL_P_TITLE => $event->getTitle()
                ]);
            } elseif ($event instanceof AuthorshipOfPhotomicrographProvided) {
                $this->queueUpdate(
                    self::TBL_PHOTOMICROGRAPH,
                    [
                        self::COL_P_CREATOR_CAPTURING => $event->getCreatorCapturingDigitalMaster(),
                        self::COL_P_CREATOR_PROCESSING => $event->getCreatorProcessingDerivatives()
                    ],
                    [self::COL_P_ID => strval($event->getAggregateId())]
                );
            } elseif ($event instanceof DicPrismPositionChanged) {
                $this->queueUpdate(
                    self::TBL_PHOTOMICROGRAPH,
                    [self::COL_P_SET_DIC_PRISM_POSITION => $event->getDicPrismPosition()],
                    [self::COL_P_ID => strval($event->getAggregateId())]
                );
            } elseif ($event instanceof PhotomicrographDeleted) {
                $this->queueDelete(self::TBL_PHOTOMICROGRAPH, [self::COL_P_ID => strval($event->getAggregateId())]);
            } elseif ($event instanceof PhotomicrographRenamed) {
                $this->queueUpdate(
                    self::TBL_PHOTOMICROGRAPH,
                    [self::COL_P_TITLE => $event->getTitle()],
                    [self::COL_P_ID => strval($event->getAggregateId())]
                );
            } elseif ($event instanceof SpecimenCarrierRecorded) {
                $this->queueInsert(self::TBL_SPECIMEN_CARRIER, [
                    self::COL_SC_ID => strval($event->getAggregateId()),
                    self::COL_SC_CARRIER_NUMBER => $event->getCarrierNumber(),
                    self::COL_SC_GATHERING_ID => strval($event->getGatheringId()),
                    self::COL_SC_LABEL_TRANSCRIPT => $event->getLabelTranscript(),
                    self::COL_SC_OWNER => $event->getOwner(),
                    self::COL_SC_PREPARATION_TYPE => $event->getPreparationType(),
                    self::COL_SC_PREVIOUS_COLLECTION => $event->getPreviousCollection()
                ]);
            } elseif ($event instanceof SpecimenCarrierManipulated) {
                $this->queueUpdate(
                    self::TBL_SPECIMEN_CARRIER,
                    [
                        self::COL_SC_CARRIER_NUMBER => $event->getCarrierNumber(),
                        self::COL_SC_LABEL_TRANSCRIPT => $event->getLabelTranscript(),
                        self::COL_SC_OWNER => $event->getOwner(),
                        self::COL_SC_PREPARATION_TYPE => $event->getPreparationType(),
                        self::COL_SC_PREVIOUS_COLLECTION => $event->getPreviousCollection()
                    ],
                    [self::COL_SC_ID => strval($event->getAggregateId())]
                );
            } elseif ($event instanceof SpecimenCarrierScannedToImage) {
                $this->queueInsert(self::TBL_CARRIER_SCAN, [
                    self::COL_CS_ID => strval(Uuid::createRandom()),
                    self::COL_CS_SPECIMEN_CARRIER_ID => strval($event->getAggregateId()),
                    self::COL_CS_CREATION_TIME => $event->getCreationTime(),
                    self::COL_CS_MODIFICATION_TIME => $event->getModificationTime(),
                    self::COL_CS_REAL_PATH => $event->getRealPath(),
                    self::COL_CS_URI => $event->getUri()
                ]);
            } elseif ($event instanceof SpecimenFromGatheringPreserved) {
                // // ignore this event, since it is just the reverse of SpecimenCarrierRecorded
            } elseif ($event instanceof HigherTaxaForOrganismProvided) {
                $this->queueUpdate(
                    self::TBL_ORGANISM,
                    [self::COL_O_HIGHER_TAXA => $event->getHigherTaxa()],
                    [
                        self::COL_O_SPECIMEN_CARRIER_ID => strval($event->getAggregateId()),
                        self::COL_O_SEQUENCE_NUMBER => $event->getSequenceNumber()
                    ]
                );
            } elseif ($event instanceof MentionedNameOfOrganismChanged) {
                $this->queueInsert(self::TBL_SCIENTIFIC_NAME, [
                    self::COL_SN_ID => strval(Uuid::createRandom()),
                    self::COL_SN_SPECIMEN_CARRIER_ID => strval($event->getAggregateId()),
                    self::COL_SN_SEQUENCE_NUMBER => $event->getSequenceNumber(),
                    self::COL_SN_AUTHORSHIP => $event->getAuthorship(),
                    self::COL_SN_GENUS => $event->getGenus(),
                    self::COL_SN_INFRASPECIFIC_EPITHET => $event->getInfraSpecificEpithet(),
                    self::COL_SN_SPECIFIC_EPITHET => $event->getSpecificEpithet(),
                    self::COL_SN_SUBGENUS => $event->getSubgenus(),
                    self::COL_SN_YEAR => $event->getYear(),
                    self::COL_SN_IS_PARENTHESIZED => $event->isParenthesized(),
                    self::COL_SN_IS_MENTIONED => true,
                    self::COL_SN_IS_VALID => false
                ]);
                $this->queueUpdate(
                    self::TBL_SCIENTIFIC_NAME,
                    [
                        self::COL_SN_AUTHORSHIP => $event->getAuthorship(),
                        self::COL_SN_GENUS => $event->getGenus(),
                        self::COL_SN_INFRASPECIFIC_EPITHET => $event->getInfraSpecificEpithet(),
                        self::COL_SN_SPECIFIC_EPITHET => $event->getSpecificEpithet(),
                        self::COL_SN_SUBGENUS => $event->getSubgenus(),
                        self::COL_SN_YEAR => $event->getYear(),
                        self::COL_SN_IS_PARENTHESIZED => $event->isParenthesized(),
                        self::COL_SN_IS_MENTIONED => true
                    ], [
                        self::COL_SN_SPECIMEN_CARRIER_ID => strval($event->getAggregateId()),
                        self::COL_SN_SEQUENCE_NUMBER => $event->getSequenceNumber(),
                        self::COL_SN_GENUS => $event->getGenus(),
                        self::COL_SN_SPECIFIC_EPITHET => $event->getSpecificEpithet(),
                        self::COL_SN_AUTHORSHIP => $event->getAuthorship(),
                        self::COL_SN_YEAR => $event->getYear()
                    ]
                );
            } elseif ($event instanceof NameOriginOfOrganismChanged) {
                $this->queueUpdate(
                    self::TBL_ORGANISM,
                    [
                        self::COL_O_IDENTIFICATION_IDENTIFIER => $event->getIdentifier(),
                        self::COL_O_IDENTIFICATION_QUALIFIER => $event->getQualifier(),
                        self::COL_O_TYPE_DESIGNATION_TYPE_STATUS => $event->getTypeStatus()
                    ],
                    [
                        self::COL_O_SPECIMEN_CARRIER_ID => strval($event->getAggregateId()),
                        self::COL_O_SEQUENCE_NUMBER => $event->getSequenceNumber()
                    ]
                );
            } elseif ($event instanceof SynonymForOrganismProvided) {
                $this->queueInsert(self::TBL_SCIENTIFIC_NAME, [
                    self::COL_SN_ID => strval(Uuid::createRandom()),
                    self::COL_SN_SPECIMEN_CARRIER_ID => strval($event->getAggregateId()),
                    self::COL_SN_SEQUENCE_NUMBER => $event->getSequenceNumber(),
                    self::COL_SN_AUTHORSHIP => $event->getAuthorship(),
                    self::COL_SN_GENUS => $event->getGenus(),
                    self::COL_SN_INFRASPECIFIC_EPITHET => $event->getInfraSpecificEpithet(),
                    self::COL_SN_SPECIFIC_EPITHET => $event->getSpecificEpithet(),
                    self::COL_SN_SUBGENUS => $event->getSubgenus(),
                    self::COL_SN_YEAR => $event->getYear(),
                    self::COL_SN_IS_PARENTHESIZED => $event->isParenthesized(),
                    self::COL_SN_IS_MENTIONED => false,
                    self::COL_SN_IS_VALID => false
                ]);
                $this->queueUpdate(
                    self::TBL_SCIENTIFIC_NAME,
                    [
                        self::COL_SN_AUTHORSHIP => $event->getAuthorship(),
                        self::COL_SN_GENUS => $event->getGenus(),
                        self::COL_SN_INFRASPECIFIC_EPITHET => $event->getInfraSpecificEpithet(),
                        self::COL_SN_SPECIFIC_EPITHET => $event->getSpecificEpithet(),
                        self::COL_SN_SUBGENUS => $event->getSubgenus(),
                        self::COL_SN_YEAR => $event->getYear(),
                        self::COL_SN_IS_PARENTHESIZED => $event->isParenthesized()
                    ], [
                        self::COL_SN_SPECIMEN_CARRIER_ID => strval($event->getAggregateId()),
                        self::COL_SN_SEQUENCE_NUMBER => $event->getSequenceNumber(),
                        self::COL_SN_GENUS => $event->getGenus(),
                        self::COL_SN_SPECIFIC_EPITHET => $event->getSpecificEpithet(),
                        self::COL_SN_AUTHORSHIP => $event->getAuthorship(),
                        self::COL_SN_YEAR => $event->getYear()
                    ]
                );
            } elseif ($event instanceof SynonymsOfOrganismCleared) {
                $this->queueDelete(self::TBL_SCIENTIFIC_NAME, [
                    self::COL_SN_SPECIMEN_CARRIER_ID => strval($event->getAggregateId()),
                    self::COL_SN_SEQUENCE_NUMBER => $event->getSequenceNumber()
                ]);
            } elseif ($event instanceof ValidNameOfOrganismChanged) {
                $this->queueInsert(self::TBL_SCIENTIFIC_NAME, [
                    self::COL_SN_ID => strval(Uuid::createRandom()),
                    self::COL_SN_SPECIMEN_CARRIER_ID => strval($event->getAggregateId()),
                    self::COL_SN_SEQUENCE_NUMBER => $event->getSequenceNumber(),
                    self::COL_SN_AUTHORSHIP => $event->getAuthorship(),
                    self::COL_SN_GENUS => $event->getGenus(),
                    self::COL_SN_INFRASPECIFIC_EPITHET => $event->getInfraSpecificEpithet(),
                    self::COL_SN_SPECIFIC_EPITHET => $event->getSpecificEpithet(),
                    self::COL_SN_SUBGENUS => $event->getSubgenus(),
                    self::COL_SN_YEAR => $event->getYear(),
                    self::COL_SN_IS_PARENTHESIZED => $event->isParenthesized(),
                    self::COL_SN_IS_MENTIONED => false,
                    self::COL_SN_IS_VALID => true
                ]);
                $this->queueUpdate(
                    self::TBL_SCIENTIFIC_NAME,
                    [
                        self::COL_SN_AUTHORSHIP => $event->getAuthorship(),
                        self::COL_SN_GENUS => $event->getGenus(),
                        self::COL_SN_INFRASPECIFIC_EPITHET => $event->getInfraSpecificEpithet(),
                        self::COL_SN_SPECIFIC_EPITHET => $event->getSpecificEpithet(),
                        self::COL_SN_SUBGENUS => $event->getSubgenus(),
                        self::COL_SN_YEAR => $event->getYear(),
                        self::COL_SN_IS_PARENTHESIZED => $event->isParenthesized(),
                        self::COL_SN_IS_VALID => true
                    ], [
                        self::COL_SN_SPECIMEN_CARRIER_ID => strval($event->getAggregateId()),
                        self::COL_SN_SEQUENCE_NUMBER => $event->getSequenceNumber(),
                        self::COL_SN_GENUS => $event->getGenus(),
                        self::COL_SN_SPECIFIC_EPITHET => $event->getSpecificEpithet(),
                        self::COL_SN_AUTHORSHIP => $event->getAuthorship(),
                        self::COL_SN_YEAR => $event->getYear()
                    ]
                );
            }
            //region obsolete taxonomy events (keep them anyway for rebuilds!)
            elseif ($event instanceof HigherTaxaProvided) {
                $this->higherTaxa[strval($event->getAggregateId())]['synonyms'] = [];
                $this->higherTaxa[strval($event->getAggregateId())]['names'] = trim(
                    preg_replace('<\s+>', ' ',
                        implode(' ', [
                            $event->getRegnum(),
                            $event->getSubregnum(),
                            $event->getSuperphylum(),
                            $event->getPhylum(),
                            $event->getSubphylum(),
                            $event->getSuperclassis(),
                            $event->getClassis(),
                            $event->getSubclassis(),
                            $event->getSuperordo(),
                            $event->getOrdo(),
                            $event->getSubordo(),
                            $event->getSuperfamilia(),
                            $event->getFamilia(),
                            $event->getSubfamilia(),
                            $event->getTribus()
                        ])
                    )
                );
            } elseif ($event instanceof SynonymAssignedToTaxon) {
                $genusOrMonomial = $event->getSynonym()->getGenusOrMonomial();
                $specificEpithet = $event->getSynonym()->getSpecificEpithet();
                $this->synonyms[$genusOrMonomial][$specificEpithet] = strval($event->getAggregateId());
                $this->higherTaxa[strval($event->getAggregateId())]['synonyms'][] = [$event->getSynonym(), false];
            } elseif ($event instanceof TaxonRegistered) {
            } elseif ($event instanceof ValidNameInitiallyAssignedToTaxon) {
                $genusOrMonomial = $event->getValidName()->getGenusOrMonomial();
                $specificEpithet = $event->getValidName()->getSpecificEpithet();
                $this->synonyms[$genusOrMonomial][$specificEpithet] = strval($event->getAggregateId());
                $this->higherTaxa[strval($event->getAggregateId())]['synonyms'][] = [$event->getValidName(), true];
            } elseif ($event instanceof OrganismOnSpecimenCarrierDesignatedAsType) {
                $taxonId = isset($this->synonyms[$event->getGenus()][$event->getSpecificEpithet()])
                    ? $this->synonyms[$event->getGenus()][$event->getSpecificEpithet()]
                    : null;
                $higherTaxon = $taxonId ? $this->higherTaxa[$taxonId] : null;
                if ($higherTaxon) {
                    foreach ($higherTaxon['synonyms'] as $synonym) {
                        /** @var ScientificName $sciName */
                        list($sciName, $valid) = $synonym;
                        $this->queueInsert(self::TBL_SCIENTIFIC_NAME, [
                            self::COL_SN_ID => strval(Uuid::createRandom()),
                            self::COL_SN_SPECIMEN_CARRIER_ID => strval($event->getAggregateId()),
                            self::COL_SN_SEQUENCE_NUMBER => $event->getSequenceNumber(),
                            self::COL_SN_AUTHORSHIP => $sciName->getAuthorship(),
                            self::COL_SN_GENUS => $sciName->getGenusOrMonomial(),
                            self::COL_SN_INFRASPECIFIC_EPITHET => $sciName->getInfraSpecificEpithet(),
                            self::COL_SN_SPECIFIC_EPITHET => $sciName->getSpecificEpithet(),
                            self::COL_SN_SUBGENUS => $sciName->getSubgenus(),
                            self::COL_SN_YEAR => $sciName->getYear(),
                            self::COL_SN_IS_PARENTHESIZED => $sciName->isParenthesized(),
                            self::COL_SN_IS_MENTIONED => false,
                            self::COL_SN_IS_VALID => $valid
                        ]);
                        $this->queueUpdate(
                            self::TBL_SCIENTIFIC_NAME,
                            [
                                self::COL_SN_AUTHORSHIP => $sciName->getAuthorship(),
                                self::COL_SN_GENUS => $sciName->getGenusOrMonomial(),
                                self::COL_SN_INFRASPECIFIC_EPITHET => $sciName->getInfraSpecificEpithet(),
                                self::COL_SN_SPECIFIC_EPITHET => $sciName->getSpecificEpithet(),
                                self::COL_SN_SUBGENUS => $sciName->getSubgenus(),
                                self::COL_SN_YEAR => $sciName->getYear(),
                                self::COL_SN_IS_PARENTHESIZED => $sciName->isParenthesized(),
                                self::COL_SN_IS_MENTIONED => false,
                                self::COL_SN_IS_VALID => $valid
                            ], [
                                self::COL_SN_SPECIMEN_CARRIER_ID => strval($event->getAggregateId()),
                                self::COL_SN_SEQUENCE_NUMBER => $event->getSequenceNumber(),
                                self::COL_SN_GENUS => $sciName->getGenusOrMonomial(),
                                self::COL_SN_SPECIFIC_EPITHET => $sciName->getSpecificEpithet(),
                                self::COL_SN_AUTHORSHIP => $event->getAuthorship(),
                                self::COL_SN_YEAR => $event->getYear()
                            ]
                        );
                    }
                }
                $this->queueUpdate(
                    self::TBL_SCIENTIFIC_NAME,
                    [self::COL_SN_IS_MENTIONED => true], [
                        self::COL_SN_SPECIMEN_CARRIER_ID => strval($event->getAggregateId()),
                        self::COL_SN_SEQUENCE_NUMBER => $event->getSequenceNumber(),
                        self::COL_SN_GENUS => $event->getGenus(),
                        self::COL_SN_SPECIFIC_EPITHET => $event->getSpecificEpithet(),
                        self::COL_SN_AUTHORSHIP => $event->getAuthorship(),
                        self::COL_SN_YEAR => $event->getYear()
                    ]
                );
                $this->queueInsert(self::TBL_ORGANISM, [
                    self::COL_O_ID => strval(Uuid::createRandom()),
                    self::COL_O_HIGHER_TAXA => $higherTaxon['names'],
                    self::COL_O_SPECIMEN_CARRIER_ID => strval($event->getAggregateId()),
                    self::COL_O_SEQUENCE_NUMBER => $event->getSequenceNumber(),
                    self::COL_O_TYPE_DESIGNATION_TYPE_STATUS => $event->getTypeStatus()
                ]);
                $this->queueUpdate(
                    self::TBL_ORGANISM,
                    [
                        self::COL_O_HIGHER_TAXA => $higherTaxon['names'],
                        self::COL_O_TYPE_DESIGNATION_TYPE_STATUS => $event->getTypeStatus(),
                        self::COL_O_IDENTIFICATION_IDENTIFIER => null,
                        self::COL_O_IDENTIFICATION_QUALIFIER => null
                    ], [
                        self::COL_O_SPECIMEN_CARRIER_ID => strval($event->getAggregateId()),
                        self::COL_O_SEQUENCE_NUMBER => $event->getSequenceNumber()
                    ]
                );
            } elseif ($event instanceof OrganismOnSpecimenCarrierIdentified) {
                $taxonId = isset($this->synonyms[$event->getGenus()][$event->getSpecificEpithet()])
                    ? $this->synonyms[$event->getGenus()][$event->getSpecificEpithet()]
                    : null;
                $higherTaxon = $taxonId ? $this->higherTaxa[$taxonId] : null;
                if ($higherTaxon) {
                    foreach ($higherTaxon['synonyms'] as $synonym) {
                        /** @var ScientificName $sciName */
                        list($sciName, $valid) = $synonym;
                        $this->queueInsert(self::TBL_SCIENTIFIC_NAME, [
                            self::COL_SN_ID => strval(Uuid::createRandom()),
                            self::COL_SN_SPECIMEN_CARRIER_ID => strval($event->getAggregateId()),
                            self::COL_SN_SEQUENCE_NUMBER => $event->getSequenceNumber(),
                            self::COL_SN_AUTHORSHIP => $sciName->getAuthorship(),
                            self::COL_SN_GENUS => $sciName->getGenusOrMonomial(),
                            self::COL_SN_INFRASPECIFIC_EPITHET => $sciName->getInfraSpecificEpithet(),
                            self::COL_SN_SPECIFIC_EPITHET => $sciName->getSpecificEpithet(),
                            self::COL_SN_SUBGENUS => $sciName->getSubgenus(),
                            self::COL_SN_YEAR => $sciName->getYear(),
                            self::COL_SN_IS_PARENTHESIZED => $sciName->isParenthesized(),
                            self::COL_SN_IS_MENTIONED => false,
                            self::COL_SN_IS_VALID => $valid
                        ]);
                        $this->queueUpdate(
                            self::TBL_SCIENTIFIC_NAME,
                            [
                                self::COL_SN_AUTHORSHIP => $sciName->getAuthorship(),
                                self::COL_SN_GENUS => $sciName->getGenusOrMonomial(),
                                self::COL_SN_INFRASPECIFIC_EPITHET => $sciName->getInfraSpecificEpithet(),
                                self::COL_SN_SPECIFIC_EPITHET => $sciName->getSpecificEpithet(),
                                self::COL_SN_SUBGENUS => $sciName->getSubgenus(),
                                self::COL_SN_YEAR => $sciName->getYear(),
                                self::COL_SN_IS_PARENTHESIZED => $sciName->isParenthesized(),
                                self::COL_SN_IS_MENTIONED => false,
                                self::COL_SN_IS_VALID => $valid
                            ], [
                                self::COL_SN_SPECIMEN_CARRIER_ID => strval($event->getAggregateId()),
                                self::COL_SN_SEQUENCE_NUMBER => $event->getSequenceNumber(),
                                self::COL_SN_GENUS => $sciName->getGenusOrMonomial(),
                                self::COL_SN_SPECIFIC_EPITHET => $sciName->getSpecificEpithet(),
                                self::COL_SN_AUTHORSHIP => $event->getAuthorship(),
                                self::COL_SN_YEAR => $event->getYear()
                            ]
                        );
                    }
                }
                $this->queueUpdate(
                    self::TBL_SCIENTIFIC_NAME,
                    [self::COL_SN_IS_MENTIONED => true], [
                        self::COL_SN_SPECIMEN_CARRIER_ID => strval($event->getAggregateId()),
                        self::COL_SN_SEQUENCE_NUMBER => $event->getSequenceNumber(),
                        self::COL_SN_GENUS => $event->getGenus(),
                        self::COL_SN_SPECIFIC_EPITHET => $event->getSpecificEpithet(),
                        self::COL_SN_AUTHORSHIP => $event->getAuthorship(),
                        self::COL_SN_YEAR => $event->getYear()
                    ]
                );
                $this->queueInsert(self::TBL_ORGANISM, [
                    self::COL_O_ID => strval(Uuid::createRandom()),
                    self::COL_O_HIGHER_TAXA => $higherTaxon['names'],
                    self::COL_O_SPECIMEN_CARRIER_ID => strval($event->getAggregateId()),
                    self::COL_O_SEQUENCE_NUMBER => $event->getSequenceNumber(),
                    self::COL_O_IDENTIFICATION_IDENTIFIER => $event->getIdentifier(),
                    self::COL_O_IDENTIFICATION_QUALIFIER => $event->getQualifier()
                ]);
                $this->queueUpdate(
                    self::TBL_ORGANISM,
                    [
                        self::COL_O_HIGHER_TAXA => $higherTaxon['names'],
                        self::COL_O_IDENTIFICATION_IDENTIFIER => $event->getIdentifier(),
                        self::COL_O_IDENTIFICATION_QUALIFIER => $event->getQualifier(),
                        self::COL_O_TYPE_DESIGNATION_TYPE_STATUS => null
                    ], [
                        self::COL_O_SPECIMEN_CARRIER_ID => strval($event->getAggregateId()),
                        self::COL_O_SEQUENCE_NUMBER => $event->getSequenceNumber()
                    ]
                );
            } elseif ($event instanceof EntireTaxonomyModelRedesigned) {
                // do nothing
            } //endregion
            else {
                throw new \InvalidArgumentException('unsupported event ' . get_class($event));
            }
        }
    }
