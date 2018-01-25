<?php
    namespace sednasoft\virmisco\domain\entity;

    use Predis\Client as PredisClient;
    use sednasoft\virmisco\domain\event\AuthorshipOfPhotomicrographProvided;
    use sednasoft\virmisco\domain\event\DicPrismPositionChanged;
    use sednasoft\virmisco\domain\event\FocalPlaneImageAppended;
    use sednasoft\virmisco\domain\event\PhotomicrographDeleted;
    use sednasoft\virmisco\domain\event\PhotomicrographDigitized;
    use sednasoft\virmisco\domain\event\PhotomicrographDigitizedV2;
    use sednasoft\virmisco\domain\event\PhotomicrographManipulated;
    use sednasoft\virmisco\domain\event\PhotomicrographManipulatedV2;
    use sednasoft\virmisco\domain\event\PhotomicrographRenamed;
    use sednasoft\virmisco\singiere\AbstractAggregateRoot;
    use sednasoft\virmisco\singiere\Uuid;

    class Photomicrograph extends AbstractAggregateRoot
    {
        private $mappedSshPixDir = __DIR__ . '/../../../../priv/sshfs-185.15.246.7/pix';
        private $queueName = 'conversion-queue';
        private $redisUri = REDIS_URI;
        /** @var string e. g. /data/pix/derivatives/Foobarus_bazus/Foobarus_bazus_HT_f_leg_II_lft/focal-series.zip */
        private $zipFilePath;

        /**
         * @param float $focusPosition
         * @param string $fileRealPath
         * @param string $fileUri
         * @param string $fileCreationTime
         * @param string $fileModificationTime
         * @param string $presentationUri
         * @param float $exposureSettingsDuration
         * @param float $exposureSettingsGain
         * @param float $histogramSettingsGamma
         * @param int $histogramSettingsBlackClip
         * @param int $histogramSettingsWhiteClip
         * @param bool $postProcessingSettingsShading
         * @param bool $postProcessingSettingsSharpening
         */
        public function appendFocalPlaneImage(
            $focusPosition,
            $fileRealPath,
            $fileUri,
            $fileCreationTime,
            $fileModificationTime,
            $presentationUri,
            $exposureSettingsDuration,
            $exposureSettingsGain,
            $histogramSettingsGamma,
            $histogramSettingsBlackClip,
            $histogramSettingsWhiteClip,
            $postProcessingSettingsShading,
            $postProcessingSettingsSharpening
        ) {
            $this->apply(
                new FocalPlaneImageAppended(
                    $focusPosition,
                    $fileRealPath,
                    $fileUri,
                    $fileCreationTime,
                    $fileModificationTime,
                    $presentationUri,
                    $exposureSettingsDuration,
                    $exposureSettingsGain,
                    $histogramSettingsGamma,
                    $histogramSettingsBlackClip,
                    $histogramSettingsWhiteClip,
                    $postProcessingSettingsShading,
                    $postProcessingSettingsSharpening
                )
            );
        }

        /**
         * @param float $dicPrismPosition
         */
        public function changeDicPrismPosition($dicPrismPosition)
        {
            $this->apply(new DicPrismPositionChanged($dicPrismPosition));
        }

        public function convertImages()
        {
            $origImageDir = str_replace(
                ['/data/pix/derivatives/', '/focal-series.zip'],
                [$this->mappedSshPixDir . '/photomicrographs/', ''],
                $this->zipFilePath
            );
            $targetDir = str_replace(
                ['/data/pix/', '/focal-series.zip'],
                [$this->mappedSshPixDir . '/', ''],
                $this->zipFilePath
            );
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0777, true);
            }
            $redis = new PredisClient($this->redisUri);
            $redis->rpush($this->queueName, [sprintf('%s:%s', realpath($origImageDir), realpath($targetDir))]);
        }

        /**
         */
        public function deletePhotomicrograph()
        {
            $this->apply(new PhotomicrographDeleted());
        }

        /**
         * @param Uuid $specimenCarrierId
         * @param string $sequenceNumber
         * @param string $title
         * @param string $detailOfPhotomicrographId
         * @param int $detailOfHotspotX
         * @param int $detailOfHotspotY
         * @param string $fileRealPath
         * @param string $fileUri
         * @param string $fileCreationTime
         * @param string $fileModificationTime
         * @param string $presentationUri
         * @param int $digitizationDataWidth
         * @param int $digitizationDataHeight
         * @param int $digitizationDataColorDepth
         * @param float $digitizationDataReproductionScaleHorizontal
         * @param float $digitizationDataReproductionScaleVertical
         * @param string $cameraCameraMaker
         * @param string $cameraCameraName
         * @param string $cameraCameraArticleOrSerialNumber
         * @param string $cameraSensorMaker
         * @param string $cameraSensorName
         * @param string $cameraSensorArticleOrSerialNumber
         * @param string $cameraOpticalFormat
         * @param string $cameraCaptureFormat
         * @param float $cameraChipWidth
         * @param float $cameraChipHeight
         * @param float $cameraPixelWidth
         * @param float $cameraPixelHeight
         * @param int $cameraActivePixelsHor
         * @param int $cameraActivePixelsVer
         * @param string $cameraColorFilterArray
         * @param string $cameraProtectiveColorFilter
         * @param string $cameraAdcResolution
         * @param float $cameraDynamicRange
         * @param float $cameraSnrMax
         * @param float $cameraReadoutNoise
         * @param string $microscopeStandMaker
         * @param string $microscopeStandName
         * @param string $microscopeStandArticleOrSerialNumber
         * @param string $microscopeCondenserMaker
         * @param string $microscopeCondenserName
         * @param string $microscopeCondenserArticleOrSerialNumber
         * @param string $microscopeCondenserTurretPrismMaker
         * @param string $microscopeCondenserTurretPrismName
         * @param string $microscopeCondenserTurretPrismArticleOrSerialNumber
         * @param string $microscopeNosepieceObjectiveMaker
         * @param string $microscopeNosepieceObjectiveName
         * @param string $microscopeNosepieceObjectiveArticleOrSerialNumber
         * @param string $microscopeNosepieceObjectiveType
         * @param float $microscopeNosepieceObjectiveNumericalAperture
         * @param float $microscopeNosepieceObjectiveMagnification
         * @param string $microscopeDicTurretPrismMaker
         * @param string $microscopeDicTurretPrismName
         * @param string $microscopeDicTurretPrismArticleOrSerialNumber
         * @param string $microscopeMagnificationChangerMaker
         * @param string $microscopeMagnificationChangerName
         * @param string $microscopeMagnificationChangerArticleOrSerialNumber
         * @param int $microscopeNumberOfPorts
         * @param string $microscopePortsMaker
         * @param string $microscopePortsName
         * @param string $microscopePortsArticleOrSerialNumber
         * @param string $microscopeCameraMountAdapterMaker
         * @param string $microscopeCameraMountAdapterName
         * @param string $microscopeCameraMountAdapterMagnification
         * @param string $microscopeCameraMountAdapterArticleOrSerialNumber
         * @param string $microscopeSettingsContrastMethod
         * @param float $microscopeSettingsDicPrismPosition
         * @param float $microscopeSettingsApertureDiaphragmOpening
         * @param float $microscopeSettingsFieldDiaphragmOpening
         * @param bool $microscopeSettingsIsPolarizerInLightPath
         * @param float $microscopeSettingsMagnificationChangerMagnification
         */
        public function digitizeFromOrganism(
            Uuid $specimenCarrierId,
            $sequenceNumber,
            $title,
            $detailOfPhotomicrographId,
            $detailOfHotspotX,
            $detailOfHotspotY,
            $fileRealPath,
            $fileUri,
            $fileCreationTime,
            $fileModificationTime,
            $presentationUri,
            $digitizationDataWidth,
            $digitizationDataHeight,
            $digitizationDataColorDepth,
            $digitizationDataReproductionScaleHorizontal,
            $digitizationDataReproductionScaleVertical,
            $cameraCameraMaker,
            $cameraCameraName,
            $cameraCameraArticleOrSerialNumber,
            $cameraSensorMaker,
            $cameraSensorName,
            $cameraSensorArticleOrSerialNumber,
            $cameraOpticalFormat,
            $cameraCaptureFormat,
            $cameraChipWidth,
            $cameraChipHeight,
            $cameraPixelWidth,
            $cameraPixelHeight,
            $cameraActivePixelsHor,
            $cameraActivePixelsVer,
            $cameraColorFilterArray,
            $cameraProtectiveColorFilter,
            $cameraAdcResolution,
            $cameraDynamicRange,
            $cameraSnrMax,
            $cameraReadoutNoise,
            $microscopeStandMaker,
            $microscopeStandName,
            $microscopeStandArticleOrSerialNumber,
            $microscopeCondenserMaker,
            $microscopeCondenserName,
            $microscopeCondenserArticleOrSerialNumber,
            $microscopeCondenserTurretPrismMaker,
            $microscopeCondenserTurretPrismName,
            $microscopeCondenserTurretPrismArticleOrSerialNumber,
            $microscopeNosepieceObjectiveMaker,
            $microscopeNosepieceObjectiveName,
            $microscopeNosepieceObjectiveArticleOrSerialNumber,
            $microscopeNosepieceObjectiveType,
            $microscopeNosepieceObjectiveNumericalAperture,
            $microscopeNosepieceObjectiveMagnification,
            $microscopeDicTurretPrismMaker,
            $microscopeDicTurretPrismName,
            $microscopeDicTurretPrismArticleOrSerialNumber,
            $microscopeMagnificationChangerMaker,
            $microscopeMagnificationChangerName,
            $microscopeMagnificationChangerArticleOrSerialNumber,
            $microscopeNumberOfPorts,
            $microscopePortsMaker,
            $microscopePortsName,
            $microscopePortsArticleOrSerialNumber,
            $microscopeCameraMountAdapterMaker,
            $microscopeCameraMountAdapterName,
            $microscopeCameraMountAdapterMagnification,
            $microscopeCameraMountAdapterArticleOrSerialNumber,
            $microscopeSettingsContrastMethod,
            $microscopeSettingsDicPrismPosition,
            $microscopeSettingsApertureDiaphragmOpening,
            $microscopeSettingsFieldDiaphragmOpening,
            $microscopeSettingsIsPolarizerInLightPath,
            $microscopeSettingsMagnificationChangerMagnification
        ) {
            $this->apply(
                new PhotomicrographDigitizedV2(
                    $specimenCarrierId,
                    $sequenceNumber,
                    $title,
                    $detailOfPhotomicrographId,
                    $detailOfHotspotX,
                    $detailOfHotspotY,
                    $fileRealPath,
                    $fileUri,
                    $fileCreationTime,
                    $fileModificationTime,
                    $presentationUri,
                    $digitizationDataWidth,
                    $digitizationDataHeight,
                    $digitizationDataColorDepth,
                    $digitizationDataReproductionScaleHorizontal,
                    $digitizationDataReproductionScaleVertical,
                    $cameraCameraMaker,
                    $cameraCameraName,
                    $cameraCameraArticleOrSerialNumber,
                    $cameraSensorMaker,
                    $cameraSensorName,
                    $cameraSensorArticleOrSerialNumber,
                    $cameraOpticalFormat,
                    $cameraCaptureFormat,
                    $cameraChipWidth,
                    $cameraChipHeight,
                    $cameraPixelWidth,
                    $cameraPixelHeight,
                    $cameraActivePixelsHor,
                    $cameraActivePixelsVer,
                    $cameraColorFilterArray,
                    $cameraProtectiveColorFilter,
                    $cameraAdcResolution,
                    $cameraDynamicRange,
                    $cameraSnrMax,
                    $cameraReadoutNoise,
                    $microscopeStandMaker,
                    $microscopeStandName,
                    $microscopeStandArticleOrSerialNumber,
                    $microscopeCondenserMaker,
                    $microscopeCondenserName,
                    $microscopeCondenserArticleOrSerialNumber,
                    $microscopeCondenserTurretPrismMaker,
                    $microscopeCondenserTurretPrismName,
                    $microscopeCondenserTurretPrismArticleOrSerialNumber,
                    $microscopeNosepieceObjectiveMaker,
                    $microscopeNosepieceObjectiveName,
                    $microscopeNosepieceObjectiveArticleOrSerialNumber,
                    $microscopeNosepieceObjectiveType,
                    $microscopeNosepieceObjectiveNumericalAperture,
                    $microscopeNosepieceObjectiveMagnification,
                    $microscopeDicTurretPrismMaker,
                    $microscopeDicTurretPrismName,
                    $microscopeDicTurretPrismArticleOrSerialNumber,
                    $microscopeMagnificationChangerMaker,
                    $microscopeMagnificationChangerName,
                    $microscopeMagnificationChangerArticleOrSerialNumber,
                    $microscopeNumberOfPorts,
                    $microscopePortsMaker,
                    $microscopePortsName,
                    $microscopePortsArticleOrSerialNumber,
                    $microscopeCameraMountAdapterMaker,
                    $microscopeCameraMountAdapterName,
                    $microscopeCameraMountAdapterMagnification,
                    $microscopeCameraMountAdapterArticleOrSerialNumber,
                    $microscopeSettingsContrastMethod,
                    $microscopeSettingsDicPrismPosition,
                    $microscopeSettingsApertureDiaphragmOpening,
                    $microscopeSettingsFieldDiaphragmOpening,
                    $microscopeSettingsIsPolarizerInLightPath,
                    $microscopeSettingsMagnificationChangerMagnification
                )
            );
        }

        /**
         * @param string $title
         * @param string $detailOfPhotomicrographId
         * @param int $detailOfHotspotX
         * @param int $detailOfHotspotY
         * @param string $creatorCapturing
         * @param string $creatorProcessing
         * @param string $fileRealPath
         * @param string $fileUri
         * @param string $fileCreationTime
         * @param string $fileModificationTime
         * @param string $presentationUri
         * @param int $digitizationDataWidth
         * @param int $digitizationDataHeight
         * @param int $digitizationDataColorDepth
         * @param float $digitizationDataReproductionScaleHorizontal
         * @param float $digitizationDataReproductionScaleVertical
         * @param string $cameraCameraMaker
         * @param string $cameraCameraName
         * @param string $cameraCameraArticleOrSerialNumber
         * @param string $cameraSensorMaker
         * @param string $cameraSensorName
         * @param string $cameraSensorArticleOrSerialNumber
         * @param string $cameraOpticalFormat
         * @param string $cameraCaptureFormat
         * @param float $cameraChipWidth
         * @param float $cameraChipHeight
         * @param float $cameraPixelWidth
         * @param float $cameraPixelHeight
         * @param int $cameraActivePixelsHor
         * @param int $cameraActivePixelsVer
         * @param string $cameraColorFilterArray
         * @param string $cameraProtectiveColorFilter
         * @param string $cameraAdcResolution
         * @param float $cameraDynamicRange
         * @param float $cameraSnrMax
         * @param float $cameraReadoutNoise
         * @param string $microscopeStandMaker
         * @param string $microscopeStandName
         * @param string $microscopeStandArticleOrSerialNumber
         * @param string $microscopeCondenserMaker
         * @param string $microscopeCondenserName
         * @param string $microscopeCondenserArticleOrSerialNumber
         * @param string $microscopeCondenserTurretPrismMaker
         * @param string $microscopeCondenserTurretPrismName
         * @param string $microscopeCondenserTurretPrismArticleOrSerialNumber
         * @param string $microscopeNosepieceObjectiveMaker
         * @param string $microscopeNosepieceObjectiveName
         * @param string $microscopeNosepieceObjectiveArticleOrSerialNumber
         * @param string $microscopeNosepieceObjectiveType
         * @param float $microscopeNosepieceObjectiveNumericalAperture
         * @param float $microscopeNosepieceObjectiveMagnification
         * @param string $microscopeDicTurretPrismMaker
         * @param string $microscopeDicTurretPrismName
         * @param string $microscopeDicTurretPrismArticleOrSerialNumber
         * @param string $microscopeMagnificationChangerMaker
         * @param string $microscopeMagnificationChangerName
         * @param string $microscopeMagnificationChangerArticleOrSerialNumber
         * @param string $microscopePortsMaker
         * @param string $microscopePortsName
         * @param string $microscopePortsArticleOrSerialNumber
         * @param string $microscopeCameraMountAdapterMaker
         * @param string $microscopeCameraMountAdapterName
         * @param string $microscopeCameraMountAdapterMagnification
         * @param string $microscopeCameraMountAdapterArticleOrSerialNumber
         * @param string $microscopeSettingsContrastMethod
         * @param float $microscopeSettingsDicPrismPosition
         * @param float $microscopeSettingsApertureDiaphragmOpening
         * @param float $microscopeSettingsFieldDiaphragmOpening
         * @param float $microscopeSettingsMagnificationChangerMagnification
         */
        public function manipulate(
            $title,
            $detailOfPhotomicrographId,
            $detailOfHotspotX,
            $detailOfHotspotY,
            $creatorCapturing,
            $creatorProcessing,
            $fileRealPath,
            $fileUri,
            $fileCreationTime,
            $fileModificationTime,
            $presentationUri,
            $digitizationDataWidth,
            $digitizationDataHeight,
            $digitizationDataColorDepth,
            $digitizationDataReproductionScaleHorizontal,
            $digitizationDataReproductionScaleVertical,
            $cameraCameraMaker,
            $cameraCameraName,
            $cameraCameraArticleOrSerialNumber,
            $cameraSensorMaker,
            $cameraSensorName,
            $cameraSensorArticleOrSerialNumber,
            $cameraOpticalFormat,
            $cameraCaptureFormat,
            $cameraChipWidth,
            $cameraChipHeight,
            $cameraPixelWidth,
            $cameraPixelHeight,
            $cameraActivePixelsHor,
            $cameraActivePixelsVer,
            $cameraColorFilterArray,
            $cameraProtectiveColorFilter,
            $cameraAdcResolution,
            $cameraDynamicRange,
            $cameraSnrMax,
            $cameraReadoutNoise,
            $microscopeStandMaker,
            $microscopeStandName,
            $microscopeStandArticleOrSerialNumber,
            $microscopeCondenserMaker,
            $microscopeCondenserName,
            $microscopeCondenserArticleOrSerialNumber,
            $microscopeCondenserTurretPrismMaker,
            $microscopeCondenserTurretPrismName,
            $microscopeCondenserTurretPrismArticleOrSerialNumber,
            $microscopeNosepieceObjectiveMaker,
            $microscopeNosepieceObjectiveName,
            $microscopeNosepieceObjectiveArticleOrSerialNumber,
            $microscopeNosepieceObjectiveType,
            $microscopeNosepieceObjectiveNumericalAperture,
            $microscopeNosepieceObjectiveMagnification,
            $microscopeDicTurretPrismMaker,
            $microscopeDicTurretPrismName,
            $microscopeDicTurretPrismArticleOrSerialNumber,
            $microscopeMagnificationChangerMaker,
            $microscopeMagnificationChangerName,
            $microscopeMagnificationChangerArticleOrSerialNumber,
            $microscopePortsMaker,
            $microscopePortsName,
            $microscopePortsArticleOrSerialNumber,
            $microscopeCameraMountAdapterMaker,
            $microscopeCameraMountAdapterName,
            $microscopeCameraMountAdapterMagnification,
            $microscopeCameraMountAdapterArticleOrSerialNumber,
            $microscopeSettingsContrastMethod,
            $microscopeSettingsDicPrismPosition,
            $microscopeSettingsApertureDiaphragmOpening,
            $microscopeSettingsFieldDiaphragmOpening,
            $microscopeSettingsMagnificationChangerMagnification
        ) {
            $this->apply(
                new PhotomicrographManipulatedV2(
                    $title, $detailOfPhotomicrographId, $detailOfHotspotX, $detailOfHotspotY, $creatorCapturing,
                    $creatorProcessing, $fileRealPath, $fileUri, $fileCreationTime, $fileModificationTime,
                    $presentationUri, $digitizationDataWidth, $digitizationDataHeight, $digitizationDataColorDepth,
                    $digitizationDataReproductionScaleHorizontal, $digitizationDataReproductionScaleVertical,
                    $cameraCameraMaker, $cameraCameraName, $cameraCameraArticleOrSerialNumber, $cameraSensorMaker,
                    $cameraSensorName, $cameraSensorArticleOrSerialNumber, $cameraOpticalFormat, $cameraCaptureFormat,
                    $cameraChipWidth, $cameraChipHeight, $cameraPixelWidth, $cameraPixelHeight, $cameraActivePixelsHor,
                    $cameraActivePixelsVer, $cameraColorFilterArray, $cameraProtectiveColorFilter, $cameraAdcResolution,
                    $cameraDynamicRange, $cameraSnrMax, $cameraReadoutNoise, $microscopeStandMaker,
                    $microscopeStandName, $microscopeStandArticleOrSerialNumber, $microscopeCondenserMaker,
                    $microscopeCondenserName, $microscopeCondenserArticleOrSerialNumber,
                    $microscopeCondenserTurretPrismMaker, $microscopeCondenserTurretPrismName,
                    $microscopeCondenserTurretPrismArticleOrSerialNumber, $microscopeNosepieceObjectiveMaker,
                    $microscopeNosepieceObjectiveName, $microscopeNosepieceObjectiveArticleOrSerialNumber,
                    $microscopeNosepieceObjectiveType, $microscopeNosepieceObjectiveNumericalAperture,
                    $microscopeNosepieceObjectiveMagnification, $microscopeDicTurretPrismMaker,
                    $microscopeDicTurretPrismName, $microscopeDicTurretPrismArticleOrSerialNumber,
                    $microscopeMagnificationChangerMaker, $microscopeMagnificationChangerName,
                    $microscopeMagnificationChangerArticleOrSerialNumber, $microscopePortsMaker, $microscopePortsName,
                    $microscopePortsArticleOrSerialNumber, $microscopeCameraMountAdapterMaker,
                    $microscopeCameraMountAdapterName, $microscopeCameraMountAdapterMagnification,
                    $microscopeCameraMountAdapterArticleOrSerialNumber, $microscopeSettingsContrastMethod,
                    $microscopeSettingsDicPrismPosition, $microscopeSettingsApertureDiaphragmOpening,
                    $microscopeSettingsFieldDiaphragmOpening, $microscopeSettingsMagnificationChangerMagnification
                )
            );
        }

        /**
         * @param string $creatorCapturingDigitalMaster
         * @param string $creatorProcessingDerivatives
         */
        public function provideAuthorship($creatorCapturingDigitalMaster, $creatorProcessingDerivatives)
        {
            $this->apply(
                new AuthorshipOfPhotomicrographProvided(
                    $creatorCapturingDigitalMaster,
                    $creatorProcessingDerivatives
                )
            );
        }

        /**
         * @param string $title
         */
        public function rename($title)
        {
            $this->apply(new PhotomicrographRenamed($title));
        }

        /**
         * @param AuthorshipOfPhotomicrographProvided $event
         */
        protected function applyAuthorshipOfPhotomicrographProvided(AuthorshipOfPhotomicrographProvided $event)
        {
            // TODO: Implement applyAuthorshipOfPhotomicrographProvided() method.
        }

        /**
         * @param DicPrismPositionChanged $event
         */
        protected function applyDicPrismPositionChanged(DicPrismPositionChanged $event)
        {
            // TODO: Implement applyDicPrismPositionChanged() method.
        }

        /**
         * @param FocalPlaneImageAppended $event
         */
        protected function applyFocalPlaneImageAppended(FocalPlaneImageAppended $event)
        {
            // TODO: Implement applyFocalPlaneImageAppended() method.
        }

        /**
         * @param PhotomicrographDeleted $event
         */
        protected function applyPhotomicrographDeleted(PhotomicrographDeleted $event)
        {
            // TODO: Implement applyPhotomicrographDeleted() method.
        }

        /**
         * @param PhotomicrographDigitized $event
         */
        protected function applyPhotomicrographDigitized(PhotomicrographDigitized $event)
        {
            $this->zipFilePath = $event->getFileRealPath();
        }

        /**
         * @param PhotomicrographDigitizedV2 $event
         */
        protected function applyPhotomicrographDigitizedV2(PhotomicrographDigitizedV2 $event)
        {
            $this->zipFilePath = $event->getFileRealPath();
        }

        /**
         * @param PhotomicrographManipulated $event
         */
        protected function applyPhotomicrographManipulated(PhotomicrographManipulated $event)
        {
            // TODO: Implement applyPhotomicrographManipulated() method.
        }

        /**
         * @param PhotomicrographManipulatedV2 $event
         */
        protected function applyPhotomicrographManipulatedV2(PhotomicrographManipulatedV2 $event)
        {
            // TODO: Implement applyPhotomicrographManipulatedV2() method.
        }

        /**
         * @param PhotomicrographRenamed $event
         */
        protected function applyPhotomicrographRenamed(PhotomicrographRenamed $event)
        {
            // TODO: Implement applyPhotomicrographRenamed() method.
        }

        /**
         * Initialize all instance variables to default values. This method will be called for instantiation and every
         * time this instance is reconstituted through replayEventStream().
         */
        protected function initializeMembers()
        {
            // TODO: Implement initializeMembers() method.
        }
    }
