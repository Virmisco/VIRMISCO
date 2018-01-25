<?php
    require_once '../vendor/autoload.php';
    require_once '../src/credentials.php';
    //
    $connection = new PDO(MARIA_DSN, MARIA_USER, MARIA_PASS, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $jsonOptions = JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE;
    header('Content-Type: application/json; charset=utf-8');
    // if a filter word has been provided, turn it into a PCRE matching it at non-word boundaries or also within words
    // when indicated by prepended and/or appended asterisks, e. g.
    // - expression
    // - *pression
    // - express*
    // - *press*
    $contains = isset($_GET['contains']) ? trim($_GET['contains']) : '';
    $leftBoundary = startsWithWildcard($contains) ? '' : '(^|[^0-9a-z])';
    $rightBoundary = endsWithWildcard($contains) ? '' : '([^0-9a-z]|$)';
    $contains = trim($contains, '*');
    $filterPcre = $contains !== ''
        ? sprintf('/%s%s%s/i', $leftBoundary, preg_quote($contains, '/'), $rightBoundary)
        : null;
    $resultArray = iterator_to_array(iterateUnprocessedUploads($connection, INDEX_URI_LAS, $filterPcre));
    usort($resultArray, "compareUploadUris");
    echo json_encode($resultArray, $jsonOptions);

    /**
     * @param PDO $connection
     * @param string $lasIndexUri
     * @param string $filterPcre A (fenced) PCRE to match URIs against, only matching ones are included in the result.
     * @return Generator
     */
    function iterateUnprocessedUploads(PDO $connection, $lasIndexUri, $filterPcre = null)
    {
        $listPhotomicrographArchiveUris = $connection->query('SELECT `file__uri` FROM `photomicrograph`');
        $listPhotomicrographArchiveUris->setFetchMode(PDO::FETCH_COLUMN, 0);
        $archiveUris = [];
        foreach ($listPhotomicrographArchiveUris as $archiveUri) {
            $archiveUris[] = urldecode(basename(dirname($archiveUri)));
        }
        $lasIndexUris = file($lasIndexUri, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lasIndexUris as $lasUri) {
            if ($lasUri[0] !== '#'
                && !in_array(basename(dirname($lasUri)), $archiveUris)
                && ($filterPcre === null || preg_match($filterPcre, $lasUri))
            ) {
                yield ['uri' => $lasUri];
            }
        }
    }

    /**
     * @param string $term
     * @return bool
     */
    function startsWithWildcard($term)
    {
        return substr($term, 0, 1) === '*';
    }

    /**
     * @param string $term
     * @return bool
     */
    function endsWithWildcard($term)
    {
        return substr($term, -1) === '*';
    }

    /**
     * @param array $a An associative array with a “uri” property.
     * @param array $b An associative array with a “uri” property.
     * @return int
     */
    function compareUploadUris($a, $b) {
        $a = explode('/', $a['uri']);
        $b = explode('/', $b['uri']);
        foreach ($a as $k => $v) {
            if (isset($b[$k])) {
                $r = strcmp($a[$k], $b[$k]);
                if ($r !== 0) {
                    return $r;
                }
            } else {
                // b is shorter
                return 1;
            }
        }

        return count($a) - count($b);
    }
