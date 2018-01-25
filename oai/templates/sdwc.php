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

    $adHocLocationId = uniqid('loc');
    $adHocOccurrenceId = uniqid('occ');
    $adHocMaterialSampleId = uniqid('mat');
    $adHocIdentificationId = uniqid('ide');
    $adHocTaxonId = uniqid('tax');
?>
<sdwc:SimpleDarwinRecordSet
        xmlns:dcterms="http://purl.org/dc/terms/"
        xmlns:dwc="http://rs.tdwg.org/dwc/terms/"
        xmlns:sdwc="http://rs.tdwg.org/dwc/xsd/simpledarwincore/"
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:schemaLocation="http://rs.tdwg.org/dwc/xsd/simpledarwincore/ http://rs.tdwg.org/dwc/xsd/tdwg_dwc_simple.xsd">
    <sdwc:SimpleDarwinRecord>
        <dwc:datasetID><?php out($photomicrograph->getId()) ?></dwc:datasetID>
        <dcterms:type><?php out($focalPlaneImages ? 'InteractiveResource' : 'StillImage') ?></dcterms:type>
        <dcterms:modified><?php out($timestamp->format(DateTime::ATOM)) ?></dcterms:modified>
        <dcterms:language>en</dcterms:language>
        <dcterms:license>http://creativecommons.org/publicdomain/zero/1.0/legalcode</dcterms:license>
        <dcterms:rightsHolder>Senckenberg Museum of Natural History Görlitz</dcterms:rightsHolder>
        <?php /* <dcterms:accessRights /> */ ?>
        <?php if ($photomicrograph->getTitle()): ?>
            <dcterms:bibliographicCitation><?php
                    out('%s, virmisco.org', $photomicrograph->getTitle())
                ?></dcterms:bibliographicCitation>
        <?php endif ?>
        <?php if ($photomicrograph->getPresentationUri()): ?>
            <dcterms:references><?php out($photomicrograph->getPresentationUri()) ?></dcterms:references>
        <?php endif ?>
        <dwc:institutionCode>Senckenberg Museum of Natural History Görlitz</dwc:institutionCode>
        <dwc:collectionCode>virmisco</dwc:collectionCode>
        <?php if ($photomicrograph->getTitle()): ?>
            <dwc:datasetName><?php out($photomicrograph->getTitle()) ?></dwc:datasetName>
        <?php endif ?>
        <?php if ($specimenCarrier->getOwner()): ?>
            <dwc:ownerInstitutionCode><?php out($specimenCarrier->getOwner()) ?></dwc:ownerInstitutionCode>
        <?php endif ?>
        <dwc:materialSampleID><?php out($adHocMaterialSampleId) ?></dwc:materialSampleID>
    </sdwc:SimpleDarwinRecord>
    <sdwc:SimpleDarwinRecord>
        <dwc:materialSampleID><?php out($adHocMaterialSampleId) ?></dwc:materialSampleID>
        <dcterms:type>PhysicalObject</dcterms:type>
        <dwc:basisOfRecord>PreservedSpecimen</dwc:basisOfRecord>
        <dwc:organismID><?php out($organism->getId()) ?></dwc:organismID>
    </sdwc:SimpleDarwinRecord>
    <sdwc:SimpleDarwinRecord>
        <dwc:organismID><?php out($organism->getId()) ?></dwc:organismID>
        <dwc:basisOfRecord>Organism</dwc:basisOfRecord>
        <?php if ($organism->getRemarks()): ?>
            <dwc:organismRemarks><?php out($organism->getRemarks()) ?></dwc:organismRemarks>
        <?php endif ?>
        <dwc:occurrenceID><?php out($adHocOccurrenceId) ?></dwc:occurrenceID>
        <?php if ($scientificNameProvider): ?>
            <dwc:identificationID><?php out($adHocIdentificationId) ?></dwc:identificationID>
        <?php endif ?>
    </sdwc:SimpleDarwinRecord>
    <sdwc:SimpleDarwinRecord>
        <dwc:occurrenceID><?php out($adHocOccurrenceId) ?></dwc:occurrenceID>
        <dwc:basisOfRecord>Occurrence</dwc:basisOfRecord>
        <dwc:recordNumber><?php out($gathering->getJournalNumber()) ?></dwc:recordNumber>
        <?php if ($gathering->getAgent()): ?>
            <dwc:recordedBy><?php out($gathering->getAgent()) ?></dwc:recordedBy>
        <?php endif ?>
        <?php if ($organism->getSex()): ?>
            <dwc:sex><?php out($organism->getSex()) ?></dwc:sex>
        <?php endif ?>
        <?php if ($organism->getPhaseOrStage()): ?>
            <dwc:lifeStage><?php out($organism->getPhaseOrStage()) ?></dwc:lifeStage>
        <?php endif ?>
        <?php if ($specimenCarrier->getPreparationType()): ?>
            <dwc:preparations><?php out($specimenCarrier->getPreparationType()) ?> mount
                | digital photomicrograph</dwc:preparations>
        <?php endif ?>
        <dwc:eventID><?php out($gathering->getId()) ?></dwc:eventID>
    </sdwc:SimpleDarwinRecord>
    <sdwc:SimpleDarwinRecord>
        <dwc:eventID><?php out($gathering->getId()) ?></dwc:eventID>
        <dwc:basisOfRecord>HumanObservation</dwc:basisOfRecord>
        <?php if ($gathering->getSamplingDate()): ?>
            <dwc:eventDate><?php out($gathering->getSamplingDate()) ?></dwc:eventDate>
        <?php endif ?>
        <?php if ($gathering->getRemarks()): ?>
            <dwc:fieldNotes><?php out($gathering->getRemarks()) ?></dwc:fieldNotes>
        <?php endif ?>
        <?php if ($gathering->getLocation()): ?>
            <dwc:locationID><?php out($adHocLocationId) ?></dwc:locationID>
        <?php endif ?>
    </sdwc:SimpleDarwinRecord>
    <?php if ($gathering->getLocation()): ?>
        <sdwc:SimpleDarwinRecord>
            <dwc:locationID><?php out($adHocLocationId) ?></dwc:locationID>
            <dwc:basisOfRecord>Location</dwc:basisOfRecord>
            <?php if ($gathering->getLocation()->getCountry()): ?>
                <dwc:countryCode><?php out($gathering->getLocation()->getCountry()) ?></dwc:countryCode>
            <?php endif ?>
            <?php if ($gathering->getLocation()->getProvince()): ?>
                <dwc:stateProvince><?php out($gathering->getLocation()->getProvince()) ?></dwc:stateProvince>
            <?php endif ?>
            <?php if ($gathering->getLocation()->getPlace() || $gathering->getLocation()->getRegion()): ?>
                <dwc:locality><?php out(
                        '%s%s%s',
                        $gathering->getLocation()->getPlace(),
                        $gathering->getLocation()->getPlace() && $gathering->getLocation()->getRegion() ? ', ' : '',
                        $gathering->getLocation()->getRegion()
                    ) ?></dwc:locality>
            <?php endif ?>
        </sdwc:SimpleDarwinRecord>
    <?php endif ?>
    <?php if ($scientificNameProvider): ?>
        <sdwc:SimpleDarwinRecord>
            <dwc:identificationID><?php out($adHocIdentificationId) ?></dwc:identificationID>
            <dwc:basisOfRecord>Identification</dwc:basisOfRecord>
            <?php if ($scientificNameProvider instanceof OrganismIdentification): ?>
                <?php if ($scientificNameProvider->getQualifier()): ?>
                    <dwc:identificationQualifier><?php out($scientificNameProvider->getQualifier()) ?></dwc:identificationQualifier>
                <?php endif ?><?php if ($scientificNameProvider->getQualifier()): ?>
                    <dwc:identifiedBy><?php out($scientificNameProvider->getQualifier()) ?></dwc:identifiedBy>
                <?php endif ?>
            <?php elseif ($scientificNameProvider instanceof OrganismTypeDesignation): ?>
                <?php if ($scientificNameProvider->getTypeStatus()): ?>
                    <dwc:typeStatus><?php out($scientificNameProvider->getTypeStatus()) ?></dwc:typeStatus>
                <?php endif ?>
            <?php endif ?>
            <?php if ($mentionedNameId): ?>
                <dwc:scientificNameID><?php out($mentionedNameId) ?></dwc:scientificNameID>
            <?php endif ?>
        </sdwc:SimpleDarwinRecord>
        <?php /** @var AugmentedScientificName $synonym */ foreach ($synonyms as $synonym): ?>
            <?php if ($synonym->getIsMentioned()): ?>
                <sdwc:SimpleDarwinRecord>
                    <dwc:scientificNameID><?php out($synonym->getId()) ?></dwc:scientificNameID>
                    <dwc:basisOfRecord>Taxon</dwc:basisOfRecord>
                    <dwc:scientificName><?php out($synonym) ?></dwc:scientificName>
                    <dwc:genus><?php out($synonym->getGenus()) ?></dwc:genus>
                    <?php if ($synonym->getSubgenus()): ?>
                        <dwc:subgenus><?php out($synonym->getSubgenus()) ?></dwc:subgenus>
                    <?php endif ?>
                    <dwc:specificEpithet><?php out($synonym->getSpecificEpithet()) ?></dwc:specificEpithet>
                    <?php if ($synonym->getInfraspecificEpithet()): ?>
                        <dwc:infraspecificEpithet><?php out($synonym->getInfraspecificEpithet()) ?></dwc:infraspecificEpithet>
                    <?php endif ?>
                    <dwc:taxonRank><?php out($synonym->getInfraspecificEpithet() ? 'subspecies' : 'species') ?></dwc:taxonRank>
                    <dwc:scientificNameAuthorship><?php out($synonym->format('(A, Y)')) ?></dwc:scientificNameAuthorship>
                    <dwc:nomenclaturalCode>ICZN</dwc:nomenclaturalCode>
                    <?php if ($synonym->getId() === $validName->getId()): $validName = null; ?>
                        <dwc:taxonomicStatus>accepted</dwc:taxonomicStatus>
                    <?php endif ?>
                    <dwc:taxonID><?php out($adHocTaxonId) ?></dwc:taxonID>
                </sdwc:SimpleDarwinRecord>
            <?php endif ?>
        <?php endforeach ?>
        <sdwc:SimpleDarwinRecord>
            <dwc:taxonID><?php out($adHocTaxonId) ?></dwc:taxonID>
            <dwc:basisOfRecord>Taxon</dwc:basisOfRecord>
            <dwc:higherClassification><?php out(implode(' | ', preg_split('<\s+>', $organism->getHigherTaxa(), -1, PREG_SPLIT_NO_EMPTY))) ?></dwc:higherClassification>
            <dwc:nomenclaturalCode>ICZN</dwc:nomenclaturalCode>
            <dwc:acceptedNameUsageID><?php out($validName->getId()) ?></dwc:acceptedNameUsageID>
        </sdwc:SimpleDarwinRecord>
        <?php if ($validName): ?>
            <sdwc:SimpleDarwinRecord>
                <dwc:scientificNameID><?php out($validName->getId()) ?></dwc:scientificNameID>
                <dwc:basisOfRecord>Taxon</dwc:basisOfRecord>
                <dwc:scientificName><?php out($validName) ?></dwc:scientificName>
                <dwc:genus><?php out($validName->getGenus()) ?></dwc:genus>
                <?php if ($validName->getSubgenus()): ?>
                    <dwc:subgenus><?php out($validName->getSubgenus()) ?></dwc:subgenus>
                <?php endif ?>
                <dwc:specificEpithet><?php out($validName->getSpecificEpithet()) ?></dwc:specificEpithet>
                <?php if ($validName->getInfraspecificEpithet()): ?>
                    <dwc:infraspecificEpithet><?php out($validName->getInfraspecificEpithet()) ?></dwc:infraspecificEpithet>
                <?php endif ?>
                <dwc:taxonRank><?php out($validName->getInfraspecificEpithet() ? 'subspecies' : 'species') ?></dwc:taxonRank>
                <dwc:scientificNameAuthorship><?php out($validName->format('(A, Y)')) ?></dwc:scientificNameAuthorship>
                <dwc:nomenclaturalCode>ICZN</dwc:nomenclaturalCode>
                <dwc:taxonomicStatus>accepted</dwc:taxonomicStatus>
            </sdwc:SimpleDarwinRecord>
        <?php endif ?>
    <?php endif ?>
</sdwc:SimpleDarwinRecordSet>
