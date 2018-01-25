<?php
    require_once '../vendor/autoload.php';
    require_once '../src/credentials.php';
    $connection = new PDO(MARIA_DSN, MARIA_USER, MARIA_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
    ]);
    $jsonOptions = JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE;
    list(, , $collection, $filters) = explode('/', $_SERVER['REQUEST_URI'] . '/', 4);
    $filters = trim($filters, '/');
    $result = loadData($connection, $collection, $filters ? explode('/', $filters) : []);
    $contentType = negotiateContentType(
        $_SERVER['HTTP_ACCEPT'],
        [
            'application/json',
            'text/json',
            'application/xml',
            'text/xml'
        ]
    );
    header('Access-Control-Allow-Origin: http://cms.virmisco.org');
    switch ($contentType) {
        case 'application/json': // fall through
        case 'text/json':
            header('Content-Type: application/json; charset=UTF-8');
            header('Pragma: no-cache');
            header('Cache-Control: no-cache');
            echo json_encode($result, $jsonOptions);
            break;
        case 'application/xml': // fall through
        case 'text/xml':
            header('Content-Type: application/xml; charset=UTF-8');
            header('Pragma: no-cache');
            header('Cache-Control: no-cache');
            $document = new DOMDocument('1.0', 'UTF-8');
            $document->formatOutput = true;
            if (is_array($result)) {
                $resultNode = $document->appendChild($document->createElement('result'));
            } else {
                $result = [$result];
                $resultNode = $document;
            }
            foreach ($result as $record) {
                $recordElement = $resultNode->appendChild($document->createElement('record'));
                foreach ($record as $property => $value) {
                    $recordElement
                        ->appendChild($document->createElement($property))
                        ->appendChild($document->createTextNode($value));
                }
            }
            echo $document->saveXML();
    }

    /**
     * @param PDO $connection
     * @param string $name
     * @param array $filters
     * @return stdClass[]|stdClass
     */
    function loadData(PDO $connection, $name, array $filters)
    {
        $listQueryFile = sprintf('%s/%s-list.sql', __DIR__, $name);
        $recordQueryFile = sprintf('%s/%s-record.sql', __DIR__, $name);
        $filteredQueryFile = sprintf('%s/%s-filtered.sql', __DIR__, $name);
        if (!$filters && is_file($listQueryFile)) {
            $listRecords = $connection->query(file_get_contents($listQueryFile));

            return $listRecords->fetchAll(PDO::FETCH_OBJ);
        } elseif (count($filters) === 1
            && preg_match('<^[0-9a-f]{8}(-[0-9a-f]{4}){3}-[0-9a-f]{12}$>', $filters[0])
            && is_file($recordQueryFile)
        ) {
            $uuid = $filters[0];
            $loadRecord = $connection->prepare(file_get_contents($recordQueryFile));
            $loadRecord->execute([$uuid]);

            return $loadRecord->fetch(PDO::FETCH_OBJ);
        } elseif (is_file($filteredQueryFile)) {
            $filteredQuery = file_get_contents($filteredQueryFile);
            $listRecords = $connection->prepare($filteredQuery);
            $filterParams = [];
            if (preg_match_all('/:(`?)(\\w+)\\1/', $filteredQuery, $matches)) {
                foreach ($matches[2] as $name) {
                    $filterParams[$name] = null;
                }
            }
            foreach ($filters as $colonSeparatedFilterProp) {
                list($name, $value) = explode(':', $colonSeparatedFilterProp . '::', 3);
                $filterParams[urldecode($name)] = urldecode($value);
            }
            $listRecords->execute($filterParams);

            return $listRecords->fetchAll(PDO::FETCH_OBJ);
        }

        return [];
    }

    /**
     * @param string $httpAcceptValue
     * @param string[] $offeredTypes
     * @return string|null
     */
    function negotiateContentType($httpAcceptValue, array $offeredTypes)
    {
        //               1                    2                               3                        4
        $pattern = '`^\s*(\*|[\w\-.+]+)\s*/\s*(\*|[\w\-.+]+)\s*(?:;\s*q\s*=\s*([01](?:\.\d{0,3})?)\s*)?($)`';
        $acceptableTypes = [];
        foreach (explode(',', $httpAcceptValue) as $item) {
            if (preg_match($pattern, $item, $matches)) {
                list(, $type, $subtype, $qValue) = $matches;
                $qValue = $qValue === '' ? 1.0 : floatval($qValue);
                $acceptableTypes[sprintf('%s/%s', $type, $subtype)] = sprintf('%.3F', $qValue);
            }
        }
        arsort($acceptableTypes);
        foreach ($acceptableTypes as $type => $qValue) {
            foreach ($offeredTypes as $offered) {
                list($ot) = explode('/', $offered);
                if ($type == $offered || $type == $ot . '/*' || $type == '*/*') {
                    return $offered;
                }
            }
        }

        return null;
    }
