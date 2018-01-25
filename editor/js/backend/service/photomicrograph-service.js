(function () {
    angular
        .module('backend')
        .factory('photomicrographService', photomicrographService);

    photomicrographService.$inject = ['dataService'];
    /**
     * Data service for photomicrographs.
     * @param {DataService} dataService
     * @class PhotomicrographService
     */
    function photomicrographService(dataService) {
        return {
            changeDicPrismPosition: changeDicPrismPosition,
            deletePhotomicrograph: deletePhotomicrograph,
            digitize: digitize,
            loadPhotomicrographList: loadPhotomicrographList,
            loadPhotomicrographListFiltered: loadPhotomicrographListFiltered,
            loadPhotomicrographRecord: loadPhotomicrographRecord,
            manipulate: manipulate,
            provideAuthorship: provideAuthorship,
            rename: rename
        };

        /**
         * @param {string} id
         * @param {number} prismPosition
         * @returns {Promise}
         * @memberOf PhotomicrographService#
         */
        function changeDicPrismPosition(id, prismPosition) {
            return dataService.sendCommand('ChangeDicPrismPositionOfPhotomicrograph', {
                aggregateId: id,
                dicPrismPosition: prismPosition
            });
        }

        /**
         * @param {string} id
         * @returns {Promise}
         * @memberOf PhotomicrographService#
         */
        function deletePhotomicrograph(id) {
            return dataService.sendCommand('DeletePhotomicrograph', {aggregateId: id});
        }

        /**
         * Loads the list of photomicrographs from the server.
         * @returns {Promise} A promise whose value is an array of photomicrograph records with properties as defined in
         * /web/query/photomicrograph-list.sql
         * @memberOf PhotomicrographService#
         */
        function loadPhotomicrographList() {
            return dataService.queryForData('photomicrograph/');
        }

        /**
         * Loads the list of photomicrographs matching the given filter criteria from the server.
         * @returns {Promise} A promise whose value is an array of photomicrograph records with properties as defined in
         * /web/query/photomicrograph-filtered.sql
         * @memberOf PhotomicrographService#
         */
        function loadPhotomicrographListFiltered(organismId) {
            return dataService.queryForData('photomicrograph/organismId:' + organismId);
        }

        /**
         * Loads the specified photomicrograph from the server.
         * @param {string} uuid A lowercase 32-bit UUID, e. g. a1b2c3d4-e5f6-4a7b-8c9d-0e1f2a3b4c5d.
         * @returns {Promise} A promise whose value is an photomicrograph record with properties as defined in
         * /web/query/photomicrograph-record.sql
         * @memberOf PhotomicrographService#
         */
        function loadPhotomicrographRecord(uuid) {
            return dataService.queryForData('photomicrograph/' + uuid);
        }

        /**
         * @param {string} id
         * @param specimenCarrierId
         * @param sequenceNumber
         * @param title
         * @param detailOfPhotomicrographId
         * @param detailOfHotspotX
         * @param detailOfHotspotY
         * @param fileRealPath
         * @param fileUri
         * @param fileCreationTime
         * @param fileModificationTime
         * @param presentationUri
         * @param digitizationDataWidth
         * @param digitizationDataHeight
         * @param digitizationDataColorDepth
         * @param digitizationDataReproductionScaleHorizontal
         * @param digitizationDataReproductionScaleVertical
         * @param cameraCameraMaker
         * @param cameraCameraName
         * @param cameraCameraArticleOrSerialNumber
         * @param cameraSensorMaker
         * @param cameraSensorName
         * @param cameraSensorArticleOrSerialNumber
         * @param cameraOpticalFormat
         * @param cameraCaptureFormat
         * @param cameraChipWidth
         * @param cameraChipHeight
         * @param cameraPixelWidth
         * @param cameraPixelHeight
         * @param cameraActivePixelsHor
         * @param cameraActivePixelsVer
         * @param cameraColorFilterArray
         * @param cameraProtectiveColorFilter
         * @param cameraAdcResolution
         * @param cameraDynamicRange
         * @param cameraSnrMax
         * @param cameraReadoutNoise
         * @param microscopeStandMaker
         * @param microscopeStandName
         * @param microscopeStandArticleOrSerialNumber
         * @param microscopeCondenserMaker
         * @param microscopeCondenserName
         * @param microscopeCondenserArticleOrSerialNumber
         * @param microscopeCondenserTurretPrismMaker
         * @param microscopeCondenserTurretPrismName
         * @param microscopeCondenserTurretPrismArticleOrSerialNumber
         * @param microscopeNosepieceObjectiveMaker
         * @param microscopeNosepieceObjectiveName
         * @param microscopeNosepieceObjectiveArticleOrSerialNumber
         * @param microscopeNosepieceObjectiveType
         * @param microscopeNosepieceObjectiveNumericalAperture
         * @param microscopeNosepieceObjectiveMagnification
         * @param microscopeDicTurretPrismMaker
         * @param microscopeDicTurretPrismName
         * @param microscopeDicTurretPrismArticleOrSerialNumber
         * @param microscopeMagnificationChangerMaker
         * @param microscopeMagnificationChangerName
         * @param microscopeMagnificationChangerArticleOrSerialNumber
         * @param microscopePortsMaker
         * @param microscopePortsName
         * @param microscopePortsArticleOrSerialNumber
         * @param microscopeCameraMountAdapterMaker
         * @param microscopeCameraMountAdapterName
         * @param microscopeCameraMountAdapterMagnification
         * @param microscopeCameraMountAdapterArticleOrSerialNumber
         * @param microscopeSettingsContrastMethod
         * @param microscopeSettingsDicPrismPosition
         * @param microscopeSettingsApertureDiaphragmOpening
         * @param microscopeSettingsFieldDiaphragmOpening
         * @param microscopeSettingsMagnificationChangerMagnification
         * @returns {Promise}
         * @memberOf PhotomicrographService#
         */
        function digitize(id, specimenCarrierId, sequenceNumber, title, detailOfPhotomicrographId, detailOfHotspotX, detailOfHotspotY, fileRealPath, fileUri, fileCreationTime, fileModificationTime, presentationUri, digitizationDataWidth, digitizationDataHeight, digitizationDataColorDepth, digitizationDataReproductionScaleHorizontal, digitizationDataReproductionScaleVertical, cameraCameraMaker, cameraCameraName, cameraCameraArticleOrSerialNumber, cameraSensorMaker, cameraSensorName, cameraSensorArticleOrSerialNumber, cameraOpticalFormat, cameraCaptureFormat, cameraChipWidth, cameraChipHeight, cameraPixelWidth, cameraPixelHeight, cameraActivePixelsHor, cameraActivePixelsVer, cameraColorFilterArray, cameraProtectiveColorFilter, cameraAdcResolution, cameraDynamicRange, cameraSnrMax, cameraReadoutNoise, microscopeStandMaker, microscopeStandName, microscopeStandArticleOrSerialNumber, microscopeCondenserMaker, microscopeCondenserName, microscopeCondenserArticleOrSerialNumber, microscopeCondenserTurretPrismMaker, microscopeCondenserTurretPrismName, microscopeCondenserTurretPrismArticleOrSerialNumber, microscopeNosepieceObjectiveMaker, microscopeNosepieceObjectiveName, microscopeNosepieceObjectiveArticleOrSerialNumber, microscopeNosepieceObjectiveType, microscopeNosepieceObjectiveNumericalAperture, microscopeNosepieceObjectiveMagnification, microscopeDicTurretPrismMaker, microscopeDicTurretPrismName, microscopeDicTurretPrismArticleOrSerialNumber, microscopeMagnificationChangerMaker, microscopeMagnificationChangerName, microscopeMagnificationChangerArticleOrSerialNumber, microscopePortsMaker, microscopePortsName, microscopePortsArticleOrSerialNumber, microscopeCameraMountAdapterMaker, microscopeCameraMountAdapterName, microscopeCameraMountAdapterMagnification, microscopeCameraMountAdapterArticleOrSerialNumber, microscopeSettingsContrastMethod, microscopeSettingsDicPrismPosition, microscopeSettingsApertureDiaphragmOpening, microscopeSettingsFieldDiaphragmOpening, microscopeSettingsMagnificationChangerMagnification) {
            return dataService.sendCommand('DigitizePhotomicrograph', {
                photomicrographId: id,
                specimenCarrierId: specimenCarrierId,
                sequenceNumber: sequenceNumber,
                title: title,
                detailOfPhotomicrographId: detailOfPhotomicrographId,
                detailOfHotspotX: detailOfHotspotX,
                detailOfHotspotY: detailOfHotspotY,
                fileRealPath: fileRealPath,
                fileUri: fileUri,
                fileCreationTime: fileCreationTime,
                fileModificationTime: fileModificationTime,
                presentationUri: presentationUri,
                digitizationDataWidth: digitizationDataWidth,
                digitizationDataHeight: digitizationDataHeight,
                digitizationDataColorDepth: digitizationDataColorDepth,
                digitizationDataReproductionScaleHorizontal: digitizationDataReproductionScaleHorizontal,
                digitizationDataReproductionScaleVertical: digitizationDataReproductionScaleVertical,
                cameraCameraMaker: cameraCameraMaker,
                cameraCameraName: cameraCameraName,
                cameraCameraArticleOrSerialNumber: cameraCameraArticleOrSerialNumber,
                cameraSensorMaker: cameraSensorMaker,
                cameraSensorName: cameraSensorName,
                cameraSensorArticleOrSerialNumber: cameraSensorArticleOrSerialNumber,
                cameraOpticalFormat: cameraOpticalFormat,
                cameraCaptureFormat: cameraCaptureFormat,
                cameraChipWidth: cameraChipWidth,
                cameraChipHeight: cameraChipHeight,
                cameraPixelWidth: cameraPixelWidth,
                cameraPixelHeight: cameraPixelHeight,
                cameraActivePixelsHor: cameraActivePixelsHor,
                cameraActivePixelsVer: cameraActivePixelsVer,
                cameraColorFilterArray: cameraColorFilterArray,
                cameraProtectiveColorFilter: cameraProtectiveColorFilter,
                cameraAdcResolution: cameraAdcResolution,
                cameraDynamicRange: cameraDynamicRange,
                cameraSnrMax: cameraSnrMax,
                cameraReadoutNoise: cameraReadoutNoise,
                microscopeStandMaker: microscopeStandMaker,
                microscopeStandName: microscopeStandName,
                microscopeStandArticleOrSerialNumber: microscopeStandArticleOrSerialNumber,
                microscopeCondenserMaker: microscopeCondenserMaker,
                microscopeCondenserName: microscopeCondenserName,
                microscopeCondenserArticleOrSerialNumber: microscopeCondenserArticleOrSerialNumber,
                microscopeCondenserTurretPrismMaker: microscopeCondenserTurretPrismMaker,
                microscopeCondenserTurretPrismName: microscopeCondenserTurretPrismName,
                microscopeCondenserTurretPrismArticleOrSerialNumber: microscopeCondenserTurretPrismArticleOrSerialNumber,
                microscopeNosepieceObjectiveMaker: microscopeNosepieceObjectiveMaker,
                microscopeNosepieceObjectiveName: microscopeNosepieceObjectiveName,
                microscopeNosepieceObjectiveArticleOrSerialNumber: microscopeNosepieceObjectiveArticleOrSerialNumber,
                microscopeNosepieceObjectiveType: microscopeNosepieceObjectiveType,
                microscopeNosepieceObjectiveNumericalAperture: microscopeNosepieceObjectiveNumericalAperture,
                microscopeNosepieceObjectiveMagnification: microscopeNosepieceObjectiveMagnification,
                microscopeDicTurretPrismMaker: microscopeDicTurretPrismMaker,
                microscopeDicTurretPrismName: microscopeDicTurretPrismName,
                microscopeDicTurretPrismArticleOrSerialNumber: microscopeDicTurretPrismArticleOrSerialNumber,
                microscopeMagnificationChangerMaker: microscopeMagnificationChangerMaker,
                microscopeMagnificationChangerName: microscopeMagnificationChangerName,
                microscopeMagnificationChangerArticleOrSerialNumber: microscopeMagnificationChangerArticleOrSerialNumber,
                microscopeNumberOfPorts: null,
                microscopePortsMaker: microscopePortsMaker,
                microscopePortsName: microscopePortsName,
                microscopePortsArticleOrSerialNumber: microscopePortsArticleOrSerialNumber,
                microscopeCameraMountAdapterMaker: microscopeCameraMountAdapterMaker,
                microscopeCameraMountAdapterName: microscopeCameraMountAdapterName,
                microscopeCameraMountAdapterMagnification: microscopeCameraMountAdapterMagnification,
                microscopeCameraMountAdapterArticleOrSerialNumber: microscopeCameraMountAdapterArticleOrSerialNumber,
                microscopeSettingsContrastMethod: microscopeSettingsContrastMethod,
                microscopeSettingsDicPrismPosition: microscopeSettingsDicPrismPosition,
                microscopeSettingsApertureDiaphragmOpening: microscopeSettingsApertureDiaphragmOpening,
                microscopeSettingsFieldDiaphragmOpening: microscopeSettingsFieldDiaphragmOpening,
                microscopeSettingsIsPolarizerInLightPath: null,
                microscopeSettingsMagnificationChangerMagnification: microscopeSettingsMagnificationChangerMagnification
            });
        }

        /**
         * @param {string} id
         * @param title
         * @param detailOfPhotomicrographId
         * @param detailOfHotspotX
         * @param detailOfHotspotY
         * @param creatorCapturing
         * @param creatorProcessing
         * @param fileRealPath
         * @param fileUri
         * @param fileCreationTime
         * @param fileModificationTime
         * @param presentationUri
         * @param digitizationDataWidth
         * @param digitizationDataHeight
         * @param digitizationDataColorDepth
         * @param digitizationDataReproductionScaleHorizontal
         * @param digitizationDataReproductionScaleVertical
         * @param cameraCameraMaker
         * @param cameraCameraName
         * @param cameraCameraArticleOrSerialNumber
         * @param cameraSensorMaker
         * @param cameraSensorName
         * @param cameraSensorArticleOrSerialNumber
         * @param cameraOpticalFormat
         * @param cameraCaptureFormat
         * @param cameraChipWidth
         * @param cameraChipHeight
         * @param cameraPixelWidth
         * @param cameraPixelHeight
         * @param cameraActivePixelsHor
         * @param cameraActivePixelsVer
         * @param cameraColorFilterArray
         * @param cameraProtectiveColorFilter
         * @param cameraAdcResolution
         * @param cameraDynamicRange
         * @param cameraSnrMax
         * @param cameraReadoutNoise
         * @param microscopeStandMaker
         * @param microscopeStandName
         * @param microscopeStandArticleOrSerialNumber
         * @param microscopeCondenserMaker
         * @param microscopeCondenserName
         * @param microscopeCondenserArticleOrSerialNumber
         * @param microscopeCondenserTurretPrismMaker
         * @param microscopeCondenserTurretPrismName
         * @param microscopeCondenserTurretPrismArticleOrSerialNumber
         * @param microscopeNosepieceObjectiveMaker
         * @param microscopeNosepieceObjectiveName
         * @param microscopeNosepieceObjectiveArticleOrSerialNumber
         * @param microscopeNosepieceObjectiveType
         * @param microscopeNosepieceObjectiveNumericalAperture
         * @param microscopeNosepieceObjectiveMagnification
         * @param microscopeDicTurretPrismMaker
         * @param microscopeDicTurretPrismName
         * @param microscopeDicTurretPrismArticleOrSerialNumber
         * @param microscopeMagnificationChangerMaker
         * @param microscopeMagnificationChangerName
         * @param microscopeMagnificationChangerArticleOrSerialNumber
         * @param microscopePortsMaker
         * @param microscopePortsName
         * @param microscopePortsArticleOrSerialNumber
         * @param microscopeCameraMountAdapterMaker
         * @param microscopeCameraMountAdapterName
         * @param microscopeCameraMountAdapterMagnification
         * @param microscopeCameraMountAdapterArticleOrSerialNumber
         * @param microscopeSettingsContrastMethod
         * @param microscopeSettingsDicPrismPosition
         * @param microscopeSettingsApertureDiaphragmOpening
         * @param microscopeSettingsFieldDiaphragmOpening
         * @param microscopeSettingsMagnificationChangerMagnification
         * @returns {Promise}
         * @memberOf PhotomicrographService#
         */
        function manipulate(id, title, detailOfPhotomicrographId, detailOfHotspotX, detailOfHotspotY, creatorCapturing, creatorProcessing, fileRealPath, fileUri, fileCreationTime, fileModificationTime, presentationUri, digitizationDataWidth, digitizationDataHeight, digitizationDataColorDepth, digitizationDataReproductionScaleHorizontal, digitizationDataReproductionScaleVertical, cameraCameraMaker, cameraCameraName, cameraCameraArticleOrSerialNumber, cameraSensorMaker, cameraSensorName, cameraSensorArticleOrSerialNumber, cameraOpticalFormat, cameraCaptureFormat, cameraChipWidth, cameraChipHeight, cameraPixelWidth, cameraPixelHeight, cameraActivePixelsHor, cameraActivePixelsVer, cameraColorFilterArray, cameraProtectiveColorFilter, cameraAdcResolution, cameraDynamicRange, cameraSnrMax, cameraReadoutNoise, microscopeStandMaker, microscopeStandName, microscopeStandArticleOrSerialNumber, microscopeCondenserMaker, microscopeCondenserName, microscopeCondenserArticleOrSerialNumber, microscopeCondenserTurretPrismMaker, microscopeCondenserTurretPrismName, microscopeCondenserTurretPrismArticleOrSerialNumber, microscopeNosepieceObjectiveMaker, microscopeNosepieceObjectiveName, microscopeNosepieceObjectiveArticleOrSerialNumber, microscopeNosepieceObjectiveType, microscopeNosepieceObjectiveNumericalAperture, microscopeNosepieceObjectiveMagnification, microscopeDicTurretPrismMaker, microscopeDicTurretPrismName, microscopeDicTurretPrismArticleOrSerialNumber, microscopeMagnificationChangerMaker, microscopeMagnificationChangerName, microscopeMagnificationChangerArticleOrSerialNumber, microscopePortsMaker, microscopePortsName, microscopePortsArticleOrSerialNumber, microscopeCameraMountAdapterMaker, microscopeCameraMountAdapterName, microscopeCameraMountAdapterMagnification, microscopeCameraMountAdapterArticleOrSerialNumber, microscopeSettingsContrastMethod, microscopeSettingsDicPrismPosition, microscopeSettingsApertureDiaphragmOpening, microscopeSettingsFieldDiaphragmOpening, microscopeSettingsMagnificationChangerMagnification) {
            return dataService.sendCommand('ManipulatePhotomicrograph', {
                photomicrographId: id,
                title: title,
                detailOfPhotomicrographId: detailOfPhotomicrographId,
                detailOfHotspotX: detailOfHotspotX,
                detailOfHotspotY: detailOfHotspotY,
                creatorCapturing: creatorCapturing,
                creatorProcessing: creatorProcessing,
                fileRealPath: fileRealPath,
                fileUri: fileUri,
                fileCreationTime: fileCreationTime,
                fileModificationTime: fileModificationTime,
                presentationUri: presentationUri,
                digitizationDataWidth: digitizationDataWidth,
                digitizationDataHeight: digitizationDataHeight,
                digitizationDataColorDepth: digitizationDataColorDepth,
                digitizationDataReproductionScaleHorizontal: digitizationDataReproductionScaleHorizontal,
                digitizationDataReproductionScaleVertical: digitizationDataReproductionScaleVertical,
                cameraCameraMaker: cameraCameraMaker,
                cameraCameraName: cameraCameraName,
                cameraCameraArticleOrSerialNumber: cameraCameraArticleOrSerialNumber,
                cameraSensorMaker: cameraSensorMaker,
                cameraSensorName: cameraSensorName,
                cameraSensorArticleOrSerialNumber: cameraSensorArticleOrSerialNumber,
                cameraOpticalFormat: cameraOpticalFormat,
                cameraCaptureFormat: cameraCaptureFormat,
                cameraChipWidth: cameraChipWidth,
                cameraChipHeight: cameraChipHeight,
                cameraPixelWidth: cameraPixelWidth,
                cameraPixelHeight: cameraPixelHeight,
                cameraActivePixelsHor: cameraActivePixelsHor,
                cameraActivePixelsVer: cameraActivePixelsVer,
                cameraColorFilterArray: cameraColorFilterArray,
                cameraProtectiveColorFilter: cameraProtectiveColorFilter,
                cameraAdcResolution: cameraAdcResolution,
                cameraDynamicRange: cameraDynamicRange,
                cameraSnrMax: cameraSnrMax,
                cameraReadoutNoise: cameraReadoutNoise,
                microscopeStandMaker: microscopeStandMaker,
                microscopeStandName: microscopeStandName,
                microscopeStandArticleOrSerialNumber: microscopeStandArticleOrSerialNumber,
                microscopeCondenserMaker: microscopeCondenserMaker,
                microscopeCondenserName: microscopeCondenserName,
                microscopeCondenserArticleOrSerialNumber: microscopeCondenserArticleOrSerialNumber,
                microscopeCondenserTurretPrismMaker: microscopeCondenserTurretPrismMaker,
                microscopeCondenserTurretPrismName: microscopeCondenserTurretPrismName,
                microscopeCondenserTurretPrismArticleOrSerialNumber: microscopeCondenserTurretPrismArticleOrSerialNumber,
                microscopeNosepieceObjectiveMaker: microscopeNosepieceObjectiveMaker,
                microscopeNosepieceObjectiveName: microscopeNosepieceObjectiveName,
                microscopeNosepieceObjectiveArticleOrSerialNumber: microscopeNosepieceObjectiveArticleOrSerialNumber,
                microscopeNosepieceObjectiveType: microscopeNosepieceObjectiveType,
                microscopeNosepieceObjectiveNumericalAperture: microscopeNosepieceObjectiveNumericalAperture,
                microscopeNosepieceObjectiveMagnification: microscopeNosepieceObjectiveMagnification,
                microscopeDicTurretPrismMaker: microscopeDicTurretPrismMaker,
                microscopeDicTurretPrismName: microscopeDicTurretPrismName,
                microscopeDicTurretPrismArticleOrSerialNumber: microscopeDicTurretPrismArticleOrSerialNumber,
                microscopeMagnificationChangerMaker: microscopeMagnificationChangerMaker,
                microscopeMagnificationChangerName: microscopeMagnificationChangerName,
                microscopeMagnificationChangerArticleOrSerialNumber: microscopeMagnificationChangerArticleOrSerialNumber,
                microscopePortsMaker: microscopePortsMaker,
                microscopePortsName: microscopePortsName,
                microscopePortsArticleOrSerialNumber: microscopePortsArticleOrSerialNumber,
                microscopeCameraMountAdapterMaker: microscopeCameraMountAdapterMaker,
                microscopeCameraMountAdapterName: microscopeCameraMountAdapterName,
                microscopeCameraMountAdapterMagnification: microscopeCameraMountAdapterMagnification,
                microscopeCameraMountAdapterArticleOrSerialNumber: microscopeCameraMountAdapterArticleOrSerialNumber,
                microscopeSettingsContrastMethod: microscopeSettingsContrastMethod,
                microscopeSettingsDicPrismPosition: microscopeSettingsDicPrismPosition,
                microscopeSettingsApertureDiaphragmOpening: microscopeSettingsApertureDiaphragmOpening,
                microscopeSettingsFieldDiaphragmOpening: microscopeSettingsFieldDiaphragmOpening,
                microscopeSettingsMagnificationChangerMagnification: microscopeSettingsMagnificationChangerMagnification
            });
        }

        /**
         * @param {string} id
         * @param {string} creatorCapturing
         * @param {string} creatorProcessing
         * @returns {Promise}
         * @memberOf PhotomicrographService#
         */
        function provideAuthorship(id, creatorCapturing, creatorProcessing) {
            return dataService.sendCommand('ProvideAuthorshipOfPhotomicrograph', {
                aggregateId: id,
                creatorCapturingDigitalMaster: creatorCapturing,
                creatorProcessingDerivatives: creatorProcessing
            });
        }

        /**
         * @param {string} id
         * @param {string} title
         * @returns {Promise}
         * @memberOf PhotomicrographService#
         */
        function rename(id, title) {
            return dataService.sendCommand('RenamePhotomicrograph', {
                aggregateId: id,
                title: title
            });
        }
    }
})();
