(function () {
    angular
        .module('vmsc')
        .controller('AdvancedSearchController', AdvancedSearchController);

    AdvancedSearchController.$inject = ['higherTaxonService', 'locationService'];
    /**
     * @param {HigherTaxonService} higherTaxonService
     * @param {LocationService} locationService
     * @constructor
     */
    function AdvancedSearchController(higherTaxonService, locationService) {
        var vm = this;
        vm.country = null;
        vm.place = null;
        vm.province = null;
        vm.region = null;
        vm.genus = null;
        vm.higherTaxon = null;
        vm.species = null;
        vm.taxonFilter = '';
        vm.countries = [];
        vm.dateBefore = null;
        vm.dateBeforeOpened = false;
        vm.dateOnOrAfter = null;
        vm.dateOnOrAfterOpened = false;
        vm.filteredGenusNames = [];
        vm.filteredPlaces = [];
        vm.filteredProvinces = [];
        vm.filteredRegions = [];
        vm.filteredSpecies = [];
        vm.filteredHigherTaxa = [];
        vm.countryChanged = countryChanged;
        vm.filterChanged = filterChanged;
        vm.genusChanged = genusChanged;
        vm.provinceChanged = provinceChanged;
        vm.regionChanged = regionChanged;
        vm.taxonChanged = taxonChanged;
        activate();

        function activate() {
            locationService.loadCountryList().then(function (countryList) {
                angular.copy(countryList, vm.countries);
                filterChanged();
                countryChanged();
            });
        }

        function countryChanged() {
            vm.place = null;
            vm.region = null;
            vm.province = null;
            vm.filteredPlaces = [];
            vm.filteredRegions = [];
            vm.filteredProvinces = [];
            locationService.loadMatchingProvinceList(vm.country).then(function (provinceList) {
                angular.copy(provinceList, vm.filteredProvinces);
            });
        }

        function filterChanged() {
            vm.species = null;
            vm.genus = null;
            vm.higherTaxon = null;
            vm.filteredSpecies = [];
            vm.filteredGenusNames = [];
            vm.filteredHigherTaxa = [];
            higherTaxonService.loadMatchingTaxonList(vm.taxonFilter).then(function (taxonMatchList) {
                angular.copy(taxonMatchList, vm.filteredHigherTaxa);
            });
        }

        function genusChanged() {
            vm.species = null;
            vm.filteredSpecies = [];
            higherTaxonService.loadMatchingSpeciesList(vm.genus).then(function (speciesList) {
                angular.copy(speciesList, vm.filteredSpecies);
            });
        }

        function provinceChanged() {
            vm.place = null;
            vm.region = null;
            vm.filteredPlaces = [];
            vm.filteredRegions = [];
            locationService.loadMatchingRegionList(vm.country, vm.province).then(function (regionList) {
                angular.copy(regionList, vm.filteredRegions);
            });
        }

        function regionChanged() {
            vm.place = null;
            vm.filteredPlaces = [];
            locationService.loadMatchingPlaceList(vm.country, vm.province, vm.region).then(function (placeList) {
                angular.copy(placeList, vm.filteredPlaces);
            });
        }

        function taxonChanged() {
            vm.species = null;
            vm.genus = null;
            vm.filteredSpecies = [];
            vm.filteredGenusNames = [];
            if (vm.higherTaxon) {
                higherTaxonService.loadMatchingGenusNameList(vm.higherTaxon.replace(/^\s+|\s+$/g, '').split(/\s+/).slice(-1)[0])
                    .then(function (genusNameList) {
                        angular.copy(genusNameList, vm.filteredGenusNames);
                    });
            }
        }
    }
})();
