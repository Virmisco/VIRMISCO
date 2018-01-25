(function () {
    angular
        .module('vmsc')
        .factory('locationService', locationService);

    locationService.$inject = ['dataService'];
    /**
     * Data service for location records.
     * @param {DataService} dataService
     * @class LocationService
     */
    function locationService(dataService) {
        return {
            loadCountryList: loadCountryList,
            loadMatchingPlaceList: loadMatchingPlaceList,
            loadMatchingProvinceList: loadMatchingProvinceList,
            loadMatchingRegionList: loadMatchingRegionList
        };

        /**
         * Loads a list of gathering-relevant countries through the query API.
         *
         * @returns {Promise} A promise whose value is a list of countries, each as a string
         * @memberOf LocationService#
         */
        function loadCountryList() {
            return dataService.queryForData('country').then(function (countryList) {
                return dataService.flattenDataList(countryList, 'country', true);
            });
        }

        /**
         * Loads a list of matching places through the query API.
         *
         * @param {string} country
         * @param {string} province
         * @param {string} region
         * @returns {Promise} A promise whose value is a list of matching places, each as a string
         * @memberOf LocationService#
         */
        function loadMatchingPlaceList(country, province, region) {
            var queryPath = 'place/country:%C/province:%P/region:%R'
                .replace('%C', encodeURIComponent(country))
                .replace('%P', encodeURIComponent(province))
                .replace('%R', encodeURIComponent(region));
            return dataService.queryForData(queryPath).then(function (placeList) {
                return dataService.flattenDataList(placeList, 'place', true);
            });
        }

        /**
         * Loads a list of matching provinces through the query API.
         *
         * @param {string} country
         * @returns {Promise} A promise whose value is a list of matching provinces, each as a string
         * @memberOf LocationService#
         */
        function loadMatchingProvinceList(country) {
            var queryPath = 'province/country:%C'.replace('%C', encodeURIComponent(country));
            return dataService.queryForData(queryPath).then(function (provinceList) {
                return dataService.flattenDataList(provinceList, 'province', true);
            });
        }

        /**
         * Loads a list of matching regions through the query API.
         *
         * @param {string} country
         * @param {string} province
         * @returns {Promise} A promise whose value is a list of matching regions, each as a string
         * @memberOf LocationService#
         */
        function loadMatchingRegionList(country, province) {
            var queryPath = 'region/country:%C/province:%P'
                .replace('%C', encodeURIComponent(country))
                .replace('%P', encodeURIComponent(province));
            return dataService.queryForData(queryPath).then(function (regionList) {
                return dataService.flattenDataList(regionList, 'region', true);
            });
        }
    }
})();
