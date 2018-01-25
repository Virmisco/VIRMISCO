(function () {
    angular
        .module('backend')
        .factory('organismService', organismService);

    organismService.$inject = ['dataService', 'gbifNameParser'];
    /**
     * Data service for organisms.
     * @param {DataService} dataService
     * @param {GbifNameParser} gbifNameParser
     * @class OrganismService
     */
    function organismService(dataService, gbifNameParser) {
        return {
            changeNameOriginOfOrganismOnSpecimenCarrier: changeNameOriginOfOrganismOnSpecimenCarrier,
            changeTaxonOfOrganismOnSpecimenCarrier: changeTaxonOfOrganismOnSpecimenCarrier,
            discardOrganismDescription: discardOrganismDescription,
            describeOrganismOnSpecimenCarrier: describeOrganismOnSpecimenCarrier,
            loadOrganismList: loadOrganismList,
            loadOrganismRecord: loadOrganismRecord,
            manipulateOrganismOnSpecimenCarrier: manipulateOrganismOnSpecimenCarrier,
            processLasFile: processLasFile
        };

        /**
         *
         * @param {string} specimenCarrierId
         * @param {string} sequenceNumber
         * @param {string} typeStatus
         * @param {string} identifier
         * @param {string} qualifier
         * @returns {Promise}
         * @memberOf OrganismService#
         */
        function changeNameOriginOfOrganismOnSpecimenCarrier(specimenCarrierId, sequenceNumber, typeStatus, identifier, qualifier) {
            return dataService.sendCommand('ChangeNameOriginOfOrganismOnSpecimenCarrier', {
                specimenCarrierId: specimenCarrierId,
                sequenceNumber: sequenceNumber,
                typeStatus: typeStatus,
                identifier: identifier,
                qualifier: qualifier
            });
        }

        /**
         *
         * @param {string} specimenCarrierId
         * @param {string} sequenceNumber
         * @param {string} scientificName
         * @param {string} validName
         * @param {string} otherSynonyms
         * @param {string} higherTaxa
         * @returns {Promise}
         * @memberOf OrganismService#
         */
        function changeTaxonOfOrganismOnSpecimenCarrier(specimenCarrierId, sequenceNumber, scientificName, validName, otherSynonyms, higherTaxa) {
            var scientificNames = otherSynonyms.split('\n');
            scientificNames.unshift(scientificName, validName);
            return gbifNameParser.parseName.apply(gbifNameParser, scientificNames).then(function (parsedNames) {
                var promise = dataService.sendCommand('ClearSynonymsOfOrganismOnSpecimenCarrier', {
                    specimenCarrierId: specimenCarrierId,
                    sequenceNumber: sequenceNumber
                }).then(function () {
                    return dataService.sendCommand('ProvideHigherTaxaForOrganismOnSpecimenCarrier', {
                        specimenCarrierId: specimenCarrierId,
                        sequenceNumber: sequenceNumber,
                        higherTaxa: higherTaxa
                    });
                });
                angular.forEach(parsedNames, function (parsedName, index) {
                    var command = 'ProvideSynonymForOrganismOnSpecimenCarrier';
                    if (index == 0) {
                        command = 'ChangeMentionedNameOfOrganismOnSpecimenCarrier';
                    } else if (index == 1) {
                        command = 'ChangeValidNameOfOrganismOnSpecimenCarrier';
                    }
                    if (index < 2 || parsedName) {
                        promise = promise.then(function () {
                            return dataService.sendCommand(command, {
                                specimenCarrierId: specimenCarrierId,
                                sequenceNumber: sequenceNumber,
                                genus: parsedName ? parsedName.genus : null,
                                subgenus: parsedName ? parsedName.subgenus : null,
                                specificEpithet: parsedName ? parsedName.specificEpithet : null,
                                infraspecificEpithet: parsedName ? parsedName.infraspecificEpithet : null,
                                authorship: parsedName ? parsedName.authorship : null,
                                year: parsedName ? parsedName.year : null,
                                parenthesized: parsedName ? (parsedName.parenthesized ? '1' : '') : null
                            });
                        });
                    }
                });
                return promise;
            });
        }

        /**
         *
         * @param {string} specimenCarrierId
         * @param {string} sequenceNumber
         * @returns {Promise}
         * @memberOf OrganismService#
         */
        function discardOrganismDescription(specimenCarrierId, sequenceNumber) {
            return dataService.sendCommand('DiscardOrganismDescription', {
                specimenCarrierId: specimenCarrierId,
                sequenceNumber: sequenceNumber
            });
        }

        /**
         * @param {string} specimenCarrierId
         * @param {string} sequenceNumber
         * @param {string} phaseOrStage
         * @param {string} sex
         * @param {string} remarks
         * @returns {Promise}
         * @memberOf OrganismService#
         */
        function describeOrganismOnSpecimenCarrier(specimenCarrierId, sequenceNumber, phaseOrStage, sex, remarks) {
            return dataService.sendCommand('DescribeOrganismOnSpecimenCarrier', {
                specimenCarrierId: specimenCarrierId,
                sequenceNumber: sequenceNumber,
                phaseOrStage: phaseOrStage,
                sex: sex,
                remarks: remarks
            });
        }

        /**
         * Loads the list of organisms from the server.
         * @returns {Promise} A promise whose value is an array of organism records with properties as defined in
         * /web/query/organism-list.sql
         * @memberOf OrganismService#
         */
        function loadOrganismList() {
            return dataService.queryForData('organism/').then(function (organismList) {
                var i, m;
                for (i = 0, m = organismList.length; i < m; i++) {
                    // mentioned/original name
                    organismList[i].scientificName = dataService.arrayOfNonEmptyValues(
                        organismList[i].genus,
                        dataService.concatIfNoneEmpty('(', organismList[i].subgenus, ')'),
                        organismList[i].specificEpithet,
                        organismList[i].infraspecificEpithet
                    ).join(' ');
                    //
                    organismList[i].validName = dataService.arrayOfNonEmptyValues(
                        organismList[i].validGenus,
                        dataService.concatIfNoneEmpty('(', organismList[i].validSubgenus, ')'),
                        organismList[i].validSpecificEpithet,
                        organismList[i].validInfraspecificEpithet
                    ).join(' ');
                    organismList[i].location = dataService.arrayOfNonEmptyValues(
                        organismList[i].place,
                        organismList[i].region,
                        organismList[i].province,
                        organismList[i].country
                    ).join(', ');
                    organismList[i].agent = dataService.arrayOfNonEmptyValues(
                        organismList[i].person,
                        dataService.concatIfNoneEmpty('(', organismList[i].organization, ')')
                    ).join(', ');
                    organismList[i].date = dataService.compactDateRange(
                        organismList[i].dateAfter,
                        organismList[i].dateBefore
                    );
                }
                return organismList;
            });
        }

        /**
         * Loads the specified organism from the server.
         * @param {string} uuid A lowercase 32-bit UUID, e. g. a1b2c3d4-e5f6-4a7b-8c9d-0e1f2a3b4c5d.
         * @returns {Promise} A promise whose value is an organism record with properties as defined in
         * /web/query/organism-record.sql
         * @memberOf OrganismService#
         */
        function loadOrganismRecord(uuid) {
            return dataService.queryForData('organism/' + uuid);
        }

        /**
         * @param {string} specimenCarrierId
         * @param {string} oldSequenceNumber
         * @param {string} newSequenceNumber
         * @param {string} phaseOrStage
         * @param {string} sex
         * @param {string} remarks
         * @returns {Promise}
         * @memberOf OrganismService#
         */
        function manipulateOrganismOnSpecimenCarrier(specimenCarrierId, oldSequenceNumber, newSequenceNumber, phaseOrStage, sex, remarks) {
            return dataService.sendCommand('ManipulateOrganismOnSpecimenCarrier', {
                specimenCarrierId: specimenCarrierId,
                oldSequenceNumber: oldSequenceNumber,
                newSequenceNumber: newSequenceNumber,
                phaseOrStage: phaseOrStage,
                sex: sex,
                remarks: remarks
            });
        }

        /**
         * @param {string} relativeUri
         * @returns {Promise}
         * @memberOf OrganismService#
         */
        function processLasFile(specimenCarrierId, sequenceNumber, relativeUri) {
            return dataService.sendCommand('ImportPhotomicrographFromLasFile', {
                specimenCarrierId: specimenCarrierId,
                sequenceNumber: sequenceNumber,
                relativeUri: relativeUri
            });
        }
    }
})();
