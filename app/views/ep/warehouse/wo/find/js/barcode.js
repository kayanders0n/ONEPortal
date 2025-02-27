var barCodeScanner = null;

function startScanning(targetId) {
    // This method will trigger user permissions
    Html5Qrcode.getCameras().then(devices => {
        /**
         * devices would be an array of objects of type:
         * { id: "id", label: "label" }
         */
        if (devices && devices.length) {
            var cameraId = "";
            if (devices.length != 1) {
                $(devices).each(function (idx, camera) {
                    console.log('Camera : ' + camera.label);

                    if (camera.label == 'Back Camera') {
                        cameraId = camera.id;
                        console.log(cameraId);
                        return false;
                    }
                });
            } else {
                cameraId = devices[0].id;
                console.log("Only One Camera!");
            }
            if (cameraId == '') {
                console.log("Camera not found!");
                alert("Camera not found!!");
                return false;
            } else {
                console.log("Camera ID: " + cameraId);
            }

            $('#barcode-stop-scanning').show();

            var is_phone = navigator.userAgent.match(/Phone/i) != null;
            var box_size = {width: 500, height: 400};

            if (is_phone) {
                box_size = {width: 300, height: 250};
            }

            barCodeScanner.start(
                cameraId,
                {
                    fps: 10,    // Optional, frame per seconds for qr code scanning
                    qrbox: box_size,  // Optional, if you want bounded box UI
                    aspectRatio: 2
                },
                (decodedText, decodedResult) => {
                    // do something when code is read
                    $('#' + targetId).val(decodedText);
                    stopScanning(targetId);
                },
                (errorMessage) => {
                    // parse error, ignore it.
                })
                .catch((err) => {
                    // Start failed, handle it.
                });

        }
    }).catch(err => {
        // handle err
    });

    return false;
}


function stopScanning(targetId) {
    barCodeScanner.stop();
    barCodeScanner.clear();
    $('#barcode-stop-scanning').hide();
    if (targetId) {
        console.log('Set Focus: ' + targetId);
        $('#' + targetId).focus();
        $('#' + targetId).blur();
    }
    return false;
}