<?php
    namespace sednasoft\virmisco\readlayer;

    use sednasoft\virmisco\readlayer\valueobject\GatheringLocation;

    class AugmentedLocation extends GatheringLocation
    {
        /**
         * @return string
         */
        public function __toString()
        {
            return implode(', ', array_merge(
                $this->getPlace() ? [$this->getPlace()] : [],
                $this->getRegion() ? [$this->getRegion()] : [],
                $this->getProvince() ? [$this->getProvince()] : [],
                $this->getCountry() ? [$this->getCountry()] : []
            ));
        }
    }
