<?php
    use sednasoft\virmisco\readlayer\AugmentedSamplingDate;

    require_once '../vendor/autoload.php';
    require_once '../src/credentials.php';
    require_once 'functions.php';
    header('Content-Type: text/html; charset=UTF-8');
    $connection = new PDO(MARIA_DSN, MARIA_USER, MARIA_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_FETCH_TABLE_NAMES => true
    ]);
    $organismForPhotomicrograph = $connection->prepare(
    /** @lang MySQL */
        <<<'EOD'
SELECT *
FROM `organism` `o`
LEFT JOIN `scientific_name` `mn` ON `mn`.`specimen_carrier_id` = `o`.`specimen_carrier_id` AND `mn`.`sequence_number` = `o`.`sequence_number` AND `mn`.`is_mentioned` = 'true'
LEFT JOIN `scientific_name` `vn` ON `vn`.`specimen_carrier_id` = `o`.`specimen_carrier_id` AND `vn`.`sequence_number` = `o`.`sequence_number` AND `vn`.`is_valid` = 'true'
LEFT JOIN `scientific_name` `sn` ON `sn`.`specimen_carrier_id` = `o`.`specimen_carrier_id` AND `sn`.`sequence_number` = `o`.`sequence_number` AND !`sn`.`is_valid` AND !`sn`.`is_mentioned`
WHERE `o`.`id` = ?
EOD
    );
    $organisms = [];
    $photomicrographs = [];
    $words = [];
    if (isset($_POST['search']) && $_POST['search']) {
        $fullTextSearchStatements = prepareFullTextSearchStatements($connection);
        $words = preg_split('<[^\w\s]*\s[^\w]*>', $_POST['search']);
        foreach ($words as $word) {
            foreach ($fullTextSearchStatements as $tableName => $statement) {
                $statement->execute(['text' => $word]);
                populatePhotomicrographList($statement, $photomicrographs);
            }
        }
    } else {
        $filters = [
            'higherTaxon' => null,
            'genus' => null,
            'species' => null,
            'country' => null,
            'province' => null,
            'region' => null,
            'place' => null,
            'dateBefore' => isset($_POST['dateBefore']) && $_POST['dateBefore'] ? $_POST['dateBefore'] : null,
            'dateOnOrAfter' => isset($_POST['dateOnOrAfter']) && $_POST['dateOnOrAfter'] ? $_POST['dateOnOrAfter'] : null
        ];
        foreach ($filters as $key => $filter) {
            if (isset($_POST[$key]) && substr($_POST[$key], 0, 7) === 'string:') {
                $filters[$key] = trim(substr($_POST[$key], 7));
                $words[] = $filters[$key];
            }
        }
        $statement = $connection->prepare(
            /** @lang MySQL */
            <<<'EOD'
SELECT *
FROM `photomicrograph` `p`
JOIN `organism` `o` ON `o`.`id` = `p`.`organism_id`
JOIN `specimen_carrier` `sc` ON `sc`.`id` = `o`.`specimen_carrier_id`
JOIN `gathering` `g` ON `g`.`id` = `sc`.`gathering_id`
LEFT JOIN `scientific_name` `mn` ON `mn`.`specimen_carrier_id` = `o`.`specimen_carrier_id` AND `mn`.`sequence_number` = `o`.`sequence_number` AND `mn`.`is_mentioned` = 'true'
LEFT JOIN `scientific_name` `vn` ON `vn`.`specimen_carrier_id` = `o`.`specimen_carrier_id` AND `vn`.`sequence_number` = `o`.`sequence_number` AND `vn`.`is_valid` = 'true'
LEFT JOIN `scientific_name` `sn` ON `sn`.`specimen_carrier_id` = `o`.`specimen_carrier_id` AND `sn`.`sequence_number` = `o`.`sequence_number` AND !`sn`.`is_valid` AND !`sn`.`is_mentioned`
WHERE (:country IS NULL OR `g`.`location__country` = :country)
AND (:province IS NULL OR `g`.`location__province` = :province)
AND (:region IS NULL OR `g`.`location__region` = :region)
AND (:place IS NULL OR `g`.`location__place` = :place)
AND (:dateBefore IS NULL OR date(`g`.`sampling_date__before`) <= :dateBefore)
AND (:dateOnOrAfter IS NULL OR date(`g`.`sampling_date__after`) >= :dateOnOrAfter)
AND (:higherTaxon IS NULL OR `o`.`higher_taxa` = :higherTaxon)
AND (:genus IS NULL OR `mn`.`genus` = :genus OR `vn`.`genus` = :genus OR `sn`.`genus` = :genus)
AND (:species IS NULL OR concat_ws(' ', `mn`.`genus`, `mn`.`specific_epithet`) = :species OR concat_ws(' ', `vn`.`genus`, `vn`.`specific_epithet`) = :species OR concat_ws(' ', `sn`.`genus`, `sn`.`specific_epithet`) = :species)
EOD
        );
        $statement->execute($filters);
        populatePhotomicrographList($statement, $photomicrographs);
    }
    uasort($photomicrographs, function ($a, $b) {
        $definiteResult = $a['@hits'] - $b['@hits'];
        if ($definiteResult !== 0) {
            return $definiteResult;
        }
        $definiteResult = isset($a['vn.genus'], $b['vn.genus']) ? strcmp($a['vn.genus'], $b['vn.genus']) : 0;
        if ($definiteResult !== 0) {
            return $definiteResult;
        }
        $definiteResult = isset($a['vn.specific_epithet'], $b['vn.specific_epithet'])
            ? strcmp($a['vn.specific_epithet'], $b['vn.specific_epithet'])
            : 0;
        if ($definiteResult !== 0) {
            return $definiteResult;
        }
        return isset($a['p.title'], $b['p.title']) ? strcmp($a['p.title'], $b['p.title']) : 0;
    });
    foreach ($photomicrographs as $record) {
        if ($record['p.presentation_uri']) {
            if (!isset($record['o.id'], $record['mn.id'], $record['vn.id'], $record['sn.id'])) {
                $organismForPhotomicrograph->execute([$record['p.organism_id']]);
                $record = $organisms[$record['p.organism_id']][$record['p.id']] = array_merge(
                    $record,
                    $organismForPhotomicrograph->fetch(PDO::FETCH_ASSOC)
                );
            } else {
                $organisms[$record['p.organism_id']][$record['p.id']] = $record;
            }
            foreach ($record as $field => $property) {
                foreach ($words as $word) {
                    if (stripos($property, $word) !== false && strpos($field, 'uri') === false) {
                        list($tbl, $col) = explode('.', $field, 2);
                        switch ($tbl) {
                            case 'cs':
                                $tbl = 'Carrier scan';
                                break;
                            case 'fpi':
                                $tbl = 'Focal plane image';
                                break;
                            case 'g':
                                $tbl = 'Gathering';
                                break;
                            case 'o':
                                $tbl = 'Organism';
                                break;
                            case 'p':
                                $tbl = 'Photomicrograph';
                                break;
                            case 'mn':
                                $tbl = 'Original name';
                                break;
                            case 'vn':
                                $tbl = 'Valid name';
                                break;
                            case 'sn':
                                $tbl = 'Other synonym';
                                break;
                            case 'sc':
                                $tbl = 'Specimen carrier';
                                break;
                        }
                        $col = str_replace('_', ' ', implode(' » ', array_map('ucfirst', explode('__', $col))));
                        $field = $tbl ? implode(' » ', [$tbl, $col]) : $col;
                        $text = '';
                        foreach (preg_split(sprintf('<(\\Q%s\\E)>i', $word), $property, -1,
                            PREG_SPLIT_DELIM_CAPTURE) as $k => $v) {
                            $v = htmlspecialchars($v);
                            if ($k % 2) {
                                $v = sprintf('<strong>%s</strong>', $v);
                            }
                            $text .= $v;
                        }
                        $organisms[$record['p.organism_id']][$record['p.id']]['@matches'][$field] = $text;
                    }
                }
            }
            if (isset($filters) && ($filters['dateBefore'] || $filters['dateOnOrAfter'])) {
                $augmentedSamplingDate = new AugmentedSamplingDate(
                    $record['g.sampling_date__after'],
                    $record['g.sampling_date__before']
                );
                $organisms[$record['p.organism_id']][$record['p.id']]['@matches']['Gathering » sampling date']
                    = strval($augmentedSamplingDate);
            }
            $organisms[$record['p.organism_id']][$record['p.id']]['@mentionedName'] = implode(
                ' ',
                array_filter([
                    $record['mn.genus'],
                    $record['mn.subgenus'] ? sprintf('(%s)', $record['mn.subgenus']) : null,
                    $record['mn.specific_epithet'],
                    $record['mn.infraspecific_epithet'],
                    sprintf(
                        $record['mn.is_parenthesized'] ? '(%s)' : '%s',
                        implode(', ', array_filter([$record['mn.authorship'], $record['mn.year']]))
                    )
                ])
            );
            $organisms[$record['p.organism_id']][$record['p.id']]['@validName'] = implode(
                ' ',
                array_filter([
                    $record['vn.genus'],
                    $record['vn.subgenus'] ? sprintf('(%s)', $record['vn.subgenus']) : null,
                    $record['vn.specific_epithet'],
                    $record['vn.infraspecific_epithet'],
                    sprintf(
                        $record['vn.is_parenthesized'] ? '(%s)' : '%s',
                        implode(', ', array_filter([$record['vn.authorship'], $record['vn.year']]))
                    )
                ])
            );
            $organisms[$record['p.organism_id']][$record['p.id']]['@scientificName'] = implode(
                ' ',
                array_filter([
                    $record['sn.genus'],
                    $record['sn.subgenus'] ? sprintf('(%s)', $record['sn.subgenus']) : null,
                    $record['sn.specific_epithet'],
                    $record['sn.infraspecific_epithet'],
                    sprintf(
                        $record['sn.is_parenthesized'] ? '(%s)' : '%s',
                        implode(', ', array_filter([$record['sn.authorship'], $record['sn.year']]))
                    )
                ])
            );
        }
    }
