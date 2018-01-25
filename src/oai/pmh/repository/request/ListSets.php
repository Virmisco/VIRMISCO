<?php
    namespace sednasoft\virmisco\oai\pmh\repository\request;

    use sednasoft\virmisco\oai\pmh\repository\Request;
    use sednasoft\virmisco\oai\pmh\repository\request\feature\Resumable;

    /**
     * This verb is used to retrieve the set structure of a repository, useful for selective harvesting.
     */
    interface ListSets extends Request, Resumable
    {
    }
