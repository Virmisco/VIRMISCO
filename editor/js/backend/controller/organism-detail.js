(function () {
    angular
        .module('backend')
        .controller('OrganismDetailController', OrganismDetailController);

    OrganismDetailController.$inject = ['preloadedOrganismRecord', 'preloadedUploadList', 'organismService', 'specimenCarrierService', 'gatheringService', 'photomicrographService', 'uploadedRawFileService', '$scope', '$q', '$location'];
    /**
     * @param preloadedOrganismRecord
     * @param preloadedUploadList
     * @param {OrganismService} organismService
     * @param {SpecimenCarrierService} specimenCarrierService
     * @param {GatheringService} gatheringService
     * @param {PhotomicrographService} photomicrographService
     * @param {UploadedRawFileService} uploadedRawFileService
     * @param $scope
     * @param $q
     * @param $location
     * @constructor
     */
    function OrganismDetailController(preloadedOrganismRecord, preloadedUploadList, organismService, specimenCarrierService, gatheringService, photomicrographService, uploadedRawFileService, $scope, $q, $location) {
        var vm = this;
        vm.alert = null;
        vm.originalOrganism = preloadedOrganismRecord;
        vm.organism = angular.copy(preloadedOrganismRecord);
        vm.originalSpecimenCarrier = null;
        vm.specimenCarrier = null;
        vm.originalGathering = null;
        vm.gathering = null;
        vm.originalPhotomicrographs = [];
        vm.photomicrographs = [];
        vm.existingGathering = false;
        vm.existingSpecimenCarrier = false;
        vm.gatheringJournalNumber = getSetGatheringJournalNumber;
        vm.specimenCarrierNumber = getSetSpecimenCarrierNumber;
        vm.lasUri = null;
        vm.uploads = preloadedUploadList;
        vm.closeAlert = closeAlert;
        vm.deleteOrganism = deleteOrganism;
        vm.copyOrganism = copyOrganism;
        vm.resetForm = resetForm;
        vm.saveForm = saveForm;
        vm.validateSeqNo = validateSeqNo;
        vm.sortBy = sortBy;

        function sortBy(field) {
            vm.desc = field === vm.sort ? !vm.desc : false;
            vm.sort = field;
        }
        
        activate();
        $scope.uploadOrderFunc = function (item) {
            if (!vm || !vm.gathering || !vm.specimenCarrier
                || !vm.specimenCarrier.carrierNumber || !vm.gathering.journalNumber
            ) {
                return 0;
            }
            return item.uri.match((vm.specimenCarrier.carrierNumber + "").replace('/', '.'))
                ? -2
                : (item.uri.match(vm.gathering.journalNumber) ? -1 : 0);
        };

        $scope.resolveOrderValue = function (value) {
            switch (-value) {
                case 1:
                    return 'Matching journal no.';
                case 2:
                    return 'Matching carrier no.';
            }
            return 'Others';
        };

        function activate() {
            specimenCarrierService.loadSpecimenCarrierRecord(vm.organism.specimenCarrierId)
                .then(function (specimenCarrierRecord) {
                    vm.originalSpecimenCarrier = specimenCarrierRecord;
                    vm.specimenCarrier = angular.copy(specimenCarrierRecord);
                    gatheringService.loadGatheringRecord(vm.specimenCarrier.gatheringId)
                        .then(function (gatheringRecord) {
                            vm.originalGathering = gatheringRecord;
                            vm.gathering = angular.copy(gatheringRecord);
                        })
                });
            photomicrographService.loadPhotomicrographListFiltered(vm.organism.id)
                .then(function (photomicrographList) {
                    vm.originalPhotomicrographs = photomicrographList;
                    vm.photomicrographs = angular.copy(photomicrographList);
                    console.debug(vm.photomicrographs);
                });
        }

        function closeAlert() {
            vm.alert = null;
        }

        function deleteOrganism() {
            if (window.confirm('This organism and all its photomicrographs will be deleted.')) {
                organismService
                    .discardOrganismDescription(vm.specimenCarrier.id, vm.originalOrganism.sequenceNumber)
                    .then(function () {
                        $location.path('/');
                    });
            }
        }
        
        function copyOrganism() {
            console.debug("test");
            
            //var formElements = document.getElementsByClassName("form-control");
            var copyWindow = window.open("#/organism/new");
	    
	    var toCopy  = ["fc01", "fc11", "fc17", "fc18", "fc19", "fc20", "fc21", "fc22", "fc23", "fc24", "fc25", "fc26"]
	    var data = {};
	    var value;
	    for(var i = 0; i < toCopy.length; i++) {
	    	value = document.getElementById(toCopy[i]);
	    	if(value && value.value && value.value.trim().length > 0)
	    		data[toCopy[i]] = value.value;
	    }
	    copyWindow.data = data;
	    
	    
	    
/*
            //var exclude = ["fc01", "fc16", "fc11"];
            var exclude = ["fc02", "fc03", "fc04", "fc05", "fc06", "fc07", "fc08", "fc09", "fc10", "fc12", "fc13", "fc14", "fc15", "fc16"]
            
            copyWindow.setTimeout(function() {
            	var copy = copyWindow.document;
            	var toCopy, newElement;
		for(var i = 0; i < formElements.length; i++) {
			toCopy = formElements[i];
			if(toCopy.id.indexOf("fc") == 0) {
				newElement = copy.getElementById(toCopy.id);
				if(newElement != null && exclude.indexOf(toCopy.id) == -1) {
					newElement.value = toCopy.value;
					console.debug(toCopy.id);
				}
			}
		}
            }, 1000);
*/
        }

        function getSetGatheringJournalNumber(value) {
            vm.gathering = vm.gathering || {};
            if (arguments.length) {
                vm.gathering.journalNumber = value;
            }
            else {
                return vm.gathering.journalNumber;
            }
        }

        function getSetSpecimenCarrierNumber(value) {
            vm.specimenCarrier = vm.specimenCarrier || {};
            if (arguments.length) {
                vm.specimenCarrier.carrierNumber = value;
            }
            else {
                return vm.specimenCarrier.carrierNumber;
            }
        }

        function resetForm() {
            angular.copy(vm.originalPhotomicrographs, vm.photomicrographs);
            angular.copy(vm.originalOrganism, vm.organism);
            angular.copy(vm.originalSpecimenCarrier, vm.specimenCarrier);
            angular.copy(vm.originalGathering, vm.gathering);
        }

        function saveForm() {
            var promise = $q.when(null);
            if (!angular.equals(vm.originalGathering, vm.gathering)) {
                promise = promise.then(function () {
                    return gatheringService.manipulateGathering(
                        vm.gathering.id,
                        vm.gathering.journalNumber,
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
                }).then(function () {
                    return gatheringService.loadGatheringRecord(vm.specimenCarrier.gatheringId);
                }).then(function (gatheringRecord) {
                    vm.originalGathering = gatheringRecord;
                    vm.gathering = angular.copy(gatheringRecord);
                });
            }
            if (!angular.equals(vm.originalSpecimenCarrier, vm.specimenCarrier)) {
                promise = promise.then(function () {
                    return specimenCarrierService.manipulateSpecimenCarrier(
                        vm.specimenCarrier.id,
                        vm.specimenCarrier.carrierNumber,
                        vm.specimenCarrier.preparationType,
                        vm.specimenCarrier.owner,
                        vm.specimenCarrier.previousCollection,
                        vm.specimenCarrier.labelTranscript
                    );
                }).then(function () {
                    return specimenCarrierService.loadSpecimenCarrierRecord(vm.organism.specimenCarrierId);
                }).then(function (specimenCarrierRecord) {
                    vm.originalSpecimenCarrier = specimenCarrierRecord;
                    vm.specimenCarrier = angular.copy(specimenCarrierRecord);
                });
            }
            if (!angular.equals(
                    [
                        vm.originalOrganism.sequenceNumber,
                        vm.originalOrganism.phaseOrStage,
                        vm.originalOrganism.sex,
                        vm.originalOrganism.remarks
                    ],
                    [
                        vm.organism.sequenceNumber,
                        vm.organism.phaseOrStage,
                        vm.organism.sex,
                        vm.organism.remarks
                    ]
                )
            ) {
                promise = promise.then(function () {
                    return organismService.manipulateOrganismOnSpecimenCarrier(
                        vm.specimenCarrier.id,
                        vm.originalOrganism.sequenceNumber,
                        vm.organism.sequenceNumber,
                        vm.organism.phaseOrStage,
                        vm.organism.sex,
                        vm.organism.remarks
                    );
                }).then(function (responseData) {
                    return organismService.loadOrganismRecord(vm.organism.id);
                }).then(function (organismRecord) {
                    vm.originalOrganism = organismRecord;
                    vm.organism.sequenceNumber = organismRecord.sequenceNumber;
                    vm.organism.phaseOrStage = organismRecord.phaseOrStage;
                    vm.organism.sex = organismRecord.sex;
                    vm.organism.remarks = organismRecord.remarks;
                });
            }
            if (!angular.equals(
                    [
                        vm.originalOrganism.typeStatus,
                        vm.originalOrganism.identifier,
                        vm.originalOrganism.qualifier
                    ],
                    [
                        vm.organism.typeStatus,
                        vm.organism.identifier,
                        vm.organism.qualifier
                    ]
                )
            ) {
                promise = promise.then(function () {
                    return organismService.changeNameOriginOfOrganismOnSpecimenCarrier(
                        vm.specimenCarrier.id,
                        vm.organism.sequenceNumber,
                        vm.organism.typeStatus,
                        vm.organism.identifier,
                        vm.organism.qualifier
                    );
                }).then(function () { return true; });
            }
            if (!angular.equals(
                    [
                        vm.originalOrganism.scientificName,
                        vm.originalOrganism.validName,
                        vm.originalOrganism.otherSynonyms,
                        vm.originalOrganism.higherTaxa
                    ],
                    [
                        vm.organism.scientificName,
                        vm.organism.validName,
                        vm.organism.otherSynonyms,
                        vm.organism.higherTaxa
                    ]
                )
            ) {
                promise = promise.then(function () {
                    return organismService.changeTaxonOfOrganismOnSpecimenCarrier(
                        vm.specimenCarrier.id,
                        vm.organism.sequenceNumber,
                        vm.organism.scientificName,
                        vm.organism.validName,
                        vm.organism.otherSynonyms,
                        vm.organism.higherTaxa
                    );
                }).then(function () { return true; });
            }
            promise = promise.then(function (needsReload) {
                return needsReload ? organismService.loadOrganismRecord(vm.organism.id) : null;
            }).then(function (organismRecord) {
                if (organismRecord) {
                    vm.originalOrganism = organismRecord;
                    vm.organism.typeStatus = organismRecord.typeStatus;
                    vm.organism.scientificName = organismRecord.scientificName;
                    vm.organism.identifier = organismRecord.identifier;
                    vm.organism.qualifier = organismRecord.qualifier;
                }
            });
            if (vm.lasUri && vm.lasUri.uri) {
                promise = promise.then(function () {
                    return organismService.processLasFile(
                        vm.specimenCarrier.id,
                        vm.organism.sequenceNumber,
                        vm.lasUri.uri
                    );
                }).then(function () {
                    return photomicrographService.loadPhotomicrographListFiltered(vm.organism.id);
                }).then(function (photomicrographList) {
                    vm.originalPhotomicrographs = photomicrographList;
                    vm.photomicrographs = angular.copy(photomicrographList);
                }).then(function () {
                    return uploadedRawFileService.loadUploadList();
                }).then(function (uploadList) {
                    vm.uploads = uploadList;
                });
            }
            if (!angular.equals(vm.originalPhotomicrographs, vm.photomicrographs)) {
                angular.forEach(vm.photomicrographs, function (photomicrograph, index) {
                    var originalPhotomicrograph = vm.originalPhotomicrographs[index];
                    if (originalPhotomicrograph.title != photomicrograph.title) {
                        promise = promise.then(function () {
                            return photomicrographService.rename(photomicrograph.id, photomicrograph.title);
                        }).then(function () {
                            return true;
                        });
                    }
                    if (originalPhotomicrograph.microscopeSettingsDicPrismPosition
                        != photomicrograph.microscopeSettingsDicPrismPosition
                    ) {
                        promise = promise.then(function () {
                            return photomicrographService.changeDicPrismPosition(
                                photomicrograph.id,
                                photomicrograph.microscopeSettingsDicPrismPosition
                            );
                        }).then(function () {
                            return true;
                        });
                    }
                    if (originalPhotomicrograph.creatorCapturing != photomicrograph.creatorCapturing
                        || originalPhotomicrograph.creatorProcessing != photomicrograph.creatorProcessing
                    ) {
                        promise = promise.then(function () {
                            return photomicrographService.provideAuthorship(
                                photomicrograph.id,
                                photomicrograph.creatorCapturing,
                                photomicrograph.creatorProcessing
                            );
                        }).then(function () {
                            return true;
                        });
                    }
                });
                promise = promise.then(function (needsReload) {
                    return needsReload ? photomicrographService.loadPhotomicrographListFiltered(vm.organism.id) : null;
                }).then(function (photomicrographList) {
                    if (photomicrographList) {
                        vm.originalPhotomicrographs = photomicrographList;
                        vm.photomicrographs = angular.copy(photomicrographList);
                    }
                });
            }
            promise.then(function () {
                vm.alert = {type: 'success', message: 'Changes saved successfully.'};
            }).catch(function (reason) {
                vm.alert = {type: 'danger', message: reason};
            });
        }

        function validateSeqNo(value) {
            return true;
        }
    }
})();
