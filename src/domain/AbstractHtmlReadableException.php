<?php
    namespace sednasoft\virmisco\domain;

    use Exception;

    /**
     * An exception thrown by the domain that can generate a markdown representation of itself.
     */
    abstract class AbstractHtmlReadableException extends Exception implements IHtmlReadable
    {
        /**
         * @return string
         */
        public function toHtml()
        {
            return sprintf(
                '<div class="message"><span class="title">%s <span class="code">[%d]</span></span>%s</div>',
                $this->classNameToWords(),
                $this->getCode(),
                ($message = $this->getMessage()) ? sprintf(' <q>%s</q>', $message) : ''
            );
        }

        /**
         * @param $camelCaseName
         * @return string
         */
        protected function camelCaseToWords($camelCaseName)
        {
            return strtolower(preg_replace('<([a-z])([A-Z])>', '\\1 \\2', $camelCaseName));
        }

        /**
         * Returns a space-separated sentence-like representation of the unqualified class name. Subclasses may
         * overwrite this to customize the name.
         *
         * @return string
         */
        protected function classNameToWords()
        {
            $unqualifiedClassName = array_slice(explode('\\', get_class($this)), -1)[0];

            return ucfirst($this->camelCaseToWords(preg_replace('<Exception$>', '', $unqualifiedClassName)));
        }
    }
