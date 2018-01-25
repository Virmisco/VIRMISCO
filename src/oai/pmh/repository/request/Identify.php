<?php
    namespace sednasoft\virmisco\oai\pmh\repository\request;

    use sednasoft\virmisco\oai\pmh\repository\Request;

    /**
     * This verb is used to retrieve information about a repository. Some of the information returned is required as
     * part of the OAI-PMH. Repositories may also employ the Identify verb to return additional descriptive information.
     */
    interface Identify extends Request
    {
    }