?>
<!DOCTYPE html>
<html>
    <head lang="en">
        <meta charset="UTF-8">
        <title>Select digital photomicrographs</title>
        <link rel="stylesheet" href="../css/bootstrap.css" />
        <link rel="stylesheet" href="../css/bootstrap-theme.css" />
        <link rel="stylesheet" href="../css/customization.css" />
        <style type="text/css">
            .results select { float: left; width: 360px; }
            .results dt { color: #666; margin: 3px 0; padding: 0; width: 200px; overflow: hidden; clear: left; float: left; }
            .results dd { margin: 3px 12px; padding: 0; }
            .results .preview { position: relative; margin-left: 200px; height: 0; border-style: solid; border-color: transparent #ccc; border-width: 75px 0 75px 75px; }
            .results .preview img { height: 150px; margin-top: -75px; }
            .results .preview img#preview { cursor: move; margin: -75px 75px 0 -275px}
            .results .preview #marker { position: absolute; top: -80px; left: -5px; width: 10px; height: 10px; border: 1px solid #000; border-radius: 5px; background: Highlight; }
            ul.well { min-height: 120px; }
            ul.well li { margin: 0 12px 12px 0; display: inline-block; width: 102px; vertical-align: top; cursor: move; }
            ul.well li * { width: 100px; }
            ul.well li img { display: block; margin-bottom: 6px; height: 100px; border: 1px solid #000; overflow: hidden; text-overflow: ellipsis; }
            ul.well li span { word-wrap: break-word; }
            #trash { background: url("trash.png") no-repeat bottom; height: 100px; font-size: 1px; color: transparent; }
        </style>
    </head>
    <body class="container">
        <header>
            <h1>virmisco | Select digital photomicrographs</h1>
        </header>
        <main>
            <div class="row">
                <div class="col-sm-4">
                    <div class="form-group">
                        <label for="resultList" class="control-label">1. Select a photomicrograph</label>
                        <select id="resultList" class="form-control" size="22">
                            <?php foreach ($organisms as $oid => $photomicrographs): ?>
                                <optgroup label="<?php
                                    foreach ($photomicrographs as $pid => $photomicrograph):
                                        echo htmlspecialchars($photomicrograph['vn.genus'] . ' ' . $photomicrograph['vn.specific_epithet']);
                                        break;
                                    endforeach
                                ?>">
                                    <?php foreach ($photomicrographs as $pid => $photomicrograph): ?>
                                        <option data-preview="<?php echo htmlspecialchars(preg_replace('<\.zip$>',
                                            '.gif',
                                            $photomicrograph['p.file__uri'])) ?>"
                                            data-higher-taxa="<?php echo htmlspecialchars(str_replace(' ', ' » ',
                                                $photomicrograph['o.higher_taxa'])) ?>"
                                            data-scientific-name="<?php echo htmlspecialchars($photomicrograph['@mentionedName']) ?>"
                                            data-valid-name="<?php echo htmlspecialchars($photomicrograph['@validName']) ?>"
                                            data-match="<?php echo htmlspecialchars(json_encode($photomicrograph['@matches'])) ?>"
                                            data-detail-of="<?php echo htmlspecialchars(
                                                implode(
                                                    ':',
                                                    $photomicrograph['p.detail_of__photomicrograph_id']
                                                        ? [
                                                            $photomicrograph['p.detail_of__photomicrograph_id'],
                                                            $photomicrograph['p.detail_of__hotspot__x'],
                                                            $photomicrograph['p.detail_of__hotspot__y']
                                                        ]
                                                        : []
                                                )
                                            ) ?>"
                                            value="<?php echo htmlspecialchars($photomicrograph['p.id']) ?>">
                                            <?php echo htmlspecialchars($photomicrograph['p.title']) ?>
                                        </option>
                                    <?php endforeach ?>
                                </optgroup>
                            <?php endforeach ?>
                        </select>
                    </div>
                </div>
                <div class="results col-sm-8">
                    <div class="form-group">
                        <label class="control-label">2. Drag the preview image down to the selection box</label>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h2 class="panel-title"></h2>
                            </div>
                            <div class="panel-body form-horizontal">
                                <div class="form-group">
                                    <label class="control-label col-sm-2">Higher taxa:</label>
                                    <div class="form-control-static col-sm-10" id="higherTaxa"></div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-2">Original name:</label>
                                    <div class="form-control-static col-sm-10" id="scientificName"></div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-2">Valid name:</label>
                                    <div class="form-control-static col-sm-10" id="validName"></div>
                                </div>
                            </div>
                            <div class="panel-heading">
                                <h3 class="panel-title">Properties matching search criteria</h3>
                            </div>
                            <div id="metadata" class="panel-body form-horizontal"></div>
                        </div>
                        <div class="preview">
                            <img src="" alt="" id="preview" />
                        </div>
                        <input style="width: 200px" type="button" value="Select" id="addBtn" />
                    </div>
                </div>
            </div>
            <label class="row control-label">3. Reorder items or drag them to the trash bin</label>
            <div class="row">
                <div class="col-sm-1" id="trash">Trash</div>
                <ul class="well well-sm col-sm-10"></ul>
                <form class="form-inline controls col-sm-1" action="../viewer/" method="post">
                    <input id="show-in-viewer" class="btn btn-default" type="submit" value="Show in viewer" />
                </form>
            </div>
        </main>
        <script type="application/javascript" src="script.js?id=5"></script>
    </body>
</html>
