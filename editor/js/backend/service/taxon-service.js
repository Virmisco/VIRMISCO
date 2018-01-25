(function () {
    angular
        .module('backend')
        .factory('taxonService', taxonService);

    taxonService.$inject = ['dataService', 'gbifNameParser'];
    /**
     * Data service for taxa.
     *
     * @param {DataService} dataService
     * @param {GbifNameParser} gbifNameParser
     * @class TaxonService
     */
    function taxonService(dataService, gbifNameParser) {
        return {
            loadTaxonList: loadTaxonList,
            loadTaxonRecord: loadTaxonRecord
        };
        /**
         * Loads the list of taxa from the server.
         * @returns {Promise} A promise whose value is a list of taxon records with properties as defined in
         * /web/query/scientific-name-list.sql
         * @memberOf TaxonService#
         */
        function loadTaxonList() {
            return dataService.queryForData('scientific-name/').then(function (taxonList) {
                var i, m;
                for (i = 0, m = taxonList.length; i < m; i++) {
                    taxonList[i].regnumGroup = dataService.arrayOfNonEmptyValues(
                        taxonList[i].regnum,
                        taxonList[i].subregnum
                    ).join(' ');
                    taxonList[i].phylumGroup = dataService.arrayOfNonEmptyValues(
                        taxonList[i].superphylum,
                        taxonList[i].phylum,
                        taxonList[i].subphylum
                    ).join(' ');
                    taxonList[i].classisGroup = dataService.arrayOfNonEmptyValues(
                        taxonList[i].superclassis,
                        taxonList[i].classis,
                        taxonList[i].subclassis
                    ).join(' ');
                    taxonList[i].ordoGroup = dataService.arrayOfNonEmptyValues(
                        taxonList[i].superordo,
                        taxonList[i].ordo,
                        taxonList[i].subordo
                    ).join(' ');
                    taxonList[i].familiaGroup = dataService.arrayOfNonEmptyValues(
                        taxonList[i].superfamilia,
                        taxonList[i].familia,
                        taxonList[i].subfamilia,
                        taxonList[i].tribus
                    ).join(' ');
                    taxonList[i].scientificName = dataService.arrayOfNonEmptyValues(
                        taxonList[i].genus,
                        dataService.concatIfNoneEmpty('(', taxonList[i].subgenus, ')'),
                        taxonList[i].specificEpithet,
                        taxonList[i].infraspecificEpithet
                    ).join(' ');
                    taxonList[i].fullScientificName = dataService.arrayOfNonEmptyValues(
                        taxonList[i].scientificName,
                        dataService.concatIfNoneEmpty(
                            taxonList[i].isParenthesized ? '(' : '#',
                            dataService.arrayOfNonEmptyValues(taxonList[i].authorship, taxonList[i].year).join(', '),
                            taxonList[i].isParenthesized ? ')' : '#'
                        ).replace(/#/g, '')
                    ).join(' ');
                }
                return taxonList;
            });
        }

        /**
         * Loads the specified taxon from the server.
         * @param {string} uuid A lowercase 32-bit UUID, e. g. a1b2c3d4-e5f6-4a7b-8c9d-0e1f2a3b4c5d.
         * @returns {Promise} A promise whose value is a taxon record with properties as defined in
         * /web/query/scientific-name-record.sql
         * @memberOf TaxonService#
         */
        function loadTaxonRecord(uuid) {
            return dataService.queryForData('scientific-name/' + uuid).then(function (scientificNameRecord) {
                scientificNameRecord.fullScientificName = dataService.arrayOfNonEmptyValues(
                    scientificNameRecord.genus,
                    dataService.concatIfNoneEmpty('(', scientificNameRecord.subgenus, ')'),
                    scientificNameRecord.specificEpithet,
                    scientificNameRecord.infraspecificEpithet,
                    dataService.concatIfNoneEmpty(
                        scientificNameRecord.isParenthesized ? '(' : '#',
                        dataService.arrayOfNonEmptyValues(scientificNameRecord.authorship, scientificNameRecord.year).join(', '),
                        scientificNameRecord.isParenthesized ? ')' : '#'
                    ).replace(/#/g, '')
                ).join(' ');
                scientificNameRecord.fullValidName = dataService.arrayOfNonEmptyValues(
                    scientificNameRecord.validGenus,
                    dataService.concatIfNoneEmpty('(', scientificNameRecord.validSubgenus, ')'),
                    scientificNameRecord.validSpecificEpithet,
                    scientificNameRecord.validInfraspecificEpithet,
                    dataService.concatIfNoneEmpty(
                        scientificNameRecord.validIsParenthesized ? '(' : '#',
                        dataService.arrayOfNonEmptyValues(scientificNameRecord.validAuthorship, scientificNameRecord.validYear).join(', '),
                        scientificNameRecord.validIsParenthesized ? ')' : '#'
                    ).replace(/#/g, '')
                ).join(' ');
                return scientificNameRecord;
            });
        }
    }
})();
/*
 id
 taxonId
 validNameId
 regnum
 subregnum
 superphylum
 phylum
 subphylum
 superclassis
 classis
 subclassis
 superordo
 ordo
 subordo
 superfamilia
 familia
 subfamilia
 tribus
 genus
 subgenus
 specificEpithet
 infraspecificEpithet
 authorship
 year
 isParenthesized
 (validGenus)
 (validSubgenus)
 (validSpecificEpithet)
 (validInfraspecificEpithet)
 (validAuthorship)
 (validYear)
 (validIsParenthesized)
 isValid
 regnumId
 regnumIndex
 subregnumIndex
 superphylumIndex
 phylumId
 phylumIndex
 subphylumIndex
 superclassisIndex
 classisId
 classisIndex
 subclassisIndex
 superordoIndex
 ordoId
 ordoIndex
 subordoIndex
 superfamiliaIndex
 familiaId
 familiaIndex
 subfamiliaIndex
 tribusIndex
 */