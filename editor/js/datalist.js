/**
 * Returns the option element that was chosen from the datalist element associated with the input element during auto-
 * completion or null if the current value of the input element was entered manually.
 *
 * @param {HTMLInputElement} inputElement The input element to test.
 * @returns {HTMLOptionElement|null} The option element providing the current value.
 */
function findValueOfInputElementWithinItsDatalist(inputElement) {
    var options = inputElement.list ? inputElement.list.options : [];
    for (var i = 0, n = options.length; i < n; i++) {
        if (options[i].value == inputElement.value) {
            return options[i];
        }
    }
    return null;
}
/**
 * Registers the given function as an event listener for the (on)input event on all input elements that are tied to the
 * datalist with the given ID by their list attribute.
 *
 * @param {string} datalistId The ID of the datalist element, which is used as the list attribute on the input elements.
 * @param {Function} listener The function to register as the listener for the (on)input event.
 */
function addInputEventListenerToDatalistConsumers(datalistId, listener) {
    var inputElements = document.getElementsByTagName('input');
    for (var i = 0, n = inputElements.length; i < n; i++) {
        if (inputElements[i].getAttribute('list') == datalistId) {
            inputElements[i].addEventListener('input', listener);
        }
    }
}
