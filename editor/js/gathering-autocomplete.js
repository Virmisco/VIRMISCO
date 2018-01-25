(function () {
    addInputEventListenerToDatalistConsumers('034d396f-b8ec-4d4c-a332-e58f74642ced', gatheringAutocomplete);

    function gatheringAutocomplete() {
        var optionElement = findValueOfInputElementWithinItsDatalist(this),
            idElement = this.form.elements.gathering_id,
            afterElement = this.form.elements.after,
            beforeElement = this.form.elements.before,
            personElement = this.form.elements.person,
            organizationElement = this.form.elements.organization,
            placeElement = this.form.elements.place,
            regionElement = this.form.elements.region,
            provinceElement = this.form.elements.province,
            countryElement = this.form.elements.country,
            remarksElement = this.form.elements.remarks,
            carrierNumberElement = this.form.elements.carrier_number;
        if (carrierNumberElement.hasAttribute('list')) {
            carrierNumberElement.value = '';
            carrierNumberElement.removeAttribute('list');
            carrierAutocomplete.call(carrierNumberElement);
        }
        if (optionElement) {
            this.value = optionElement.getAttribute('data-journal-number');
            idElement.value = optionElement.value;
            afterElement.value = optionElement.getAttribute('data-after');
            afterElement.disabled = true;
            beforeElement.value = optionElement.getAttribute('data-before');
            beforeElement.disabled = true;
            personElement.value = optionElement.getAttribute('data-person');
            personElement.disabled = true;
            organizationElement.value = optionElement.getAttribute('data-organization');
            organizationElement.disabled = true;
            placeElement.value = optionElement.getAttribute('data-place');
            placeElement.disabled = true;
            regionElement.value = optionElement.getAttribute('data-region');
            regionElement.disabled = true;
            provinceElement.value = optionElement.getAttribute('data-province');
            provinceElement.disabled = true;
            countryElement.value = optionElement.getAttribute('data-country');
            countryElement.disabled = true;
            remarksElement.value = optionElement.getAttribute('data-remarks');
            remarksElement.disabled = true;
            carrierNumberElement.oninput = carrierAutocomplete;
            carrierNumberElement.setAttribute('list', optionElement.value);
        }
        else {
            idElement.value = '';
            afterElement.value = '';
            afterElement.disabled = false;
            beforeElement.value = '';
            beforeElement.disabled = false;
            personElement.value = '';
            personElement.disabled = false;
            organizationElement.value = '';
            organizationElement.disabled = false;
            placeElement.value = '';
            placeElement.disabled = false;
            regionElement.value = '';
            regionElement.disabled = false;
            provinceElement.value = '';
            provinceElement.disabled = false;
            countryElement.value = '';
            countryElement.disabled = false;
            remarksElement.value = '';
            remarksElement.disabled = false;
        }
    }

    function carrierAutocomplete() {
        var optionElement = findValueOfInputElementWithinItsDatalist(this),
            idElement = this.form.elements.carrier_id,
            preparationTypeElement = this.form.elements.preparation_type,
            ownerElement = this.form.elements.owner,
            previousCollectionElement = this.form.elements.previous_collection,
            labelTranscriptElement = this.form.elements.label_transcript,
            sequenceNumberElement = this.form.elements.sequence_number;
        if (sequenceNumberElement.hasAttribute('list')) {
            sequenceNumberElement.value = '';
            sequenceNumberElement.removeAttribute('list');
            organismAutocomplete.call(sequenceNumberElement);
        }
        if (optionElement) {
            this.value = optionElement.getAttribute('data-carrier-number');
            idElement.value = optionElement.value;
            preparationTypeElement.value = optionElement.getAttribute('data-preparation-type');
            preparationTypeElement.disabled = true;
            ownerElement.value = optionElement.getAttribute('data-owner');
            ownerElement.disabled = true;
            previousCollectionElement.value = optionElement.getAttribute('data-previous-collection');
            previousCollectionElement.disabled = true;
            labelTranscriptElement.value = optionElement.getAttribute('data-label-transcript');
            labelTranscriptElement.disabled = true;
            sequenceNumberElement.oninput = organismAutocomplete;
            sequenceNumberElement.setAttribute('list', optionElement.value);
        }
        else {
            idElement.value = '';
            preparationTypeElement.value = '';
            preparationTypeElement.disabled = false;
            ownerElement.value = '';
            ownerElement.disabled = false;
            previousCollectionElement.value = '';
            previousCollectionElement.disabled = false;
            labelTranscriptElement.value = '';
            labelTranscriptElement.disabled = false;
        }
    }

    function organismAutocomplete() {
        var optionElement = findValueOfInputElementWithinItsDatalist(this),
            idElement = this.form.elements.organism_id,
            typeStatusElement = this.form.elements.type_status,
            scientificNameElement = this.form.elements.scientific_name,
            identifierElement = this.form.elements.identifier,
            qualifierElement = this.form.elements.qualifier,
            phaseOrStageElement = this.form.elements.phase_or_stage,
            sexElement = this.form.elements.sex,
            organismRemarksElement = this.form.elements.organism_remarks,
            actionButton = this.form.elements.action;
        if (optionElement) {
            this.value = optionElement.getAttribute('data-sequence-number');
            idElement.value = optionElement.value;
            typeStatusElement.value = optionElement.getAttribute('data-type-status');
            typeStatusElement.disabled = true;
            scientificNameElement.value = optionElement.getAttribute('data-scientific-name');
            scientificNameElement.disabled = true;
            identifierElement.value = optionElement.getAttribute('data-identifier');
            identifierElement.disabled = true;
            qualifierElement.value = optionElement.getAttribute('data-qualifier');
            qualifierElement.disabled = true;
            phaseOrStageElement.value = optionElement.getAttribute('data-phase-or-stage');
            phaseOrStageElement.disabled = true;
            sexElement.value = optionElement.getAttribute('data-sex');
            sexElement.disabled = true;
            organismRemarksElement.value = optionElement.getAttribute('data-remarks');
            organismRemarksElement.disabled = true;
            actionButton.disabled = true;
        }
        else {
            idElement.value = '';
            typeStatusElement.value = '';
            typeStatusElement.disabled = false;
            scientificNameElement.value = '';
            scientificNameElement.disabled = false;
            identifierElement.value = '';
            identifierElement.disabled = false;
            qualifierElement.value = '';
            qualifierElement.disabled = false;
            phaseOrStageElement.value = '';
            phaseOrStageElement.disabled = false;
            sexElement.value = '';
            sexElement.disabled = false;
            organismRemarksElement.value = '';
            organismRemarksElement.disabled = false;
            actionButton.disabled = false;
        }
    }
})();
