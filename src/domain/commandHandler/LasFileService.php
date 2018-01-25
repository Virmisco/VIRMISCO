<?php
    namespace sednasoft\virmisco\domain\commandHandler;

    use Exception;
    use sednasoft\virmisco\domain\AbstractChainedCommandHandler;
    use sednasoft\virmisco\domain\command\AppendFocalPlaneImageToPhotomicrograph;
    use sednasoft\virmisco\domain\command\DigitizePhotomicrograph;
    use sednasoft\virmisco\domain\command\ImportPhotomicrographFromLasFile;
    use sednasoft\virmisco\singiere\AbstractCommand;
    use sednasoft\virmisco\singiere\Uuid;
    use sednasoft\virmisco\util\CustomLasDecorator;
    use sednasoft\virmisco\util\LasGateway;

    class LasFileService extends AbstractChainedCommandHandler
    {
        /**
         * Executes the command by loading the required aggregate from the repository and calling methods on the
         * aggregate. In case of exceptions they should be let bubble up. After the modifying method calls, the
         * repository should be instructed to save the aggregate.
         *
         * @param AbstractCommand $command
         * @throws Exception
         */
        public function execute(AbstractCommand $command)
        {
            if ($command instanceof ImportPhotomicrographFromLasFile) {
                $lasUri = $this->createAbsoluteUri($command->getRelativeUri());
                $images = $this->getImages(dirname($lasUri));
                $firstImageExif = exif_read_data($images[0]);
                $imageWidth = $firstImageExif["ImageWidth"];
                $imageHeight = $firstImageExif["ImageLength"];
                
                $imageSamples = $firstImageExif["BitsPerSample"];
                $imageDepth = $imageSamples[0] + $imageSamples[1] + $imageSamples[2];
                
                $las = new CustomLasDecorator(new LasGateway(file_get_contents($lasUri)));
                $photomicrographId = $command->getAggregateId();
                $digitizePhotomicrograph = new DigitizePhotomicrograph(
                    $photomicrographId,
                    $command->getSpecimenCarrierId(),
                    $command->getSequenceNumber(),
                    $las->getRootItemName(),
                    null, null, null,
                    $this->createFileRealPath($lasUri),
                    $this->createFileUri($lasUri),
                    $las->getCreationDate(),
                    $las->getModificationDate(),
                    $this->createPresentationUri($photomicrographId),
                    $las->getImageWidthInPixels(0) > 0 ? $las->getImageWidthInPixels(0) : $imageWidth,
                    $las->getImageHeightInPixels(0) > 0 ? $las->getImageHeightInPixels(0) : $imageHeight,
                    $las->getImageColorDepthInBitsPerPixel(0) > 0 ? $las->getImageColorDepthInBitsPerPixel(0) : $imageDepth,
                    $las->getReproductionScaleInMetersPerPixelHorizontal(0),
                    $las->getReproductionScaleInMetersPerPixelVertical(0),
                    $las->getCameraMaker(0),
                    $las->getCameraName(0),
                    $las->getCameraSerialNumber(0),
                    $las->getCameraSensorMaker(0),
                    $las->getCameraSensorName(0),
                    $las->getCameraSensorArticleOrSerialNumber(0),
                    $las->getCameraOpticalFormat(0),
                    $las->getCameraCaptureFormat(0),
                    $las->getCameraChipWidth(0),
                    $las->getCameraChipHeight(0),
                    $las->getCameraPixelWidth(0),
                    $las->getCameraPixelHeight(0),
                    $las->getCameraActivePixelsHor(0),
                    $las->getCameraActivePixelsVer(0),
                    $las->getCameraColorFilterArray(0),
                    $las->getCameraProtectiveColorFilter(0),
                    $las->getCameraAdcResolution(0),
                    $las->getCameraDynamicRange(0),
                    $las->getCameraSnrMax(0),
                    $las->getCameraReadoutNoise(0),
                    $las->getMicroscopeStandMaker(0),
                    $las->getMicroscopeStandName(0),
                    $las->getMicroscopeStandArticleOrSerialNumber(0),
                    $las->getMicroscopeCondenserMaker(0),
                    $las->getMicroscopeCondenserName(0),
                    $las->getMicroscopeCondenserArticleOrSerialNumber(0),
                    $las->getMicroscopeCondenserTurretPrismMaker(0),
                    $las->getMicroscopeCondenserTurretPrismName(0),
                    $las->getMicroscopeCondenserTurretPrismArticleOrSerialNumber(0),
                    $las->getMicroscopeNosepieceObjectiveMaker(0),
                    $las->getMicroscopeNosepieceObjectiveName(0),
                    $las->getMicroscopeNosepieceObjectiveArticleOrSerialNumber(0),
                    $las->getMicroscopeNosepieceObjectiveType(0),
                    $las->getMicroscopeSettingsNosepieceObjectiveApertureOpening(0),
                    $las->getMicroscopeSettingsNosepieceObjectiveMagnification(0),
                    $las->getMicroscopeDicTurretPrismMaker(0),
                    $las->getMicroscopeDicTurretPrismName(0),
                    $las->getMicroscopeDicTurretPrismArticleOrSerialNumber(0),
                    $las->getMicroscopeMagnificationChangerMaker(0),
                    $las->getMicroscopeMagnificationChangerName(0),
                    $las->getMicroscopeMagnificationChangerArticleOrSerialNumber(0),
                    null,
                    $las->getMicroscopePortsMaker(0),
                    $las->getMicroscopePortsName(0),
                    $las->getMicroscopePortsArticleOrSerialNumber(0),
                    $las->getMicroscopeCameraMountAdapterMaker(0),
                    $las->getMicroscopeCameraMountAdapterName(0),
                    $las->getMicroscopeCameraMountAdapterMagnification(0),
                    $las->getMicroscopeCameraMountAdapterArticleOrSerialNumber(0),
                    $las->getMicroscopeSettingsContrastMethod(0),
                    $las->getMicroscopeSettingsDicPrismPosition(0),
                    $las->getMicroscopeSettingsApertureDiaphragmOpening(0),
                    $las->getMicroscopeSettingsFieldDiaphragmOpening(0),
                    null,
                    $las->getMicroscopeSettingsMagnificationChangerMagnification(0)
                );
                $this->dispatchToNextHandler($digitizePhotomicrograph);
                if($las->getImageCount() > 0)
                	$this->createImagesFromLas($las, $photomicrographId);
                else
                	$this->createImagesFromDir($images, $photomicrographId);
            } else {
                $this->dispatchToNextHandler($command);
            }
        }
        
        private function getImages($dir) {
        	$types = array("jpg", "jpeg", "tif", "bmp", "png");
        	$files = scandir($dir);
        	$erg = array();
        	foreach($files as $file) {
        		$type = array_reverse(explode(".", $file))[0];
        		if(!in_array($type, $types)) {
				continue;
			}
        		array_push($erg, "${dir}/${file}");
        	}
        	return $erg;
        }
        
        public function createImagesFromDir($files, $photomicrographId) {
        	foreach($files as $file) {
       			$exif = exif_read_data($file);
			$ts = $exif["FileDateTime"] * 1;
			$date = date_create();
			date_timestamp_set($date, $ts);
			$date = date_format($date, 'Y-m-d\TH:i:s');
			$this->dispatchToNextHandler(
				new AppendFocalPlaneImageToPhotomicrograph(
					$photomicrographId,
					0, //$las->getFocusPositionInMeters($i),
					$file, //$this->createImageFileRealPath($lasUri, $las->getImageName($i)),
					null,
					$date,
					$date,
					null,
					0,
					0,
					0,
					0,
					0,
					true,
					true
				)
			);
        	}
        }
        
        public function createImagesFromLas($las, $photomicrographId) {
        	for ($i = 0, $m = $las->getImageCount(); $i < $m; $i++) {
                    $planeFileUri = null;
                    $planePresentationUri = null;
                    $this->dispatchToNextHandler(
                        new AppendFocalPlaneImageToPhotomicrograph(
                            $photomicrographId,
                            $las->getFocusPositionInMeters($i),
                            $this->createImageFileRealPath($lasUri, $las->getImageName($i)),
                            $planeFileUri,
                            $las->getImageCreationDate($i),
                            $las->getImageModificationDate($i),
                            $planePresentationUri,
                            $las->getExposureDurationInSeconds($i),
                            $las->getExposureGainFactor($i),
                            $las->getHistogramGamma($i),
                            $las->getHistogramBlackClip($i),
                            $las->getHistogramWhiteClip($i),
                            $las->isPostprocessingShading($i),
                            $las->isPostprocessingSharpening($i)
                        )
                    );
                }
        }

        /**
         * @param $relativeUri
         * @return string
         */
        private function createAbsoluteUri($relativeUri)
        {
            return RAWPATH."/".$relativeUri;
        }

        /**
         * @param $lasUri
         * @return mixed
         */
        private function createFileRealPath($lasUri)
        {
		$uri = explode("/", $lasUri);
		$uri = array_slice($uri, 6);
		$uri = implode("/", $uri);
		$uri = DERIVATIVESPATH."/${uri}";
		return str_replace("/.las", "/focal-series.zip", $uri);
/*
            return str_replace(
                ['http://185.15.246.7/pix/photomicrographs/', '/.las'],
                ['/data/pix/derivatives/', '/focal-series.zip'],
                $lasUri
            );
*/
        }

        /**
         * @param $lasUri
         * @return mixed
         */
        private function createFileUri($lasUri)
        {
		$uri = explode("/", $lasUri);
		$uri = array_slice($uri, 6);
		$uri = implode("/", $uri);
		$uri = DERIVATIVESURL."/${uri}";
		return str_replace(["/.las", " "], ["/focal-series.zip", "%20"], $uri);
/*
            return str_replace(
                ['http://185.15.246.7/pix/photomicrographs/', '/.las', ' '],
                ['http://virmisco.org/media/derivatives/', '/focal-series.zip', '%20'],
                $lasUri
            );
*/
        }

        /**
         * @param $lasUri
         * @param $imageName
         * @return mixed
         */
        private function createImageFileRealPath($lasUri, $imageName)
        {
            return str_replace(
                ['http://185.15.246.7/', '/.las'],
                ['/data/', '/' . $imageName],
                $lasUri
            );
        }

        /**
         * @param Uuid $photomicrographId
         * @return string
         */
        private function createPresentationUri(Uuid $photomicrographId)
        {
            return '/viewer/?id=' . $photomicrographId;
        }
    }
