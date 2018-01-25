<?php
    namespace sednasoft\virmisco\util;

    use DOMDocument;
    use DOMElement;
    use DOMXPath;
    use ErrorException;
    use Exception;
    use sednasoft\virmisco\domain\error\XmlFileReadException;

    /**
     * Parses a .las file on instantiation and provides getters to all captured information items.
     */
    class LasGateway implements ILasTechnicalDataset
    {
        /** @var string[] */
        private $cameraCaptureFormat;
        /** @var string[] */
        private $cameraName;
        /** @var string[] */
        private $cameraSerialNumber;
        /** @var float[] */
        private $exposureDurationInSeconds;
        /** @var float[] */
        private $exposureGainFactor;
        /** @var float[] */
        private $focusPositionInMeters;
        /** @var int[] */
        private $histogramBlackClip;
        /** @var float[] */
        private $histogramGamma;
        /** @var int[] */
        private $histogramWhiteClip;
        /** @var int[] */
        private $imageColorDepthInBitsPerPixel;
        /** @var int */
        private $imageCount;
        /** @var string[] */
        private $imageCreationDate;
        /** @var string[] */
        private $imageCreationUserName;
        /** @var int[] */
        private $imageHeightInPixels;
        /** @var string[] */
        private $imageModificationDate;
        /** @var string[] */
        private $imageModificationUserName;
        /** @var string[] */
        private $imageName;
        /** @var string[] */
        private $imageWidthInPixels;
        /** @var string[] */
        private $microscopeCondenserArticleOrSerialNumber;
        /** @var string[] */
        private $microscopeCondenserMaker;
        /** @var string[] */
        private $microscopeCondenserName;
        /** @var string[] */
        private $microscopeCondenserTurretPrismArticleOrSerialNumber;
        /** @var string[] */
        private $microscopeCondenserTurretPrismMaker;
        /** @var string[] */
        private $microscopeCondenserTurretPrismName;
        /** @var string[] */
        private $microscopeDicTurretPrismArticleOrSerialNumber;
        /** @var string[] */
        private $microscopeDicTurretPrismMaker;
        /** @var string[] */
        private $microscopeDicTurretPrismName;
        /** @var string[] */
        private $microscopeMagnificationChangerArticleOrSerialNumber;
        /** @var string[] */
        private $microscopeMagnificationChangerMaker;
        /** @var string[] */
        private $microscopeMagnificationChangerName;
        /** @var string[] */
        private $microscopeNosepieceObjectiveArticleOrSerialNumber;
        /** @var string[] */
        private $microscopeNosepieceObjectiveMaker;
        /** @var string[] */
        private $microscopeNosepieceObjectiveName;
        /** @var string[] */
        private $microscopeNosepieceObjectiveType;
        /** @var int[] */
        private $microscopeNumberOfPorts;
        /** @var bool[] */
        private $microscopePolarizerInLightPath;
        /** @var string[] */
        private $microscopePortsArticleOrSerialNumber;
        /** @var string[] */
        private $microscopePortsMaker;
        /** @var string[] */
        private $microscopePortsName;
        /** @var float[] */
        private $microscopeSettingsApertureDiaphragmOpening;
        /** @var string[] */
        private $microscopeSettingsContrastMethod;
        /** @var float[] */
        private $microscopeSettingsFieldDiaphragmOpening;
        /** @var float[] */
        private $microscopeSettingsLampIntensity;
        /** @var bool[] */
        private $microscopeSettingsLampShutterOpen;
        /** @var float[] */
        private $microscopeSettingsMagnificationChangerMagnification;
        /** @var float[] */
        private $microscopeSettingsNosepieceObjectiveApertureOpening;
        /** @var float[] */
        private $microscopeSettingsNosepieceObjectiveMagnification;
        /** @var string[] */
        private $microscopeStandArticleOrSerialNumber;
        /** @var string[] */
        private $microscopeStandMaker;
        /** @var string[] */
        private $microscopeStandName;
        /** @var bool[] */
        private $postprocessingShading;
        /** @var bool[] */
        private $postprocessingSharpening;
        /** @var float[] */
        private $reproductionScaleInMetersPerPixelHorizontal;
        /** @var float[] */
        private $reproductionScaleInMetersPerPixelVertical;
        /** @var string */
        private $rootItemName;

        /**
         * @param $lasFileContents
         * @throws XmlFileReadException
         */
        public function __construct($lasFileContents)
        {
            $parser = new LocalizedQuantityParser();
            $xpath = $this->createXPathForXmlData($lasFileContents, 'l');
            $this->rootItemName = $xpath->evaluate('string(/l:Item/l:Name)');
            $itemQuery = "/l:Item/l:Children/l:Item[l:Type='Collection' and l:SubType='Image']";
            /** @var DOMElement $imageElement */
            foreach ($xpath->query($itemQuery) as $imageIndex => $imageElement) {
                $this->imageCount = $imageIndex + 1;
                /**
                 * @var array $microscope
                 * [contrast_method] => TL-DIC
                 * [stand_serial_number] => 319287
                 * [stand_name] => DM5500B
                 * [manualtube] => 1
                 * [ports_port_name] => VISUAL
                 * [ports_port_magnification] => 10
                 * [ports_port_article_no] => 11507807
                 * [ports_article_no] => 11505146
                 * [ports_num] => 2
                 * [magchanger_magnification] => 16
                 * [magchanger_article_no] => 11888096
                 * [dicturret_prism_name] => B1
                 * [nosepiece_objective_magnification] => 10
                 * [nosepiece_objective_article_no] => 11506259
                 * [nosepiece_objective_aperture] => 25
                 * [nosepiece_objective_type] => DRY
                 * [focus_position] => 25,481101 mm
                 * [zdrive_hardware_controller_generation] => 2
                 * [zdrive_initialization_distance] => 4
                 * [zdrive_initialization_position] => 5652921
                 * [lamp] => 89
                 * [condenser_name] => UP-S1-0.90
                 * [condenser_article_no] => 11505143
                 * [condenserturret_prism_name] => K2
                 * [tl_polarizer] => 1
                 * [tl_field_diaphragm] => 27
                 * [tl_aperture_diaphragm] => 23
                 * [tl_shutter_lamp] => 1
                 */
                $microscope = $this->gatherMicroscopeInformation($xpath, $imageElement);
                $this->cameraName[$imageIndex] = $xpath->evaluate('string(l:LasImage/l:Camera/l:Name)', $imageElement);
                $this->cameraSerialNumber[$imageIndex] = $xpath->evaluate('string(l:LasImage/l:Camera/l:Serial_Number)',
                    $imageElement);
                $this->cameraCaptureFormat[$imageIndex] = $xpath->evaluate('string(l:LasImage/l:Camera/l:Capture_Format)',
                    $imageElement);
                $this->imageWidthInPixels[$imageIndex] = $xpath->evaluate('number(l:LasImage/l:NumPixelsX)',
                    $imageElement);
                $this->imageHeightInPixels[$imageIndex] = $xpath->evaluate('number(l:LasImage/l:NumPixelsY)',
                    $imageElement);
                $this->imageColorDepthInBitsPerPixel[$imageIndex] = $xpath->evaluate('number(l:LasImage/l:BitDepth)',
                    $imageElement);
                $this->microscopeStandMaker[$imageIndex] = null;
                $this->microscopeStandName[$imageIndex] = isset($microscope['stand_name']) ? $microscope['stand_name'] : null;
                $this->microscopeStandArticleOrSerialNumber[$imageIndex] = isset($microscope['stand_serial_number'])
                    ? $microscope['stand_serial_number']
                    : null;
                $this->microscopeCondenserMaker[$imageIndex] = null;
                $this->microscopeCondenserName[$imageIndex] = isset($microscope['condenser_name']) ? $microscope['condenser_name'] : null;
                $this->microscopeCondenserArticleOrSerialNumber[$imageIndex] = isset($microscope['condenser_article_no'])
                    ? $microscope['condenser_article_no']
                    : null;
                $this->microscopeCondenserTurretPrismMaker[$imageIndex] = null;
                $this->microscopeCondenserTurretPrismName[$imageIndex] = isset($microscope['condenserturret_prism_name'])
                    ? $microscope['condenserturret_prism_name']
                    : null;
                $this->microscopeCondenserTurretPrismArticleOrSerialNumber[$imageIndex] = null;
                $this->microscopeNosepieceObjectiveMaker[$imageIndex] = null;
                $this->microscopeNosepieceObjectiveName[$imageIndex] = null;
                $this->microscopeNosepieceObjectiveType[$imageIndex] = isset($microscope['nosepiece_objective_type'])
                    ? $microscope['nosepiece_objective_type']
                    : null;
                $this->microscopeNosepieceObjectiveArticleOrSerialNumber[$imageIndex] = isset($microscope['nosepiece_objective_article_no'])
                    ? $microscope['nosepiece_objective_article_no']
                    : null;
                $this->microscopeDicTurretPrismMaker[$imageIndex] = null;
                $this->microscopeDicTurretPrismName[$imageIndex] = isset($microscope['dicturret_prism_name'])
                    ? $microscope['dicturret_prism_name']
                    : null;
                $this->microscopeDicTurretPrismArticleOrSerialNumber[$imageIndex] = null;
                $this->microscopeMagnificationChangerMaker[$imageIndex] = null;
                $this->microscopeMagnificationChangerName[$imageIndex] = null;
                $this->microscopeMagnificationChangerArticleOrSerialNumber[$imageIndex] = isset($microscope['magchanger_article_no'])
                    ? $microscope['magchanger_article_no']
                    : null;
                $this->microscopeNumberOfPorts[$imageIndex] = isset($microscope['ports_num']) ? $microscope['ports_num'] : 0;
                $this->microscopePortsMaker[$imageIndex] = null;
                $this->microscopePortsName[$imageIndex] = null;
                $this->microscopePortsArticleOrSerialNumber[$imageIndex] = isset($microscope['ports_article_no']) ? $microscope['ports_article_no'] : null;
                $this->microscopeSettingsContrastMethod[$imageIndex] = isset($microscope['contrast_method']) ? $microscope['contrast_method'] : null;
                $this->microscopeSettingsLampIntensity[$imageIndex] = isset($microscope['lamp']) ? floatval($microscope['lamp']) : 0;
                $this->microscopeSettingsLampShutterOpen[$imageIndex] = isset($microscope['tl_shutter_lamp'])
                    ? boolval($microscope['tl_shutter_lamp'])
                    : false;
                $this->microscopeSettingsApertureDiaphragmOpening[$imageIndex] = isset($microscope['tl_aperture_diaphragm'])
                    ? floatval($microscope['tl_aperture_diaphragm'])
                    : null;
                $this->microscopeSettingsFieldDiaphragmOpening[$imageIndex] = isset($microscope['tl_field_diaphragm'])
                    ? floatval($microscope['tl_field_diaphragm'])
                    : null;
                $this->microscopePolarizerInLightPath[$imageIndex] = isset($microscope['tl_polarizer'])
                    ? boolval($microscope['tl_polarizer'])
                    : false;
                $this->microscopeSettingsNosepieceObjectiveApertureOpening[$imageIndex] = isset($microscope['nosepiece_objective_aperture'])
                    ? floatval($microscope['nosepiece_objective_aperture'] / 100)
                    : null;
                $this->microscopeSettingsNosepieceObjectiveMagnification[$imageIndex] = isset($microscope['nosepiece_objective_magnification'])
                    ? floatval($microscope['nosepiece_objective_magnification'])
                    : null;
                $this->microscopeSettingsMagnificationChangerMagnification[$imageIndex] = isset($microscope['magchanger_magnification'])
                    ? floatval(sprintf('0.%se1', $microscope['magchanger_magnification']))
                    : null;
                $this->reproductionScaleInMetersPerPixelHorizontal[$imageIndex] = $xpath->evaluate('number(l:LasImage/l:XMetresPerPixel)',
                    $imageElement);
                $this->reproductionScaleInMetersPerPixelVertical[$imageIndex] = $xpath->evaluate('number(l:LasImage/l:YMetresPerPixel)',
                    $imageElement);
                $this->imageName[$imageIndex] = $xpath->evaluate('string(l:Name)', $imageElement);
                $this->imageCreationUserName[$imageIndex] = $xpath->evaluate('string(l:LasImage/l:CreationUserName)',
                    $imageElement);
                $this->imageCreationDate[$imageIndex] = min(
                    $xpath->evaluate('string(l:LasImage/l:AcquiredDate)', $imageElement),
                    $xpath->evaluate('string(l:LasImage/l:CreationDate)', $imageElement)
                );
                $this->imageModificationUserName[$imageIndex] = $xpath->evaluate('string(l:LasImage/l:ModificationUserName)',
                    $imageElement);
                $this->imageModificationDate[$imageIndex] = $xpath->evaluate('string(l:LastModifiedDate)',
                    $imageElement);
                $this->exposureDurationInSeconds[$imageIndex] = $parser->allowSiPrefixes(true)
                    ->clearUnitRegistrations()->registerUnit('min', 60)->registerUnit('s', 1)
                    ->parse($xpath->evaluate('string(l:LasImage/l:Camera/l:Exposure)', $imageElement));
                $this->exposureGainFactor[$imageIndex] = $parser->allowSiPrefixes(false)->clearUnitRegistrations()->registerUnit('x',
                    1)
                    ->parse($xpath->evaluate('string(l:LasImage/l:Camera/l:Gain)', $imageElement));
                $this->histogramGamma[$imageIndex] = $parser->allowSiPrefixes(false)->clearUnitRegistrations()
                    ->parse($xpath->evaluate('string(l:LasImage/l:Camera/l:Gamma)', $imageElement));
                $this->histogramBlackClip[$imageIndex] = $xpath->evaluate('number(l:LasImage/l:Camera/l:Black_Clip)',
                    $imageElement);
                $this->histogramWhiteClip[$imageIndex] = $xpath->evaluate('number(l:LasImage/l:Camera/l:White_Clip)',
                    $imageElement);
                $this->postprocessingShading[$imageIndex] = !preg_match(
                    '<^\((?:geen|none|aucun|kein|κανένας)\w*\)$>i',
                    $xpath->evaluate('string(l:LasImage/l:Camera/l:Shading)', $imageElement)
                );
                $this->postprocessingSharpening[$imageIndex] = !preg_match(
                    '<^uit|off|pois|aus>i',
                    $xpath->evaluate('string(l:LasImage/l:Camera/l:Sharpening)', $imageElement)
                );
                $this->focusPositionInMeters[$imageIndex] = $parser->allowSiPrefixes(true)->clearUnitRegistrations()->registerUnit('m',
                    1)
                    ->parse($microscope['focus_position']);
            }

        }

        /**
         * @param int $imageIndex
         * @return int
         */
        public function getCameraActivePixelsHor($imageIndex)
        {
            return null;
        }

        /**
         * @param int $imageIndex
         * @return int
         */
        public function getCameraActivePixelsVer($imageIndex)
        {
            return null;
        }

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getCameraAdcResolution($imageIndex)
        {
            return null;
        }

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getCameraCaptureFormat($imageIndex)
        {
            return $this->cameraCaptureFormat[$imageIndex];
        }

        /**
         * @param int $imageIndex
         * @return float
         */
        public function getCameraChipHeight($imageIndex)
        {
            return null;
        }

        /**
         * @param int $imageIndex
         * @return float
         */
        public function getCameraChipWidth($imageIndex)
        {
            return null;
        }

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getCameraColorFilterArray($imageIndex)
        {
            return null;
        }

        /**
         * @param int $imageIndex
         * @return int
         */
        public function getCameraDynamicRange($imageIndex)
        {
            return null;
        }

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getCameraMaker($imageIndex)
        {
            return null;
        }

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getCameraName($imageIndex)
        {
            return $this->cameraName[$imageIndex];
        }

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getCameraOpticalFormat($imageIndex)
        {
            return null;
        }

        /**
         * @param int $imageIndex
         * @return float
         */
        public function getCameraPixelHeight($imageIndex)
        {
            return null;
        }

        /**
         * @param int $imageIndex
         * @return float
         */
        public function getCameraPixelWidth($imageIndex)
        {
            return null;
        }

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getCameraProtectiveColorFilter($imageIndex)
        {
            return null;
        }

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getCameraReadoutNoise($imageIndex)
        {
            return null;
        }

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getCameraSensorArticleOrSerialNumber($imageIndex)
        {
            return null;
        }

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getCameraSensorMaker($imageIndex)
        {
            return null;
        }

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getCameraSensorName($imageIndex)
        {
            return null;
        }

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getCameraSerialNumber($imageIndex)
        {
            return $this->cameraSerialNumber[$imageIndex];
        }

        /**
         * @param int $imageIndex
         * @return int
         */
        public function getCameraSnrMax($imageIndex)
        {
            return null;
        }

        /**
         * @return string
         */
        public function getCreationDate()
        {
            return min($this->imageCreationDate);
        }

        /**
         * @param int $imageIndex
         * @return float
         */
        public function getExposureDurationInSeconds($imageIndex)
        {
            return $this->exposureDurationInSeconds[$imageIndex];
        }

        /**
         * @param int $imageIndex
         * @return float
         */
        public function getExposureGainFactor($imageIndex)
        {
            return $this->exposureGainFactor[$imageIndex];
        }

        /**
         * @param int $imageIndex
         * @return float
         */
        public function getFocusPositionInMeters($imageIndex)
        {
            return $this->focusPositionInMeters[$imageIndex];
        }

        /**
         * @param int $imageIndex
         * @return int
         */
        public function getHistogramBlackClip($imageIndex)
        {
            return $this->histogramBlackClip[$imageIndex];
        }

        /**
         * @param int $imageIndex
         * @return float
         */
        public function getHistogramGamma($imageIndex)
        {
            return $this->histogramGamma[$imageIndex];
        }

        /**
         * @param int $imageIndex
         * @return int
         */
        public function getHistogramWhiteClip($imageIndex)
        {
            return $this->histogramWhiteClip[$imageIndex];
        }

        /**
         * @param int $imageIndex
         * @return int
         */
        public function getImageColorDepthInBitsPerPixel($imageIndex)
        {
            return $this->imageColorDepthInBitsPerPixel[$imageIndex];
        }

        /**
         * @return int
         */
        public function getImageCount()
        {
            return $this->imageCount;
        }

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getImageCreationDate($imageIndex)
        {
            return $this->imageCreationDate[$imageIndex];
        }

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getImageCreationUserName($imageIndex)
        {
            return $this->imageCreationUserName[$imageIndex];
        }

        /**
         * @param int $imageIndex
         * @return int
         */
        public function getImageHeightInPixels($imageIndex)
        {
            return $this->imageHeightInPixels[$imageIndex];
        }

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getImageModificationDate($imageIndex)
        {
            return $this->imageModificationDate[$imageIndex];
        }

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getImageModificationUserName($imageIndex)
        {
            return $this->imageModificationUserName[$imageIndex];
        }

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getImageName($imageIndex)
        {
            return $this->imageName[$imageIndex];
        }

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getImageWidthInPixels($imageIndex)
        {
            return $this->imageWidthInPixels[$imageIndex];
        }

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getMicroscopeCameraMountAdapterArticleOrSerialNumber($imageIndex)
        {
            return null;
        }

        /**
         * @param int $imageIndex
         * @return float
         */
        public function getMicroscopeCameraMountAdapterMagnification($imageIndex)
        {
            return null;
        }

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getMicroscopeCameraMountAdapterMaker($imageIndex)
        {
            return null;
        }

        /**
         * @param int $imageIndex
         * @return null
         */
        public function getMicroscopeCameraMountAdapterName($imageIndex)
        {
            return null;
        }

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getMicroscopeCondenserArticleOrSerialNumber($imageIndex)
        {
            return $this->microscopeCondenserArticleOrSerialNumber[$imageIndex];
        }

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getMicroscopeCondenserMaker($imageIndex)
        {
            return $this->microscopeCondenserMaker[$imageIndex];
        }

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getMicroscopeCondenserName($imageIndex)
        {
            return $this->microscopeCondenserName[$imageIndex];
        }

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getMicroscopeCondenserTurretPrismArticleOrSerialNumber($imageIndex)
        {
            return $this->microscopeCondenserTurretPrismArticleOrSerialNumber[$imageIndex];
        }

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getMicroscopeCondenserTurretPrismMaker($imageIndex)
        {
            return $this->microscopeCondenserTurretPrismMaker[$imageIndex];
        }

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getMicroscopeCondenserTurretPrismName($imageIndex)
        {
            return $this->microscopeCondenserTurretPrismName[$imageIndex];
        }

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getMicroscopeDicTurretPrismArticleOrSerialNumber($imageIndex)
        {
            return $this->microscopeDicTurretPrismArticleOrSerialNumber[$imageIndex];
        }

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getMicroscopeDicTurretPrismMaker($imageIndex)
        {
            return $this->microscopeDicTurretPrismMaker[$imageIndex];
        }

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getMicroscopeDicTurretPrismName($imageIndex)
        {
            return $this->microscopeDicTurretPrismName[$imageIndex];
        }

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getMicroscopeMagnificationChangerArticleOrSerialNumber($imageIndex)
        {
            return $this->microscopeMagnificationChangerArticleOrSerialNumber[$imageIndex];
        }

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getMicroscopeMagnificationChangerMaker($imageIndex)
        {
            return $this->microscopeMagnificationChangerMaker[$imageIndex];
        }

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getMicroscopeMagnificationChangerName($imageIndex)
        {
            return $this->microscopeMagnificationChangerName[$imageIndex];
        }

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getMicroscopeNosepieceObjectiveArticleOrSerialNumber($imageIndex)
        {
            return $this->microscopeNosepieceObjectiveArticleOrSerialNumber[$imageIndex];
        }

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getMicroscopeNosepieceObjectiveMaker($imageIndex)
        {
            return $this->microscopeNosepieceObjectiveMaker[$imageIndex];
        }

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getMicroscopeNosepieceObjectiveName($imageIndex)
        {
            return $this->microscopeNosepieceObjectiveName[$imageIndex];
        }

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getMicroscopeNosepieceObjectiveType($imageIndex)
        {
            return $this->microscopeNosepieceObjectiveType[$imageIndex];
        }

        /**
         * @param int $imageIndex
         * @return int
         */
        public function getMicroscopeNumberOfPorts($imageIndex)
        {
            return $this->microscopeNumberOfPorts[$imageIndex];
        }

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getMicroscopePortsArticleOrSerialNumber($imageIndex)
        {
            return $this->microscopePortsArticleOrSerialNumber[$imageIndex];
        }

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getMicroscopePortsMaker($imageIndex)
        {
            return $this->microscopePortsMaker[$imageIndex];
        }

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getMicroscopePortsName($imageIndex)
        {
            return $this->microscopePortsName[$imageIndex];
        }

        /**
         * @param int $imageIndex
         * @return float
         */
        public function getMicroscopeSettingsApertureDiaphragmOpening($imageIndex)
        {
            return $this->microscopeSettingsApertureDiaphragmOpening[$imageIndex];
        }

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getMicroscopeSettingsContrastMethod($imageIndex)
        {
            return $this->microscopeSettingsContrastMethod[$imageIndex];
        }

        /**
         * @param int $imageIndex
         * @return null
         */
        public function getMicroscopeSettingsDicPrismPosition($imageIndex)
        {
            return null;
        }

        /**
         * @param int $imageIndex
         * @return float
         */
        public function getMicroscopeSettingsFieldDiaphragmOpening($imageIndex)
        {
            return $this->microscopeSettingsFieldDiaphragmOpening[$imageIndex];
        }

        /**
         * @param int $imageIndex
         * @return float
         */
        public function getMicroscopeSettingsLampIntensity($imageIndex)
        {
            return $this->microscopeSettingsLampIntensity[$imageIndex];
        }

        /**
         * @param int $imageIndex
         * @return float
         */
        public function getMicroscopeSettingsMagnificationChangerMagnification($imageIndex)
        {
            return $this->microscopeSettingsMagnificationChangerMagnification[$imageIndex];
        }

        /**
         * @param int $imageIndex
         * @return float
         */
        public function getMicroscopeSettingsNosepieceObjectiveApertureOpening($imageIndex)
        {
            return $this->microscopeSettingsNosepieceObjectiveApertureOpening[$imageIndex];
        }

        /**
         * @param int $imageIndex
         * @return float
         */
        public function getMicroscopeSettingsNosepieceObjectiveMagnification($imageIndex)
        {
            return $this->microscopeSettingsNosepieceObjectiveMagnification[$imageIndex];
        }

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getMicroscopeStandArticleOrSerialNumber($imageIndex)
        {
            return $this->microscopeStandArticleOrSerialNumber[$imageIndex];
        }

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getMicroscopeStandMaker($imageIndex)
        {
            return $this->microscopeStandMaker[$imageIndex];
        }

        /**
         * @param int $imageIndex
         * @return string
         */
        public function getMicroscopeStandName($imageIndex)
        {
            return $this->microscopeStandName[$imageIndex];
        }

        /**
         * @return string
         */
        public function getModificationDate()
        {
            return max($this->imageModificationDate);
        }

        /**
         * @param int $imageIndex
         * @return float
         */
        public function getReproductionScaleInMetersPerPixelHorizontal($imageIndex)
        {
            return $this->reproductionScaleInMetersPerPixelHorizontal[$imageIndex];
        }

        /**
         * @param int $imageIndex
         * @return float
         */
        public function getReproductionScaleInMetersPerPixelVertical($imageIndex)
        {
            return $this->reproductionScaleInMetersPerPixelVertical[$imageIndex];
        }

        /**
         * @return string
         */
        public function getRootItemName()
        {
            return $this->rootItemName;
        }

        /**
         * @param int $imageIndex
         * @return boolean
         */
        public function isMicroscopePolarizerInLightPath($imageIndex)
        {
            return $this->microscopePolarizerInLightPath[$imageIndex];
        }

        /**
         * @param int $imageIndex
         * @return bool
         */
        public function isMicroscopeSettingsLampShutterOpen($imageIndex)
        {
            return $this->microscopeSettingsLampShutterOpen[$imageIndex];
        }

        /**
         * @param int $imageIndex
         * @return boolean
         */
        public function isPostprocessingShading($imageIndex)
        {
            return $this->postprocessingShading[$imageIndex];
        }

        /**
         * @param int $imageIndex
         * @return boolean
         */
        public function isPostprocessingSharpening($imageIndex)
        {
            return $this->postprocessingSharpening[$imageIndex];
        }

        /**
         * @param string $fileContents
         * @param string $defaultNsPrefix
         * @return DOMXPath
         * @throws XmlFileReadException
         */
        private function createXPathForXmlData($fileContents, $defaultNsPrefix = null)
        {
            try {
                $xpath = null;
                set_error_handler(function ($type, $message, $file, $line) {
                    throw new ErrorException($message, 0, $type, $file, $line);
                });
                $document = new DOMDocument();
                $document->loadXML($fileContents);
                $xpath = new DOMXPath($document);
                if ($defaultNsPrefix) {
                    $xpath->registerNamespace($defaultNsPrefix, $document->documentElement->namespaceURI);
                }
                restore_error_handler();

                return $xpath;
            } catch (Exception $e) {
                throw new XmlFileReadException($e->getMessage(), $e->getCode(), $e);
            }
        }

        /**
         * @param DOMXPath $xpath
         * @param DOMElement $imageElement
         * @return array
         */
        private function gatherMicroscopeInformation(DOMXPath $xpath, DOMElement $imageElement)
        {
            $microscope = [];
            /** @var DOMElement $valueElement */
            foreach ($xpath->query('l:LasImage/l:ImageValues/l:ImageValue', $imageElement) as $valueElement) {
                $name = strtolower($xpath->evaluate('string(l:Name)', $valueElement));
                if (strpos($name, 'microscope_') === 0) {
                    $name = substr($name, 11);
                }
                $value = $xpath->evaluate('string(l:Value)', $valueElement);
                if ($xpath->evaluate('string(l:Type)', $valueElement) === 'System.Int32') {
                    $value = intval($value);
                }
                $microscope[$name] = $value;
            }

            return $microscope;
        }
    }
