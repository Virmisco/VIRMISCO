<?php
    namespace sednasoft\virmisco\domain\commandHandler;

    use Exception;
    use sednasoft\virmisco\domain\AbstractChainedCommandHandler;
    use sednasoft\virmisco\domain\command\AppendFocalPlaneImageToPhotomicrograph;
    use sednasoft\virmisco\domain\command\AssignSynonymToTaxon;
    use sednasoft\virmisco\domain\command\DescribeOrganismOnSpecimenCarrier;
    use sednasoft\virmisco\domain\command\DesignateOrganismOnSpecimenCarrierAsType;
    use sednasoft\virmisco\domain\command\DigitizePhotomicrograph;
    use sednasoft\virmisco\domain\command\IdentifyOrganismOnSpecimenCarrier;
    use sednasoft\virmisco\domain\command\LogGathering;
    use sednasoft\virmisco\domain\command\RecordSpecimenCarrier;
    use sednasoft\virmisco\domain\command\RegisterTaxon;
    use sednasoft\virmisco\domain\command\ScanSpecimenCarrier;
    use sednasoft\virmisco\domain\entity\Gathering;
    use sednasoft\virmisco\domain\entity\Photomicrograph;
    use sednasoft\virmisco\domain\entity\SpecimenCarrier;
    use sednasoft\virmisco\domain\entity\Taxon;
    use sednasoft\virmisco\singiere\AbstractCommand;

    class DbImportCommandHandler extends AbstractChainedCommandHandler
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
            if ($command instanceof LogGathering) {
                /** @var Gathering $gathering */
                $gathering = $this->getRepository()->create(Gathering::class, $command->getAggregateId());
                $gathering->log(
                    $command->getJournalNumber(),
                    $command->getSamplingDateAfter(),
                    $command->getSamplingDateBefore(),
                    $command->getAgentPerson(),
                    $command->getAgentOrganization(),
                    $command->getLocationCountry(),
                    $command->getLocationProvince(),
                    $command->getLocationRegion(),
                    $command->getLocationPlace(),
                    $command->getRemarks()
                );
                $this->getRepository()->save($gathering);
            } elseif ($command instanceof RecordSpecimenCarrier) {
                /** @var SpecimenCarrier $specimenCarrier */
                $specimenCarrier = $this->getRepository()->create(SpecimenCarrier::class, $command->getAggregateId());
                $specimenCarrier->record(
                    $command->getGatheringId(),
                    $command->getCarrierNumber(),
                    $command->getPreparationType(),
                    $command->getOwner(),
                    $command->getPreviousCollection(),
                    $command->getLabelTranscript()
                );
                $this->getRepository()->save($specimenCarrier);
                /** @var Gathering $gathering */
                $gathering = $this->getRepository()->load(Gathering::class, $command->getGatheringId());
                $gathering->preserveSpecimen($specimenCarrier->getAggregateId());
                $this->getRepository()->save($gathering);
            } elseif ($command instanceof DescribeOrganismOnSpecimenCarrier) {
                /** @var SpecimenCarrier $specimenCarrier */
                $specimenCarrier = $this->getRepository()->load(SpecimenCarrier::class, $command->getAggregateId());
                $specimenCarrier->describeOrganism(
                    $command->getSequenceNumber(),
                    $command->getSex(),
                    $command->getPhaseOrStage(),
                    $command->getRemarks()
                );
                $this->getRepository()->save($specimenCarrier);
            } elseif ($command instanceof ScanSpecimenCarrier) {
                /** @var SpecimenCarrier $specimenCarrier */
                $specimenCarrier = $this->getRepository()->load(SpecimenCarrier::class, $command->getAggregateId());
                $specimenCarrier->scanToImageFile(
                    $command->getRealPath(),
                    $command->getUri(),
                    $command->getCreationTime(),
                    $command->getModificationTime()
                );
                $this->getRepository()->save($specimenCarrier);
            } elseif ($command instanceof DigitizePhotomicrograph) {
                /** @var Photomicrograph $photomicrograph */
                $photomicrograph = $this->getRepository()->create(Photomicrograph::class, $command->getAggregateId());
                $photomicrograph->digitizeFromOrganism(
                    $command->getSpecimenCarrierId(),
                    $command->getSequenceNumber(),
                    $command->getTitle(),
                    $command->getDetailOfPhotomicrographId(),
                    $command->getDetailOfHotspotX(),
                    $command->getDetailOfHotspotY(),
                    $command->getFileRealPath(),
                    $command->getFileUri(),
                    $command->getFileCreationTime(),
                    $command->getFileModificationTime(),
                    $command->getPresentationUri(),
                    $command->getDigitizationDataWidth(),
                    $command->getDigitizationDataHeight(),
                    $command->getDigitizationDataColorDepth(),
                    $command->getDigitizationDataReproductionScaleHorizontal(),
                    $command->getDigitizationDataReproductionScaleVertical(),
                    $command->getCameraCameraMaker(),
                    $command->getCameraCameraName(),
                    $command->getCameraCameraArticleOrSerialNumber(),
                    $command->getCameraSensorMaker(),
                    $command->getCameraSensorName(),
                    $command->getCameraSensorArticleOrSerialNumber(),
                    $command->getCameraOpticalFormat(),
                    $command->getCameraCaptureFormat(),
                    $command->getCameraChipWidth(),
                    $command->getCameraChipHeight(),
                    $command->getCameraPixelWidth(),
                    $command->getCameraPixelHeight(),
                    $command->getCameraActivePixelsHor(),
                    $command->getCameraActivePixelsVer(),
                    $command->getCameraColorFilterArray(),
                    $command->getCameraProtectiveColorFilter(),
                    $command->getCameraAdcResolution(),
                    $command->getCameraDynamicRange(),
                    $command->getCameraSnrMax(),
                    $command->getCameraReadoutNoise(),
                    $command->getMicroscopeStandMaker(),
                    $command->getMicroscopeStandName(),
                    $command->getMicroscopeStandArticleOrSerialNumber(),
                    $command->getMicroscopeCondenserMaker(),
                    $command->getMicroscopeCondenserName(),
                    $command->getMicroscopeCondenserArticleOrSerialNumber(),
                    $command->getMicroscopeCondenserTurretPrismMaker(),
                    $command->getMicroscopeCondenserTurretPrismName(),
                    $command->getMicroscopeCondenserTurretPrismArticleOrSerialNumber(),
                    $command->getMicroscopeNosepieceObjectiveMaker(),
                    $command->getMicroscopeNosepieceObjectiveName(),
                    $command->getMicroscopeNosepieceObjectiveArticleOrSerialNumber(),
                    $command->getMicroscopeNosepieceObjectiveType(),
                    $command->getMicroscopeNosepieceObjectiveNumericalAperture(),
                    $command->getMicroscopeNosepieceObjectiveMagnification(),
                    $command->getMicroscopeDicTurretPrismMaker(),
                    $command->getMicroscopeDicTurretPrismName(),
                    $command->getMicroscopeDicTurretPrismArticleOrSerialNumber(),
                    $command->getMicroscopeMagnificationChangerMaker(),
                    $command->getMicroscopeMagnificationChangerName(),
                    $command->getMicroscopeMagnificationChangerArticleOrSerialNumber(),
                    $command->getMicroscopeNumberOfPorts(),
                    $command->getMicroscopePortsMaker(),
                    $command->getMicroscopePortsName(),
                    $command->getMicroscopePortsArticleOrSerialNumber(),
                    $command->getMicroscopeCameraMountAdapterMaker(),
                    $command->getMicroscopeCameraMountAdapterName(),
                    $command->getMicroscopeCameraMountAdapterMagnification(),
                    $command->getMicroscopeCameraMountAdapterArticleOrSerialNumber(),
                    $command->getMicroscopeSettingsContrastMethod(),
                    $command->getMicroscopeSettingsDicPrismPosition(),
                    $command->getMicroscopeSettingsApertureDiaphragmOpening(),
                    $command->getMicroscopeSettingsFieldDiaphragmOpening(),
                    $command->isMicroscopeSettingsPolarizerInLightPath(),
                    $command->getMicroscopeSettingsMagnificationChangerMagnification()
                );
                $this->getRepository()->save($photomicrograph);
                /** @var SpecimenCarrier $specimenCarrier */
                $specimenCarrier = $this->getRepository()->load(SpecimenCarrier::class,
                    $command->getSpecimenCarrierId());
                $specimenCarrier->digitizeOrganism($command->getSequenceNumber(), $command->getAggregateId());
                $this->getRepository()->save($specimenCarrier);
            } elseif ($command instanceof AppendFocalPlaneImageToPhotomicrograph) {
                /** @var Photomicrograph $photomicrograph */
                $photomicrograph = $this->getRepository()->load(Photomicrograph::class, $command->getAggregateId());
                $photomicrograph->appendFocalPlaneImage(
                    $command->getFocusPosition(),
                    $command->getFileRealPath(),
                    $command->getFileUri(),
                    $command->getFileCreationTime(),
                    $command->getFileModificationTime(),
                    $command->getPresentationUri(),
                    $command->getExposureSettingsDuration(),
                    $command->getExposureSettingsGain(),
                    $command->getHistogramSettingsGamma(),
                    $command->getHistogramSettingsBlackClip(),
                    $command->getHistogramSettingsWhiteClip(),
                    $command->isPostProcessingSettingsShading(),
                    $command->isPostProcessingSettingsSharpening()
                );
                $this->getRepository()->save($photomicrograph);
            } else {
                $this->dispatchToNextHandler($command);
            }
        }
    }
