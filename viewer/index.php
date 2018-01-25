<?php
    use sednasoft\virmisco\readlayer\AugmentedAgent;
    use sednasoft\virmisco\readlayer\AugmentedLocation;
    use sednasoft\virmisco\readlayer\AugmentedSamplingDate;
    use sednasoft\virmisco\readlayer\AugmentedScientificName;
    use sednasoft\virmisco\readlayer\entity\FocalPlaneImage;
    use sednasoft\virmisco\readlayer\entity\Gathering;
    use sednasoft\virmisco\readlayer\entity\Organism;
    use sednasoft\virmisco\readlayer\entity\Photomicrograph;
    use sednasoft\virmisco\readlayer\entity\SpecimenCarrier;

    header('Content-Type: text/plain; charset=UTF-8');
    require_once '../vendor/autoload.php';
    require_once '../src/credentials.php';
    $otherIds = isset($_POST['choice']) ? $_POST['choice'] : [];
    $photomicrographId = isset($_GET['id']) ? $_GET['id']
        : (isset($_POST['id']) ? $_POST['id']
            : ($otherIds ? $otherIds[0]
                : null
            )
        );
    if (!$photomicrographId) {
        header('Location: /search/', true, 302);
        exit;
    }
    header('Content-Type: text/html; charset=UTF-8');
    $connection = new PDO(MARIA_DSN, MARIA_USER, MARIA_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
    ]);
    Gathering::$agentClass = AugmentedAgent::class;
    Gathering::$locationClass = AugmentedLocation::class;
    Gathering::$samplingDateClass = AugmentedSamplingDate::class;
    $listFocalPlanes = $connection->prepare(
        'SELECT * FROM `focal_plane_image` WHERE `photomicrograph_id` = ? ORDER BY `file__real_path`'
    );
    $listFocalPlanes->setFetchMode(PDO::FETCH_CLASS, FocalPlaneImage::class);
    $loadPhotomicrograph = $connection->prepare('SELECT * FROM `photomicrograph` WHERE `id` = ?');
    $loadPhotomicrograph->setFetchMode(PDO::FETCH_CLASS, Photomicrograph::class);
    $loadOrganism = $connection->prepare('SELECT * FROM `organism` WHERE `id` = ?');
    $loadOrganism->setFetchMode(PDO::FETCH_CLASS, Organism::class);
    $loadSpecimenCarrier = $connection->prepare('SELECT * FROM `specimen_carrier` WHERE `id` = ?');
    $loadSpecimenCarrier->setFetchMode(PDO::FETCH_CLASS, SpecimenCarrier::class);
    $loadGathering = $connection->prepare('SELECT * FROM `gathering` WHERE `id` = ?');
    $loadGathering->setFetchMode(PDO::FETCH_CLASS, Gathering::class);
    $loadScientificName = $connection->prepare(
        "SELECT * FROM `scientific_name` WHERE `specimen_carrier_id` = ? AND `sequence_number` = ? AND `is_mentioned` = 'true'"
    );
    $loadScientificName->setFetchMode(PDO::FETCH_CLASS, AugmentedScientificName::class);
    $loadValidName = $connection->prepare(
        "SELECT * FROM `scientific_name` WHERE `specimen_carrier_id` = ? AND `sequence_number` = ? AND `is_valid` = 'true'"
    );
    $loadValidName->setFetchMode(PDO::FETCH_CLASS, AugmentedScientificName::class);
    //
    $listFocalPlanes->execute([$photomicrographId]);
    $loadPhotomicrograph->execute([$photomicrographId]);
    /** @var Photomicrograph $photomicrograph */
    $photomicrograph = $loadPhotomicrograph->fetch();
    $loadOrganism->execute([$photomicrograph->getOrganismId()]);
    /** @var Organism $organism */
    $organism = $loadOrganism->fetch();
    $loadSpecimenCarrier->execute([$organism->getSpecimenCarrierId()]);
    /** @var SpecimenCarrier $specimenCarrier */
    $specimenCarrier = $loadSpecimenCarrier->fetch();
    //echo $specimenCarrier->getGatheringId();
    $loadGathering->execute([$specimenCarrier->getGatheringId()]);
    /** @var Gathering $gathering */
    $gathering = $loadGathering->fetch();
    $loadScientificName->execute([$organism->getSpecimenCarrierId(), $organism->getSequenceNumber()]);
    /** @var AugmentedScientificName|null $scientificName */
    $scientificName = $loadScientificName->fetch();
    $loadValidName->execute([$organism->getSpecimenCarrierId(), $organism->getSequenceNumber()]);
    /** @var AugmentedScientificName|null $scientificName */
    $validName = $loadValidName->fetch();
    //
    /** @var FocalPlaneImage[] $focalPlanes */
    $focalPlanes = iterator_to_array($listFocalPlanes);
    $maxPlane = count($focalPlanes) - 1;
    if($maxPlane < 0)
	$focalPlaneDistance = "unknown";
    else
    	$focalPlaneDistance = ($focalPlanes[$maxPlane]->getFocusPosition() - $focalPlanes[0]->getFocusPosition()) / $maxPlane;
    //
    $hierarchySeparator = ' » ';

    /**
     * @param string ...$string An sprintf-like format string and optional parameters
     */
    function out(...$string)
    {
        echo htmlspecialchars(sprintf(...$string));
    }
