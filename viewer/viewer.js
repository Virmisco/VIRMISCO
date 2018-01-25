(function () {
    // controls
    var playbackReverseControl = document.getElementById('playbackReverse');
    var playbackStepBackControl = document.getElementById('playbackStepBack');
    var playbackPauseControl = document.getElementById('playbackPause');
    var playbackStepAheadControl = document.getElementById('playbackStepAhead');
    var playbackForwardControl = document.getElementById('playbackForward');
    var playbackLoopControl = document.getElementById('playbackLoop');
    var brightnessControl = document.getElementById('brightness');
    var contrastControl = document.getElementById('contrast');
    var focusControl = document.getElementById('focus');
    var loopBeginControl = document.getElementById('loopBegin');
    var loopEndControl = document.getElementById('loopEnd');
    var measurementControl = document.getElementById('measurement');
    var playbackRateControl = document.getElementById('playbackRate');
    var rotationControl = document.getElementById('rotation');
    var zoomControl = document.getElementById('zoom');
    var resetControl = document.getElementById('reset');

    // important elements
    var loopRangeIndicator = document.getElementById('range');
    var scaleBar = document.getElementById('scaleBar');
    var viewportBoundingBox = document.getElementById('viewportBoundingBox');
    var turnTable = viewportBoundingBox.parentNode;
    var videoPlayer = document.getElementById('video');
    var viewport = document.getElementById('viewport');
    var regionSnapshotLink = document.getElementById('snapshot-region');
    var frameSnapshotLink = document.getElementById('snapshot-frame');
    var imageCanvas = document.getElementById('image');
    var overlayCanvas = document.getElementById('overlay');
    var deltaXOutput = document.getElementById('deltaX');
    var deltaYOutput = document.getElementById('deltaY');
    var deltaZOutput = document.getElementById('deltaZ');
    var deltaProjectedOutput = document.getElementById('deltaProjected');
    var deltaTrueOutput = document.getElementById('deltaTrue');
    var activeFocalPlaneMetadata = null;

    // drawing contexts
    var imageContext = imageCanvas.getContext('2d');
    var overlayContext = overlayCanvas.getContext('2d');

    // handles and other transient vars
    var redrawTimeout = null;
    var autoplayInterval = null;
    var imageData = null;
    var xOffset = -viewport.offsetWidth / 2;
    var yOffset = -viewport.offsetHeight / 2;
    var playrate = 1;
    var startPosition = null;
    var wheelPlaybackStopTimeout = null;
    var tapeMeasureBegin = null;
    var tapeMeasureEnd = null;

    // helpers

    function createPopup (sourceUri, width, height, captionText) {
        var rootPanel = document.createElement('div');
        var dimPanel = document.createElement('div');
        var container = document.createElement('div');
        var captionBlock = document.createElement('p');
        var image = document.createElement('img');
        document.body.appendChild(rootPanel);
        rootPanel.className = 'popup-root';
        dimPanel.className = 'dim';
        container.className = 'container';
        captionBlock.className = 'caption';
        rootPanel.appendChild(dimPanel);
        rootPanel.appendChild(container);
        container.appendChild(image);
        container.appendChild(captionBlock);
        container.style.marginTop = (height / -2) + 'px';
        container.style.marginLeft = (width / -2) + 'px';
        captionBlock.appendChild(document.createTextNode(captionText));
        image.width = width;
        image.height = height;
        image.src = sourceUri;
        return {
            dispose: function() { document.body.removeChild(rootPanel); },
            root: rootPanel,
            img: image
        };
    }

    function createSnapshot (wholeFrame) {
        var snapshotCanvas = imageCanvas;
        var snapshotContext;
        var factor = Math.pow(10, zoomControl.value / 100);
        var scale = 1;
        //linkElement.target = '_blank';
        if (!wholeFrame) {
            snapshotCanvas = document.createElement("canvas");
            snapshotCanvas.style.width = viewport.offsetWidth + 'px';
            snapshotCanvas.style.height = viewport.offsetHeight + 'px';
            snapshotCanvas.width = viewport.offsetWidth;
            snapshotCanvas.height = "auto"; //viewport.offsetHeight;
            viewport.appendChild(snapshotCanvas);
            snapshotContext = snapshotCanvas.getContext('2d');
            scale = snapshotCanvas.width / imageCanvas.width;
            snapshotContext.translate(imageCanvas.offsetLeft, imageCanvas.offsetTop);
            snapshotContext.scale(scale, scale);
            snapshotContext.translate(imageCanvas.width / 2 * factor, imageCanvas.height / 2 * factor);
            snapshotContext.rotate(rotationControl.value / 180 * Math.PI);
            snapshotContext.translate(-imageCanvas.width / 2 * factor, -imageCanvas.height / 2 * factor);
            snapshotContext.drawImage(
                imageCanvas,
                0,
                0,
                imageCanvas.width,
                imageCanvas.height,
                0,
                0,
                imageCanvas.width * factor,
                imageCanvas.height * factor
            );
        }
        var popup = createPopup(
            snapshotCanvas.toDataURL('image/jpeg', .95),
            viewport.offsetWidth,
            viewport.offsetHeight,
            'Right-click on the image and choose to save it.'
        );
        if (!wholeFrame) {
            viewport.removeChild(snapshotCanvas);
        }
        popup.root.oncontextmenu = popup.root.onclick = function () {
            var opacity = 1.0;
            var interval;
            function fadeOut () {
                opacity -= .04;
                popup.root.style.opacity = opacity;
                if (opacity < .01) {
                    popup.dispose();
                    window.clearInterval(interval);
                }
            }
            popup.root.onclick = popup.root.oncontextmenu = null;
            interval = window.setInterval(fadeOut, 40);
        };
    }

    function drawTapeMeasure (begin, end, connect) {
        var viewportBegin, viewportEnd, label, textWidth, dx, dy, dz, dp, dt;
        var padding = 2;
        var textHeight = 12;
        if (begin) {
            viewportBegin = begin && imageToViewport(begin);
            overlayContext.clearRect(0, 0, overlayCanvas.width, overlayCanvas.height);
            overlayContext.strokeStyle = '#00f';
            overlayContext.beginPath();
            overlayContext.arc(viewportBegin.x, viewportBegin.y, 5, 0, 2 * Math.PI);
            overlayContext.stroke();
            if (end) {
                viewportEnd = end && imageToViewport(end);
                overlayContext.strokeStyle = '#f00';
                overlayContext.beginPath();
                overlayContext.arc(viewportEnd.x, viewportEnd.y, 5, 0, 2 * Math.PI);
                overlayContext.stroke();
                if (connect) {
                    dx = (end.x - begin.x) * reproductionScale;
                    dy = (end.y - begin.y) * reproductionScale;
                    dz = (end.z - begin.z) * focalPlaneDistance;
                    dp = Math.sqrt(Math.pow(dx, 2) + Math.pow(dy, 2));
                    dt = Math.sqrt(Math.pow(dp, 2) + Math.pow(dz, 2));
                    label = (Math.round(dt * 1e7) / 10) + 'µm';
                    deltaXOutput.firstChild.nodeValue = (Math.round(dx * 1e7) / 10) + 'µm';
                    deltaYOutput.firstChild.nodeValue = (Math.round(dy * 1e7) / 10) + 'µm';
                    deltaZOutput.firstChild.nodeValue = (Math.round(dz * 1e7) / 10) + 'µm';
                    deltaProjectedOutput.firstChild.nodeValue = (Math.round(dp * 1e7) / 10) + 'µm';
                    deltaTrueOutput.firstChild.nodeValue = label;
                    overlayContext.font = '12px sans-serif';
                    textWidth = overlayContext.measureText(label).width;
                    overlayContext.strokeStyle = '#000';
                    overlayContext.lineWidth = 2;
                    overlayContext.beginPath();
                    overlayContext.moveTo(viewportBegin.x, viewportBegin.y);
                    overlayContext.lineTo(viewportEnd.x, viewportEnd.y);
                    overlayContext.stroke();
                    overlayContext.strokeStyle = '#fff';
                    overlayContext.lineWidth = 1;
                    overlayContext.beginPath();
                    overlayContext.moveTo(viewportBegin.x, viewportBegin.y);
                    overlayContext.lineTo(viewportEnd.x, viewportEnd.y);
                    overlayContext.stroke();
                    overlayContext.fillStyle = '#222';
                    overlayContext.fillRect(
                        (viewportBegin.x + viewportEnd.x - textWidth) / 2 - padding,
                        (viewportBegin.y + viewportEnd.y - textHeight) / 2 - padding,
                        textWidth + 2 * padding,
                        textHeight + 2 * padding
                    );
                    overlayContext.fillStyle = '#eee';
                    overlayContext.font = '12px sans-serif';
                    overlayContext.fillText(
                        label,
                        (viewportBegin.x + viewportEnd.x - textWidth) / 2,
                        (viewportBegin.y + viewportEnd.y + textHeight / 2) / 2
                    );
                    overlayContext.fillStyle = '#000';
                }
            }
        }
    }

    function imageToViewport (point) {
        var angleRad = rotationControl.value / 180 * Math.PI;
        var zoom = Math.pow(10, zoomControl.value / 100);
        var x = point.x;
        var y = point.y;
        var p;
        //
        x -= imageCanvas.width / 2;
        y -= imageCanvas.height / 2;
        p = {
            x: Math.cos(-angleRad) * x + Math.sin(-angleRad) * y,
            y: Math.cos(-angleRad) * y - Math.sin(-angleRad) * x
        };
        x = p.x + imageCanvas.width / 2;
        y = p.y + imageCanvas.height / 2;
        //
        x /= imageCanvas.width / viewport.offsetWidth;
        y /= imageCanvas.height / viewport.offsetHeight;
        x *= zoom;
        y *= zoom;
        x += imageCanvas.offsetLeft;
        y += imageCanvas.offsetTop;
        return {x: x, y: y};
    }

    /**
     * Returns the nearest value equal to or below the given amount which can be represented as 1, 2 or 5 times any power of
     * ten, just like typical monetary values of coins and banknotes.
     *
     * @param {Number} amount The original amount.
     * @returns {number}
     */
    function reduceTo125(amount) {
        var amountString = '' + amount;
        var firstChar = amountString.substr(0, 1);
        if (firstChar >= 5) firstChar = 5;
        else if (firstChar >= 2) firstChar = 2;
        else if (firstChar >= 1) firstChar = 1;
        else return reduceTo125(amount * 10) / 10;
        return amountString.replace(/[.,].*$/, '').replace(/./g, '0').replace(/^./, firstChar) - 0;
    }

    function setCanvasPositionInViewport(x, y) {
        var factor = Math.pow(10, zoomControl.value / 100);
        imageCanvas.style.left = x + "px";
        imageCanvas.style.top = y + "px";
        x = -x / factor * videoPlayer.offsetWidth / viewport.offsetWidth;
        y = -y / factor * videoPlayer.offsetHeight / viewport.offsetHeight;
        viewportBoundingBox.style.marginLeft = x + "px";
        viewportBoundingBox.style.marginTop = y + "px";
    }

    function showMetadata (index) {
        var section = document.getElementById('fpi-' + index);
        if (activeFocalPlaneMetadata) {
            activeFocalPlaneMetadata.style.display = '';
        }
        if (section) {
            section.style.display = 'block';
            activeFocalPlaneMetadata = section;
        }
    }

    function updateScaleBar(scaleBarElement) {
    	if(reproductionScale == 0) {
    		scaleBarElement.style.visibility = "hidden";
    		return;
    	}
    	scaleBarElement.style.visibility = "visible";
        var zoom = imageCanvas.offsetWidth / width;
        var textNode = scaleBarElement.getElementsByTagName('span')[0].firstChild;
        var resolutionInMicronsPerPixel = reproductionScale * 1e6;
        var maxLength = viewport.offsetWidth / 2;
        // max display width of scale * µm per pixel * zoom
        var fullWidthValue = maxLength * resolutionInMicronsPerPixel / zoom;
        var displayValue = reduceTo125(fullWidthValue);
        var displayWidth = displayValue * zoom / resolutionInMicronsPerPixel - 1; // reduce by 1: fencepost error
        textNode.nodeValue = displayValue + 'µm';
        scaleBarElement.style.width = displayWidth + 'px';
    }

    function viewportToImage (point) {
        var angleRad = rotationControl.value / 180 * Math.PI;
        var zoom = Math.pow(10, zoomControl.value / 100);
        var x = point.x;
        var y = point.y;
        var p;
        x -= imageCanvas.offsetLeft;
        y -= imageCanvas.offsetTop;
        x /= zoom;
        y /= zoom;
        x *= imageCanvas.width / viewport.offsetWidth;
        y *= imageCanvas.height / viewport.offsetHeight;
        //
        x -= imageCanvas.width / 2;
        y -= imageCanvas.height / 2;
        p = {
            x: Math.cos(angleRad) * x + Math.sin(angleRad) * y,
            y: Math.cos(angleRad) * y - Math.sin(angleRad) * x
        };
        x = p.x + imageCanvas.width / 2;
        y = p.y + imageCanvas.height / 2;
        //
        return {x: x, y: y};
    }

    // handlers

    function autoplayFrameChanged() {
        var frameNo = Math.round(videoPlayer.currentTime * frameRate);
        var revFrameNo = 2 * frameCountSingle - 1 - frameNo;
        var left = loopBeginControl.value - 0;
        var right = loopEndControl.value - 0;
        imageContext.drawImage(videoPlayer, 0, 0, width, height);
        if (frameNo < frameCountSingle) {
            // |->-|
            focusControl.value = frameNo;
        }
        else {
            // |-<-|
            focusControl.value = revFrameNo;
        }
        focusControl.parentNode.getElementsByTagName('output')[0].firstChild.nodeValue = focusControl.value;
        showMetadata(focusControl.value);
        if (playbackLoopControl.checked) {
            if (frameNo < left || revFrameNo <= left) {
                // |-x-|---|---|
                videoPlayer.currentTime = left / frameRate;
                focusControl.value = left;
            }
            else if (frameNo >= right && revFrameNo > right) {
                // |---|---|-x-|
                videoPlayer.currentTime = (2 * frameCountSingle - 1 - right) / frameRate;
                focusControl.value = right;
            }
        }
        else if (playbackForwardControl.checked && frameNo >= frameCountSingle - 1
            || playbackReverseControl.checked && revFrameNo == 0
            || videoPlayer.ended
        ) {
            focusControl.value = playbackForwardControl.checked ? frameCountSingle - 1 : 0;
            focusControl.parentNode.getElementsByTagName('output')[0].firstChild.nodeValue = focusControl.value;
            showMetadata(focusControl.value);
            playbackPauseControl.checked = true;
            playbackPauseRequested();
        }
    }

    function brightnessContrastChanged() {
        if (!playbackLoopControl.checked && !playbackForwardControl.checked && !playbackReverseControl.checked) {
            if (redrawTimeout) {
                window.clearTimeout(redrawTimeout);
                //noinspection JSUnusedAssignment
                redrawTimeout = null;
            }
            imageContext.drawImage(videoPlayer, 0, 0, width, height);
            redrawTimeout = window.setTimeout(manualFrameChanged, 1);
        }
    }

    function focusChanged() {
        if (!playbackLoopControl.checked && !playbackForwardControl.checked && !playbackReverseControl.checked) {
            var frameNo = Math.round(videoPlayer.currentTime * frameRate);
            var targetFrame;
            frameNo = Math.min(frameNo, 2 * frameCountSingle - 1 - frameNo);
            // instead of stepping (seeking) backwards, forward the reversed frames in the 2nd half
            targetFrame = frameNo > focusControl.value
                ? 2 * frameCountSingle - 1 - focusControl.value
                : focusControl.value;
            videoPlayer.currentTime = targetFrame / frameRate;
            focusControl.parentNode.getElementsByTagName('output')[0].firstChild.nodeValue = focusControl.value;
            showMetadata(focusControl.value);
        }
    }

    function frameSnapshotRequested () {
        createSnapshot(true);
    }

    function loopBeginChanged() {
        loopBeginControl.value = Math.min(loopBeginControl.value, loopEndControl.value - 1);
        loopRangeIndicator.style.left = (loopBeginControl.offsetWidth - 8)
        / (loopBeginControl.max - loopBeginControl.min)
        * (loopBeginControl.value - loopBeginControl.min)
        + 'px';
        loopRangeIndicator.style.width = (loopEndControl.offsetWidth - 8)
        / (loopEndControl.max - loopEndControl.min)
        * (loopEndControl.value - loopBeginControl.value)
        + 'px';
    }

    function loopEndChanged() {
        loopEndControl.value = Math.max(loopBeginControl.value - -1, loopEndControl.value);
        loopRangeIndicator.style.width = (loopEndControl.offsetWidth - 8)
        / (loopEndControl.max - loopEndControl.min)
        * (loopEndControl.value - loopBeginControl.value)
        + 'px';
    }

    function manualFrameChanged() {
        if (!imageData) {
            var brightVal = brightnessControl.value / 50 * 128;
            var contVal = Math.pow(10, contrastControl.value / 50);
            if (brightnessControl.value != 0 || contrastControl.value != 0) {
                imageData = imageContext.getImageData(0, 0, width, height);
                var data = imageData.data;
                var len = data.length;
                for (var i = 0; i < len; i += 4) {
                    data[i] = Math.max(0, Math.min(255, contVal * (data[i] - 128) + 128 + brightVal));
                    data[i + 1] = Math.max(0, Math.min(255, contVal * (data[i + 1] - 128) + 128 + brightVal));
                    data[i + 2] = Math.max(0, Math.min(255, contVal * (data[i + 2] - 128) + 128 + brightVal));
                }
                imageContext.putImageData(imageData, 0, 0);
            }
            brightnessControl.parentNode.getElementsByTagName('output')[0].firstChild.nodeValue
                = Math.round(brightVal * 1000) / 1000;
            contrastControl.parentNode.getElementsByTagName('output')[0].firstChild.nodeValue
                = (Math.round(contVal * 1000) / 1000) + 'x';
            imageData = null;
            redrawTimeout = null;
        }
    }

    function measurementToggled() {
        if (measurementControl.checked) {
            if (tapeMeasureEnd) {
                tapeMeasureBegin = null;
                tapeMeasureEnd = null;
                overlayContext.clearRect(0, 0, overlayCanvas.width, overlayCanvas.height);
                deltaXOutput.firstChild.nodeValue = '';
                deltaYOutput.firstChild.nodeValue = '';
                deltaZOutput.firstChild.nodeValue = '';
                deltaProjectedOutput.firstChild.nodeValue = '';
                deltaTrueOutput.firstChild.nodeValue = '';
                window.setTimeout(function () {
                    measurementControl.parentNode.getElementsByTagName('label')[0].firstChild.nodeValue = 'set begin';
                    measurementControl.checked = false;
                }, 100);
            }
            else {
                overlayCanvas.className = 'tape';
            }
        }
        else {
            overlayCanvas.className = '';
        }
    }

    function panningBegin(e) {
        var event = e || window.event;
        if (measurementControl.checked && !tapeMeasureEnd) {
            if (tapeMeasureBegin) {
                tapeMeasureEnd = viewportToImage({
                    x: event.clientX - viewport.offsetLeft,
                    y: event.clientY - viewport.offsetTop
                });
                tapeMeasureEnd.z = focusControl.value;
                drawTapeMeasure(tapeMeasureBegin, tapeMeasureEnd, true);
                measurementControl.checked = false;
                measurementControl.parentNode.getElementsByTagName('label')[0].firstChild.nodeValue = 'clear';
                overlayCanvas.className = '';
            }
            else {
                tapeMeasureBegin = viewportToImage({
                    x: event.clientX - viewport.offsetLeft,
                    y: event.clientY - viewport.offsetTop
                });
                tapeMeasureBegin.z = focusControl.value;
                drawTapeMeasure(tapeMeasureBegin);
                measurementControl.parentNode.getElementsByTagName('label')[0].firstChild.nodeValue = 'set end';
            }
        }
        else {
            startPosition = {
                mouse: {x: event.clientX, y: event.clientY},
                canvas: {x: imageCanvas.offsetLeft, y: imageCanvas.offsetTop}
            };
        }
    }

    function panningContinue(e) {
        var event = e || window.event;
        var dx, dy, x, y;
        if (measurementControl.checked && !tapeMeasureEnd) {
            x = event.clientX - viewport.offsetLeft;
            y = event.clientY - viewport.offsetTop;
            if (tapeMeasureBegin) {
                drawTapeMeasure(tapeMeasureBegin, viewportToImage({x: x, y: y}));
            }
            else {
                drawTapeMeasure(viewportToImage({x: x, y: y}));
            }
        }
        else if (startPosition) {
            dx = event.clientX - startPosition.mouse.x;
            dy = event.clientY - startPosition.mouse.y;
            x = startPosition.canvas.x + dx;
            y = startPosition.canvas.y + dy;
            setCanvasPositionInViewport(x, y);
            drawTapeMeasure(tapeMeasureBegin, tapeMeasureEnd, true);
        }
    }

    function panningEnd() {
        var factor = Math.pow(10, zoomControl.value / 100);
        var x = imageCanvas.offsetLeft;
        var y = imageCanvas.offsetTop;
        if (startPosition) {
            startPosition = null;
            setCanvasPositionInViewport(x, y);
            xOffset = (x - viewport.offsetWidth / 2) / factor;
            yOffset = (y - viewport.offsetHeight / 2) / factor;
            drawTapeMeasure(tapeMeasureBegin, tapeMeasureEnd, true);
        }
    }

    function playbackForwardRequested() {
        playbackPauseRequested();
        videoPlayer.removeEventListener('timeupdate', brightnessContrastChanged);
        videoPlayer.currentTime = focusControl.value / frameRate;
        focusControl.disabled = true;
        autoplayInterval = window.setInterval(autoplayFrameChanged, 500 / frameRate / playrate);
        videoPlayer.defaultPlaybackRate = playrate;
        videoPlayer.playbackRate = playrate;
        videoPlayer.play();
    }

    function playbackLoopRequested() {
        playbackPauseRequested();
        videoPlayer.removeEventListener('timeupdate', brightnessContrastChanged);
        focusControl.disabled = true;
        autoplayInterval = window.setInterval(autoplayFrameChanged, 500 / frameRate / playrate);
        videoPlayer.loop = true;
        videoPlayer.defaultPlaybackRate = playrate;
        videoPlayer.playbackRate = playrate;
        videoPlayer.play();
    }

    function playbackRateChanged() {
        playrate = playbackRateControl.value > 6
            ? Math.pow(2, playbackRateControl.value / 6 - 1)
            : Math.pow(2, playbackRateControl.value / 2 - 3);
        videoPlayer.defaultPlaybackRate = playrate;
        videoPlayer.playbackRate = playrate;
        playbackRateControl.parentNode.getElementsByTagName('output')[0].firstChild.nodeValue
            = (Math.round(playrate * 1000) / 1000) + 'x';
    }

    function playbackPauseRequested() {
        if (autoplayInterval) {
            videoPlayer.loop = false;
            videoPlayer.pause();
            window.clearInterval(autoplayInterval);
            autoplayInterval = null;
            focusControl.disabled = false;
            videoPlayer.addEventListener('timeupdate', brightnessContrastChanged);
        }
    }

    function playbackReverseRequested() {
        playbackPauseRequested();
        videoPlayer.removeEventListener('timeupdate', brightnessContrastChanged);
        videoPlayer.currentTime = (2 * frameCountSingle - 1 - focusControl.value) / frameRate;
        focusControl.disabled = true;
        autoplayInterval = window.setInterval(autoplayFrameChanged, 500 / frameRate / playrate);
        videoPlayer.defaultPlaybackRate = playrate;
        videoPlayer.playbackRate = playrate;
        videoPlayer.play();
    }

    function playbackStepAheadRequested() {
        if (autoplayInterval) {
            videoPlayer.pause();
            window.clearInterval(autoplayInterval);
            autoplayInterval = null;
            focusControl.disabled = false;
            videoPlayer.addEventListener('timeupdate', brightnessContrastChanged);
        }
        focusControl.value -= -1;
        focusChanged();
        window.setTimeout(function () { playbackPauseControl.checked = true; }, 100);
    }

    function playbackStepBackRequested() {
        if (autoplayInterval) {
            videoPlayer.pause();
            window.clearInterval(autoplayInterval);
            autoplayInterval = null;
            focusControl.disabled = false;
            videoPlayer.addEventListener('timeupdate', brightnessContrastChanged);
        }
        focusControl.value--;
        focusChanged();
        window.setTimeout(function () { playbackPauseControl.checked = true; }, 100);
    }

    function regionSnapshotRequested () {
        createSnapshot(false);
    }

    function rotationChanged() {
        imageCanvas.style.transform = 'rotate(#deg)'.replace('#', rotationControl.value);
        turnTable.style.transform = 'rotate(#deg)'.replace('#', -rotationControl.value);
        rotationControl.parentNode.getElementsByTagName('output')[0].firstChild.nodeValue = rotationControl.value + '°';
        drawTapeMeasure(tapeMeasureBegin, tapeMeasureEnd, true);
    }

    function zoomChanged() {
        var factor = Math.pow(10, zoomControl.value / 100);
        var x = viewport.offsetWidth * (1 - factor) / 2;
        var y = viewport.offsetHeight * (1 - factor) / 2;
        imageCanvas.style.width = (viewport.offsetWidth * factor) + "px";
        imageCanvas.style.height = "auto"; //(viewport.offsetHeight * factor) + "px";
        viewportBoundingBox.style.width = (videoPlayer.offsetWidth / factor) + "px";
        viewportBoundingBox.style.height = (videoPlayer.offsetHeight / factor) + "px";
        if (factor > 1) {
            x = xOffset * factor + viewport.offsetWidth / 2;
            y = yOffset * factor + viewport.offsetHeight / 2;
            x = Math.min(0, Math.max(viewport.offsetWidth - imageCanvas.offsetWidth, x));
            y = Math.min(0, Math.max(viewport.offsetHeight - imageCanvas.offsetHeight, y));
        }
        setCanvasPositionInViewport(x, y);
        zoomControl.parentNode.getElementsByTagName('output')[0].firstChild.nodeValue
            = (Math.round(factor * 1000) / 1000) + 'x';
        updateScaleBar(scaleBar);
        drawTapeMeasure(tapeMeasureBegin, tapeMeasureEnd, true);
    }

    function keyPressed(e) {
        var event = e || window.event;
        if (!event.shiftKey && !event.ctrlKey && !event.altKey) {
            switch (event.keyCode) {
                case 100: // Num+4
                    playbackStepBackControl.checked = true;
                    playbackStepBackRequested();
                    break;
                case 101: // Num+5
                    playbackPauseControl.checked = true;
                    playbackPauseRequested();
                    break;
                case 102: // Num+6
                    playbackStepAheadControl.checked = true;
                    playbackStepAheadRequested();
                    break;
                case 103: // Num+7
                    playbackReverseControl.checked = true;
                    playbackReverseRequested();
                    break;
                case 104: // Num+8
                    playbackLoopControl.checked = true;
                    playbackLoopRequested();
                    break;
                case 105: // Num+9
                    playbackForwardControl.checked = true;
                    playbackForwardRequested();
                    break;
            }
        }
    }
    
    function hideEmptyFields(node, isRoot) {
    	if(node == null)
    		return;
    	
    	var nextSibling = null;
    	if(node.nodeName == "SECTION" || node.nodeName == "DL") {
    		hideEmptyFields(node.firstChild, false);
    	}
    	if(node.nodeName == "DD") {
    		if(isEmpty(node.textContent.trim())) {
    			var parent = node.parentNode;
    			nextSibling = node.nextElementSibling;
    			parent.removeChild(node.previousElementSibling);
    			parent.removeChild(node);
    		}
    	}
    	if(!isRoot) {
    		hideEmptyFields(nextSibling == null ? node.nextElementSibling : nextSibling, false);
    	}
    }
    
    function isEmpty(txt) {
    	if(txt.length == 0)
    		return true;
    	if(!isNaN(txt * 1) && (txt * 1) == 0) {
    		return true;
    	}
    	if(txt == "()") {
    		return true;
    	}
    	
    	var arr = txt.split("×");
    	if(arr.length > 1){
    		for(var i = 0; i < arr.length;i++) {
    			if(!isEmpty(arr[i]))
    				return false;
    		}
    		return true;
    	}
    	
    	return false;
    }
    
    
    function setVisibility() {
    	if(reproductionScale == 0)
    		document.getElementById("measurementBox").style.visibility = "hidden";
    		
    	hideEmptyFields(document.getElementById("metadata"), true);
    }
    
    function resetControls() {
    	rotationControl.value = 0;
    	zoomControl.value = 0;
    	playbackRateControl.value = 0;
    	focusControl.value = 0;
    	loopBeginControl.value = 0;
    	loopEndControl.value = loopEndControl.max;
    	brightnessControl.value = 0;
    	contrastControl.value = 0;
    	
    	rotationChanged();
    	zoomChanged();
    	playbackRateChanged();
    	focusChanged();
    	loopBeginChanged();
    	loopEndChanged();
    	brightnessContrastChanged();
    	playbackLoopControl.click();
    }

    // wire things up
    loopRangeIndicator.style.width = (loopBeginControl.offsetWidth - 8) + 'px';
    imageCanvas.width = width;
    imageCanvas.height = height;
    overlayCanvas.width = viewport.offsetWidth;
    overlayCanvas.height = viewport.offsetHeight;
    rotationControl.addEventListener('input', rotationChanged);
    rotationControl.addEventListener('change', rotationChanged);
    zoomControl.addEventListener('input', zoomChanged);
    zoomControl.addEventListener('change', zoomChanged);
    playbackRateControl.addEventListener('input', playbackRateChanged);
    playbackRateControl.addEventListener('change', playbackRateChanged);
    loopBeginControl.addEventListener('input', loopBeginChanged);
    loopBeginControl.addEventListener('change', loopBeginChanged);
    loopEndControl.addEventListener('input', loopEndChanged);
    loopEndControl.addEventListener('change', loopEndChanged);
    focusControl.addEventListener('input', focusChanged);
    focusControl.addEventListener('change', focusChanged);
    brightnessControl.addEventListener('mouseup', brightnessContrastChanged);
    brightnessControl.addEventListener('keyup', brightnessContrastChanged);
    contrastControl.addEventListener('mouseup', brightnessContrastChanged);
    contrastControl.addEventListener('keyup', brightnessContrastChanged);
    overlayCanvas.addEventListener('mousedown', panningBegin);
    document.addEventListener('mousemove', panningContinue);
    document.addEventListener('mouseup', panningEnd);
    regionSnapshotLink.addEventListener('mousedown', regionSnapshotRequested);
    frameSnapshotLink.addEventListener('mousedown', frameSnapshotRequested);
    measurementControl.addEventListener('change', measurementToggled);
    playbackReverse.addEventListener('click', playbackReverseRequested);
    playbackStepBack.addEventListener('click', playbackStepBackRequested);
    playbackPauseControl.addEventListener('click', playbackPauseRequested);
    playbackStepAhead.addEventListener('click', playbackStepAheadRequested);
    playbackForward.addEventListener('click', playbackForwardRequested);
    resetControl.addEventListener('click', resetControls);
    playbackLoopControl.addEventListener('click', playbackLoopRequested);
    document.documentElement.addEventListener('keyup', keyPressed);

    // init controls with presets from the session before page reload
    window.setTimeout(function () {
    	setVisibility();
        measurementControl.checked = false;
        loopBeginChanged();
        loopEndChanged();
        rotationChanged();
        zoomChanged();
        panningEnd();
        playbackRateChanged();
        manualFrameChanged();
        playbackLoopControl.checked = true;
        playbackLoopRequested();
    }, 10);
})();
