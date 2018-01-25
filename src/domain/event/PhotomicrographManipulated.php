<?php
    namespace sednasoft\virmisco\domain\event;

    use sednasoft\virmisco\singiere\AbstractEvent;

    class PhotomicrographManipulated extends AbstractEvent
    {
        /** @var int */
        private $cameraActivePixelsHor;
        /** @var int */
        private $cameraActivePixelsVer;
        /** @var string */
        private $cameraAdcResolution;
        /** @var string */
        private $cameraCameraArticleOrSerialNumber;
        /** @var string */
        private $cameraCameraMaker;
        /** @var string */
        private $cameraCameraName;
        /** @var string */
        private $cameraCaptureFormat;
        /** @var float */
        private $cameraChipHeight;
        /** @var float */
        private $cameraChipWidth;
        /** @var string */
        private $cameraColorFilterArray;
        /** @var float */
        private $cameraDynamicRange;
        /** @var string */
        private $cameraOpticalFormat;
        /** @var float */
        private $cameraPixelHeight;
        /** @var float */
        private $cameraPixelWidth;
        /** @var string */
        private $cameraProtectiveColorFilter;
        /** @var float */
        private $cameraReadoutNoise;
        /** @var string */
        private $cameraSensorArticleOrSerialNumber;
        /** @var string */
        private $cameraSensorMaker;
        /** @var string */
        private $cameraSensorName;
        /** @var float */
        private $cameraSnrMax;
        /** @var string */
        private $creatorCapturing;
        /** @var string */
        private $creatorProcessing;
        /** @var int */
        private $detailOfHotspotX;
        /** @var int */
        private $detailOfHotspotY;
        /** @var string */
        private $detailOfPhotomicrographId;
        /** @var int */
        private $digitizationDataColorDepth;
        /** @var int */
        private $digitizationDataHeight;
        /** @var float */
        private $digitizationDataReproductionScaleHorizontal;
        /** @var float */
        private $digitizationDataReproductionScaleVertical;
        /** @var int */
        private $digitizationDataWidth;
        /** @var string */
        private $fileCreationTime;
        /** @var string */
        private $fileModificationTime;
        /** @var string */
        private $fileRealPath;
        /** @var string */
        private $fileUri;
        /** @var string */
        private $microscopeCameraMountAdapterArticleOrSerialNumber;
        /** @var string */
        private $microscopeCameraMountAdapterMagnification;
        /** @var string */
        private $microscopeCameraMountAdapterMaker;
        /** @var string */
        private $microscopeCameraMountAdapterName;
        /** @var string */
        private $microscopeCondenserArticleOrSerialNumber;
        /** @var string */
        private $microscopeCondenserMaker;
        /** @var string */
        private $microscopeCondenserName;
        /** @var string */
        private $microscopeCondenserTurretPrismArticleOrSerialNumber;
        /** @var string */
        private $microscopeCondenserTurretPrismMaker;
        /** @var string */
        private $microscopeCondenserTurretPrismName;
        /** @var string */
        private $microscopeDicTurretPrismArticleOrSerialNumber;
        /** @var string */
        private $microscopeDicTurretPrismMaker;
        /** @var string */
        private $microscopeDicTurretPrismName;
        /** @var string */
        private $microscopeMagnificationChangerArticleOrSerialNumber;
        /** @var string */
        private $microscopeMagnificationChangerMaker;
        /** @var string */
        private $microscopeMagnificationChangerName;
        /** @var string */
        private $microscopeNosepieceObjectiveArticleOrSerialNumber;
        /** @var float */
        private $microscopeNosepieceObjectiveMagnification;
        /** @var string */
        private $microscopeNosepieceObjectiveMaker;
        /** @var string */
        private $microscopeNosepieceObjectiveName;
        /** @var float */
        private $microscopeNosepieceObjectiveNumericalAperture;
        /** @var string */
        private $microscopeNosepieceObjectiveType;
        /** @var string */
        private $microscopePortsArticleOrSerialNumber;
        /** @var string */
        private $microscopePortsMaker;
        /** @var string */
        private $microscopePortsName;
        /** @var float */
        private $microscopeSettingsApertureDiaphragmOpening;
        /** @var string */
        private $microscopeSettingsContrastMethod;
        /** @var float */
        private $microscopeSettingsDicPrismPosition;
        /** @var float */
        private $microscopeSettingsFieldDiaphragmOpening;
        /** @var float */
        private $microscopeSettingsMagnificationChangerMagnification;
        /** @var string */
        private $microscopeStandArticleOrSerialNumber;
        /** @var string */
        private $microscopeStandMaker;
        /** @var string */
        private $microscopeStandName;
        /** @var string */
        private $presentationUri;
        /** @var string */
        private $title;

        /**
         * Creates a new instance. Subclasses should add more parameters for payload.
         *
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
        public function __construct(
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
            parent::__construct();
            $this->title = (string)$title;
            $this->detailOfPhotomicrographId = (string)$detailOfPhotomicrographId;
            $this->detailOfHotspotX = (int)$detailOfHotspotX;
            $this->detailOfHotspotY = (int)$detailOfHotspotY;
            $this->creatorCapturing = $creatorCapturing;
            $this->creatorProcessing = $creatorProcessing;
            $this->fileRealPath = (string)$fileRealPath;
            $this->fileUri = (string)$fileUri;
            $this->fileCreationTime = (string)$fileCreationTime;
            $this->fileModificationTime = (string)$fileModificationTime;
            $this->presentationUri = (string)$presentationUri;
            $this->digitizationDataWidth = (int)$digitizationDataWidth;
            $this->digitizationDataHeight = (int)$digitizationDataHeight;
            $this->digitizationDataColorDepth = (int)$digitizationDataColorDepth;
            $this->digitizationDataReproductionScaleHorizontal = (float)$digitizationDataReproductionScaleHorizontal;
            $this->digitizationDataReproductionScaleVertical = (float)$digitizationDataReproductionScaleVertical;
            $this->cameraCameraMaker = (string)$cameraCameraMaker;
            $this->cameraCameraName = (string)$cameraCameraName;
            $this->cameraCameraArticleOrSerialNumber = (string)$cameraCameraArticleOrSerialNumber;
            $this->cameraSensorMaker = (string)$cameraSensorMaker;
            $this->cameraSensorName = (string)$cameraSensorName;
            $this->cameraSensorArticleOrSerialNumber = (string)$cameraSensorArticleOrSerialNumber;
            $this->cameraOpticalFormat = (string)$cameraOpticalFormat;
            $this->cameraCaptureFormat = (string)$cameraCaptureFormat;
            $this->cameraChipWidth = (float)$cameraChipWidth;
            $this->cameraChipHeight = (float)$cameraChipHeight;
            $this->cameraPixelWidth = (float)$cameraPixelWidth;
            $this->cameraPixelHeight = (float)$cameraPixelHeight;
            $this->cameraActivePixelsHor = (int)$cameraActivePixelsHor;
            $this->cameraActivePixelsVer = (int)$cameraActivePixelsVer;
            $this->cameraColorFilterArray = (string)$cameraColorFilterArray;
            $this->cameraProtectiveColorFilter = (string)$cameraProtectiveColorFilter;
            $this->cameraAdcResolution = (string)$cameraAdcResolution;
            $this->cameraDynamicRange = (float)$cameraDynamicRange;
            $this->cameraSnrMax = (float)$cameraSnrMax;
            $this->cameraReadoutNoise = (float)$cameraReadoutNoise;
            $this->microscopeStandMaker = (string)$microscopeStandMaker;
            $this->microscopeStandName = (string)$microscopeStandName;
            $this->microscopeStandArticleOrSerialNumber = (string)$microscopeStandArticleOrSerialNumber;
            $this->microscopeCondenserMaker = (string)$microscopeCondenserMaker;
            $this->microscopeCondenserName = (string)$microscopeCondenserName;
            $this->microscopeCondenserArticleOrSerialNumber = (string)$microscopeCondenserArticleOrSerialNumber;
            $this->microscopeCondenserTurretPrismMaker = (string)$microscopeCondenserTurretPrismMaker;
            $this->microscopeCondenserTurretPrismName = (string)$microscopeCondenserTurretPrismName;
            $this->microscopeCondenserTurretPrismArticleOrSerialNumber = (string)$microscopeCondenserTurretPrismArticleOrSerialNumber;
            $this->microscopeNosepieceObjectiveMaker = (string)$microscopeNosepieceObjectiveMaker;
            $this->microscopeNosepieceObjectiveName = (string)$microscopeNosepieceObjectiveName;
            $this->microscopeNosepieceObjectiveArticleOrSerialNumber = (string)$microscopeNosepieceObjectiveArticleOrSerialNumber;
            $this->microscopeNosepieceObjectiveType = (string)$microscopeNosepieceObjectiveType;
            $this->microscopeNosepieceObjectiveNumericalAperture = (float)$microscopeNosepieceObjectiveNumericalAperture;
            $this->microscopeNosepieceObjectiveMagnification = (float)$microscopeNosepieceObjectiveMagnification;
            $this->microscopeDicTurretPrismMaker = (string)$microscopeDicTurretPrismMaker;
            $this->microscopeDicTurretPrismName = (string)$microscopeDicTurretPrismName;
            $this->microscopeDicTurretPrismArticleOrSerialNumber = (string)$microscopeDicTurretPrismArticleOrSerialNumber;
            $this->microscopeMagnificationChangerMaker = (string)$microscopeMagnificationChangerMaker;
            $this->microscopeMagnificationChangerName = (string)$microscopeMagnificationChangerName;
            $this->microscopeMagnificationChangerArticleOrSerialNumber = (string)$microscopeMagnificationChangerArticleOrSerialNumber;
            $this->microscopePortsMaker = (string)$microscopePortsMaker;
            $this->microscopePortsName = (string)$microscopePortsName;
            $this->microscopePortsArticleOrSerialNumber = (string)$microscopePortsArticleOrSerialNumber;
            $this->microscopeCameraMountAdapterMaker = (string)$microscopeCameraMountAdapterMaker;
            $this->microscopeCameraMountAdapterName = (string)$microscopeCameraMountAdapterName;
            $this->microscopeCameraMountAdapterMagnification = (string)$microscopeCameraMountAdapterMagnification;
            $this->microscopeCameraMountAdapterArticleOrSerialNumber = (string)$microscopeCameraMountAdapterArticleOrSerialNumber;
            $this->microscopeSettingsContrastMethod = (string)$microscopeSettingsContrastMethod;
            $this->microscopeSettingsDicPrismPosition = (float)$microscopeSettingsDicPrismPosition;
            $this->microscopeSettingsApertureDiaphragmOpening = (float)$microscopeSettingsApertureDiaphragmOpening;
            $this->microscopeSettingsFieldDiaphragmOpening = (float)$microscopeSettingsFieldDiaphragmOpening;
            $this->microscopeSettingsMagnificationChangerMagnification = (float)$microscopeSettingsMagnificationChangerMagnification;
        }

        /**
         * @return int
         */
        public function getCameraActivePixelsHor()
        {
            return $this->cameraActivePixelsHor;
        }

        /**
         * @return int
         */
        public function getCameraActivePixelsVer()
        {
            return $this->cameraActivePixelsVer;
        }

        /**
         * @return string
         */
        public function getCameraAdcResolution()
        {
            return $this->cameraAdcResolution;
        }

        /**
         * @return string
         */
        public function getCameraCameraArticleOrSerialNumber()
        {
            return $this->cameraCameraArticleOrSerialNumber;
        }

        /**
         * @return string
         */
        public function getCameraCameraMaker()
        {
            return $this->cameraCameraMaker;
        }

        /**
         * @return string
         */
        public function getCameraCameraName()
        {
            return $this->cameraCameraName;
        }

        /**
         * @return string
         */
        public function getCameraCaptureFormat()
        {
            return $this->cameraCaptureFormat;
        }

        /**
         * @return float
         */
        public function getCameraChipHeight()
        {
            return $this->cameraChipHeight;
        }

        /**
         * @return float
         */
        public function getCameraChipWidth()
        {
            return $this->cameraChipWidth;
        }

        /**
         * @return string
         */
        public function getCameraColorFilterArray()
        {
            return $this->cameraColorFilterArray;
        }

        /**
         * @return float
         */
        public function getCameraDynamicRange()
        {
            return $this->cameraDynamicRange;
        }

        /**
         * @return string
         */
        public function getCameraOpticalFormat()
        {
            return $this->cameraOpticalFormat;
        }

        /**
         * @return float
         */
        public function getCameraPixelHeight()
        {
            return $this->cameraPixelHeight;
        }

        /**
         * @return float
         */
        public function getCameraPixelWidth()
        {
            return $this->cameraPixelWidth;
        }

        /**
         * @return string
         */
        public function getCameraProtectiveColorFilter()
        {
            return $this->cameraProtectiveColorFilter;
        }

        /**
         * @return float
         */
        public function getCameraReadoutNoise()
        {
            return $this->cameraReadoutNoise;
        }

        /**
         * @return string
         */
        public function getCameraSensorArticleOrSerialNumber()
        {
            return $this->cameraSensorArticleOrSerialNumber;
        }

        /**
         * @return string
         */
        public function getCameraSensorMaker()
        {
            return $this->cameraSensorMaker;
        }

        /**
         * @return string
         */
        public function getCameraSensorName()
        {
            return $this->cameraSensorName;
        }

        /**
         * @return float
         */
        public function getCameraSnrMax()
        {
            return $this->cameraSnrMax;
        }

        /**
         * @return string
         */
        public function getCreatorCapturing()
        {
            return $this->creatorCapturing;
        }

        /**
         * @return string
         */
        public function getCreatorProcessing()
        {
            return $this->creatorProcessing;
        }

        /**
         * @return int
         */
        public function getDetailOfHotspotX()
        {
            return $this->detailOfHotspotX;
        }

        /**
         * @return int
         */
        public function getDetailOfHotspotY()
        {
            return $this->detailOfHotspotY;
        }

        /**
         * @return string
         */
        public function getDetailOfPhotomicrographId()
        {
            return $this->detailOfPhotomicrographId;
        }

        /**
         * @return int
         */
        public function getDigitizationDataColorDepth()
        {
            return $this->digitizationDataColorDepth;
        }

        /**
         * @return int
         */
        public function getDigitizationDataHeight()
        {
            return $this->digitizationDataHeight;
        }

        /**
         * @return float
         */
        public function getDigitizationDataReproductionScaleHorizontal()
        {
            return $this->digitizationDataReproductionScaleHorizontal;
        }

        /**
         * @return float
         */
        public function getDigitizationDataReproductionScaleVertical()
        {
            return $this->digitizationDataReproductionScaleVertical;
        }

        /**
         * @return int
         */
        public function getDigitizationDataWidth()
        {
            return $this->digitizationDataWidth;
        }

        /**
         * @return string
         */
        public function getFileCreationTime()
        {
            return $this->fileCreationTime;
        }

        /**
         * @return string
         */
        public function getFileModificationTime()
        {
            return $this->fileModificationTime;
        }

        /**
         * @return string
         */
        public function getFileRealPath()
        {
            return $this->fileRealPath;
        }

        /**
         * @return string
         */
        public function getFileUri()
        {
            return $this->fileUri;
        }

        /**
         * @return string
         */
        public function getMicroscopeCameraMountAdapterArticleOrSerialNumber()
        {
            return $this->microscopeCameraMountAdapterArticleOrSerialNumber;
        }

        /**
         * @return string
         */
        public function getMicroscopeCameraMountAdapterMagnification()
        {
            return $this->microscopeCameraMountAdapterMagnification;
        }

        /**
         * @return string
         */
        public function getMicroscopeCameraMountAdapterMaker()
        {
            return $this->microscopeCameraMountAdapterMaker;
        }

        /**
         * @return string
         */
        public function getMicroscopeCameraMountAdapterName()
        {
            return $this->microscopeCameraMountAdapterName;
        }

        /**
         * @return string
         */
        public function getMicroscopeCondenserArticleOrSerialNumber()
        {
            return $this->microscopeCondenserArticleOrSerialNumber;
        }

        /**
         * @return string
         */
        public function getMicroscopeCondenserMaker()
        {
            return $this->microscopeCondenserMaker;
        }

        /**
         * @return string
         */
        public function getMicroscopeCondenserName()
        {
            return $this->microscopeCondenserName;
        }

        /**
         * @return string
         */
        public function getMicroscopeCondenserTurretPrismArticleOrSerialNumber()
        {
            return $this->microscopeCondenserTurretPrismArticleOrSerialNumber;
        }

        /**
         * @return string
         */
        public function getMicroscopeCondenserTurretPrismMaker()
        {
            return $this->microscopeCondenserTurretPrismMaker;
        }

        /**
         * @return string
         */
        public function getMicroscopeCondenserTurretPrismName()
        {
            return $this->microscopeCondenserTurretPrismName;
        }

        /**
         * @return string
         */
        public function getMicroscopeDicTurretPrismArticleOrSerialNumber()
        {
            return $this->microscopeDicTurretPrismArticleOrSerialNumber;
        }

        /**
         * @return string
         */
        public function getMicroscopeDicTurretPrismMaker()
        {
            return $this->microscopeDicTurretPrismMaker;
        }

        /**
         * @return string
         */
        public function getMicroscopeDicTurretPrismName()
        {
            return $this->microscopeDicTurretPrismName;
        }

        /**
         * @return string
         */
        public function getMicroscopeMagnificationChangerArticleOrSerialNumber()
        {
            return $this->microscopeMagnificationChangerArticleOrSerialNumber;
        }

        /**
         * @return string
         */
        public function getMicroscopeMagnificationChangerMaker()
        {
            return $this->microscopeMagnificationChangerMaker;
        }

        /**
         * @return string
         */
        public function getMicroscopeMagnificationChangerName()
        {
            return $this->microscopeMagnificationChangerName;
        }

        /**
         * @return string
         */
        public function getMicroscopeNosepieceObjectiveArticleOrSerialNumber()
        {
            return $this->microscopeNosepieceObjectiveArticleOrSerialNumber;
        }

        /**
         * @return float
         */
        public function getMicroscopeNosepieceObjectiveMagnification()
        {
            return $this->microscopeNosepieceObjectiveMagnification;
        }

        /**
         * @return string
         */
        public function getMicroscopeNosepieceObjectiveMaker()
        {
            return $this->microscopeNosepieceObjectiveMaker;
        }

        /**
         * @return string
         */
        public function getMicroscopeNosepieceObjectiveName()
        {
            return $this->microscopeNosepieceObjectiveName;
        }

        /**
         * @return float
         */
        public function getMicroscopeNosepieceObjectiveNumericalAperture()
        {
            return $this->microscopeNosepieceObjectiveNumericalAperture;
        }

        /**
         * @return string
         */
        public function getMicroscopeNosepieceObjectiveType()
        {
            return $this->microscopeNosepieceObjectiveType;
        }

        /**
         * @return string
         */
        public function getMicroscopePortsArticleOrSerialNumber()
        {
            return $this->microscopePortsArticleOrSerialNumber;
        }

        /**
         * @return string
         */
        public function getMicroscopePortsMaker()
        {
            return $this->microscopePortsMaker;
        }

        /**
         * @return string
         */
        public function getMicroscopePortsName()
        {
            return $this->microscopePortsName;
        }

        /**
         * @return float
         */
        public function getMicroscopeSettingsApertureDiaphragmOpening()
        {
            return $this->microscopeSettingsApertureDiaphragmOpening;
        }

        /**
         * @return string
         */
        public function getMicroscopeSettingsContrastMethod()
        {
            return $this->microscopeSettingsContrastMethod;
        }

        /**
         * @return float
         */
        public function getMicroscopeSettingsDicPrismPosition()
        {
            return $this->microscopeSettingsDicPrismPosition;
        }

        /**
         * @return float
         */
        public function getMicroscopeSettingsFieldDiaphragmOpening()
        {
            return $this->microscopeSettingsFieldDiaphragmOpening;
        }

        /**
         * @return float
         */
        public function getMicroscopeSettingsMagnificationChangerMagnification()
        {
            return $this->microscopeSettingsMagnificationChangerMagnification;
        }

        /**
         * @return string
         */
        public function getMicroscopeStandArticleOrSerialNumber()
        {
            return $this->microscopeStandArticleOrSerialNumber;
        }

        /**
         * @return string
         */
        public function getMicroscopeStandMaker()
        {
            return $this->microscopeStandMaker;
        }

        /**
         * @return string
         */
        public function getMicroscopeStandName()
        {
            return $this->microscopeStandName;
        }

        /**
         * @return string
         */
        public function getPresentationUri()
        {
            return $this->presentationUri;
        }

        /**
         * @return string
         */
        public function getTitle()
        {
            return $this->title;
        }
    }
