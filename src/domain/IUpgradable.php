<?php
    namespace sednasoft\virmisco\domain;

    use sednasoft\virmisco\singiere\AbstractEvent;

    /**
     * An event implementing this interface can upgrade itself to the next higher version, so that projections can work
     * on the latest version of each event.
     *
     * Example: You have an event FoobarQuuxified, which has been stored in an event store already. Now you create a new
     * event class FoobarQuuxifiedV2 as a copy of the old one and change the members to fit your new desires. Finally
     * you let the old FoobarQuuxified implement this interface and implement it to return an equivalent instance of
     * FoobarQuuxifiedV2. Likewise, when FoobarQuuxifiedV3 comes into play, you make FoobarQuuxifiedV2 implement this
     * interface. When loading a FoobarQuuxified event now, it can be converted to FoobarQuuxifiedV2 and then to
     * FoobarQuuxifiedV3.
     */
    interface IUpgradable
    {
        /**
         * @return AbstractEvent The equivalent instance to this instance but in the next higher version of the event.
         */
        public function upgrade();
    }
