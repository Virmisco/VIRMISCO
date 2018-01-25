<?xml version="1.0" encoding="UTF-8"?>
<?php
    use sednasoft\virmisco\readlayer\AugmentedScientificName;
    use sednasoft\virmisco\readlayer\entity\CarrierScan;
    use sednasoft\virmisco\readlayer\entity\FocalPlaneImage;
    use sednasoft\virmisco\readlayer\entity\Gathering;
    use sednasoft\virmisco\readlayer\entity\Organism;
    use sednasoft\virmisco\readlayer\entity\Photomicrograph;
    use sednasoft\virmisco\readlayer\entity\SpecimenCarrier;

    /** @var DateTimeInterface $timestamp */
    /** @var CarrierScan[] $carrierScans */
    /** @var Organism[] $carrierOrganisms */
    /** @var FocalPlaneImage[] $focalPlaneImages */
    /** @var Gathering $gathering */
    /** @var Organism $organism */
    /** @var Photomicrograph $photomicrograph */
    /** @var SpecimenCarrier $specimenCarrier */
    /** @var AugmentedScientificName[]|null $synonyms */
    /** @var AugmentedScientificName|null $validName */
    /** @var string|null $mentionedNameId */
?>
<vmsc:records
        xmlns:vmsc="http://virmisco.org/xmlns/vmsc-1.3/"
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:schemaLocation="http://virmisco.org/xmlns/vmsc-1.3/ http://virmisco.org/xmlns/vmsc-1.3/records.xsd">
    <vmsc:gathering>
        <vmsc:id><?php out($gathering->getId()) ?></vmsc:id>
        <?php if ($gathering->getJournalNumber() !== null): ?>
            <vmsc:journal-number><?php out($gathering->getJournalNumber()) ?></vmsc:journal-number>
        <?php endif ?>
        <?php if ($gathering->getSamplingDate() !== null): ?>
            <vmsc:sampling-date>
                <vmsc:after><?php out($gathering->getSamplingDate()->getAfter()) ?></vmsc:after>
                <vmsc:before><?php out($gathering->getSamplingDate()->getBefore()) ?></vmsc:before>
            </vmsc:sampling-date>
        <?php endif ?>
        <?php if ($gathering->getAgent() !== null): ?>
            <vmsc:agent>
                <?php if ($gathering->getAgent()->getPerson() !== null): ?>
                    <vmsc:person><?php out($gathering->getAgent()->getPerson()) ?></vmsc:person>
                <?php endif ?>
                <?php if ($gathering->getAgent()->getOrganization() !== null): ?>
                    <vmsc:organization><?php out($gathering->getAgent()->getOrganization()) ?></vmsc:organization>
                <?php endif ?>
            </vmsc:agent>
        <?php endif ?>
        <?php if ($gathering->getLocation() !== null): ?>
            <vmsc:location>
                <?php if ($gathering->getLocation()->getCountry() !== null): ?>
                    <vmsc:country><?php out($gathering->getLocation()->getCountry()) ?></vmsc:country>
                <?php endif ?>
                <?php if ($gathering->getLocation()->getProvince() !== null): ?>
                    <vmsc:province><?php out($gathering->getLocation()->getProvince()) ?></vmsc:province>
                <?php endif ?>
                <?php if ($gathering->getLocation()->getRegion() !== null): ?>
                    <vmsc:region><?php out($gathering->getLocation()->getRegion()) ?></vmsc:region>
                <?php endif ?>
                <?php if ($gathering->getLocation()->getPlace() !== null): ?>
                    <vmsc:place><?php out($gathering->getLocation()->getPlace()) ?></vmsc:place>
                <?php endif ?>
            </vmsc:location>
        <?php endif ?>
        <?php if ($gathering->getRemarks() !== null): ?>
            <vmsc:remarks><?php out($gathering->getRemarks()) ?></vmsc:remarks>
        <?php endif ?>
    </vmsc:gathering>
    <vmsc:specimen-carrier>
        <vmsc:id><?php out($specimenCarrier->getId()) ?></vmsc:id>
        <vmsc:gathering-id><?php out($gathering->getId()) ?></vmsc:gathering-id>
        <?php if ($specimenCarrier->getCarrierNumber() !== null): ?>
            <vmsc:carrier-number><?php out($specimenCarrier->getCarrierNumber()) ?></vmsc:carrier-number>
        <?php endif ?>
        <?php if ($specimenCarrier->getPreparationType() !== null): ?>
            <vmsc:preparation-type><?php out($specimenCarrier->getPreparationType()) ?></vmsc:preparation-type>
        <?php endif ?>
        <?php if ($specimenCarrier->getOwner() !== null): ?>
            <vmsc:owner><?php out($specimenCarrier->getOwner()) ?></vmsc:owner>
        <?php endif ?>
        <?php if ($specimenCarrier->getPreviousCollection() !== null): ?>
            <vmsc:previous-collection><?php out($specimenCarrier->getPreviousCollection()) ?></vmsc:previous-collection>
        <?php endif ?>
        <?php if ($specimenCarrier->getLabelTranscript() !== null): ?>
            <vmsc:label-transcript><?php out($specimenCarrier->getLabelTranscript()) ?></vmsc:label-transcript>
        <?php endif ?>
        <?php if ($carrierScans): ?>
            <vmsc:carrier-scans>
                <?php /** @var CarrierScan $carrierScan */ foreach ($carrierScans as $carrierScan): ?>
                    <vmsc:file>
                        <vmsc:real-path><?php out($carrierScan->getRealPath()) ?></vmsc:real-path>
                        <?php if ($carrierScan->getUri() !== null): ?>
                            <vmsc:uri><?php out($carrierScan->getUri()) ?></vmsc:uri>
                        <?php endif ?>
                        <?php if ($carrierScan->getCreationTime() !== null): ?>
                            <vmsc:creation-time><?php out($carrierScan->getCreationTime()) ?></vmsc:creation-time>
                        <?php endif ?>
                        <?php if ($carrierScan->getModificationTime() !== null): ?>
                            <vmsc:modification-time><?php out($carrierScan->getModificationTime()) ?></vmsc:modification-time>
                        <?php endif ?>
                    </vmsc:file>
                <?php endforeach ?>
            </vmsc:carrier-scans>
        <?php endif ?>
        <?php if ($carrierOrganisms): ?>
            <vmsc:organisms>
                <?php /** @var Organism $organism */ foreach ($carrierOrganisms as $organism): ?>
                    <vmsc:organism>
                        <vmsc:id><?php out($organism->getId()) ?></vmsc:id>
                        <vmsc:sequence-number><?php out($organism->getSequenceNumber()) ?></vmsc:sequence-number>
                        <?php if ($organism->getIdentification() !== null): ?>
                            <vmsc:identification>
                                <?php if ($mentionedNameId): ?>
                                    <vmsc:scientific-name-id><?php out($mentionedNameId) ?></vmsc:scientific-name-id>
                                <?php endif ?>
                                <?php if ($organism->getIdentification()->getIdentifier() !== null): ?>
                                    <vmsc:identifier><?php out($organism->getIdentification()->getIdentifier()) ?></vmsc:identifier>
                                <?php endif ?>
                                <?php if ($organism->getIdentification()->getQualifier() !== null): ?>
                                    <vmsc:qualifier><?php out($organism->getIdentification()->getQualifier()) ?></vmsc:qualifier>
                                <?php endif ?>
                            </vmsc:identification>
                        <?php elseif ($organism->getTypeDesignation() !== null): ?>
                            <vmsc:type-designation>
                                <?php if ($mentionedNameId): ?>
                                    <vmsc:scientific-name-id><?php out($mentionedNameId) ?></vmsc:scientific-name-id>
                                <?php endif ?>
                                <vmsc:type-status><?php out($organism->getTypeDesignation()->getTypeStatus()) ?></vmsc:type-status>
                            </vmsc:type-designation>
                        <?php endif ?>
                        <?php if ($organism->getPhaseOrStage() !== null): ?>
                            <vmsc:phase-or-stage><?php out($organism->getPhaseOrStage()) ?></vmsc:phase-or-stage>
                        <?php endif ?>
                        <?php if ($organism->getSex() !== null): ?>
                            <vmsc:sex><?php out($organism->getSex()) ?></vmsc:sex>
                        <?php endif ?>
                        <?php if ($organism->getRemarks() !== null): ?>
                            <vmsc:remarks><?php out($organism->getRemarks()) ?></vmsc:remarks>
                        <?php endif ?>
                    </vmsc:organism>
                <?php endforeach ?>
            </vmsc:organisms>
        <?php endif ?>
    </vmsc:specimen-carrier>
    <vmsc:taxon>
        <?php if ($validName): ?>
            <vmsc:valid-name>
                <vmsc:id><?php out($validName->getId()) ?></vmsc:id>
                <vmsc:genus><?php out($validName->getGenus()) ?></vmsc:genus>
                <?php if ($validName->getSubgenus() !== null): ?>
                    <vmsc:subgenus><?php out($validName->getSubgenus()) ?></vmsc:subgenus>
                <?php endif ?>
                <vmsc:specific-epithet><?php out($validName->getSpecificEpithet()) ?></vmsc:specific-epithet>
                <?php if ($validName->getInfraspecificEpithet() !== null): ?>
                    <vmsc:infraspecific-epithet><?php out($validName->getInfraspecificEpithet()) ?></vmsc:infraspecific-epithet>
                <?php endif ?>
                <vmsc:authorship><?php out($validName->getAuthorship()) ?></vmsc:authorship>
                <vmsc:year><?php out($validName->getYear()) ?></vmsc:year>
                <vmsc:is-parenthesized><?php out($validName->getIsParenthesized() ? 'true' : 'false') ?></vmsc:is-parenthesized>
            </vmsc:valid-name>
        <?php endif ?>
        <?php if ($synonyms): ?>
            <vmsc:synonyms>
                <?php /** @var AugmentedScientificName $synonym */ foreach ($synonyms as $synonym): ?>
                    <vmsc:scientific-name>
                        <vmsc:id><?php out($synonym->getId()) ?></vmsc:id>
                        <vmsc:genus><?php out($synonym->getGenus()) ?></vmsc:genus>
                        <?php if ($synonym->getSubgenus() !== null): ?>
                            <vmsc:subgenus><?php out($synonym->getSubgenus()) ?></vmsc:subgenus>
                        <?php endif ?>
                        <vmsc:specific-epithet><?php out($synonym->getSpecificEpithet()) ?></vmsc:specific-epithet>
                        <?php if ($synonym->getInfraspecificEpithet() !== null): ?>
                            <vmsc:infraspecific-epithet><?php out($synonym->getInfraspecificEpithet()) ?></vmsc:infraspecific-epithet>
                        <?php endif ?>
                        <vmsc:authorship><?php out($synonym->getAuthorship()) ?></vmsc:authorship>
                        <vmsc:year><?php out($synonym->getYear()) ?></vmsc:year>
                        <vmsc:is-parenthesized><?php out($synonym->getIsParenthesized() ? 'true' : 'false') ?></vmsc:is-parenthesized>
                    </vmsc:scientific-name>
                <?php endforeach ?>
            </vmsc:synonyms>
        <?php endif ?>
        <vmsc:higher-taxa>
            <?php
                if (trim($organism->getHigherTaxa())):
                    /** @var string $higherTaxon */
                    foreach (preg_split('<\s+>', $organism->getHigherTaxa(), -1, PREG_SPLIT_NO_EMPTY) as $higherTaxon):
                        ?>
                        <vmsc:unranked><?php out($higherTaxon) ?></vmsc:unranked>
                    <?php endforeach; endif ?>
        </vmsc:higher-taxa>
    </vmsc:taxon>
    <vmsc:photomicrograph>
        <vmsc:id><?php out($photomicrograph->getId()) ?></vmsc:id>
        <vmsc:organism-id><?php out($photomicrograph->getOrganismId()) ?></vmsc:organism-id>
        <?php if ($photomicrograph->getTitle() !== null): ?>
            <vmsc:title><?php out($photomicrograph->getTitle()) ?></vmsc:title>
        <?php endif ?>
        <?php if ($photomicrograph->getDetailOf() !== null): ?>
            <vmsc:detail-of>
                <vmsc:photomicrograph-id><?php out($photomicrograph->getDetailOf()->getPhotomicrographId()) ?></vmsc:photomicrograph-id>
                <?php if ($photomicrograph->getDetailOf()->getHotspotX() !== null): ?>
                    <vmsc:hotspot>
                        <vmsc:x><?php out($photomicrograph->getDetailOf()->getHotspotX()) ?></vmsc:x>
                        <vmsc:y><?php out($photomicrograph->getDetailOf()->getHotspotY()) ?></vmsc:y>
                    </vmsc:hotspot>
                <?php endif ?>
            </vmsc:detail-of>
        <?php endif ?>
        <?php if ($photomicrograph->getFile() !== null): ?>
            <vmsc:file>
                <vmsc:real-path><?php out($photomicrograph->getFile()->getRealPath()) ?></vmsc:real-path>
                <?php if ($photomicrograph->getFile()->getUri() !== null): ?>
                    <vmsc:uri><?php out($photomicrograph->getFile()->getUri()) ?></vmsc:uri>
                <?php endif ?>
                <?php if ($photomicrograph->getFile()->getCreationTime() !== null): ?>
                    <vmsc:creation-time><?php out($photomicrograph->getFile()->getCreationTime()) ?></vmsc:creation-time>
                <?php endif ?>
                <?php if ($photomicrograph->getFile()->getModificationTime() !== null): ?>
                    <vmsc:modification-time><?php out($photomicrograph->getFile()->getModificationTime()) ?></vmsc:modification-time>
                <?php endif ?>
            </vmsc:file>
        <?php endif ?>
        <?php if ($photomicrograph->getPresentationUri() !== null): ?>
            <vmsc:presentation-uri><?php out($photomicrograph->getPresentationUri()) ?></vmsc:presentation-uri>
        <?php endif ?>
        <?php if ($photomicrograph->getDigitizationData() !== null): ?>
            <vmsc:digitization-data>
                <?php if ($photomicrograph->getDigitizationData()->getWidth() !== null): ?>
                    <vmsc:width><?php out($photomicrograph->getDigitizationData()->getWidth()) ?></vmsc:width>
                <?php endif ?>
                <?php if ($photomicrograph->getDigitizationData()->getHeight() !== null): ?>
                    <vmsc:height><?php out($photomicrograph->getDigitizationData()->getHeight()) ?></vmsc:height>
                <?php endif ?>
                <?php if ($photomicrograph->getDigitizationData()->getColorDepth() !== null): ?>
                    <vmsc:color-depth><?php out($photomicrograph->getDigitizationData()->getColorDepth()) ?></vmsc:color-depth>
                <?php endif ?>
                <?php if ($photomicrograph->getDigitizationData()->getReproductionScaleHorizontal() !== null): ?>
                    <vmsc:reproduction-scale>
                        <vmsc:horizontal><?php out($photomicrograph->getDigitizationData()->getReproductionScaleHorizontal()) ?></vmsc:horizontal>
                        <vmsc:vertical><?php out($photomicrograph->getDigitizationData()->getReproductionScaleVertical()) ?></vmsc:vertical>
                    </vmsc:reproduction-scale>
                <?php endif ?>
            </vmsc:digitization-data>
        <?php endif ?>
        <?php if ($photomicrograph->getCamera() || $photomicrograph->getMicroscope() !== null): ?>
            <vmsc:hardware>
                <?php if ($photomicrograph->getCamera() !== null): ?>
                    <vmsc:camera>
                        <?php if ($photomicrograph->getCamera()->getCameraMaker() !== null): ?>
                            <vmsc:camera-maker><?php out($photomicrograph->getCamera()->getCameraMaker()) ?></vmsc:camera-maker>
                        <?php endif ?>
                        <?php if ($photomicrograph->getCamera()->getCameraName() !== null): ?>
                            <vmsc:camera-name><?php out($photomicrograph->getCamera()->getCameraName()) ?></vmsc:camera-name>
                        <?php endif ?>
                        <?php if ($photomicrograph->getCamera()->getCameraArticleOrSerialNumber() !== null): ?>
                            <vmsc:camera-article-or-serial-number><?php out($photomicrograph->getCamera()->getCameraArticleOrSerialNumber()) ?></vmsc:camera-article-or-serial-number>
                        <?php endif ?>
                        <?php if ($photomicrograph->getCamera()->getSensorMaker() !== null): ?>
                            <vmsc:sensor-maker><?php out($photomicrograph->getCamera()->getSensorMaker()) ?></vmsc:sensor-maker>
                        <?php endif ?>
                        <?php if ($photomicrograph->getCamera()->getSensorName() !== null): ?>
                            <vmsc:sensor-name><?php out($photomicrograph->getCamera()->getSensorName()) ?></vmsc:sensor-name>
                        <?php endif ?>
                        <?php if ($photomicrograph->getCamera()->getSensorArticleOrSerialNumber() !== null): ?>
                            <vmsc:sensor-article-or-serial-number><?php out($photomicrograph->getCamera()->getSensorArticleOrSerialNumber()) ?></vmsc:sensor-article-or-serial-number>
                        <?php endif ?>
                        <?php if ($photomicrograph->getCamera()->getOpticalFormat() !== null): ?>
                            <vmsc:optical-format><?php out($photomicrograph->getCamera()->getOpticalFormat()) ?></vmsc:optical-format>
                        <?php endif ?>
                        <?php if ($photomicrograph->getCamera()->getCaptureFormat() !== null): ?>
                            <vmsc:capture-format><?php out($photomicrograph->getCamera()->getCaptureFormat()) ?></vmsc:capture-format>
                        <?php endif ?>
                        <?php if ($photomicrograph->getCamera()->getChipWidth() !== null): ?>
                            <vmsc:chip-width><?php out($photomicrograph->getCamera()->getChipWidth()) ?></vmsc:chip-width>
                        <?php endif ?>
                        <?php if ($photomicrograph->getCamera()->getChipHeight() !== null): ?>
                            <vmsc:chip-height><?php out($photomicrograph->getCamera()->getChipHeight()) ?></vmsc:chip-height>
                        <?php endif ?>
                        <?php if ($photomicrograph->getCamera()->getPixelWidth() !== null): ?>
                            <vmsc:pixel-width><?php out($photomicrograph->getCamera()->getPixelWidth()) ?></vmsc:pixel-width>
                        <?php endif ?>
                        <?php if ($photomicrograph->getCamera()->getPixelHeight() !== null): ?>
                            <vmsc:pixel-height><?php out($photomicrograph->getCamera()->getPixelHeight()) ?></vmsc:pixel-height>
                        <?php endif ?>
                        <?php if ($photomicrograph->getCamera()->getActivePixelsHor() !== null): ?>
                            <vmsc:active-pixels-hor><?php out($photomicrograph->getCamera()->getActivePixelsHor()) ?></vmsc:active-pixels-hor>
                        <?php endif ?>
                        <?php if ($photomicrograph->getCamera()->getActivePixelsVer() !== null): ?>
                            <vmsc:active-pixels-ver><?php out($photomicrograph->getCamera()->getActivePixelsVer()) ?></vmsc:active-pixels-ver>
                        <?php endif ?>
                        <?php if ($photomicrograph->getCamera()->getColorFilterArray() !== null): ?>
                            <vmsc:color-filter-array><?php out($photomicrograph->getCamera()->getColorFilterArray()) ?></vmsc:color-filter-array>
                        <?php endif ?>
                        <?php if ($photomicrograph->getCamera()->getProtectiveColorFilter() !== null): ?>
                            <vmsc:protective-color-filter><?php out($photomicrograph->getCamera()->getProtectiveColorFilter()) ?></vmsc:protective-color-filter>
                        <?php endif ?>
                        <?php if ($photomicrograph->getCamera()->getAdcResolution() !== null): ?>
                            <vmsc:adc-resolution><?php out($photomicrograph->getCamera()->getAdcResolution()) ?></vmsc:adc-resolution>
                        <?php endif ?>
                        <?php if ($photomicrograph->getCamera()->getDynamicRange() !== null): ?>
                            <vmsc:dynamic-range><?php out($photomicrograph->getCamera()->getDynamicRange()) ?></vmsc:dynamic-range>
                        <?php endif ?>
                        <?php if ($photomicrograph->getCamera()->getSnrMax() !== null): ?>
                            <vmsc:snr-max><?php out($photomicrograph->getCamera()->getSnrMax()) ?></vmsc:snr-max>
                        <?php endif ?>
                        <?php if ($photomicrograph->getCamera()->getReadoutNoise() !== null): ?>
                            <vmsc:readout-noise><?php out($photomicrograph->getCamera()->getReadoutNoise()) ?></vmsc:readout-noise>
                        <?php endif ?>
                    </vmsc:camera>
                <?php endif ?>
                <?php if ($photomicrograph->getMicroscope() !== null): ?>
                    <vmsc:microscope>
                        <?php if ($photomicrograph->getMicroscope()->getStandMaker() !== null): ?>
                            <vmsc:stand-maker><?php out($photomicrograph->getMicroscope()->getStandMaker()) ?></vmsc:stand-maker>
                        <?php endif ?>
                        <?php if ($photomicrograph->getMicroscope()->getStandName() !== null): ?>
                            <vmsc:stand-name><?php out($photomicrograph->getMicroscope()->getStandName()) ?></vmsc:stand-name>
                        <?php endif ?>
                        <?php if ($photomicrograph->getMicroscope()->getStandArticleOrSerialNumber() !== null): ?>
                            <vmsc:stand-article-or-serial-number><?php out($photomicrograph->getMicroscope()->getStandArticleOrSerialNumber()) ?></vmsc:stand-article-or-serial-number>
                        <?php endif ?>
                        <?php if ($photomicrograph->getMicroscope()->getCondenserMaker() !== null): ?>
                            <vmsc:condenser-maker><?php out($photomicrograph->getMicroscope()->getCondenserMaker()) ?></vmsc:condenser-maker>
                        <?php endif ?>
                        <?php if ($photomicrograph->getMicroscope()->getCondenserName() !== null): ?>
                            <vmsc:condenser-name><?php out($photomicrograph->getMicroscope()->getCondenserName()) ?></vmsc:condenser-name>
                        <?php endif ?>
                        <?php if ($photomicrograph->getMicroscope()->getCondenserArticleOrSerialNumber() !== null): ?>
                            <vmsc:condenser-article-or-serial-number><?php out($photomicrograph->getMicroscope()->getCondenserArticleOrSerialNumber()) ?></vmsc:condenser-article-or-serial-number>
                        <?php endif ?>
                        <?php if ($photomicrograph->getMicroscope()->getCondenserTurretPrismMaker() !== null): ?>
                            <vmsc:condenser-turret-prism-maker><?php out($photomicrograph->getMicroscope()->getCondenserTurretPrismMaker()) ?></vmsc:condenser-turret-prism-maker>
                        <?php endif ?>
                        <?php if ($photomicrograph->getMicroscope()->getCondenserTurretPrismName() !== null): ?>
                            <vmsc:condenser-turret-prism-name><?php out($photomicrograph->getMicroscope()->getCondenserTurretPrismName()) ?></vmsc:condenser-turret-prism-name>
                        <?php endif ?>
                        <?php if ($photomicrograph->getMicroscope()->getCondenserTurretPrismArticleOrSerialNumber() !== null): ?>
                            <vmsc:condenser-turret-prism-article-or-serial-number><?php out($photomicrograph->getMicroscope()->getCondenserTurretPrismArticleOrSerialNumber()) ?></vmsc:condenser-turret-prism-article-or-serial-number>
                        <?php endif ?>
                        <?php if ($photomicrograph->getMicroscope()->getNosepieceObjectiveMaker() !== null): ?>
                            <vmsc:nosepiece-objective-maker><?php out($photomicrograph->getMicroscope()->getNosepieceObjectiveMaker()) ?></vmsc:nosepiece-objective-maker>
                        <?php endif ?>
                        <?php if ($photomicrograph->getMicroscope()->getNosepieceObjectiveName() !== null): ?>
                            <vmsc:nosepiece-objective-name><?php out($photomicrograph->getMicroscope()->getNosepieceObjectiveName()) ?></vmsc:nosepiece-objective-name>
                        <?php endif ?>
                        <?php if ($photomicrograph->getMicroscope()->getNosepieceObjectiveArticleOrSerialNumber() !== null): ?>
                            <vmsc:nosepiece-objective-article-or-serial-number><?php out($photomicrograph->getMicroscope()->getNosepieceObjectiveArticleOrSerialNumber()) ?></vmsc:nosepiece-objective-article-or-serial-number>
                        <?php endif ?>
                        <?php if ($photomicrograph->getMicroscope()->getNosepieceObjectiveType() !== null): ?>
                            <vmsc:nosepiece-objective-type><?php out($photomicrograph->getMicroscope()->getNosepieceObjectiveType()) ?></vmsc:nosepiece-objective-type>
                        <?php endif ?>
                        <?php if ($photomicrograph->getMicroscope()->getNosepieceObjectiveNumericalAperture() !== null): ?>
                            <vmsc:nosepiece-objective-numerical-aperture><?php out($photomicrograph->getMicroscope()->getNosepieceObjectiveNumericalAperture()) ?></vmsc:nosepiece-objective-numerical-aperture>
                        <?php endif ?>
                        <?php if ($photomicrograph->getMicroscope()->getNosepieceObjectiveMagnification() !== null): ?>
                            <vmsc:nosepiece-objective-magnification><?php out($photomicrograph->getMicroscope()->getNosepieceObjectiveMagnification()) ?></vmsc:nosepiece-objective-magnification>
                        <?php endif ?>
                        <?php if ($photomicrograph->getMicroscope()->getDicTurretPrismMaker() !== null): ?>
                            <vmsc:dic-turret-prism-maker><?php out($photomicrograph->getMicroscope()->getDicTurretPrismMaker()) ?></vmsc:dic-turret-prism-maker>
                        <?php endif ?>
                        <?php if ($photomicrograph->getMicroscope()->getDicTurretPrismName() !== null): ?>
                            <vmsc:dic-turret-prism-name><?php out($photomicrograph->getMicroscope()->getDicTurretPrismName()) ?></vmsc:dic-turret-prism-name>
                        <?php endif ?>
                        <?php if ($photomicrograph->getMicroscope()->getDicTurretPrismArticleOrSerialNumber() !== null): ?>
                            <vmsc:dic-turret-prism-article-or-serial-number><?php out($photomicrograph->getMicroscope()->getDicTurretPrismArticleOrSerialNumber()) ?></vmsc:dic-turret-prism-article-or-serial-number>
                        <?php endif ?>
                        <?php if ($photomicrograph->getMicroscope()->getMagnificationChangerMaker() !== null): ?>
                            <vmsc:magnification-changer-maker><?php out($photomicrograph->getMicroscope()->getMagnificationChangerMaker()) ?></vmsc:magnification-changer-maker>
                        <?php endif ?>
                        <?php if ($photomicrograph->getMicroscope()->getMagnificationChangerName() !== null): ?>
                            <vmsc:magnification-changer-name><?php out($photomicrograph->getMicroscope()->getMagnificationChangerName()) ?></vmsc:magnification-changer-name>
                        <?php endif ?>
                        <?php if ($photomicrograph->getMicroscope()->getMagnificationChangerArticleOrSerialNumber() !== null): ?>
                            <vmsc:magnification-changer-article-or-serial-number><?php out($photomicrograph->getMicroscope()->getMagnificationChangerArticleOrSerialNumber()) ?></vmsc:magnification-changer-article-or-serial-number>
                        <?php endif ?>
                        <?php if ($photomicrograph->getMicroscope()->getPortsMaker() !== null): ?>
                            <vmsc:ports-maker><?php out($photomicrograph->getMicroscope()->getPortsMaker()) ?></vmsc:ports-maker>
                        <?php endif ?>
                        <?php if ($photomicrograph->getMicroscope()->getPortsName() !== null): ?>
                            <vmsc:ports-name><?php out($photomicrograph->getMicroscope()->getPortsName()) ?></vmsc:ports-name>
                        <?php endif ?>
                        <?php if ($photomicrograph->getMicroscope()->getPortsArticleOrSerialNumber() !== null): ?>
                            <vmsc:ports-article-or-serial-number><?php out($photomicrograph->getMicroscope()->getPortsArticleOrSerialNumber()) ?></vmsc:ports-article-or-serial-number>
                        <?php endif ?>
                        <?php if ($photomicrograph->getMicroscope()->getCameraMountAdapterMaker() !== null): ?>
                            <vmsc:camera-mount-adapter-maker><?php out($photomicrograph->getMicroscope()->getCameraMountAdapterMaker()) ?></vmsc:camera-mount-adapter-maker>
                        <?php endif ?>
                        <?php if ($photomicrograph->getMicroscope()->getCameraMountAdapterName() !== null): ?>
                            <vmsc:camera-mount-adapter-name><?php out($photomicrograph->getMicroscope()->getCameraMountAdapterName()) ?></vmsc:camera-mount-adapter-name>
                        <?php endif ?>
                        <?php if ($photomicrograph->getMicroscope()->getCameraMountAdapterArticleOrSerialNumber() !== null): ?>
                            <vmsc:camera-mount-adapter-article-or-serial-number><?php out($photomicrograph->getMicroscope()->getCameraMountAdapterArticleOrSerialNumber()) ?></vmsc:camera-mount-adapter-article-or-serial-number>
                        <?php endif ?>
                        <?php if ($photomicrograph->getMicroscope()->getCameraMountAdapterMagnification() !== null): ?>
                            <vmsc:camera-mount-adapter-magnification><?php out($photomicrograph->getMicroscope()->getCameraMountAdapterMagnification()) ?></vmsc:camera-mount-adapter-magnification>
                        <?php endif ?>
                    </vmsc:microscope>
                <?php endif ?>
            </vmsc:hardware>
        <?php endif ?>
        <?php if ($photomicrograph->getMicroscopeSettings() !== null): ?>
            <vmsc:microscope-settings>
                <?php if ($photomicrograph->getMicroscopeSettings()->getContrastMethod() !== null): ?>
                    <vmsc:contrast-method><?php out($photomicrograph->getMicroscopeSettings()->getContrastMethod()) ?></vmsc:contrast-method>
                <?php endif ?>
                <?php if ($photomicrograph->getMicroscopeSettings()->getDicPrismPosition() !== null): ?>
                    <vmsc:dic-prism-position><?php out($photomicrograph->getMicroscopeSettings()->getDicPrismPosition()) ?></vmsc:dic-prism-position>
                <?php endif ?>
                <?php if ($photomicrograph->getMicroscopeSettings()->getApertureDiaphragmOpening() !== null): ?>
                    <vmsc:aperture-diaphragm-opening><?php out($photomicrograph->getMicroscopeSettings()->getApertureDiaphragmOpening()) ?></vmsc:aperture-diaphragm-opening>
                <?php endif ?>
                <?php if ($photomicrograph->getMicroscopeSettings()->getFieldDiaphragmOpening() !== null): ?>
                    <vmsc:field-diaphragm-opening><?php out($photomicrograph->getMicroscopeSettings()->getFieldDiaphragmOpening()) ?></vmsc:field-diaphragm-opening>
                <?php endif ?>
                <?php if ($photomicrograph->getMicroscopeSettings()->getMagnificationChangerMagnification() !== null): ?>
                    <vmsc:magnification-changer-magnification><?php out($photomicrograph->getMicroscopeSettings()->getMagnificationChangerMagnification()) ?></vmsc:magnification-changer-magnification>
                <?php endif ?>
            </vmsc:microscope-settings>
        <?php endif ?>
        <?php if ($focalPlaneImages): ?>
            <vmsc:focal-plane-images>
                <?php /** @var FocalPlaneImage $focalPlaneImage */ foreach ($focalPlaneImages as $focalPlaneImage): ?>
                    <vmsc:focal-plane-image>
                        <vmsc:id><?php out($focalPlaneImage->getId()) ?></vmsc:id>
                        <?php if ($focalPlaneImage->getFocusPosition() !== null): ?>
                            <vmsc:focus-position><?php out($focalPlaneImage->getFocusPosition()) ?></vmsc:focus-position>
                        <?php endif ?>
                        <?php if ($focalPlaneImage->getFile() !== null): ?>
                            <vmsc:file>
                                <vmsc:real-path><?php out($focalPlaneImage->getFile()->getRealPath()) ?></vmsc:real-path>
                                <?php if ($focalPlaneImage->getFile()->getUri() !== null): ?>
                                    <vmsc:uri><?php out($focalPlaneImage->getFile()->getUri()) ?></vmsc:uri>
                                <?php endif ?>
                                <?php if ($focalPlaneImage->getFile()->getCreationTime() !== null): ?>
                                    <vmsc:creation-time><?php out($focalPlaneImage->getFile()->getCreationTime()) ?></vmsc:creation-time>
                                <?php endif ?>
                                <?php if ($focalPlaneImage->getFile()->getModificationTime() !== null): ?>
                                    <vmsc:modification-time><?php out($focalPlaneImage->getFile()->getModificationTime()) ?></vmsc:modification-time>
                                <?php endif ?>
                            </vmsc:file>
                        <?php endif ?>
                        <?php if ($focalPlaneImage->getPresentationUri() !== null): ?>
                            <vmsc:presentation-uri><?php out($focalPlaneImage->getPresentationUri()) ?></vmsc:presentation-uri>
                        <?php endif ?>
                        <?php if ($focalPlaneImage->getExposureSettings() !== null): ?>
                            <vmsc:exposure-settings>
                                <?php if ($focalPlaneImage->getExposureSettings()->getDuration() !== null): ?>
                                    <vmsc:duration><?php out($focalPlaneImage->getExposureSettings()->getDuration()) ?></vmsc:duration>
                                <?php endif ?>
                                <?php if ($focalPlaneImage->getExposureSettings()->getGain() !== null): ?>
                                    <vmsc:gain><?php out($focalPlaneImage->getExposureSettings()->getGain()) ?></vmsc:gain>
                                <?php endif ?>
                            </vmsc:exposure-settings>
                        <?php endif ?>
                        <?php if ($focalPlaneImage->getHistogramSettings() !== null): ?>
                            <vmsc:histogram-settings>
                                <?php if ($focalPlaneImage->getHistogramSettings()->getGamma() !== null): ?>
                                    <vmsc:gamma><?php out($focalPlaneImage->getHistogramSettings()->getGamma()) ?></vmsc:gamma>
                                <?php endif ?>
                                <?php if ($focalPlaneImage->getHistogramSettings()->getBlackClip() !== null): ?>
                                    <vmsc:black-clip><?php out($focalPlaneImage->getHistogramSettings()->getBlackClip()) ?></vmsc:black-clip>
                                <?php endif ?>
                                <?php if ($focalPlaneImage->getHistogramSettings()->getWhiteClip() !== null): ?>
                                    <vmsc:white-clip><?php out($focalPlaneImage->getHistogramSettings()->getWhiteClip()) ?></vmsc:white-clip>
                                <?php endif ?>
                            </vmsc:histogram-settings>
                        <?php endif ?>
                        <?php if ($focalPlaneImage->getPostProcessingSettings() !== null): ?>
                            <vmsc:post-processing-settings>
                                <vmsc:shading><?php out($focalPlaneImage->getPostProcessingSettings()->getShading() ? 'true' : 'false') ?></vmsc:shading>
                                <vmsc:sharpening><?php out($focalPlaneImage->getPostProcessingSettings()->getSharpening() ? 'true' : 'false') ?></vmsc:sharpening>
                            </vmsc:post-processing-settings>
                        <?php endif ?>
                    </vmsc:focal-plane-image>
                <?php endforeach ?>
            </vmsc:focal-plane-images>
        <?php endif ?>
    </vmsc:photomicrograph>
</vmsc:records>