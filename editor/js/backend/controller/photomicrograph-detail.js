(function () {
    angular
        .module('backend')
        .controller('PhotomicrographDetailController', PhotomicrographDetailController);

    PhotomicrographDetailController.$inject = ['preloadedPhotomicrographRecord', 'photomicrographService', '$q', '$location'];
    /**
     * TODO manage focal plane images
     * @param preloadedPhotomicrographRecord
     * @param {PhotomicrographService} photomicrographService
     * @param $q
     * @param $location
     * @constructor
     */
    function PhotomicrographDetailController(preloadedPhotomicrographRecord, photomicrographService, $q, $location) {
        var vm = this;
        vm.originalPhotomicrograph = preloadedPhotomicrographRecord;
        vm.photomicrograph = angular.copy(preloadedPhotomicrographRecord);
        vm.closeAlert = closeAlert;
        vm.deletePhotomicrograph = deletePhotomicrograph;
        vm.resetForm = resetForm;
        vm.saveForm = saveForm;
        activate();

        function activate() {
        }

        function closeAlert() {
            vm.alert = null;
        }

        function deletePhotomicrograph() {
            if (window.confirm('This photomicrograph will be deleted.')) {
                photomicrographService
                    .deletePhotomicrograph(vm.originalPhotomicrograph.id)
                    .then(function () {
                        $location.path('/organism/' + vm.originalPhotomicrograph.organismId);
                    });
            }
        }

        function resetForm() {
            angular.copy(vm.originalPhotomicrograph, vm.photomicrograph);
        }

        function saveForm() {
            var promise = $q.when(null);
            if (!angular.equals(vm.originalPhotomicrograph, vm.photomicrograph)) {
                promise = promise.then(function () {
                    return photomicrographService.manipulate(
                        vm.originalPhotomicrograph.id,
                        vm.photomicrograph.title,
                        vm.photomicrograph.detailOfPhotomicrographId,
                        vm.photomicrograph.detailOfHotspotX,
                        vm.photomicrograph.detailOfHotspotY,
                        vm.photomicrograph.creatorCapturing,
                        vm.photomicrograph.creatorProcessing,
                        vm.photomicrograph.fileRealPath,
                        vm.photomicrograph.fileUri,
                        vm.photomicrograph.fileCreationTime,
                        vm.photomicrograph.fileModificationTime,
                        vm.photomicrograph.presentationUri,
                        vm.photomicrograph.digitizationDataWidth,
                        vm.photomicrograph.digitizationDataHeight,
                        vm.photomicrograph.digitizationDataColorDepth,
                        vm.photomicrograph.digitizationDataReproductionScaleHorizontal,
                        vm.photomicrograph.digitizationDataReproductionScaleVertical,
                        vm.photomicrograph.cameraCameraMaker,
                        vm.photomicrograph.cameraCameraName,
                        vm.photomicrograph.cameraCameraArticleOrSerialNumber,
                        vm.photomicrograph.cameraSensorMaker,
                        vm.photomicrograph.cameraSensorName,
                        vm.photomicrograph.cameraSensorArticleOrSerialNumber,
                        vm.photomicrograph.cameraOpticalFormat,
                        vm.photomicrograph.cameraCaptureFormat,
                        vm.photomicrograph.cameraChipWidth,
                        vm.photomicrograph.cameraChipHeight,
                        vm.photomicrograph.cameraPixelWidth,
                        vm.photomicrograph.cameraPixelHeight,
                        vm.photomicrograph.cameraActivePixelsHor,
                        vm.photomicrograph.cameraActivePixelsVer,
                        vm.photomicrograph.cameraColorFilterArray,
                        vm.photomicrograph.cameraProtectiveColorFilter,
                        vm.photomicrograph.cameraAdcResolution,
                        vm.photomicrograph.cameraDynamicRange,
                        vm.photomicrograph.cameraSnrMax,
                        vm.photomicrograph.cameraReadoutNoise,
                        vm.photomicrograph.microscopeStandMaker,
                        vm.photomicrograph.microscopeStandName,
                        vm.photomicrograph.microscopeStandArticleOrSerialNumber,
                        vm.photomicrograph.microscopeCondenserMaker,
                        vm.photomicrograph.microscopeCondenserName,
                        vm.photomicrograph.microscopeCondenserArticleOrSerialNumber,
                        vm.photomicrograph.microscopeCondenserTurretPrismMaker,
                        vm.photomicrograph.microscopeCondenserTurretPrismName,
                        vm.photomicrograph.microscopeCondenserTurretPrismArticleOrSerialNumber,
                        vm.photomicrograph.microscopeNosepieceObjectiveMaker,
                        vm.photomicrograph.microscopeNosepieceObjectiveName,
                        vm.photomicrograph.microscopeNosepieceObjectiveArticleOrSerialNumber,
                        vm.photomicrograph.microscopeNosepieceObjectiveType,
                        vm.photomicrograph.microscopeNosepieceObjectiveNumericalAperture,
                        vm.photomicrograph.microscopeNosepieceObjectiveMagnification,
                        vm.photomicrograph.microscopeDicTurretPrismMaker,
                        vm.photomicrograph.microscopeDicTurretPrismName,
                        vm.photomicrograph.microscopeDicTurretPrismArticleOrSerialNumber,
                        vm.photomicrograph.microscopeMagnificationChangerMaker,
                        vm.photomicrograph.microscopeMagnificationChangerName,
                        vm.photomicrograph.microscopeMagnificationChangerArticleOrSerialNumber,
                        vm.photomicrograph.microscopePortsMaker,
                        vm.photomicrograph.microscopePortsName,
                        vm.photomicrograph.microscopePortsArticleOrSerialNumber,
                        vm.photomicrograph.microscopeCameraMountAdapterMaker,
                        vm.photomicrograph.microscopeCameraMountAdapterName,
                        vm.photomicrograph.microscopeCameraMountAdapterMagnification,
                        vm.photomicrograph.microscopeCameraMountAdapterArticleOrSerialNumber,
                        vm.photomicrograph.microscopeSettingsContrastMethod,
                        vm.photomicrograph.microscopeSettingsDicPrismPosition,
                        vm.photomicrograph.microscopeSettingsApertureDiaphragmOpening,
                        vm.photomicrograph.microscopeSettingsFieldDiaphragmOpening,
                        vm.photomicrograph.microscopeSettingsMagnificationChangerMagnification
                    );
                }).then(function () {
                    return photomicrographService.loadPhotomicrographRecord(vm.originalPhotomicrograph.id);
                }).then(function (photomicrographRecord) {
                    vm.originalPhotomicrograph = photomicrographRecord;
                    vm.photomicrograph = angular.copy(photomicrographRecord);
                });
            }
            promise.then(function () {
                vm.alert = {type: 'success', message: 'Changes saved successfully.'};
            }).catch(function (reason) {
                vm.alert = {type: 'danger', message: reason};
            });
        }
    }
})();
