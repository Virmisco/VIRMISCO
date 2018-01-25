<?php
    namespace sednasoft\virmisco\domain\event;

    use sednasoft\virmisco\singiere\AbstractEvent;

    class FocalPlaneImageAppended extends AbstractEvent
    {
        /** @var float */
        private $exposureSettingsDuration;
        /** @var float */
        private $exposureSettingsGain;
        /** @var string */
        private $fileCreationTime;
        /** @var string */
        private $fileModificationTime;
        /** @var string */
        private $fileRealPath;
        /** @var string */
        private $fileUri;
        /** @var float */
        private $focusPosition;
        /** @var int */
        private $histogramSettingsBlackClip;
        /** @var float */
        private $histogramSettingsGamma;
        /** @var int */
        private $histogramSettingsWhiteClip;
        /** @var bool */
        private $postProcessingSettingsShading;
        /** @var bool */
        private $postProcessingSettingsSharpening;
        /** @var string */
        private $presentationUri;

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
        public function __construct(
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
            parent::__construct();
            $this->focusPosition = $focusPosition;
            $this->fileRealPath = $fileRealPath;
            $this->fileUri = $fileUri;
            $this->fileCreationTime = $fileCreationTime;
            $this->fileModificationTime = $fileModificationTime;
            $this->presentationUri = $presentationUri;
            $this->exposureSettingsDuration = $exposureSettingsDuration;
            $this->exposureSettingsGain = $exposureSettingsGain;
            $this->histogramSettingsGamma = $histogramSettingsGamma;
            $this->histogramSettingsBlackClip = $histogramSettingsBlackClip;
            $this->histogramSettingsWhiteClip = $histogramSettingsWhiteClip;
            $this->postProcessingSettingsShading = $postProcessingSettingsShading;
            $this->postProcessingSettingsSharpening = $postProcessingSettingsSharpening;
        }

        /**
         * @return float
         */
        public function getExposureSettingsDuration()
        {
            return $this->exposureSettingsDuration;
        }

        /**
         * @return float
         */
        public function getExposureSettingsGain()
        {
            return $this->exposureSettingsGain;
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
         * @return float
         */
        public function getFocusPosition()
        {
            return $this->focusPosition;
        }

        /**
         * @return int
         */
        public function getHistogramSettingsBlackClip()
        {
            return $this->histogramSettingsBlackClip;
        }

        /**
         * @return float
         */
        public function getHistogramSettingsGamma()
        {
            return $this->histogramSettingsGamma;
        }

        /**
         * @return int
         */
        public function getHistogramSettingsWhiteClip()
        {
            return $this->histogramSettingsWhiteClip;
        }

        /**
         * @return string
         */
        public function getPresentationUri()
        {
            return $this->presentationUri;
        }

        /**
         * @return boolean
         */
        public function isPostProcessingSettingsShading()
        {
            return $this->postProcessingSettingsShading;
        }

        /**
         * @return boolean
         */
        public function isPostProcessingSettingsSharpening()
        {
            return $this->postProcessingSettingsSharpening;
        }
    }
