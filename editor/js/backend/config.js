(function () {
    angular
        .module('backend')
        .config(config);

    config.$inject = ['$routeProvider'];
    function config($routeProvider) {
        $routeProvider
            .when('/organism/new', {
                templateUrl: 'tpl/organism-detail.html',
                controller: 'OrganismCreateController',
                controllerAs: 'vm'
            })
            .when('/organism/:uuid', {
                templateUrl: 'tpl/organism-detail.html',
                controller: 'OrganismDetailController',
                controllerAs: 'vm',
                resolve: {
                    preloadedOrganismRecord: preloadOrganismRecord,
                    preloadedUploadList: preloadUploadList
                }
            })
            .when('/photomicrograph/new/:uuid', {
                templateUrl: 'tpl/photomicrograph-detail.html',
                controller: 'PhotomicrographCreateController',
                controllerAs: 'vm',
                resolve: {
                    preloadedOrganismRecord: preloadOrganismRecord
                }
            })
            .when('/photomicrograph/:uuid', {
                templateUrl: 'tpl/photomicrograph-detail.html',
                controller: 'PhotomicrographDetailController',
                controllerAs: 'vm',
                resolve: {
                    preloadedPhotomicrographRecord: preloadPhotomicrographRecord
                }
            })
            .otherwise({
                templateUrl: 'tpl/organism-list.html',
                controller: 'OrganismListController',
                controllerAs: 'vm',
                resolve: {
                    preloadedOrganismList: preloadOrganismList
                }
            });

        getPhotomicrographParent.$inject = ['$route'];
        /**
         * @param $route
         * @returns {{specimenCarrierId: string, sequenceNumber: string}}
         */
        function getPhotomicrographParent($route) {
            return {
                specimenCarrierId: $route.current.params.uuid,
                sequenceNumber: $route.current.params.no
            };
        }

        preloadOrganismList.$inject = ['organismService'];
        /**
         * @param {OrganismService} organismService
         * @returns {Promise} A promise whose value is an array of organism records with properties as defined in
         * /web/query/organism-list.sql
         */
        function preloadOrganismList(organismService) {
            return organismService.loadOrganismList();
        }

        preloadOrganismRecord.$inject = ['organismService', '$route'];
        /**
         * @param {OrganismService} organismService
         * @param {object} $route
         * @returns {Promise} A promise whose value is an organism records with properties as defined in
         * /web/query/organism-record.sql
         */
        function preloadOrganismRecord(organismService, $route) {
            return organismService.loadOrganismRecord($route.current.params.uuid);
        }

        preloadPhotomicrographRecord.$inject = ['photomicrographService', '$route'];
        /**
         * @param {PhotomicrographService} photomicrographService
         * @param {object} $route
         * @returns {Promise} A promise whose value is a photomicrograph record with properties as defined in
         * /web/query/photomicrograph-record.sql
         */
        function preloadPhotomicrographRecord(photomicrographService, $route) {
            return photomicrographService.loadPhotomicrographRecord($route.current.params.uuid);
        }

        preloadUploadList.$inject = ['uploadedRawFileService'];
        /**
         * @param {UploadedRawFileService} uploadedRawFileService
         * @returns {Promise} A promise whose value is an array of upload records.
         */
        function preloadUploadList(uploadedRawFileService) {
            return uploadedRawFileService.loadUploadList();
        }
    }
})();
