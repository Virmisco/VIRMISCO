<?php
    namespace sednasoft\virmisco\domain;

    /**
     * Something that can be converted into a human-readable string with markdown formatting.
     */
    interface IHtmlReadable
    {
        /**
         * @return string
         */
        public function toHtml();
    }
