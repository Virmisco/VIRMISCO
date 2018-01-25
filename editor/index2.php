<?php
    require_once '../vendor/autoload.php';
    require_once '../src/credentials.php';
    //
    $connection = new PDO(MARIA_DSN, MARIA_USER, MARIA_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
    ]);
    $listOrganisms = $connection->query(
    // TODO no date truncation before applying TZ offset
        <<<'EOD'
SELECT
    `g`.`id` `gathering_id`,
    `sc`.`id` `carrier_id`,
    `o`.`id` `organism_id`,
    `g`.`journal_number`,
    `sc`.`carrier_number`,
    `o`.`sequence_number`,
    concat_ws(
        ' ',
        `sn`.`genus`,
        concat('(', if(`sn`.`subgenus` = '', NULL, `sn`.`subgenus`), ')'),
        `sn`.`specific_epithet`,
        if(`sn`.`infraspecific_epithet` = '', NULL, `sn`.`infraspecific_epithet`)
    ) `scientific_name`,
    `o`.`type_designation__type_status` `type_status`,
    `g`.`location__place` `place`,
    `g`.`location__region` `region`,
    `g`.`location__province` `province`,
    `g`.`location__country` `country`,
    concat_ws(
        ', ',
        if(`g`.`location__place` = '', NULL, `g`.`location__place`),
        if(`g`.`location__region` = '', NULL, `g`.`location__region`),
        if(`g`.`location__province` = '', NULL, `g`.`location__province`),
        if(`g`.`location__country` = '', NULL, `g`.`location__country`)
    ) `location`,
    `g`.`agent__person` `person`,
    `g`.`agent__organization` `organization`,
    ifnull(`g`.`agent__person`, `g`.`agent__organization`) `agent`,
    `g`.`sampling_date__after` `after`,
    `g`.`sampling_date__before` `before`,
    `g`.`remarks`,
    `sc`.`label_transcript`,
    `sc`.`owner`,
    `sc`.`preparation_type`,
    `sc`.`previous_collection`,
    `o`.`identification__identifier` `identifier`,
    `o`.`identification__qualifier` `qualifier`,
    `o`.`phase_or_stage`,
    `o`.`remarks` `organism_remarks`,
    `o`.`sex`
FROM `organism` `o`
JOIN `specimen_carrier` `sc` ON `sc`.`id` = `o`.`specimen_carrier_id`
JOIN `gathering` `g` ON `g`.`id` = `sc`.`gathering_id`
LEFT JOIN `scientific_name` `sn`
    ON `sn`.`id` = `o`.`identification__scientific_name_id` OR `sn`.`id` = `o`.`type_designation__scientific_name_id`
ORDER BY `after` DESC, `before`, `carrier_number`, `sequence_number`
EOD
    );
    $gatherings = [];
    $carriers = [];
    $organisms = [];
    foreach ($listOrganisms as $organism) {
        $gatherings[$organism->gathering_id] = $organism;
        $carriers[$organism->gathering_id][$organism->carrier_id] = $organism;
        $organisms[$organism->carrier_id][$organism->organism_id] = $organism;
    }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <link rel="stylesheet" href="css/bootstrap.css"/>
        <link rel="stylesheet" href="css/bootstrap-theme.css"/>
        <style type="text/css">
            thead th.column-sort { cursor: pointer; }
            thead th.column-sort:after { content: ' △▽'; }
            thead th.asc:after { content: ' ▲▽'; }
            thead th.desc:after { content: ' △▼'; }
        </style>
        <script src="js/datalist.js"></script>
        <title>virmisco | backend</title>
    </head>
    <body class="container-fluid">
        <header>
            <h1>virmisco | backend</h1>
        </header>
        <main>
            <p>
                <a href="index.html" class="btn btn-default btn-xs">
                    <span class="glyphicon glyphicon-chevron-left"></span>
                    back to the list
                </a>
            </p>
            <form class="form-horizontal">
                <datalist id="034d396f-b8ec-4d4c-a332-e58f74642ced">
                    <?php /** @var stdClass $gathering */ foreach ($gatherings as $gathering): ?>
                        <option value="<?php echo $gathering->gathering_id ?>"
                            data-journal-number="<?php echo htmlspecialchars($gathering->journal_number) ?>"
                            data-after="<?php echo htmlspecialchars(substr($gathering->after, 0, 10)) ?>"
                            data-before="<?php echo htmlspecialchars(substr($gathering->before, 0, 10)) ?>"
                            data-person="<?php echo htmlspecialchars($gathering->person) ?>"
                            data-organization="<?php echo htmlspecialchars($gathering->organization) ?>"
                            data-place="<?php echo htmlspecialchars($gathering->place) ?>"
                            data-region="<?php echo htmlspecialchars($gathering->region) ?>"
                            data-province="<?php echo htmlspecialchars($gathering->province) ?>"
                            data-country="<?php echo htmlspecialchars($gathering->country) ?>"
                            data-remarks="<?php echo htmlspecialchars($gathering->remarks) ?>"
                            ><?php echo htmlspecialchars(
                                sprintf(
                                    '%s: %s; %s (%s)',
                                    $gathering->journal_number,
                                    formatDateInterval($gathering->after, $gathering->before),
                                    $gathering->agent,
                                    $gathering->location
                                )
                            ) ?></option>
                    <?php endforeach ?>
                </datalist>
                <?php /** @var stdClass $gathering */ foreach ($carriers as $gatheringId => $gathering): ?>
                    <datalist id="<?php echo $gatheringId ?>">
                        <?php /** @var stdClass $carrier */ foreach ($gathering as $carrier): ?>
                            <option value="<?php echo $carrier->carrier_id ?>"
                                data-carrier-number="<?php echo htmlspecialchars($carrier->carrier_number) ?>"
                                data-preparation-type="<?php echo htmlspecialchars($carrier->preparation_type) ?>"
                                data-owner="<?php echo htmlspecialchars($carrier->owner) ?>"
                                data-previous-collection="<?php echo htmlspecialchars($carrier->previous_collection) ?>"
                                data-label-transcript="<?php echo htmlspecialchars($carrier->label_transcript) ?>"
                                ><?php echo htmlspecialchars(
                                    sprintf(
                                        '%s: %s “%s”',
                                        $carrier->carrier_number,
                                        $carrier->preparation_type,
                                        $carrier->label_transcript
                                    )
                                ) ?></option>
                        <?php endforeach ?>
                    </datalist>
                <?php endforeach ?>
                <?php /** @var stdClass $carrier */ foreach ($organisms as $carrierId => $carrier): ?>
                    <datalist id="<?php echo $carrierId ?>">
                        <?php /** @var stdClass $organism */ foreach ($carrier as $organism): ?>
                            <option value="<?php echo $organism->organism_id ?>"
                                data-sequence-number="<?php echo htmlspecialchars($organism->sequence_number) ?>"
                                data-type-status="<?php echo htmlspecialchars($organism->type_status) ?>"
                                data-scientific-name="<?php echo htmlspecialchars($organism->scientific_name) ?>"
                                data-identifier="<?php echo htmlspecialchars($organism->identifier) ?>"
                                data-qualifier="<?php echo htmlspecialchars($organism->qualifier) ?>"
                                data-phase-or-stage="<?php echo htmlspecialchars($organism->phase_or_stage) ?>"
                                data-sex="<?php echo htmlspecialchars($organism->sex) ?>"
                                data-remarks="<?php echo htmlspecialchars($organism->organism_remarks) ?>"
                                ><?php echo htmlspecialchars(
                                    sprintf(
                                        '%s: %s (%s, %s)',
                                        $organism->sequence_number,
                                        $organism->scientific_name,
                                        $organism->phase_or_stage,
                                        $organism->sex
                                    )
                                ) ?></option>
                        <?php endforeach ?>
                    </datalist>
                <?php endforeach ?>
                <div class="row">
                    <fieldset class="col-md-4">
                        <legend class="col-sm-offset-3 col-md-offset-5 col-sm-9 col-md-7">Gathering</legend>
                        <input type="hidden" name="gathering_id" />
                        <div class="form-group">
                            <label class="control-label col-sm-3 col-md-5" for="fc01">Journal number</label>
                            <div class="col-sm-9 col-md-7">
                                <input id="fc01" class="form-control" type="text" name="journal_number" autocomplete="off" list="034d396f-b8ec-4d4c-a332-e58f74642ced" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-3 col-md-5" for="fc02">Sampled: on or after</label>
                            <div class="col-sm-9 col-md-7">
                                <input id="fc02" class="form-control" type="text" name="after" />
                            </div>
                            <label class="control-label col-sm-3 col-md-5" for="fc03">before</label>
                            <div class="col-sm-9 col-md-7">
                                <input id="fc03" class="form-control" type="text" name="before" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-3 col-md-5" for="fc04">Agent: person</label>
                            <div class="col-sm-9 col-md-7">
                                <input id="fc04" class="form-control" type="text" name="person" />
                            </div>
                            <label class="control-label col-sm-3 col-md-5" for="fc05">organization</label>
                            <div class="col-sm-9 col-md-7">
                                <input id="fc05" class="form-control" type="text" name="organization" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-3 col-md-5" for="fc06">Location: place</label>
                            <div class="col-sm-9 col-md-7">
                                <input id="fc06" class="form-control" type="text" name="place" />
                            </div>
                            <label class="control-label col-sm-3 col-md-5" for="fc07">region</label>
                            <div class="col-sm-9 col-md-7">
                                <input id="fc07" class="form-control" type="text" name="region" />
                            </div>
                            <label class="control-label col-sm-3 col-md-5" for="fc08">province</label>
                            <div class="col-sm-9 col-md-7">
                                <input id="fc08" class="form-control" type="text" name="province" />
                            </div>
                            <label class="control-label col-sm-3 col-md-5" for="fc09">country</label>
                            <div class="col-sm-9 col-md-7">
                                <input id="fc09" class="form-control" type="text" name="country" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-3 col-md-5" for="fc10">Remarks</label>
                            <div class="col-sm-9 col-md-7">
                                <textarea id="fc10" class="form-control" name="remarks"></textarea>
                            </div>
                        </div>
                    </fieldset>
                    <fieldset class="col-md-4">
                        <legend class="col-sm-offset-3 col-md-offset-5 col-sm-9 col-md-7">Specimen carrier</legend>
                        <input type="hidden" name="carrier_id" />
                        <div class="form-group">
                            <label class="control-label col-sm-3 col-md-5" for="fc11">Carrier number</label>
                            <div class="col-sm-9 col-md-7">
                                <input id="fc11" class="form-control" type="text" name="carrier_number" autocomplete="off" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-3 col-md-5" for="fc12">Preparation type</label>
                            <div class="col-sm-9 col-md-7">
                                <select id="fc12" class="form-control" name="preparation_type">
                                    <option value="permanentDry">permanent dry mount</option>
                                    <option value="permanent">permanent microscope slide</option>
                                    <option value="temporary">temporary wet mount</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-3 col-md-5" for="fc13">Owner</label>
                            <div class="col-sm-9 col-md-7">
                                <input id="fc13" class="form-control" type="text" name="owner" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-3 col-md-5" for="fc14">Previous collection</label>
                            <div class="col-sm-9 col-md-7">
                                <input id="fc14" class="form-control" type="text" name="previous_collection" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-3 col-md-5" for="fc15">Label transcript</label>
                            <div class="col-sm-9 col-md-7">
                                <textarea id="fc15" class="form-control" name="label_transcript"></textarea>
                            </div>
                        </div>
                    </fieldset>
                    <fieldset class="col-md-4">
                        <legend class="col-sm-offset-3 col-md-offset-5 col-sm-9 col-md-7">Organism</legend>
                        <input type="hidden" name="organism_id" />
                        <div class="form-group">
                            <label class="control-label col-sm-3 col-md-5" for="fc16">Sequence number</label>
                            <div class="col-sm-9 col-md-7">
                                <input id="fc16" class="form-control" type="text" name="sequence_number" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-3 col-md-5" for="fc17">Type status</label>
                            <div class="col-sm-9 col-md-7">
                                <select id="fc17" class="form-control" name="type_status">
                                    <option value="">(none)</option>
                                    <option value="holotype">holotype</option>
                                    <option value="paratype">paratype</option>
                                    <option value="syntype">syntype</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-3 col-md-5" for="fc18">Full scientific name</label>
                            <div class="col-sm-9 col-md-7">
                                <input id="fc18" class="form-control" type="text" name="scientific_name" />
                            </div>
                        </div>
                        <div class="form-group" data-ng-hide="vm.typeDesignation.typeStatus">
                            <label class="control-label col-sm-3 col-md-5" for="fc19">Identified by</label>
                            <div class="col-sm-9 col-md-7">
                                <input id="fc19" class="form-control" type="text" name="identifier" />
                            </div>
                        </div>
                        <div class="form-group" data-ng-hide="vm.typeDesignation.typeStatus">
                            <label class="control-label col-sm-3 col-md-5" for="fc20">Identification qualifier</label>
                            <div class="col-sm-9 col-md-7">
                                <input id="fc20" class="form-control" type="text" name="qualifier" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-3 col-md-5" for="fc21">Phase or stage</label>
                            <div class="col-sm-9 col-md-7">
                                <input id="fc21" class="form-control" type="text" name="phase_or_stage" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-3 col-md-5" for="fc22">Sex</label>
                            <div class="col-sm-9 col-md-7">
                                <select id="fc22" class="form-control" name="sex">
                                    <option value="unknown">unknown</option>
                                    <option value="female">female</option>
				    <option value="hermaphrodite">hermaphrodite</option>
                                    <option value="male">male</option>
                                    <option value="not-applicable">not-applicable</option>
                                    <option value="mixed">mixed</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-3 col-md-5" for="fc23">Remarks</label>
                            <div class="col-sm-9 col-md-7">
                                <textarea id="fc23" class="form-control" name="organism_remarks"></textarea>
                            </div>
                        </div>
                    </fieldset>
                </div>
                <button type="submit" name="action" value="describe-organism">Submit</button>
            </form>
        </main>
        <script src="js/type-status.js"></script>
        <script src="js/gathering-autocomplete.js"></script>
    </body>
</html>
<?php
    // TODO fix date range formatting
    /**
     * @param string $onOrAfter
     * @param string $before
     * @return string
     */
    function formatDateInterval($onOrAfter, $before)
    {
        // set to UTC noon, otherwise we get P30D days instead of P1M over DST shifts
        $after = (new DateTime($onOrAfter))->setTimezone(new DateTimeZone('UTC'))->setTime(12, 0, 0);
        $before = (new DateTime($before))->setTimezone(new DateTimeZone('UTC'))->setTime(12, 0, 0);
        // calculate difference as ISO 8601 duration
        $duration = $before->diff($after)->format('/P%yY%mM%dDT%hH%iM%sS');
        // strip components being 0
        $duration = preg_replace('`(?<!\d)0[YMDHMS]`', '', $duration);
        // strip time indicator when no time deltas follow
        $duration = rtrim($duration, 'TP/');

        // ignore time components and replace trivial intervals by their simplest representation
        return str_replace(
            ['-01-01/P1Y', '-01/P1M', '/P1D'],
            '',
            substr($onOrAfter, 0, 10) . $duration
        );
    }
?>
