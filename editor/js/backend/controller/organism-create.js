var test;
(function () {
    angular
        .module('backend')
        .controller('OrganismCreateController', OrganismCreateController);

    OrganismCreateController.$inject = ['$scope', '$q', '$location', 'uuid', 'gatheringService', 'specimenCarrierService', 'organismService'];
    /**
     * @param $scope
     * @param $q
     * @param $location
     * @param {Uuid} uuid
     * @param {GatheringService} gatheringService
     * @param {SpecimenCarrierService} specimenCarrierService
     * @param {OrganismService} organismService
     * @constructor
     */
    function OrganismCreateController($scope, $q, $location, uuid, gatheringService, specimenCarrierService, organismService) {
        var vm = this;
        var journalNo = null;
        var carrierNo = null;
        vm.existingGathering = false;
        vm.existingSpecimenCarrier = false;
        vm.organism = null;
        vm.scientificName = null;
        vm.taxon = null;
        vm.identification = null;
        vm.typeDesignation = null;
        vm.specimenCarrier = null;
        vm.gathering = null;
        vm.samplingDate = null;
        vm.agent = null;
        vm.location = null;
        vm.lasUri = null;
        vm.carrierScans = null;
        vm.uploads = null;
        vm.gatheringJournalNumber = getSetGatheringJournalNumber;
        vm.specimenCarrierNumber = getSetSpecimenCarrierNumber;
        vm.existingGatherings = [];
        vm.existingSpecimenCarriers = [];
        vm.existingOrganisms = [];
        vm.filterCarriers = filterCarriers;
        vm.resetForm = resetForm;
        vm.saveForm = saveForm;
        vm.validateSeqNo = validateSeqNo;
        activate();

        function activate() {
            gatheringService.loadGatheringList().then(function (gatheringList) {
                vm.existingGatherings = gatheringList;
                if(window.data && window.data.fc01) {
                	vm.gatheringJournalNumber(window.data.fc01);
                }
            });
            specimenCarrierService.loadSpecimenCarrierList().then(function (specimenCarrierList) {
                vm.existingSpecimenCarriers = specimenCarrierList;
                if(window.data && window.data.fc11) {
                	vm.specimenCarrierNumber(window.data.fc11);
                } else return;
                
                var exclude = ["fc01", "fc11"];
                for(var key in window.data) {
                	if(exclude.indexOf(key) == -1) {
                		document.getElementById(key).value = window.data[key];
                		$scope.form01[key].$setViewValue(window.data[key]);
                	}
                	$scope.form01[key].$setTouched();
                	$scope.form01[key].$validate();
                }
                test = $scope;
            });
            organismService.loadOrganismList().then(function (organismList) {
                vm.existingOrganisms = organismList;
                
                //test = $scope.form01;
	    	//console.debug($scope.form01.fc17);
            });
            
            //console.debug(window.data);
        }

        function getSetGatheringJournalNumber(value) {
            var found = false;
            var i, m;
            vm.gathering = vm.gathering || {};
            if (arguments.length) {
                journalNo = value;
                for (i = 0, m = vm.existingGatherings.length; i < m; i++) {
                    if (vm.existingGatherings[i].journalNumber == value) {
                        vm.gathering = angular.copy(vm.existingGatherings[i]);
                        //console.debug(vm.existingGatherings[i]);
                        vm.specimenCarrierNumber(null); // reset specimen carriers
                        found = true;
                        break;
                    }
                }
                if (!found && vm.existingGathering) {
                    vm.gathering = null;
                    vm.specimenCarrierNumber(null); // reset specimen carriers
                }
                vm.existingGathering = found;
            }
            else {
                return journalNo;
            }
        }

        function getSetSpecimenCarrierNumber(value) {
            var found = false;
            var i, m;
            vm.specimenCarrier = vm.specimenCarrier || {};
            if (arguments.length) {
                carrierNo = value;
                for (i = 0, m = vm.existingSpecimenCarriers.length; i < m; i++) {
                    if (vm.existingSpecimenCarriers[i].carrierNumber == value) {
                        vm.specimenCarrier = angular.copy(vm.existingSpecimenCarriers[i]);
                        found = true;
                        $scope.form01.fc16.$validate();
                        break;
                    }
                }
                if (!found && vm.existingSpecimenCarrier) {
                    vm.specimenCarrier = null;
                }
                vm.existingSpecimenCarrier = found;
                // TODO reset organism, but all attempts failed so far
            }
            else {
                return carrierNo;
            }
        }

        function filterCarriers(value, index, array) {
            return vm.gathering && vm.gathering.id == value.gatheringId;
        }

        function resetForm() {
            // TODO
        }

        function saveForm() {
            var promise = $q.when(null);
            if (!vm.gathering.id) {
                promise = promise.then(function () {
                    vm.gathering.id = uuid.createRandom();
                    return gatheringService.logGathering(
                        vm.gathering.id,
                        journalNo,
                        vm.gathering.dateAfter,
                        vm.gathering.dateBefore,
                        vm.gathering.person,
                        vm.gathering.organization,
                        vm.gathering.country,
                        vm.gathering.province,
                        vm.gathering.region,
                        vm.gathering.place,
                        vm.gathering.remarks
                    );
                });
            }
            if (!vm.specimenCarrier.id) {
                promise = promise.then(function () {
                    vm.specimenCarrier.id = uuid.createRandom();
                    return specimenCarrierService.recordSpecimenCarrier(
                        vm.specimenCarrier.id,
                        vm.gathering.id,
                        carrierNo,
                        vm.specimenCarrier.preparationType,
                        vm.specimenCarrier.owner,
                        vm.specimenCarrier.previousCollection,
                        vm.specimenCarrier.labelTranscript
                    );
                })
            }
            promise.then(function () {
                return organismService.describeOrganismOnSpecimenCarrier(
                    vm.specimenCarrier.id,
                    vm.organism.sequenceNumber,
                    vm.organism.phaseOrStage,
                    vm.organism.sex,
                    vm.organism.remarks
                );
            }).then(function () {
                return organismService.changeNameOriginOfOrganismOnSpecimenCarrier(
                    vm.specimenCarrier.id,
                    vm.organism.sequenceNumber,
                    vm.organism.typeStatus,
                    vm.organism.identifier,
                    vm.organism.qualifier
                );
            }).then(function () {
                return organismService.changeTaxonOfOrganismOnSpecimenCarrier(
                    vm.specimenCarrier.id,
                    vm.organism.sequenceNumber,
                    vm.organism.scientificName,
                    vm.organism.validName,
                    vm.organism.otherSynonyms || '',
                    vm.organism.higherTaxa
                );
            }).then(function () {
                $location.path('/');
            });
        }

        function validateSeqNo(value) {
            var i, m;
            if (!vm.specimenCarrier || !vm.specimenCarrier.id) {
                return true;
            }
            for (i = 0, m = vm.existingOrganisms.length; i < m; i++) {
                if (vm.existingOrganisms[i].carrierNumber == vm.specimenCarrier.carrierNumber
                    && vm.existingOrganisms[i].sequenceNumber == value
                ) {
                    return false;
                }
            }
            return true;
        }
    }
})();
