(function () {
    var selectElement = document.getElementsByName('type_status').item(0);
    var identifierElement = document.getElementsByName('identifier').item(0);
    var qualifierElement = document.getElementsByName('qualifier').item(0);
    selectElement.onchange = function () {
        if (this.selectedIndex === 0) {
            identifierElement.disabled = false;
            if (identifierElement.hasAttribute('data-prev-val')) {
                identifierElement.value = identifierElement.getAttribute('data-prev-val');
                identifierElement.removeAttribute('data-prev-val');
            }
            qualifierElement.disabled = false;
            if (qualifierElement.hasAttribute('data-prev-val')) {
                qualifierElement.value = qualifierElement.getAttribute('data-prev-val');
                qualifierElement.removeAttribute('data-prev-val');
            }
        }
        else {
            identifierElement.setAttribute('data-prev-val', identifierElement.value);
            identifierElement.value = '';
            identifierElement.disabled = true;
            qualifierElement.setAttribute('data-prev-val', qualifierElement.value);
            qualifierElement.value = '';
            qualifierElement.disabled = true;
        }
    };
    selectElement.onchange();
})();
