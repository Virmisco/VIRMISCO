<?php
    use Predis\Client as Redis;
    use sednasoft\virmisco\domain\projection\SqlQueue;
    use sednasoft\virmisco\domain\RedisEventStore;
    use Predis\Collection\Iterator\ListKey as ListKeyIterator;

    require_once '../vendor/autoload.php';
    require_once '../src/credentials.php';
    const EVENTS_ALL = 'events/all';
    const EVENTS_AGGREGATE = 'events/aggregate/';
    $redis = new Redis(REDIS_URI);
    $eventStore = new RedisEventStore(REDIS_URI);

    // UNDO ////////////////////////////////////////////////////////////////////////////////////////////////////////////


    $lastOfAll = $redis->rpop(EVENTS_ALL);
    list($uuid, $version) = explode(':', $lastOfAll);
    $lastOfAggregate = $redis->rpop(EVENTS_AGGREGATE . $uuid);
    list($version, $type, $binary, $json) = explode(':', $lastOfAggregate, 4);
    $data = gzuncompress(base64_decode($binary));
    file_put_contents('last-undone-event.dat', $data);
    $jsonOptions = JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE;
    echo $type, " ", json_encode(json_decode($json), $jsonOptions);

    // REBUILD /////////////////////////////////////////////////////////////////////////////////////////////////////////

    //region clean any left-overs in the SQL queue, actually this should never happen to be necessary
    $redis->del(REDIS_SQL_QUEUE);
    //endregion

    //region wipe the reporting database
    $mariadb = new PDO(MARIA_DSN, MARIA_USER, MARIA_PASS, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    foreach (explode(";\n\n", file_get_contents('../sql/create-reporting-tables.sql')) as $statement) {
        $mariadb->exec($statement);
    }
    //endregion

    //region replay all events and project them into the SQL queue
    $eventStore = new RedisEventStore(REDIS_URI);
    $projection = new SqlQueue(REDIS_URI, REDIS_SQL_QUEUE);
    foreach ($eventStore->iterateEventsForAllAggregates() as $event) {
        $projection->apply($event);
    }
    $redis = $projection->transform();
    //endregion

    //region echo and execute every statement in the SQL queue, rebuilding the reporting database
    foreach ($projection->commitToDatabase($mariadb) as $statement => $values) {
        printf("%s\n", $statement);
        foreach ($values as $k => $v) {
            printf("\t%s: %s\n", $k, json_encode($v));
        }
        echo "\n";
    }
    //endregion