?>
<!DOCTYPE html>
<html>
    <head lang="en">
        <meta charset="UTF-8">
        <title></title>
        <style type="text/css">
            * { padding: 0; margin: 0; color: #eee; font-size: 12px; line-height: 14.4px; font-family: "Lucida Sans Unicode", "Lucida Grande", "Tahoma", sans-serif; }
            body { background: #222; }
            h1 { font-size: 18px; }
            h2 { font-size: 15px; }
            .feedback { margin: -18px 0 0 10px; width: 600px; text-align: right; }
            #viewport { position: absolute; top: 30px; left: 10px; width: 600px; height: 450px; overflow: hidden; }
            #image { position: absolute; top: 0; left: 0; width: 600px; height: 450px; }
            #controls { position: absolute; top: 525px; bottom: 10px; left: 10px; width: 615px; overflow: auto; }
            #controls div { position: relative; }
            #controls div #range { position: absolute; margin: 7px 4px; width: 100%; height: 67%; z-index: -1; background: #799914; }
            #video { position: absolute; top: 300px; left: 630px; width: 160px; height: auto; }
            .snapshot { position: absolute; top: 425px; left: 630px; margin: 0; }
            .snapshot a { display: block; text-align: center; background: #444; border: 1px solid #eee; border-radius: 3px; padding: 1px 6px; margin: 3px 0 0 20px; width: 132px; text-decoration: none; }
            .measurement { position: absolute; top: 525px; left: 630px; width: 158px; margin: 0; }
            .measurement label { width: 60px; margin: 0; }
            #measurementComponents { position: absolute; top: 548px; left: 630px; width: 158px; padding-bottom: 5px; border: 1px solid #eee; margin: 0; }
            #measurementComponents label { margin: 4px; display: table-row }
            #measurementComponents label>* { display: table-cell; padding: 4px 0 0 20px; }
            #measurementComponents output { text-align: right; }
            .clipping { position: absolute; top: 299px; left: 629px; width: 160px; height: 120px; overflow: hidden; }
            .turntable { position: absolute; top: 0; left: 0; width: 160px; height: 120px; }
            #viewportBoundingBox { position: absolute; top: 0; left: 0; width: 160px; height: 120px; border: 1px dashed #f00; }
            label { display: block; line-height: 18px; }
            label[for] { display: inline-block; text-align: center; background: #444; border: 1px solid #eee; border-radius: 3px; padding: 0 3px; }
            input { width: 450px; margin-right: 10px; }
            input[type=checkbox], input[type=radio] { display: none; margin-left: -9999px; }
            input:checked~label { border: 1px solid #abd81e; border-radius: 3px; background: #799914; color: #000; }
            .playback { margin: 3px 0; }
            .playback label { font-weight: bold; width: 40px; }
            .playback input { display: none; margin-left: -9999px; }
            input[type=range] { height: 12px; }
            input[type=range]::-moz-range-track { height: 2px; }
            input[type=range]::-ms-track { height: 2px; }
            input[type=range]::-moz-range-thumb { background: #888; border: 1px solid #eee; border-radius: 3px; width: 6px; height: 9px; }
            input[type=range]::-ms-thumb { background: #888; border: 1px solid #eee; border-radius: 3px; width: 6px; height: 9px; }
            #overlay { position: absolute; top: 30px; left: 10px; width: 600px; height: 450px; cursor: move; }
            #overlay.tape { cursor: url("crosshair.cur"), crosshair; }
            canvas {
                image-rendering: optimizeSpeed;             /* Older versions of FF          */
                image-rendering: -moz-crisp-edges;          /* FF 6.0+                       */
                image-rendering: -webkit-optimize-contrast; /* Safari                        */
                image-rendering: -o-crisp-edges;            /* OS X & Windows Opera (12.02+) */
                image-rendering: pixelated;                 /* Awesome future-browsers       */
                -ms-interpolation-mode: nearest-neighbor;   /* IE                            */
            }
            #footer { position: absolute; top: 480px; left: 10px; width: 600px; height: 36px; background: #333 url("bar-empty.png") no-repeat; }
            #footer p { font-size: 11px; padding: 0; margin: 1px 0 0 6px; }
            #scaleBar { margin: 15px 0 0 10px; border: 1px solid #fff; border-top: 0 none; width: 300px; text-align: right; line-height: 10px; }
            #scaleBar span { position: relative; top: -5px; left: -5px; line-height: 10px; }
            .wrapper { position: absolute; top: 5px; right: 5px; bottom: 5px; left: 610px; overflow: auto; }
            #navigation { margin: -10px 0 0 0; max-height: 200px; overflow: auto; }
            #navigation p { margin: 8px 0;}
            #navigation p.active { display: inline-block; margin: 0; padding: 1px 6px; border: 1px solid #abd81e; border-radius: 3px; background: #799914; color: #000; }
            #navigation button { background: #444; border: 1px solid #eee; border-radius: 3px; padding: 0 3px;}
            #metadata { margin: 20px 0 0 200px; }
            .focal-plane { display: none; }
            section, dl { margin: 5px 0 5px 20px; }
            dt { /*font-size: 11px;*/ display: block; float: left; clear: left; width: 200px; overflow: hidden; }
            dd { display: block; font-style: italic; padding-left: 10px; overflow: hidden; }
            .popup-root { position: absolute; top: 0; right: 0; bottom: 0; left: 0; }
            .popup-root .dim { position: absolute; top: 0; right: 0; bottom: 0; left: 0; background: #222; opacity: 0.95; }
            .popup-root .container { position: absolute; top: 50%; left: 50%; }
            .popup-root .container .caption { text-align: center; }
        </style>
    </head>
    <body>
        <main>
            <video id="video" loop autoplay>
                <source src="<?php echo str_replace(
                    ['http://virmisco.org/media/', '.zip'],
                    ['/media/', '.mp4'],
                    $photomicrograph->getFile()->getUri()
                ) ?>" type="video/mp4" />
                <source src="<?php echo str_replace(
                    ['http://virmisco.org/media/', '.zip'],
                    ['/media/', '.ogg'],
                    $photomicrograph->getFile()->getUri()
                ) ?>?12" type="video/ogg" />
            </video>
            <div class="clipping"><div class="turntable"><div id="viewportBoundingBox"></div></div></div>
            <div id="viewport"><canvas id="image"></canvas></div>
            <canvas id="overlay"></canvas>
            <div id="footer">
                <p><?php out('%s', $photomicrograph->getTitle()) ?></p>
            </div>
            <div id="scaleBar"><span>&#xa0;</span></div>
            <section class="feedback">
                <a href="http://cms.virmisco.org/index.php/contact.html?id=<?php out($photomicrographId) ?>" target="_blank">Leave feedback</a>
            </section>
            <div id="controls">
                <label><input id="rotation" type="range" min="-180" max="180" value="0" /> Rotation <output>°</output></label>
                <label><input id="zoom" type="range" min="-100" max="100" value="0" /> Zoom <output>x</output></label>
                <label><input id="playbackRate" type="range" min="0" max="12" value="0" /> Playback rate <output>x</output></label>
                <div>
                    <div id="range"></div>
                    <label><input id="loopBegin" type="range" min="0" max="<?php echo $maxPlane ?>" value="0" />
                        Loop begin <output>&#xa0;</output></label>
                    <label><input id="focus" type="range" min="0" max="<?php echo $maxPlane ?>" value="0" />
                        Focal plane <output>&#xa0;</output></label>
                    <label><input id="loopEnd" type="range" min="0" max="<?php echo $maxPlane ?>"
                            value="<?php echo $maxPlane ?>" />
                        Loop end <output>&#xa0;</output></label>
                </div>
                <label><input id="brightness" type="range" min="-50" max="50" value="0" /> Brightness <output>&#xa0;</output></label>
                <label><input id="contrast" type="range" min="-50" max="50" value="0" /> Contrast <output>x</output></label>
                <div class="playback">
                    <span class="reverse" title="continuous reverse playback (Num7)">
                        <input type="radio" id="playbackReverse" name="playback"/>
                        <label for="playbackReverse">&lt;&lt;</label>
                    </span>
                    <span class="step-back" title="single step back (Num4)">
                        <input type="radio" id="playbackStepBack" name="playback"/>
                        <label for="playbackStepBack">|&lt;</label>
                    </span>
                    <span class="pause" title="pause (Num5)">
                        <input type="radio" id="playbackPause" name="playback"/>
                        <label for="playbackPause">||</label>
                    </span>
                    <span class="step-ahead" title="single step forward (Num6)">
                        <input type="radio" id="playbackStepAhead" name="playback"/>
                        <label for="playbackStepAhead">>|</label>
                    </span>
                    <span class="forward" title="continuous forward playback (Num9)">
                        <input type="radio" id="playbackForward" name="playback"/>
                        <label for="playbackForward">>></label>
                    </span>
                    |
                    <span class="loop" title="continuous loop within selected range (Num8)">
                        <input type="radio" id="playbackLoop" name="playback" checked/>
                        <label for="playbackLoop">|&lt;=>|</label>
                    </span>
                    <span style="display: inline-block; text-align: center; background: #444; border: 1px solid #eee; border-radius: 3px; padding: 0 46px; line-height: 18px; font-weight: bold; margin-left: 143px">
                    	<a  id="reset" href="#" style="text-decoration: none;">Reset</a>
                    </span>
                </div>
            </div>
            <section class="wrapper">
                <section id="navigation">
                    <p><a href="../search/">Back to search</a></p>
                    <form action="?" method="post">
                        <?php foreach ($otherIds as $k => $id): ?>
                            <input type="hidden" name="choice[]" value="<?php echo $id ?>" />
                            <?php if ($id == $photomicrographId): ?>
                                <p class="active"><?php out($photomicrograph->getTitle()) ?></p>
                            <?php else:
                                $loadPhotomicrograph->execute([$id]);
                                /** @var Photomicrograph $otherPhotomicrograph */
                                $otherPhotomicrograph = $loadPhotomicrograph->fetch();
                                ?>
                                <p>
                                    <button type="submit" name="id" value="<?php echo $id ?>"><?php
                                            out($otherPhotomicrograph->getTitle()) ?></button>
                                </p>
                            <?php endif ?>
                        <?php endforeach ?>
                    </form>
                </section>
                <section id="metadata">
                    <h1><?php out($photomicrograph->getTitle()) ?></h1>
                    <section>
                        <h2>Species & Specimen</h2>
                        <dl>
                            <dt>Original name</dt><dd><?php out($scientificName) ?>&#xa0;</dd>
                            <dt>Valid name</dt><dd><?php out($validName) ?>&#xa0;</dd>
                            <dt>Higher taxa</dt><dd><?php out(str_replace(' ', ' » ', trim($organism->getHigherTaxa()))) ?>&#xa0;</dd>
                            <?php if ($organism->getTypeDesignation()): ?>
                                <dt>Type status</dt>
                                <dd><?php out($organism->getTypeDesignation()->getTypeStatus()) ?>&#xa0;</dd>
                            <?php elseif ($organism->getIdentification()): ?>
                                <dt>Identified by</dt>
                                <dd><?php out($organism->getIdentification()->getIdentifier()) ?>&#xa0;</dd>
                                <dt>Qualifier</dt>
                                <dd><?php out($organism->getIdentification()->getQualifier()) ?>&#xa0;</dd>
                            <?php endif ?>
                            <dt>Phase or stage</dt><dd><?php out($organism->getPhaseOrStage()) ?>&#xa0;</dd>
                            <dt>Sex</dt><dd><?php out($organism->getSex()) ?>&#xa0;</dd>
                            <dt>Remarks</dt><dd><?php out($organism->getRemarks()) ?>&#xa0;</dd>
                        </dl>
                    </section>
                    <section>
                        <h2>Collection object</h2>
                        <dl>
                            <dt>Collection</dt><dd><?php out($specimenCarrier->getOwner()) ?>&#xa0;</dd>
                            <!--<dt>Previous collection</dt><dd><?php out($specimenCarrier->getPreviousCollection()) ?>&#xa0;</dd>-->
                            <!--<dt>Journal number</dt><dd><?php out($gathering->getJournalNumber()) ?>&#xa0;</dd>-->
                            <dt>Collection number</dt><dd><?php out($specimenCarrier->getCarrierNumber()) ?>&#xa0;</dd>
                            <dt>Object number</dt><dd><?php out($specimenCarrier->getCarrierNumber()) ?>-<?php out($organism->getSequenceNumber()) ?>&#xa0;</dd>
                            <dt>Preparation type</dt><dd><?php out($specimenCarrier->getPreparationType()) ?>&#xa0;</dd>
                        </dl>
                    </section>
                    <section>
                        <h2>Locality & Sampling</h2>
                        <dl>
                            <dt>Location</dt><dd><?php out($gathering->getLocation()) ?>&#xa0;</dd>
                            <dt>Sampling date</dt><dd><?php out($gathering->getSamplingDate()) ?>&#xa0;</dd>
                            <dt>Collector(s)</dt><dd><?php out($gathering->getAgent()) ?>&#xa0;</dd>
                            <dt>Remarks</dt><dd><?php out($gathering->getRemarks()) ?>&#xa0;</dd>
                            <dt>Label transcript</dt><dd><?php out($specimenCarrier->getLabelTranscript()) ?>&#xa0;</dd>
                        </dl>
                    </section>
                    <section>
                        <h2>Photomicrograph</h2>
                        <dl>
                            <dt>Direct link</dt>
                            <dd><a href="<?php out($photomicrograph->getPresentationUri()) ?>"><?php
                                        out($photomicrograph->getTitle()) ?></a></dd>
                            <dt>Download</dt>
                            <dd>
                                <a href="<?php out('%s', $photomicrograph->getFile()->getUri()) ?>">ZIP archive</a>
                                |
                                <a href="<?php out('%s', str_replace('.zip', '.ogg',$photomicrograph->getFile()->getUri())) ?>">Ogg/Theora</a>
                                |
                                <a href="<?php out('%s', str_replace('.zip', '.mp4',$photomicrograph->getFile()->getUri())) ?>">H.264/MPEG-4 AVC</a>
                            </dd>
                            <dt>Creator capturing</dt><dd><?php out($photomicrograph->getCreatorCapturing()) ?>&#xa0;</dd>
                            <dt>Creator processing</dt><dd><?php out($photomicrograph->getCreatorProcessing()) ?>&#xa0;</dd>
                        </dl>
                        <section>
                            <h3>Digitization</h3>
                            <dl>
                                <dt>Width [px]</dt>
                                <dd><?php out($photomicrograph->getDigitizationData()->getWidth()) ?>&#xa0;</dd>
                                <dt>Height [px]</dt>
                                <dd><?php out($photomicrograph->getDigitizationData()->getHeight()) ?>&#xa0;</dd>
                                <dt>Color depth [bit]</dt>
                                <dd><?php out($photomicrograph->getDigitizationData()->getColorDepth()) ?>&#xa0;</dd>
                                <dt>Reproduction scale [µm/px]</dt>
                                <dd><?php out(
                                        '%.5F × %.5F',
                                        $photomicrograph->getDigitizationData()->getReproductionScaleHorizontal() * 1e6,
                                        $photomicrograph->getDigitizationData()->getReproductionScaleVertical() * 1e6
                                    ) ?></dd>
                            </dl>
                        </section>
                        <section>
                            <h3>Microscope settings</h3>
                            <dl>
                                <dt>Focal plane distance [µm]</dt>
                                <dd><?php out('%.3F', $focalPlaneDistance * 1e6) ?>&#xa0;</dd>
                                <dt>Contrast method</dt>
                                <dd><?php out($photomicrograph->getMicroscopeSettings()->getContrastMethod()) ?>&#xa0;</dd>
                                <dt>DIC prism position</dt>
                                <dd><?php out($photomicrograph->getMicroscopeSettings()->getDicPrismPosition()) ?>&#xa0;</dd>
                                <dt>Aperture diaphragm opening</dt>
                                <dd><?php out($photomicrograph->getMicroscopeSettings()->getApertureDiaphragmOpening()) ?>&#xa0;</dd>
                                <dt>Field diaphragm opening</dt>
                                <dd><?php out($photomicrograph->getMicroscopeSettings()->getFieldDiaphragmOpening()) ?>&#xa0;</dd>
                                <dt>Mag. changer magnification</dt>
                                <dd><?php out(
                                        '%.2F',
                                        $photomicrograph->getMicroscopeSettings()->getMagnificationChangerMagnification()
                                    ) ?>&#xa0;</dd>
                            </dl>
                        </section>
                        <section>
                            <h3>Microscope</h3>
                            <dl>
                                <dt>Stand</dt>
                                <dd><?php out($photomicrograph->getMicroscope()->getStandMaker()) ?>
                                    <?php out($photomicrograph->getMicroscope()->getStandName()) ?>
                                    (<?php out($photomicrograph->getMicroscope()->getStandArticleOrSerialNumber()) ?>)</dd>
                                <dt>Condenser</dt>
                                <dd><?php out($photomicrograph->getMicroscope()->getCondenserMaker()) ?>
                                    <?php out($photomicrograph->getMicroscope()->getCondenserName()) ?>
                                    (<?php out($photomicrograph->getMicroscope()->getCondenserArticleOrSerialNumber()) ?>) </dd>
                                <dt>Condenser turret prism</dt>
                                <dd><?php out($photomicrograph->getMicroscope()->getCondenserTurretPrismMaker()) ?>
                                    <?php out($photomicrograph->getMicroscope()->getCondenserTurretPrismName()) ?>
                                    (<?php out($photomicrograph->getMicroscope()->getCondenserTurretPrismArticleOrSerialNumber())
                                    ?>)</dd>
                                <dt>Nosepiece objective</dt>
                                <dd><?php out($photomicrograph->getMicroscope()->getNosepieceObjectiveMaker()) ?>
                                    <?php out($photomicrograph->getMicroscope()->getNosepieceObjectiveName()) ?>
                                    (<?php out($photomicrograph->getMicroscope()->getNosepieceObjectiveArticleOrSerialNumber())
                                    ?>)</dd>
                                <dt>Nosepiece obj. » Type</dt>
                                <dd><?php out($photomicrograph->getMicroscope()->getNosepieceObjectiveType()) ?>&#xa0;</dd>
                                <dt>Nosepiece obj. » Num. aperture</dt>
                                <dd><?php out('%.2F', $photomicrograph->getMicroscope()->getNosepieceObjectiveNumericalAperture()) ?>&#xa0;</dd>
                                <dt>Nosepiece obj. » Magnification</dt>
                                <dd><?php out('%.1F', $photomicrograph->getMicroscope()->getNosepieceObjectiveMagnification()) ?>&#xa0;</dd>
                                <dt>DIC turret prism</dt>
                                <dd><?php out($photomicrograph->getMicroscope()->getDicTurretPrismMaker()) ?>
                                    <?php out($photomicrograph->getMicroscope()->getDicTurretPrismName()) ?>
                                    (<?php out($photomicrograph->getMicroscope()->getDicTurretPrismArticleOrSerialNumber())
                                    ?>)</dd>
                                <dt>Magnification changer</dt>
                                <dd><?php out($photomicrograph->getMicroscope()->getMagnificationChangerMaker()) ?>
                                    <?php out($photomicrograph->getMicroscope()->getMagnificationChangerName()) ?>
                                    (<?php out($photomicrograph->getMicroscope()->getMagnificationChangerArticleOrSerialNumber())
                                    ?>)</dd>
                                <dt>Ports</dt>
                                <dd><?php out($photomicrograph->getMicroscope()->getPortsMaker()) ?>
                                    <?php out($photomicrograph->getMicroscope()->getPortsName()) ?>
                                    (<?php out($photomicrograph->getMicroscope()->getPortsArticleOrSerialNumber()) ?>)</dd>
                                <dt>C-mount adapter</dt>
                                <dd><?php out($photomicrograph->getMicroscope()->getCameraMountAdapterMaker()) ?>
                                    <?php out($photomicrograph->getMicroscope()->getCameraMountAdapterName()) ?>
                                    (<?php out($photomicrograph->getMicroscope()->getCameraMountAdapterArticleOrSerialNumber())
                                    ?>)</dd>
                                <dt>C-mount ad. » Magnification</dt>
                                <dd><?php out('%.3F', $photomicrograph->getMicroscope()->getCameraMountAdapterMagnification()) ?></dd>
                            </dl>
                        </section>
                        <section>
                            <h3>Camera</h3>
                            <dl>
                                <dt>Name</dt>
                                <dd><?php out($photomicrograph->getCamera()->getCameraMaker()) ?>
                                    <?php out($photomicrograph->getCamera()->getCameraName()) ?>
                                    (<?php out($photomicrograph->getCamera()->getCameraArticleOrSerialNumber()) ?>)</dd>
                                <dt>Sensor</dt>
                                <dd><?php out($photomicrograph->getCamera()->getSensorMaker()) ?>
                                    <?php out($photomicrograph->getCamera()->getSensorName()) ?>
                                    (<?php out($photomicrograph->getCamera()->getSensorArticleOrSerialNumber()) ?>)</dd>
                                <dt>Optical format</dt>
                                <dd><?php out($photomicrograph->getCamera()->getOpticalFormat()) ?>&#xa0;</dd>
                                <dt>Capture format</dt>
                                <dd><?php out($photomicrograph->getCamera()->getCaptureFormat()) ?>&#xa0;</dd>
                                <dt>Chip size [mm]</dt>
                                <dd><?php out(
                                        '%.2F × %.2F',
                                        $photomicrograph->getCamera()->getChipWidth() * 1e3,
                                        $photomicrograph->getCamera()->getChipHeight() * 1e3
                                    ) ?></dd>
                                <dt>Pixel size [µm]</dt>
                                <dd><?php out(
                                        '%.1F × %.1F',
                                        $photomicrograph->getCamera()->getPixelWidth() * 1e6,
                                        $photomicrograph->getCamera()->getPixelHeight() * 1e6
                                    ) ?></dd>
                                <dt>Active pixels</dt>
                                <dd><?php out($photomicrograph->getCamera()->getActivePixelsHor()) ?>
                                    × <?php out($photomicrograph->getCamera()->getActivePixelsVer()) ?></dd>
                                <dt>Color filter array</dt>
                                <dd><?php out($photomicrograph->getCamera()->getColorFilterArray()) ?>&#xa0;</dd>
                                <dt>Protective color filter</dt>
                                <dd><?php out($photomicrograph->getCamera()->getProtectiveColorFilter()) ?>&#xa0;</dd>
                                <dt>ADC resolution</dt>
                                <dd><?php out($photomicrograph->getCamera()->getAdcResolution()) ?>&#xa0;</dd>
                                <dt>Dynamic range [dB]</dt>
                                <dd><?php out($photomicrograph->getCamera()->getDynamicRange()) ?>&#xa0;</dd>
                                <dt>SNRmax [dB]</dt>
                                <dd><?php out($photomicrograph->getCamera()->getSnrMax()) ?>&#xa0;</dd>
                                <dt>Readout noise</dt>
                                <dd><?php out($photomicrograph->getCamera()->getReadoutNoise()) ?>&#xa0;</dd>
                            </dl>
                        </section>
                    </section>
                    <?php foreach ($focalPlanes as $index => $focalPlane): ?>
                        <section class="focal-plane" id="fpi-<?php out($index) ?>">
                            <h2>Focal plane image #<?php out($index) ?></h2>
                            <dl>
                                <dt>Focus position [mm]</dt>
                                <dd><?php out('%.5F', $focalPlane->getFocusPosition() * 1e3) ?>&#xa0;</dd>
                            </dl>
                            <section>
                                <h3>Exposure settings</h3>
                                <dl>
                                    <dt>Exposure time [s]</dt><dd><?php out('%.3F', $focalPlane->getExposureSettings()->getDuration()) ?>&#xa0;</dd>
                                    <dt>Gain</dt><dd><?php out($focalPlane->getExposureSettings()->getGain()) ?>&#xa0;</dd>
                                </dl>
                            </section>
                            <section>
                                <h3>Histogram adjustments</h3>
                                <dl>
                                    <dt>Gamma</dt><dd><?php out($focalPlane->getHistogramSettings()->getGamma()) ?>&#xa0;</dd>
                                    <dt>Black clip</dt><dd><?php out($focalPlane->getHistogramSettings()->getBlackClip()) ?>&#xa0;</dd>
                                    <dt>White clip</dt><dd><?php out($focalPlane->getHistogramSettings()->getWhiteClip()) ?>&#xa0;</dd>
                                </dl>
                            </section>
                        </section>
                    <?php endforeach ?>
                </section>
            </section>
            <section class="snapshot">
                Snapshot
                <p><a id="snapshot-region" href="#">Current region</a></p>
                <p><a id="snapshot-frame" href="#">Whole image (frame)</a></p>
            </section>
            <div id="measurementBox">
		    <div class="measurement">
		        Tape measure
		        <input id="measurement" type="checkbox"/>
		        <label for="measurement">Set begin</label>
		    </div>
		    <section id="measurementComponents">
		        <label><span><abbr title="distance along X axis">Δx</abbr></span><output id="deltaX">&#xa0;</output></label>
		        <label><span><abbr title="distance along Y axis">Δy</abbr></span><output id="deltaY">&#xa0;</output></label>
		        <label><span><abbr title="distance between focal planes">Δz</abbr></span><output id="deltaZ">&#xa0;</output></label>
		        <label><span><abbr title="projected distance (ignoring Δz)">Δproj</abbr></span><output id="deltaProjected">&#xa0;</output></label>
		        <label><span><abbr title="true total distance">Δtrue</abbr></span><output id="deltaTrue">&#xa0;</output></label>
		    </section>
	    </div>
        </main>
        <script type="text/javascript">
            var frameRate = 15;
            var frameCountSingle = '<?php echo $maxPlane + 1 ?>' - 0;
            var width = '<?php echo $photomicrograph->getDigitizationData()->getWidth() ?>' - 0;
            var height = '<?php echo $photomicrograph->getDigitizationData()->getHeight() ?>' - 0;
            var reproductionScale = '<?php echo $photomicrograph->getDigitizationData()->getReproductionScaleHorizontal() ?>' - 0;
            var focalPlaneDistance = '<?php echo $focalPlaneDistance ?>' - 0;
        </script>
        <script type="text/javascript" src="viewer.js"></script>
    </body>
</html>
