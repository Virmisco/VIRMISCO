<?php
    namespace sednasoft\virmisco\domain\commandHandler;

    use DateTime;
    use Exception;
    use sednasoft\virmisco\domain\AbstractChainedCommandHandler;
    use sednasoft\virmisco\domain\command\ChangeDicPrismPositionOfPhotomicrograph;
    use sednasoft\virmisco\domain\command\ChangeMentionedNameOfOrganismOnSpecimenCarrier;
    use sednasoft\virmisco\domain\command\ChangeNameOriginOfOrganismOnSpecimenCarrier;
    use sednasoft\virmisco\domain\command\ChangeValidNameOfOrganismOnSpecimenCarrier;
    use sednasoft\virmisco\domain\command\ClearSynonymsOfOrganismOnSpecimenCarrier;
    use sednasoft\virmisco\domain\command\ConvertImagesOfPhotomicrograph;
    use sednasoft\virmisco\domain\command\DeletePhotomicrograph;
    use sednasoft\virmisco\domain\command\DiscardOrganismDescription;
    use sednasoft\virmisco\domain\command\ManipulateGathering;
    use sednasoft\virmisco\domain\command\ManipulateOrganismOnSpecimenCarrier;
    use sednasoft\virmisco\domain\command\ManipulatePhotomicrograph;
    use sednasoft\virmisco\domain\command\ManipulateSpecimenCarrier;
    use sednasoft\virmisco\domain\command\ProvideAuthorshipOfPhotomicrograph;
    use sednasoft\virmisco\domain\command\ProvideHigherTaxaForOrganismOnSpecimenCarrier;
    use sednasoft\virmisco\domain\command\ProvideSynonymForOrganismOnSpecimenCarrier;
    use sednasoft\virmisco\domain\command\RenamePhotomicrograph;
    use sednasoft\virmisco\domain\entity\Gathering;
    use sednasoft\virmisco\domain\entity\Photomicrograph;
    use sednasoft\virmisco\domain\entity\SpecimenCarrier;
    use sednasoft\virmisco\singiere\AbstractCommand;

    class EditorCommandHandler extends AbstractChainedCommandHandler
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
            if ($command instanceof ManipulateGathering) {
                /** @var Gathering $gathering */
                $gathering = $this->getRepository()->load(Gathering::class, $command->getAggregateId());
                $gathering->manipulate(
                    $command->getJournalNumber(),
                    // ensure valid ISO 8601 date(time)
                    //(new DateTime($command->getSamplingDateAfter()))->format(DateTime::ATOM),
                    //(new DateTime($command->getSamplingDateBefore()))->format(DateTime::ATOM),
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
            } elseif ($command instanceof ManipulateSpecimenCarrier) {
                /** @var SpecimenCarrier $specimenCarrier */
                $specimenCarrier = $this->getRepository()->load(SpecimenCarrier::class, $command->getAggregateId());
                $specimenCarrier->manipulate(
                    $command->getCarrierNumber(),
                    $command->getPreparationType(),
                    $command->getOwner(),
                    $command->getPreviousCollection(),
                    $command->getLabelTranscript()
                );
                $this->getRepository()->save($specimenCarrier);
            } elseif ($command instanceof ManipulateOrganismOnSpecimenCarrier) {
                /** @var SpecimenCarrier $specimenCarrier */
                $specimenCarrier = $this->getRepository()->load(SpecimenCarrier::class, $command->getAggregateId());
                $specimenCarrier->manipulateOrganism(
                    $command->getOldSequenceNumber(),
                    $command->getSequenceNumber(),
                    $command->getSex(),
                    $command->getPhaseOrStage(),
                    $command->getRemarks()
                );
                $this->getRepository()->save($specimenCarrier);
            } elseif ($command instanceof ChangeMentionedNameOfOrganismOnSpecimenCarrier) {
                /** @var SpecimenCarrier $specimenCarrier */
                $specimenCarrier = $this->getRepository()->load(SpecimenCarrier::class, $command->getAggregateId());
                $specimenCarrier->changeMentionedNameOfOrganism(
                    $command->getSequenceNumber(),
                    $command->getGenus(),
                    $command->getSubgenus(),
                    $command->getSpecificEpithet(),
                    $command->getInfraspecificEpithet(),
                    $command->getAuthorship(),
                    $command->getYear(),
                    $command->isParenthesized()
                );
                $this->getRepository()->save($specimenCarrier);
            } elseif ($command instanceof ChangeNameOriginOfOrganismOnSpecimenCarrier) {
                /** @var SpecimenCarrier $specimenCarrier */
                $specimenCarrier = $this->getRepository()->load(SpecimenCarrier::class, $command->getAggregateId());
                $specimenCarrier->changeNameOriginOfOrganism(
                    $command->getSequenceNumber(),
                    $command->getTypeStatus(),
                    $command->getIdentifier(),
                    $command->getQualifier()
                );
                $this->getRepository()->save($specimenCarrier);
            } elseif ($command instanceof ChangeValidNameOfOrganismOnSpecimenCarrier) {
                /** @var SpecimenCarrier $specimenCarrier */
                $specimenCarrier = $this->getRepository()->load(SpecimenCarrier::class, $command->getAggregateId());
                $specimenCarrier->changeValidNameOfOrganism(
                    $command->getSequenceNumber(),
                    $command->getGenus(),
                    $command->getSubgenus(),
                    $command->getSpecificEpithet(),
                    $command->getInfraspecificEpithet(),
                    $command->getAuthorship(),
                    $command->getYear(),
                    $command->isParenthesized()
                );
                $this->getRepository()->save($specimenCarrier);
            } elseif ($command instanceof ClearSynonymsOfOrganismOnSpecimenCarrier) {
                /** @var SpecimenCarrier $specimenCarrier */
                $specimenCarrier = $this->getRepository()->load(SpecimenCarrier::class, $command->getAggregateId());
                $specimenCarrier->clearSynonymsOfOrganism(
                    $command->getSequenceNumber()
                );
                $this->getRepository()->save($specimenCarrier);
            } elseif ($command instanceof ProvideHigherTaxaForOrganismOnSpecimenCarrier) {
                /** @var SpecimenCarrier $specimenCarrier */
                $specimenCarrier = $this->getRepository()->load(SpecimenCarrier::class, $command->getAggregateId());
                $specimenCarrier->provideHigherTaxaForOrganism(
                    $command->getSequenceNumber(),
                    $command->getHigherTaxa()
                );
                $this->getRepository()->save($specimenCarrier);
            } elseif ($command instanceof ProvideSynonymForOrganismOnSpecimenCarrier) {
                /** @var SpecimenCarrier $specimenCarrier */
                $specimenCarrier = $this->getRepository()->load(SpecimenCarrier::class, $command->getAggregateId());
                $specimenCarrier->provideSynonymForOrganism(
                    $command->getSequenceNumber(),
                    $command->getGenus(),
                    $command->getSubgenus(),
                    $command->getSpecificEpithet(),
                    $command->getInfraspecificEpithet(),
                    $command->getAuthorship(),
                    $command->getYear(),
                    $command->isParenthesized()
                );
                $this->getRepository()->save($specimenCarrier);
            } elseif ($command instanceof DiscardOrganismDescription) {
                /** @var SpecimenCarrier $specimenCarrier */
                $specimenCarrier = $this->getRepository()->load(SpecimenCarrier::class, $command->getAggregateId());
                $specimenCarrier->discardOrganismDescription($command->getSequenceNumber());
                $this->getRepository()->save($specimenCarrier);
            } elseif ($command instanceof ChangeDicPrismPositionOfPhotomicrograph) {
                /** @var Photomicrograph $photomicrograph */
                $photomicrograph = $this->getRepository()->load(Photomicrograph::class, $command->getAggregateId());
                $photomicrograph->changeDicPrismPosition($command->getDicPrismPosition());
                $this->getRepository()->save($photomicrograph);
            } elseif ($command instanceof ConvertImagesOfPhotomicrograph) {
                /** @var Photomicrograph $photomicrograph */
                $photomicrograph = $this->getRepository()->load(Photomicrograph::class, $command->getAggregateId());
                $photomicrograph->convertImages();
                $this->getRepository()->save($photomicrograph);
            } elseif ($command instanceof DeletePhotomicrograph) {
                /** @var Photomicrograph $photomicrograph */
                $photomicrograph = $this->getRepository()->load(Photomicrograph::class, $command->getAggregateId());
                $photomicrograph->deletePhotomicrograph();
                $this->getRepository()->save($photomicrograph);
            } elseif ($command instanceof RenamePhotomicrograph) {
                /** @var Photomicrograph $photomicrograph */
                $photomicrograph = $this->getRepository()->load(Photomicrograph::class, $command->getAggregateId());
                $photomicrograph->rename($command->getTitle());
                $this->getRepository()->save($photomicrograph);
            } elseif ($command instanceof ProvideAuthorshipOfPhotomicrograph) {
                /** @var Photomicrograph $photomicrograph */
                $photomicrograph = $this->getRepository()->load(Photomicrograph::class, $command->getAggregateId());
                $photomicrograph->provideAuthorship(
                    $command->getCreatorCapturingDigitalMaster(),
                    $command->getCreatorProcessingDerivatives()
                );
                $this->getRepository()->save($photomicrograph);
            } elseif ($command instanceof ManipulatePhotomicrograph) {
                /** @var Photomicrograph $photomicrograph */
                $photomicrograph = $this->getRepository()->load(Photomicrograph::class, $command->getAggregateId());
                $photomicrograph->manipulate(
                    $command->getTitle(),
                    $command->getDetailOfPhotomicrographId(),
                    $command->getDetailOfHotspotX(),
                    $command->getDetailOfHotspotY(),
                    $command->getCreatorCapturing(),
                    $command->getCreatorProcessing(),
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
                    $command->getMicroscopeSettingsMagnificationChangerMagnification()
                );
                $this->getRepository()->save($photomicrograph);
            } else {
                $this->dispatchToNextHandler($command);
            }
        }
    }
