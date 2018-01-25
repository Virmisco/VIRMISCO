<?php
    use sednasoft\virmisco\oai\pmh\repository\Response;
    use sednasoft\virmisco\vpmh\EmptyResultErrorHandler;
    use sednasoft\virmisco\vpmh\GetRecordHandler;
    use sednasoft\virmisco\vpmh\IdentifyHandler;
    use sednasoft\virmisco\vpmh\InvalidResumptionTokenErrorHandler;
    use sednasoft\virmisco\vpmh\ListMetadataFormatsWithoutIdentifierHandler;
    use sednasoft\virmisco\vpmh\ListRecordsHandler;
    use sednasoft\virmisco\vpmh\ListSetsHandler;
    use sednasoft\virmisco\vpmh\MetadataFormatProvider;
    use sednasoft\virmisco\vpmh\OaiPmhFrontController;
    use sednasoft\virmisco\vpmh\RecordProvider;
    use sednasoft\virmisco\vpmh\SetProvider;
    use sednasoft\virmisco\vpmh\SimpleIdentificationProvider;

    require_once '../vendor/autoload.php';
    require_once '../src/credentials.php';
    $connection = new PDO(MARIA_DSN, MARIA_USER, MARIA_PASS, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    // the order is carefully selected and the request will be passed to these handlers from bottom to top until one
    // returns a Response instead of null
    $recordProvider = new RecordProvider($connection, sprintf('%s/templates/', __DIR__));
    $earliestRecordTimeProvider = $recordProvider;
    $setProvider = new SetProvider($connection);
    $pageSize = 50;
    $metadataFormatProvider = new MetadataFormatProvider();
    $harvestingGranularity = Response::HG_SECOND;
    $identificationProvider = new SimpleIdentificationProvider(
        '“Virtual Microscope Slide Collection” of the “Senckenberg Museum of Natural History Görlitz”',
        'http://virmisco.org/oai/',
        'webmaster@virmisco.org'
    );
    $handler = new EmptyResultErrorHandler(null);
    $handler = new InvalidResumptionTokenErrorHandler($handler);
    $handler = new IdentifyHandler($handler, $earliestRecordTimeProvider, $identificationProvider);
    $handler = new ListMetadataFormatsWithoutIdentifierHandler($handler, $metadataFormatProvider);
    $handler = new ListSetsHandler(
        $handler,
        $earliestRecordTimeProvider,
        $metadataFormatProvider,
        $setProvider,
        $pageSize
    );
    $handler = new ListRecordsHandler(
        $handler,
        $earliestRecordTimeProvider,
        $metadataFormatProvider,
        $recordProvider,
        $setProvider,
        $pageSize
    );
    $handler = new GetRecordHandler($handler, $recordProvider);
    OaiPmhFrontController::processRequestFromSapi($handler, $harvestingGranularity);

    /**
     * @param string ...$string An sprintf-like format string and optional parameters
     */
    function out(...$string)
    {
        $count = count($string);
        if ($count === 0) {
            echo '';
        } elseif ($count === 1) {
            echo htmlspecialchars($string[0]);
        } else {
            echo htmlspecialchars(sprintf(...$string));
        }
    }
