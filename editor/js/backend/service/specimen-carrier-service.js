(function () {
    angular
        .module('backend')
        .factory('specimenCarrierService', specimenCarrierService);

    specimenCarrierService.$inject = ['dataService'];
    /**
     * Data service for specimen carriers.
     * @param {DataService} dataService
     * @class SpecimenCarrierService
     */
    function specimenCarrierService(dataService) {
        return {
            loadSpecimenCarrierList: loadSpecimenCarrierList,
            loadSpecimenCarrierRecord: loadSpecimenCarrierRecord,
            manipulateSpecimenCarrier: manipulateSpecimenCarrier,
            recordSpecimenCarrier: recordSpecimenCarrier
        };
        /**
         * Loads the list of specimen carriers from the server.
         * @returns {Promise} A promise whose value is a list of specimen carrier records with properties as defined in
         * /web/query/specimen-carrier-list.sql
         * @memberOf SpecimenCarrierService#
         */
        function loadSpecimenCarrierList() {
            return dataService.queryForData('specimen-carrier/').then(function (specimenCarrierList) {
                var i, m;
                for (i = 0, m = specimenCarrierList.length; i < m; i++) {
                    specimenCarrierList[i].location = dataService.arrayOfNonEmptyValues(
                        specimenCarrierList[i].place,
                        specimenCarrierList[i].region,
                        specimenCarrierList[i].province,
                        specimenCarrierList[i].country
                    ).join(', ');
                    specimenCarrierList[i].agent = dataService.arrayOfNonEmptyValues(
                        specimenCarrierList[i].person,
                        dataService.concatIfNoneEmpty('(', specimenCarrierList[i].organization, ')')
                    ).join(', ');
                    specimenCarrierList[i].date = dataService.compactDateRange(
                        specimenCarrierList[i].dateAfter,
                        specimenCarrierList[i].dateBefore
                    );
                }
                return specimenCarrierList;
            });
        }

        /**
         * Loads the specified specimen carrier from the server.
         * @param {string} uuid A lowercase 32-bit UUID, e. g. a1b2c3d4-e5f6-4a7b-8c9d-0e1f2a3b4c5d.
         * @returns {Promise} A promise whose value is a specimen carrier record with properties as defined in
         * /web/query/specimen-carrier-record.sql
         * @memberOf SpecimenCarrierService#
         */
        function loadSpecimenCarrierRecord(uuid) {
            return dataService.queryForData('specimen-carrier/' + uuid);
        }

        /**
         * @param {string} id
         * @param {string} carrierNumber
         * @param {string} preparationType
         * @param {string} owner
         * @param {string} previousCollection
         * @param {string} labelTranscript
         * @returns {Promise}
         * @memberOf SpecimenCarrierService#
         */
        function manipulateSpecimenCarrier(id, carrierNumber, preparationType, owner, previousCollection, labelTranscript) {
            return dataService.sendCommand('ManipulateSpecimenCarrier', {
                specimenCarrierId: id,
                carrierNumber: carrierNumber,
                preparationType: preparationType,
                owner: owner,
                previousCollection: previousCollection,
                labelTranscript: labelTranscript
            });
        }

        /**
         * @param {string} id
         * @param {string} gatheringId
         * @param {string} carrierNumber
         * @param {string} preparationType
         * @param {string} owner
         * @param {string} previousCollection
         * @param {string} labelTranscript
         * @returns {Promise}
         * @memberOf SpecimenCarrierService#
         */
        function recordSpecimenCarrier(id, gatheringId, carrierNumber, preparationType, owner, previousCollection, labelTranscript) {
            return dataService.sendCommand('RecordSpecimenCarrier', {
                specimenCarrierId: id,
                gatheringId: gatheringId,
                carrierNumber: carrierNumber,
                preparationType: preparationType,
                owner: owner,
                previousCollection: previousCollection,
                labelTranscript: labelTranscript
            })
        }
    }
})();
