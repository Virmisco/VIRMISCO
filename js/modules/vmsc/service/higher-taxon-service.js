/**
 * @typedef {object} SpeciesMatch
 * @param {string} genus
 * @param {string} subgenus
 * @param {string} specificEpithet
 * @param {string} infraspecificEpithet
 */

/**
 * @typedef {object} TaxonMatch
 * @param {string} above The taxa from ranks above the matching one or '' if none matched.
 * @param {string} match The matching taxon or '' if none matched.
 * @param {string} below The taxa from ranks below the matching one or from all ranks if none matched.
 */

/** */
(function () {
    angular
        .module('vmsc')
        .factory('higherTaxonService', higherTaxonService);

    higherTaxonService.$inject = ['dataService'];
    /**
     * Data service for higherTaxon records.
     * @param {DataService} dataService
     * @class HigherTaxonService
     */
    function higherTaxonService(dataService) {
        return {
            loadMatchingGenusNameList: loadMatchingGenusNameList,
            loadMatchingSpeciesList: loadMatchingSpeciesList,
            loadMatchingTaxonList: loadMatchingTaxonList
        };

        /**
         * Loads a list of matching genus names through the query API.
         *
         * @param {string} taxon
         * @returns {Promise} A promise whose value is a list of matching genus names, each as a string
         * @memberOf HigherTaxonService#
         */
        function loadMatchingGenusNameList(taxon) {
            return dataService.queryForData('genus/taxon:' + taxon).then(function (genusList) {
                return dataService.flattenDataList(genusList, 'genus', false);
            });
        }

        /**
         * Loads a list of matching species through the query API.
         *
         * @param {string} genusName
         * @returns {Promise} A promise whose value is a list of matching taxa, each as a {@link SpeciesMatch}
         * @memberOf HigherTaxonService#
         */
        function loadMatchingSpeciesList(genusName) {
            return dataService.queryForData('species/genus:' + genusName);
        }

        /**
         * Loads a list of matching taxa through the query API.
         *
         * @param {string} monomial
         * @returns {Promise} A promise whose value is a list of matching taxa, each as a {@link TaxonMatch}
         * @memberOf HigherTaxonService#
         */
        function loadMatchingTaxonList(monomial) {
            return dataService.queryForData('higher-taxon/monomial:' + monomial);
        }
    }
})();
