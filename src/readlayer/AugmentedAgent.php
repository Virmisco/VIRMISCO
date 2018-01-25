<?php
    namespace sednasoft\virmisco\readlayer;

    use sednasoft\virmisco\readlayer\valueobject\GatheringAgent;

    class AugmentedAgent extends GatheringAgent
    {
        /**
         * @return string
         */
        public function __toString()
        {
            return implode(', ', array_merge(
                $this->getPerson() ? [$this->getPerson()] : [],
                $this->getOrganization() ? [$this->getOrganization()] : []
            ));
        }
    }
