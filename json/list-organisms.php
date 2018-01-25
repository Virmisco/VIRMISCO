<?php

    require_once '../vendor/autoload.php';
    require_once '../src/credentials.php';
    //
    $connection = new PDO(MARIA_DSN, MARIA_USER, MARIA_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
    ]);
    $jsonOptions = JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE;
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(iterator_to_array(iterateOrganisms($connection)), $jsonOptions);

    /**
     * @param PDO $connection
     * @return Generator
     */
    function iterateOrganisms(PDO $connection)
    {
        $listOrganisms = $connection->query(
            <<<'EOD'
SELECT
    `o`.`id`,
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
    concat_ws(
        ', ',
        if(`g`.`location__place` = '', NULL, `g`.`location__place`),
        if(`g`.`location__region` = '', NULL, `g`.`location__region`),
        if(`g`.`location__province` = '', NULL, `g`.`location__province`),
        if(`g`.`location__country` = '', NULL, `g`.`location__country`)
    ) `location`,
    ifnull(`g`.`agent__person`, `g`.`agent__organization`) `agent`,
    `g`.`sampling_date__after` `after`,
    `g`.`sampling_date__before` `before`
FROM `organism` `o`
JOIN `specimen_carrier` `sc` ON `sc`.`id` = `o`.`specimen_carrier_id`
JOIN `gathering` `g` ON `g`.`id` = `sc`.`gathering_id`
LEFT JOIN `scientific_name` `sn`
    ON `sn`.`id` = `o`.`identification__scientific_name_id` OR `sn`.`id` = `o`.`type_designation__scientific_name_id`
ORDER BY `after` DESC, `before`, `carrier_number`, `sequence_number`
EOD
        );
        $countPhotomicrographs = $connection->prepare('SELECT count(*) FROM `photomicrograph` WHERE `organism_id` = ?');
        foreach ($listOrganisms as $organism) {
            // set to UTC noon, otherwise we get P30D days instead of P1M over DST shifts
            $after = (new DateTime($organism->after))->setTimezone(new DateTimeZone('UTC'))->setTime(12, 0, 0);
            $before = (new DateTime($organism->before))->setTimezone(new DateTimeZone('UTC'))->setTime(12, 0, 0);
            // calculate difference as ISO 8601 duration
            $duration = $before->diff($after)->format('/P%yY%mM%dDT%hH%iM%sS');
            // strip components being 0
            $duration = preg_replace('`(?<!\d)0[YMDHMS]`', '', $duration);
            // strip time indicator when no time deltas follow
            $duration = rtrim($duration, 'TP/');
            // ignore time components and replace trivial intervals by their simplest representation
            $organism->date = str_replace(
                ['-01-01/P1Y', '-01/P1M', '/P1D'],
                '',
                substr($organism->after, 0, 10) . $duration
            );
            $countPhotomicrographs->execute([$organism->id]);
            $organism->number_of_photomicrographs = intval($countPhotomicrographs->fetchColumn(0));
            unset($organism->after, $organism->before);
            yield $organism;
        }
    }
