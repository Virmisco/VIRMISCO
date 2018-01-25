<?php
    namespace sednasoft\virmisco\oai\pmh\repository;

    /**
     * A message sent to the client as a reaction to a client's request.
     */
    interface Response
    {
        /** The finest harvesting granularity supported by the repository is a day. */
        const HG_DAY = 'YYYY-MM-DD';
        /** The finest harvesting granularity supported by the repository is a second. */
        const HG_SECOND = 'YYYY-MM-DDThh:mm:ssZ';
    }
