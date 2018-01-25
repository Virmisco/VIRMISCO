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
<oai_dc:dc
        xmlns:oai_dc="http://www.openarchives.org/OAI/2.0/oai_dc/"
        xmlns:dc="http://purl.org/dc/elements/1.1/"
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:schemaLocation="http://www.openarchives.org/OAI/2.0/oai_dc/ http://www.openarchives.org/OAI/2.0/oai_dc.xsd">
    <dc:title><?php out($photomicrograph->getTitle() ?: $photomicrograph->getId()) ?></dc:title>
    <dc:creator>Senckenberg Museum of Natural History Görlitz</dc:creator>
    <dc:subject>digital photomicrograph</dc:subject>
    <dc:description>A<?php out($focalPlaneImages ? 'n interactive' : '') ?>
        digital photomicrograph<?php out($focalPlaneImages ? ' comprising multiple focal plane images' : '') ?>
        of a preserved zoological specimen mounted on a microscope slide.</dc:description>
    <dc:publisher>virmisco.org, a project of the Senckenberg Museum of Natural History Görlitz</dc:publisher>
    <dc:contributor>Senckenberg Museum of Natural History Görlitz</dc:contributor>
    <dc:date><?php out($timestamp->format(DateTime::ATOM)) ?></dc:date>
    <dc:type><?php out($focalPlaneImages ? 'InteractiveResource' : 'StillImage') ?></dc:type>
    <dc:format><?php out($focalPlaneImages ? 'text/html, video/ogg, video/mp4' : 'image/jpeg') ?></dc:format>
    <dc:identifier><?php out($photomicrograph->getPresentationUri() ?: $photomicrograph->getFile()->getUri()) ?></dc:identifier>
    <dc:source><?php out('%s-%s', $specimenCarrier->getCarrierNumber(), $organism->getSequenceNumber()) ?></dc:source>
    <dc:language>en</dc:language>
    <dc:rights>CC0 1.0 Universal (CC0 1.0) Public Domain Dedication (http://creativecommons.org/publicdomain/zero/1.0/legalcode)</dc:rights>
</oai_dc:dc>
