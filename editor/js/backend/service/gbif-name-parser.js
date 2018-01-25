(function () {
    angular
        .module('backend')
        .factory('gbifNameParser', gbifNameParser);

    gbifNameParser.$inject = ['$http'];
    /**
     * Data service as a common base service for specific record types.
     * @class GbifNameParser
     */
    function gbifNameParser($http) {
        return { parseName: parseName };

        /**
         * @param {string} fullScientificName
         * @returns {Promise}
         * @memberOf GbifNameParser#
         */
        function parseName(fullScientificName) {
            var args = [];
            angular.forEach(arguments, function (arg) {
                args.push('name=' + encodeURIComponent(arg));
            });
            return $http.get('http://api.gbif.org/v1/parser/name?' + args.join('&')).then(
                function (response) {
                    var result = [];
                    angular.forEach(response.data, function (parsedName) {
                        result.push(
                            parsedName.type == 'SCIENTIFIC'
                                ?
                            {
                                genus: parsedName.genusOrAbove,
                                subgenus: parsedName.infraGeneric,
                                specificEpithet: parsedName.specificEpithet,
                                infraspecificEpithet: parsedName.infraSpecificEpithet,
                                authorship: parsedName.bracketAuthorship || parsedName.authorship,
                                year: parsedName.bracketYear || parsedName.year,
                                parenthesized: !!parsedName.bracketAuthorship
                            }
                                : null
                        );
                    });
                    return result.length == 1 ? result[0] : result;
                },
                function (response) {
                    alert(response.statusText + '\n' + response.data);
                }
            );
        }
    }
})();
