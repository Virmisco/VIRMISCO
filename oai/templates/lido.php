<?xml version="1.0" encoding="UTF-8" ?>
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
<lido:lidoWrap xmlns:lido="http://www.lido-schema.org"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:schemaLocation="http://www.lido-schema.org http://www.lido-schema.org/schema/v1.0/lido-v1.0.xsd">
    <lido:lido>
        <lido:lidoRecID lido:type="url" lido:pref="preferred"><?php out($photomicrograph->getPresentationUri() ?: $photomicrograph->getFile()->getUri()) ?></lido:lidoRecID>
        <lido:lidoRecID lido:type="uuid" lido:pref="alternate"><?php out($photomicrograph->getId()) ?></lido:lidoRecID>
        <lido:descriptiveMetadata xml:lang="en">
            <lido:objectClassificationWrap>
                <lido:objectWorkTypeWrap>
                    <lido:objectWorkType lido:type="dc:type">
                        <lido:term><?php out($focalPlaneImages ? 'InteractiveResource' : 'StillImage') ?></lido:term>
                    </lido:objectWorkType>
                </lido:objectWorkTypeWrap>
                <lido:classificationWrap>
                    <lido:classification lido:type="dwc:basisOfRecord">
                        <lido:term>PreservedSpecimen</lido:term>
                    </lido:classification>
                    <lido:classification lido:type="dwc:Taxon:higherClassification">
                        <lido:term><?php
                                out(implode(' | ', preg_split('<\s+>', $organism->getHigherTaxa(), -1, PREG_SPLIT_NO_EMPTY)))
                            ?></lido:term>
                    </lido:classification>
                    <lido:classification lido:type="dwc:Taxon:scientificName">
                        <lido:term><?php out($validName) ?></lido:term>
                    </lido:classification>
                    <?php if ($organism->getTypeDesignation()): ?>
                        <lido:classification lido:type="dwc:Identification:typeStatus">
                            <lido:term><?php out($organism->getTypeDesignation()->getTypeStatus()) ?></lido:term>
                        </lido:classification>
                    <?php endif ?>
                    <lido:classification lido:type="dwc:Occurrence:lifeStage">
                        <lido:term><?php out($organism->getPhaseOrStage()) ?></lido:term>
                    </lido:classification>
                    <lido:classification lido:type="dwc:Occurrence:sex">
                        <lido:term><?php out($organism->getSex()) ?></lido:term>
                    </lido:classification>
                </lido:classificationWrap>
            </lido:objectClassificationWrap>
            <lido:objectIdentificationWrap>
                <lido:titleWrap>
                    <lido:titleSet>
                        <lido:appellationValue><?php out($photomicrograph->getTitle()) ?></lido:appellationValue>
                    </lido:titleSet>
                </lido:titleWrap>
                <lido:inscriptionsWrap>
                    <lido:inscriptions lido:type="specimen carrier label">
                        <lido:inscriptionTranscription><?php out($specimenCarrier->getLabelTranscript()) ?></lido:inscriptionTranscription>
                    </lido:inscriptions>
                </lido:inscriptionsWrap>
                <lido:objectDescriptionWrap>
                    <lido:objectDescriptionSet>
                        <lido:descriptiveNoteValue>A digital photomicrograph of a preserved biological organism or parts/features thereof.</lido:descriptiveNoteValue>
                    </lido:objectDescriptionSet>
                </lido:objectDescriptionWrap>
            </lido:objectIdentificationWrap>
        </lido:descriptiveMetadata>
        <lido:administrativeMetadata xml:lang="en">
            <lido:recordWrap>
                <lido:recordID lido:type="local"><?php out('%s-%s:%s', $specimenCarrier->getCarrierNumber(), $organism->getSequenceNumber(), $photomicrograph->getTitle()) ?></lido:recordID>
                <lido:recordType><lido:term>item</lido:term></lido:recordType>
                <lido:recordSource>
                    <lido:legalBodyName>
                        <lido:appellationValue>Senckenberg Museum für Naturkunde Görlitz</lido:appellationValue>
                    </lido:legalBodyName>
                    <lido:legalBodyWeblink>http://www.virmisco.org</lido:legalBodyWeblink>
                </lido:recordSource>
                <lido:recordRights>
                    <lido:rightsType>
                        <lido:conceptID lido:source="CC" lido:type="URI">https://creativecommons.org/publicdomain/zero/1.0/</lido:conceptID>
                        <lido:term>CC0</lido:term>
                    </lido:rightsType>
                </lido:recordRights>
            </lido:recordWrap>
        </lido:administrativeMetadata>
    </lido:lido>
</lido:lidoWrap>
