<?php
    namespace sednasoft\virmisco\domain\command;

    use sednasoft\virmisco\singiere\AbstractCommand;
    use sednasoft\virmisco\singiere\Uuid;

    class AppendFocalPlaneImageToPhotomicrograph extends AbstractCommand
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
         * Creates a new instance. Subclasses should add more parameters for payload.
         *
         * @param Uuid $photomicrographId The unique identifier of the aggregate to receive this command.
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
            Uuid $photomicrographId,
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
            parent::__construct($photomicrographId);
            $this->focusPosition = (float)$focusPosition;
            $this->fileRealPath = (string)$fileRealPath;
            $this->fileUri = (string)$fileUri;
            $this->fileCreationTime = (string)$fileCreationTime;
            $this->fileModificationTime = (string)$fileModificationTime;
            $this->presentationUri = (string)$presentationUri;
            $this->exposureSettingsDuration = (float)$exposureSettingsDuration;
            $this->exposureSettingsGain = (float)$exposureSettingsGain;
            $this->histogramSettingsGamma = (float)$histogramSettingsGamma;
            $this->histogramSettingsBlackClip = (int)$histogramSettingsBlackClip;
            $this->histogramSettingsWhiteClip = (int)$histogramSettingsWhiteClip;
            $this->postProcessingSettingsShading = (bool)$postProcessingSettingsShading;
            $this->postProcessingSettingsSharpening = (bool)$postProcessingSettingsSharpening;
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
