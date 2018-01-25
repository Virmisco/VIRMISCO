(function () {
    angular
        .module('vmsc')
        .factory('dataService', dataService);

    dataService.$inject = ['$http', '$httpParamSerializer'];
    /**
     * Data service as a common base service for specific record types.
     * @class DataService
     */
    function dataService($http, $httpParamSerializer) {
        var commandUri = '../command/';
        var queryUri = '../query/';
        return {
            queryForData: queryForData,
            sendCommand: sendCommand,
            flattenDataList: flattenDataList
        };

        /**
         * Loads data from the server through the query API.
         *
         * @param {string} queryPath The URI path relative to the base query URI to retrieve from.
         * @returns {Promise}
         * @memberOf DataService#
         */
        function queryForData(queryPath) {
            return $http.get(queryUri + queryPath).then(
                function (response) {
                    return response.data;
                },
                function (response) {
                    alert(response.statusText + '\n' + response.data);
                }
            );
        }

        /**
         * Send a command to the server using its command API.
         *
         * @param {string} command The name of command to send.
         * @param {object} data The data.
         * @returns {Promise}
         * @memberOf DataService#
         */
        function sendCommand(command, data) {
            return $http.post(
                commandUri + command + '/',
                $httpParamSerializer(data),
                {headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'}}
            ).then(
                function (response) {
                    return response.data;
                },
                function (response) {
                    alert(response.statusText + '\n' + response.data);
                }
            );
        }

        /**
         * Simplifies a list of records to a list of values for the given property.
         *
         * @param {Object} dataList The list to simplify
         * @param {string} propertyName The property to extract from each record
         * @param {boolean} includeNulls Include null values for records with null or undefined property (true)
         * or strip those records from the result (false)
         * @returns {Array}
         * @memberOf DataService#
         */
        function flattenDataList(dataList, propertyName, includeNulls) {
            var result = [];
            angular.forEach(dataList, function (dataRecord) {
                var value = dataRecord.hasOwnProperty(propertyName) ? dataRecord[propertyName] : null;
                if (value || includeNulls) {
                    result.push(value);
                }
            });
            return result;
        }
    }
})();
