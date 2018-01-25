(function () {
    var resultItem = null;
    var selectedItem = null;
    var trash = document.getElementById('trash');
    var resultList = document.getElementById('resultList');
    var preview = document.getElementById('preview');
    var metadata = document.getElementById('metadata');
    var higherTaxa = document.getElementById('higherTaxa');
    var scientificName = document.getElementById('scientificName');
    var validName = document.getElementById('validName');
    var header = document.getElementsByTagName('h2')[0];
    var choice = document.getElementsByTagName('ul')[0];
    var showButton = document.getElementById('show-in-viewer');
    resultList.onchange = function (event) {
        var option = resultList.options[resultList.selectedIndex];
        var data = eval('(%)'.replace('%', option.getAttribute('data-match')));
        var details = option.getAttribute('data-detail-of');
        var name;
        preview.src = option.getAttribute('data-preview');
        preview.alt = option.label;
        preview.setAttribute('data-id', option.value);
        while (higherTaxa.firstChild) higherTaxa.removeChild(higherTaxa.firstChild);
        while (scientificName.firstChild) scientificName.removeChild(scientificName.firstChild);
        while (validName.firstChild) validName.removeChild(validName.firstChild);
        while (header.firstChild) header.removeChild(header.firstChild);
        while (metadata.firstChild) metadata.removeChild(metadata.firstChild);
        while (preview.nextSibling) preview.parentNode.removeChild(preview.nextSibling);
        header.appendChild(document.createTextNode(option.label));
        higherTaxa.appendChild(document.createTextNode(option.getAttribute('data-higher-taxa')));
        scientificName.appendChild(document.createTextNode(option.getAttribute('data-scientific-name')));
        validName.appendChild(document.createTextNode(option.getAttribute('data-valid-name')));
        for (name in data) {
            if (data.hasOwnProperty(name)) {
                metadata.appendChild(document.createElement('div'));
                metadata.lastChild.className = 'form-group';
                metadata.lastChild.appendChild(document.createElement('label'));
                metadata.lastChild.lastChild.className = 'control-label col-sm-5';
                metadata.lastChild.lastChild.appendChild(document.createTextNode(name + ':'));
                metadata.lastChild.appendChild(document.createElement('div'));
                metadata.lastChild.lastChild.className = 'form-control-static col-sm-7';
                metadata.lastChild.lastChild.innerHTML = data[name];
            }
        }
        if (details) {
            var imgElement = document.createElement('img');
            var marker = document.createElement('div');
            var x = 0;
            var y = 0;
            details = details.split(':'); // UUID, x, y
            x = details[1];
            y = details[2];
            preview.parentNode.appendChild(imgElement);
            if (x > 0 || y > 0) {
                preview.parentNode.appendChild(marker);
                imgElement.onload = function () {
                    marker.style.marginLeft = (x / imgElement.naturalWidth * imgElement.offsetWidth) + 'px';
                    marker.style.marginTop = (y / imgElement.naturalHeight * imgElement.offsetHeight) + 'px';
                    marker.id = 'marker';
                };
            }
            imgElement.src = 'long-shot.php?id=' + details[0];
            imgElement.onload();
        }
        var addBtn = document.getElementById('addBtn');
        addBtn.onclick = function() {
        	resultItem = preview;
        	choice.ondrop();
        }
        
    };
    preview.draggable = true;
    preview.ondragstart = function (e) {
        var event = e || window.event;
//                event.dataTransfer.setData('text/plain', preview.alt);
        resultItem = preview;
    };
    
    

    
    choice.ondragenter = function (e) {
        var event = e || window.event;
        if (resultItem || selectedItem) {
            event.dataTransfer.effectAllowed = "move";
            event.preventDefault();
        }
    };
    choice.ondragover = function (e) {
        var event = e || window.event;
        if (resultItem || selectedItem) {
            event.dataTransfer.effectAllowed = "move";
            event.preventDefault();
        }
    };
    choice.ondrop = function (e) {
        var event = e || window.event;
        var refPos = null;
        var handle;
        var item;
        var img;
        if (resultItem) {
            item = document.createElement('li');
            item.setAttribute('data-id', resultItem.getAttribute('data-id'));
            choice.appendChild(item);
            img = item.appendChild(document.createElement('img'));
            item.appendChild(document.createElement('span')).appendChild(document.createTextNode(resultItem.alt));
            item.style.opacity = 0;
            img.src = resultItem.src;
            img.alt = resultItem.alt;
            handle = window.setInterval(function () {
                item.style.opacity -= -.05;
                if (item.style.opacity > .95) {
                    item.style.opacity = 1;
                    window.clearInterval(handle);
                }
            }, 20);
            item.draggable = true;
            item.ondragstart = function (e) {
                var event = e || window.event;
//                        event.dataTransfer.setData('text/plain', item.textContent);
                selectedItem = item;
            };
        }
        item = item || selectedItem;
        if (item) {
            // shift the position so that the dragged item is inserted between the two others
            if(event) refPos = document.elementFromPoint(event.clientX + item.offsetWidth * .5, event.clientY);
            // if we hit the gap and thus the parent ul instead of an li element, shift further
            if (refPos && refPos.nodeName.toLowerCase() == 'ul' && event) {
                refPos = document.elementFromPoint(event.clientX + item.offsetWidth * .75, event.clientY);
            }
            // move up until we are at an li parent or have no parent at all
            // the latter includes pointing to an open space in the ul,
            // thus making refPos null for appending the item at the end
            while (refPos && refPos.nodeName.toLowerCase() != 'li') {
                refPos = refPos.parentNode;
            }
            choice.insertBefore(item, refPos);
            selectedItem = null;
            resultItem = null;
        }
        if(event) event.preventDefault();
    };
    trash.ondragenter = function (e) {
        var event = e || window.event;
        if (selectedItem) {
            event.dataTransfer.effectAllowed = "move";
            event.preventDefault();
        }
    };
    trash.ondragover = function (e) {
        var event = e || window.event;
        if (selectedItem) {
            event.dataTransfer.effectAllowed = "move";
            event.preventDefault();
        }
    };
    trash.ondrop = function (e) {
        var event = e || window.event;
        var handle;
        if (selectedItem) {
            selectedItem.style.opacity = 1;
            handle = window.setInterval(function () {
                selectedItem.style.opacity -= .05;
                if (selectedItem.style.opacity < .05) {
                    selectedItem.parentNode.removeChild(selectedItem);
                    selectedItem = null;
                    window.clearInterval(handle);
                }
            }, 20);
            event.preventDefault();
        }
    };
    showButton.onclick = function (e) {
    	console.debug(1);
        var event = e || window.event;
        var items = choice.getElementsByTagName('li');
        var showForm = showButton.parentNode;
        var input;
        while (showForm.firstChild.nodeName.toLowerCase() == 'input') {
            showForm.removeChild(showForm.firstChild);
        }
        for (var i = 0, m = items.length; i < m; i++) {
            input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'choice[]';
            input.value = items[i].getAttribute('data-id');
            showForm.insertBefore(input, showForm.firstChild);
        }
    };
    
    showButton.ontap = function (e) {
        alert(1);
    };
    resultList.selectedIndex = 0;
    resultList.onchange();
})();
