<?php
    namespace sednasoft\virmisco\oai\pmh\repository\request;

    use sednasoft\virmisco\oai\pmh\repository\Request;
    use sednasoft\virmisco\oai\pmh\repository\request\feature\Identifiable;

    /**
     * This verb is used to retrieve the metadata formats available from a repository. An optional argument restricts
     * the request to the formats available for a specific item.
     */
    interface ListMetadataFormats extends Request, Identifiable
    {
    }
