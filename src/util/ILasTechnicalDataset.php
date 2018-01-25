<?php
    namespace sednasoft\virmisco\util;

    interface ILasTechnicalDataset
    {
        /**
         * @param int $imageIndex
         * @return int
         */
        public function getCameraActivePixelsHor($imageIndex);

        /**
         * @param int $imageIndex
         * @return int
         */
        public function getCameraActivePixelsVer($imageIndex);

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getCameraAdcResolution($imageIndex);

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getCameraCaptureFormat($imageIndex);

        /**
         * @param int $imageIndex
         * @return float
         */
        public function getCameraChipHeight($imageIndex);

        /**
         * @param int $imageIndex
         * @return float
         */
        public function getCameraChipWidth($imageIndex);

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getCameraColorFilterArray($imageIndex);

        /**
         * @param int $imageIndex
         * @return int
         */
        public function getCameraDynamicRange($imageIndex);

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getCameraMaker($imageIndex);

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getCameraName($imageIndex);

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getCameraOpticalFormat($imageIndex);

        /**
         * @param int $imageIndex
         * @return float
         */
        public function getCameraPixelHeight($imageIndex);

        /**
         * @param int $imageIndex
         * @return float
         */
        public function getCameraPixelWidth($imageIndex);

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getCameraProtectiveColorFilter($imageIndex);

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getCameraReadoutNoise($imageIndex);

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getCameraSensorArticleOrSerialNumber($imageIndex);

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getCameraSensorMaker($imageIndex);

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getCameraSensorName($imageIndex);

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getCameraSerialNumber($imageIndex);

        /**
         * @param int $imageIndex
         * @return int
         */
        public function getCameraSnrMax($imageIndex);

        /**
         * @return string
         */
        public function getCreationDate();

        /**
         * @param int $imageIndex
         * @return float
         */
        public function getExposureDurationInSeconds($imageIndex);

        /**
         * @param int $imageIndex
         * @return float
         */
        public function getExposureGainFactor($imageIndex);

        /**
         * @param int $imageIndex
         * @return float
         */
        public function getFocusPositionInMeters($imageIndex);

        /**
         * @param int $imageIndex
         * @return int
         */
        public function getHistogramBlackClip($imageIndex);

        /**
         * @param int $imageIndex
         * @return float
         */
        public function getHistogramGamma($imageIndex);

        /**
         * @param int $imageIndex
         * @return int
         */
        public function getHistogramWhiteClip($imageIndex);

        /**
         * @param int $imageIndex
         * @return int
         */
        public function getImageColorDepthInBitsPerPixel($imageIndex);

        /**
         * @return int
         */
        public function getImageCount();

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getImageCreationDate($imageIndex);

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getImageCreationUserName($imageIndex);

        /**
         * @param int $imageIndex
         * @return int
         */
        public function getImageHeightInPixels($imageIndex);

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getImageModificationDate($imageIndex);

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getImageModificationUserName($imageIndex);

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getImageName($imageIndex);

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getImageWidthInPixels($imageIndex);

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getMicroscopeCameraMountAdapterArticleOrSerialNumber($imageIndex);

        /**
         * @param int $imageIndex
         * @return float
         */
        public function getMicroscopeCameraMountAdapterMagnification($imageIndex);

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getMicroscopeCameraMountAdapterMaker($imageIndex);

        /**
         * @param int $imageIndex
         * @return null
         */
        public function getMicroscopeCameraMountAdapterName($imageIndex);

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getMicroscopeCondenserArticleOrSerialNumber($imageIndex);

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getMicroscopeCondenserMaker($imageIndex);

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getMicroscopeCondenserName($imageIndex);

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getMicroscopeCondenserTurretPrismArticleOrSerialNumber($imageIndex);

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getMicroscopeCondenserTurretPrismMaker($imageIndex);

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getMicroscopeCondenserTurretPrismName($imageIndex);

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getMicroscopeDicTurretPrismArticleOrSerialNumber($imageIndex);

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getMicroscopeDicTurretPrismMaker($imageIndex);

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getMicroscopeDicTurretPrismName($imageIndex);

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getMicroscopeMagnificationChangerArticleOrSerialNumber($imageIndex);

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getMicroscopeMagnificationChangerMaker($imageIndex);

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getMicroscopeMagnificationChangerName($imageIndex);

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getMicroscopeNosepieceObjectiveArticleOrSerialNumber($imageIndex);

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getMicroscopeNosepieceObjectiveMaker($imageIndex);

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getMicroscopeNosepieceObjectiveName($imageIndex);

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getMicroscopeNosepieceObjectiveType($imageIndex);

        /**
         * @param int $imageIndex
         * @return int
         */
        public function getMicroscopeNumberOfPorts($imageIndex);

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getMicroscopePortsArticleOrSerialNumber($imageIndex);

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getMicroscopePortsMaker($imageIndex);

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getMicroscopePortsName($imageIndex);

        /**
         * @param int $imageIndex
         * @return float
         */
        public function getMicroscopeSettingsApertureDiaphragmOpening($imageIndex);

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getMicroscopeSettingsContrastMethod($imageIndex);

        /**
         * @param int $imageIndex
         * @return null
         */
        public function getMicroscopeSettingsDicPrismPosition($imageIndex);

        /**
         * @param int $imageIndex
         * @return float
         */
        public function getMicroscopeSettingsFieldDiaphragmOpening($imageIndex);

        /**
         * @param int $imageIndex
         * @return float
         */
        public function getMicroscopeSettingsLampIntensity($imageIndex);

        /**
         * @param int $imageIndex
         * @return float
         */
        public function getMicroscopeSettingsMagnificationChangerMagnification($imageIndex);

        /**
         * @param int $imageIndex
         * @return float
         */
        public function getMicroscopeSettingsNosepieceObjectiveApertureOpening($imageIndex);

        /**
         * @param int $imageIndex
         * @return float
         */
        public function getMicroscopeSettingsNosepieceObjectiveMagnification($imageIndex);

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getMicroscopeStandArticleOrSerialNumber($imageIndex);

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getMicroscopeStandMaker($imageIndex);

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getMicroscopeStandName($imageIndex);

        /**
         * @return string
         */
        public function getModificationDate();

        /**
         * @param int $imageIndex
         * @return float
         */
        public function getReproductionScaleInMetersPerPixelHorizontal($imageIndex);

        /**
         * @param int $imageIndex
         * @return float
         */
        public function getReproductionScaleInMetersPerPixelVertical($imageIndex);

        /**
         * @return string
         */
        public function getRootItemName();

        /**
         * @param int $imageIndex
         * @return boolean
         */
        public function isMicroscopePolarizerInLightPath($imageIndex);

        /**
         * @param int $imageIndex
         * @return bool
         */
        public function isMicroscopeSettingsLampShutterOpen($imageIndex);

        /**
         * @param int $imageIndex
         * @return boolean
         */
        public function isPostprocessingShading($imageIndex);

        /**
         * @param int $imageIndex
         * @return boolean
         */
        public function isPostprocessingSharpening($imageIndex);
    }