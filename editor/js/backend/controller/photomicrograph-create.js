(function () {
    angular
        .module('backend')
        .controller('PhotomicrographCreateController', PhotomicrographCreateController);

    PhotomicrographCreateController.$inject = ['preloadedOrganismRecord', 'photomicrographService', 'uuid', '$location'];
    /**
     * @param preloadedOrganismRecord
     * @param {PhotomicrographService} photomicrographService
     * @param {Uuid} uuid
 * @param $location
     * @constructor
     */
    function PhotomicrographCreateController(preloadedOrganismRecord, photomicrographService, uuid, $location) {
        var vm = this;
        vm.photomicrograph = {};
        vm.resetForm = resetForm;
        vm.saveForm = saveForm;
        activate();

        function activate() {
        }

        function resetForm() {
        }

        function saveForm() {
            photomicrographService.digitize(
                uuid.createRandom(),
                preloadedOrganismRecord.specimenCarrierId,
                preloadedOrganismRecord.sequenceNumber,
                vm.photomicrograph.title,
                vm.photomicrograph.detailOfPhotomicrographId,
                vm.photomicrograph.detailOfHotspotX,
                vm.photomicrograph.detailOfHotspotY,
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
            ).then(function () {
                $location.path('/organism/' + preloadedOrganismRecord.id);
            });
        }
    }
})();
