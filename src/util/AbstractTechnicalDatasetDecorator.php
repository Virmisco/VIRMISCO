<?php
    namespace sednasoft\virmisco\util;

    class AbstractTechnicalDatasetDecorator implements ILasTechnicalDataset
    {
        /** @var ILasTechnicalDataset */
        private $baseInstance;

        /**
         * AbstractTechnicalDatasetDecorator constructor.
         * @param ILasTechnicalDataset $baseInstance
         */
        public function __construct(ILasTechnicalDataset $baseInstance)
        {
            $this->baseInstance = $baseInstance;
        }

        /**
         * @param int $imageIndex
         * @return int
         */
        public function getCameraActivePixelsHor($imageIndex)
        {
            return $this->baseInstance->getCameraActivePixelsHor($imageIndex);
        }

        /**
         * @param int $imageIndex
         * @return int
         */
        public function getCameraActivePixelsVer($imageIndex)
        {
            return $this->baseInstance->getCameraActivePixelsVer($imageIndex);
        }

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getCameraAdcResolution($imageIndex)
        {
            return $this->baseInstance->getCameraAdcResolution($imageIndex);
        }

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getCameraCaptureFormat($imageIndex)
        {
            return $this->baseInstance->getCameraCaptureFormat($imageIndex);
        }

        /**
         * @param int $imageIndex
         * @return float
         */
        public function getCameraChipHeight($imageIndex)
        {
            return $this->baseInstance->getCameraChipHeight($imageIndex);
        }

        /**
         * @param int $imageIndex
         * @return float
         */
        public function getCameraChipWidth($imageIndex)
        {
            return $this->baseInstance->getCameraChipWidth($imageIndex);
        }

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getCameraColorFilterArray($imageIndex)
        {
            return $this->baseInstance->getCameraColorFilterArray($imageIndex);
        }

        /**
         * @param int $imageIndex
         * @return int
         */
        public function getCameraDynamicRange($imageIndex)
        {
            return $this->baseInstance->getCameraDynamicRange($imageIndex);
        }

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getCameraMaker($imageIndex)
        {
            return $this->baseInstance->getCameraMaker($imageIndex);
        }

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getCameraName($imageIndex)
        {
            return $this->baseInstance->getCameraName($imageIndex);
        }

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getCameraOpticalFormat($imageIndex)
        {
            return $this->baseInstance->getCameraOpticalFormat($imageIndex);
        }

        /**
         * @param int $imageIndex
         * @return float
         */
        public function getCameraPixelHeight($imageIndex)
        {
            return $this->baseInstance->getCameraPixelHeight($imageIndex);
        }

        /**
         * @param int $imageIndex
         * @return float
         */
        public function getCameraPixelWidth($imageIndex)
        {
            return $this->baseInstance->getCameraPixelWidth($imageIndex);
        }

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getCameraProtectiveColorFilter($imageIndex)
        {
            return $this->baseInstance->getCameraProtectiveColorFilter($imageIndex);
        }

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getCameraReadoutNoise($imageIndex)
        {
            return $this->baseInstance->getCameraReadoutNoise($imageIndex);
        }

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getCameraSensorArticleOrSerialNumber($imageIndex)
        {
            return $this->baseInstance->getCameraSensorArticleOrSerialNumber($imageIndex);
        }

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getCameraSensorMaker($imageIndex)
        {
            return $this->baseInstance->getCameraSensorMaker($imageIndex);
        }

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getCameraSensorName($imageIndex)
        {
            return $this->baseInstance->getCameraSensorName($imageIndex);
        }

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getCameraSerialNumber($imageIndex)
        {
            return $this->baseInstance->getCameraSerialNumber($imageIndex);
        }

        /**
         * @param int $imageIndex
         * @return int
         */
        public function getCameraSnrMax($imageIndex)
        {
            return $this->baseInstance->getCameraSnrMax($imageIndex);
        }

        /**
         * @return string
         */
        public function getCreationDate()
        {
            return $this->baseInstance->getCreationDate();
        }

        /**
         * @param int $imageIndex
         * @return float
         */
        public function getExposureDurationInSeconds($imageIndex)
        {
            return $this->baseInstance->getExposureDurationInSeconds($imageIndex);
        }

        /**
         * @param int $imageIndex
         * @return float
         */
        public function getExposureGainFactor($imageIndex)
        {
            return $this->baseInstance->getExposureGainFactor($imageIndex);
        }

        /**
         * @param int $imageIndex
         * @return float
         */
        public function getFocusPositionInMeters($imageIndex)
        {
            return $this->baseInstance->getFocusPositionInMeters($imageIndex);
        }

        /**
         * @param int $imageIndex
         * @return int
         */
        public function getHistogramBlackClip($imageIndex)
        {
            return $this->baseInstance->getHistogramBlackClip($imageIndex);
        }

        /**
         * @param int $imageIndex
         * @return float
         */
        public function getHistogramGamma($imageIndex)
        {
            return $this->baseInstance->getHistogramGamma($imageIndex);
        }

        /**
         * @param int $imageIndex
         * @return int
         */
        public function getHistogramWhiteClip($imageIndex)
        {
            return $this->baseInstance->getHistogramWhiteClip($imageIndex);
        }

        /**
         * @param int $imageIndex
         * @return int
         */
        public function getImageColorDepthInBitsPerPixel($imageIndex)
        {
            return $this->baseInstance->getImageColorDepthInBitsPerPixel($imageIndex);
        }

        /**
         * @return int
         */
        public function getImageCount()
        {
            return $this->baseInstance->getImageCount();
        }

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getImageCreationDate($imageIndex)
        {
            return $this->baseInstance->getImageCreationDate($imageIndex);
        }

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getImageCreationUserName($imageIndex)
        {
            return $this->baseInstance->getImageCreationUserName($imageIndex);
        }

        /**
         * @param int $imageIndex
         * @return int
         */
        public function getImageHeightInPixels($imageIndex)
        {
            return $this->baseInstance->getImageHeightInPixels($imageIndex);
        }

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getImageModificationDate($imageIndex)
        {
            return $this->baseInstance->getImageModificationDate($imageIndex);
        }

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getImageModificationUserName($imageIndex)
        {
            return $this->baseInstance->getImageModificationUserName($imageIndex);
        }

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getImageName($imageIndex)
        {
            return $this->baseInstance->getImageName($imageIndex);
        }

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getImageWidthInPixels($imageIndex)
        {
            return $this->baseInstance->getImageWidthInPixels($imageIndex);
        }

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getMicroscopeCameraMountAdapterArticleOrSerialNumber($imageIndex)
        {
            return $this->baseInstance->getMicroscopeCameraMountAdapterArticleOrSerialNumber($imageIndex);
        }

        /**
         * @param int $imageIndex
         * @return float
         */
        public function getMicroscopeCameraMountAdapterMagnification($imageIndex)
        {
            return $this->baseInstance->getMicroscopeCameraMountAdapterMagnification($imageIndex);
        }

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getMicroscopeCameraMountAdapterMaker($imageIndex)
        {
            return $this->baseInstance->getMicroscopeCameraMountAdapterMaker($imageIndex);
        }

        /**
         * @param int $imageIndex
         * @return null
         */
        public function getMicroscopeCameraMountAdapterName($imageIndex)
        {
            return $this->baseInstance->getMicroscopeCameraMountAdapterName($imageIndex);
        }

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getMicroscopeCondenserArticleOrSerialNumber($imageIndex)
        {
            return $this->baseInstance->getMicroscopeCondenserArticleOrSerialNumber($imageIndex);
        }

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getMicroscopeCondenserMaker($imageIndex)
        {
            return $this->baseInstance->getMicroscopeCondenserMaker($imageIndex);
        }

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getMicroscopeCondenserName($imageIndex)
        {
            return $this->baseInstance->getMicroscopeCondenserName($imageIndex);
        }

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getMicroscopeCondenserTurretPrismArticleOrSerialNumber($imageIndex)
        {
            return $this->baseInstance->getMicroscopeCondenserTurretPrismArticleOrSerialNumber($imageIndex);
        }

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getMicroscopeCondenserTurretPrismMaker($imageIndex)
        {
            return $this->baseInstance->getMicroscopeCondenserTurretPrismMaker($imageIndex);
        }

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getMicroscopeCondenserTurretPrismName($imageIndex)
        {
            return $this->baseInstance->getMicroscopeCondenserTurretPrismName($imageIndex);
        }

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getMicroscopeDicTurretPrismArticleOrSerialNumber($imageIndex)
        {
            return $this->baseInstance->getMicroscopeDicTurretPrismArticleOrSerialNumber($imageIndex);
        }

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getMicroscopeDicTurretPrismMaker($imageIndex)
        {
            return $this->baseInstance->getMicroscopeDicTurretPrismMaker($imageIndex);
        }

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getMicroscopeDicTurretPrismName($imageIndex)
        {
            return $this->baseInstance->getMicroscopeDicTurretPrismName($imageIndex);
        }

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getMicroscopeMagnificationChangerArticleOrSerialNumber($imageIndex)
        {
            return $this->baseInstance->getMicroscopeMagnificationChangerArticleOrSerialNumber($imageIndex);
        }

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getMicroscopeMagnificationChangerMaker($imageIndex)
        {
            return $this->baseInstance->getMicroscopeMagnificationChangerMaker($imageIndex);
        }

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getMicroscopeMagnificationChangerName($imageIndex)
        {
            return $this->baseInstance->getMicroscopeMagnificationChangerName($imageIndex);
        }

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getMicroscopeNosepieceObjectiveArticleOrSerialNumber($imageIndex)
        {
            return $this->baseInstance->getMicroscopeNosepieceObjectiveArticleOrSerialNumber($imageIndex);
        }

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getMicroscopeNosepieceObjectiveMaker($imageIndex)
        {
            return $this->baseInstance->getMicroscopeNosepieceObjectiveMaker($imageIndex);
        }

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getMicroscopeNosepieceObjectiveName($imageIndex)
        {
            return $this->baseInstance->getMicroscopeNosepieceObjectiveName($imageIndex);
        }

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getMicroscopeNosepieceObjectiveType($imageIndex)
        {
            return $this->baseInstance->getMicroscopeNosepieceObjectiveType($imageIndex);
        }

        /**
         * @param int $imageIndex
         * @return int
         */
        public function getMicroscopeNumberOfPorts($imageIndex)
        {
            return $this->baseInstance->getMicroscopeNumberOfPorts($imageIndex);
        }

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getMicroscopePortsArticleOrSerialNumber($imageIndex)
        {
            return $this->baseInstance->getMicroscopePortsArticleOrSerialNumber($imageIndex);
        }

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getMicroscopePortsMaker($imageIndex)
        {
            return $this->baseInstance->getMicroscopePortsMaker($imageIndex);
        }

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getMicroscopePortsName($imageIndex)
        {
            return $this->baseInstance->getMicroscopePortsName($imageIndex);
        }

        /**
         * @param int $imageIndex
         * @return float
         */
        public function getMicroscopeSettingsApertureDiaphragmOpening($imageIndex)
        {
            return $this->baseInstance->getMicroscopeSettingsApertureDiaphragmOpening($imageIndex);
        }

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getMicroscopeSettingsContrastMethod($imageIndex)
        {
            return $this->baseInstance->getMicroscopeSettingsContrastMethod($imageIndex);
        }

        /**
         * @param int $imageIndex
         * @return null
         */
        public function getMicroscopeSettingsDicPrismPosition($imageIndex)
        {
            return $this->baseInstance->getMicroscopeSettingsDicPrismPosition($imageIndex);
        }

        /**
         * @param int $imageIndex
         * @return float
         */
        public function getMicroscopeSettingsFieldDiaphragmOpening($imageIndex)
        {
            return $this->baseInstance->getMicroscopeSettingsFieldDiaphragmOpening($imageIndex);
        }

        /**
         * @param int $imageIndex
         * @return float
         */
        public function getMicroscopeSettingsLampIntensity($imageIndex)
        {
            return $this->baseInstance->getMicroscopeSettingsLampIntensity($imageIndex);
        }

        /**
         * @param int $imageIndex
         * @return float
         */
        public function getMicroscopeSettingsMagnificationChangerMagnification($imageIndex)
        {
            return $this->baseInstance->getMicroscopeSettingsMagnificationChangerMagnification($imageIndex);
        }

        /**
         * @param int $imageIndex
         * @return float
         */
        public function getMicroscopeSettingsNosepieceObjectiveApertureOpening($imageIndex)
        {
            return $this->baseInstance->getMicroscopeSettingsNosepieceObjectiveApertureOpening($imageIndex);
        }

        /**
         * @param int $imageIndex
         * @return float
         */
        public function getMicroscopeSettingsNosepieceObjectiveMagnification($imageIndex)
        {
            return $this->baseInstance->getMicroscopeSettingsNosepieceObjectiveMagnification($imageIndex);
        }

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getMicroscopeStandArticleOrSerialNumber($imageIndex)
        {
            return $this->baseInstance->getMicroscopeStandArticleOrSerialNumber($imageIndex);
        }

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getMicroscopeStandMaker($imageIndex)
        {
            return $this->baseInstance->getMicroscopeStandMaker($imageIndex);
        }

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getMicroscopeStandName($imageIndex)
        {
            return $this->baseInstance->getMicroscopeStandName($imageIndex);
        }

        /**
         * @return string
         */
        public function getModificationDate()
        {
            return $this->baseInstance->getModificationDate();
        }

        /**
         * @param int $imageIndex
         * @return float
         */
        public function getReproductionScaleInMetersPerPixelHorizontal($imageIndex)
        {
            return $this->baseInstance->getReproductionScaleInMetersPerPixelHorizontal($imageIndex);
        }

        /**
         * @param int $imageIndex
         * @return float
         */
        public function getReproductionScaleInMetersPerPixelVertical($imageIndex)
        {
            return $this->baseInstance->getReproductionScaleInMetersPerPixelVertical($imageIndex);
        }

        /**
         * @return string
         */
        public function getRootItemName()
        {
            return $this->baseInstance->getRootItemName();
        }

        /**
         * @param int $imageIndex
         * @return boolean
         */
        public function isMicroscopePolarizerInLightPath($imageIndex)
        {
            return $this->baseInstance->isMicroscopePolarizerInLightPath($imageIndex);
        }

        /**
         * @param int $imageIndex
         * @return bool
         */
        public function isMicroscopeSettingsLampShutterOpen($imageIndex)
        {
            return $this->baseInstance->isMicroscopeSettingsLampShutterOpen($imageIndex);
        }

        /**
         * @param int $imageIndex
         * @return boolean
         */
        public function isPostprocessingShading($imageIndex)
        {
            return $this->baseInstance->isPostprocessingShading($imageIndex);
        }

        /**
         * @param int $imageIndex
         * @return boolean
         */
        public function isPostprocessingSharpening($imageIndex)
        {
            return $this->baseInstance->isPostprocessingSharpening($imageIndex);
        }
    }