(function () {
    angular
        .module('backend')
        .factory('uploadedRawFileService', uploadedRawFileService);

    uploadedRawFileService.$inject = ['$http'];
    /**
     * Data service for uploads.
     * @class UploadedRawFileService
     */
    function uploadedRawFileService($http) {
        return {
            loadUploadList: loadUploadList
        };
        /**
         * Loads the list of uploaded raw files from the server.
         * @returns {Promise} A promise whose value is an array of upload records.
         * @memberOf UploadedRawFileService#
         */
        function loadUploadList() {
            return $http.get('../json/list-unprocessed-uploads.php').then(
                function (response) {
                    return response.data;
                },
                function (response) {
                    alert(response.statusText + '\n' + response.data);
                }
            ).then(function (uploadList) {
/*                var i, m;
                for (i = 0, m = uploadList.length; i < m; i++) {
                    uploadList[i].uri = uploadList[i].uri.replace(/^https?:\/\/[^\/]+\/[^\/]+\//, '');
                }
*/
                return uploadList;
            });
        }
    }
})();
