<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Welcome to CodeIgniter 4!</title>
    <meta name="description" content="The small framework with powerful features">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/png" href="/favicon.ico">
    <script src="https://cdn.tailwindcss.com"></script>


    <!-- STYLES -->

    <style {csp-style-nonce}>
        * {
            transition: background-color 300ms ease, color 300ms ease;
        }

        *:focus {
            background-color: rgba(221, 72, 20, .2);
            outline: none;
        }

        html,
        body {
            color: rgba(33, 37, 41, 1);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Helvetica, Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji";
            font-size: 16px;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            text-rendering: optimizeLegibility;
        }

        header .heroe {
            margin: 0 auto;
            max-width: 1100px;
            padding: 1rem 1.75rem 1.75rem 1.75rem;
        }

        section {
            margin: 0 auto;
            max-width: 1100px;
            padding: 2.5rem 1.75rem 3.5rem 1.75rem;
        }


        footer {
            background-color: rgba(221, 72, 20, .8);
            text-align: center;
        }

        footer .environment {
            color: rgba(255, 255, 255, 1);
            padding: 2rem 1.75rem;
        }

        footer .copyrights {
            background-color: rgba(62, 62, 62, 1);
            color: rgba(200, 200, 200, 1);
            padding: .25rem 1.75rem;
        }
    </style>
</head>

<body>

    <!-- HEADER: MENU + HEROE SECTION -->
    <header>

        <div class="heroe">

            <h1>Welcome to Cam Geo Mapper</h1>

            <h2>It will display the location where you are capturing the camera.</h2>

        </div>

    </header>

    <!-- CONTENT -->

    <section>

        <div>
            <button type="button" id="recordvideo" class="focus:outline-none text-white bg-yellow-400 hover:bg-yellow-500 focus:ring-4 focus:ring-yellow-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2">Record Video</button>

            <button id="takephoto" type="button" class="focus:outline-none text-white bg-purple-700 hover:bg-purple-800 focus:ring-4 focus:ring-purple-300 font-medium rounded-lg text-sm px-5 py-2.5 mb-2">Take Photo</button>

        </div>

        <div id='camdisplay' class="hidden">
            <div>
                <div class="flex items-center">
                    <video id="camera" autoplay width="200" height="200"></video>
                    <div class="flex flex">
                        <button type="button" id="capture" class="focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 ml-4">Take Photo</button>
                        <button id="switchcamera" class="focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 ml-4">
                            Switch camera
                        </button>
                    </div>
                </div>

                <div id="photoresult" class="mt-4"></div>
                <div id="imagesContainer" class="flex p-4"></div>

            </div>

        </div>

    </section>

    <section id="form" class="hidden">
        <form id="imageForm" method="post" enctype="multipart/form-data" action="<?= base_url('save') ?>">
            <input type="hidden" name="image" id="image">
            <input type="hidden" name="video" id="video">
            <input type="hidden" name="latitude" id="latitude">
            <input type="hidden" name="longitude" id="longitude">
            <input type="hidden" name="locationName" id="locationName">
            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2">Save Details</button>
        </form>
    </section>

    <!-- /Video Container -->
    <section class="hidden" id="videocontainer">
        <div id="videoresult" class="mt-4"></div>

        <video id="preview" autoplay muted class="hidden"></video>
        <video id="recording" controls></video>
        <div>
            <div class="flex flex">
                <button id="switchCameraButton" class="focus:outline-none text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 ml-4">Switch Camera</button>
                <button id="startButton" class="focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 ml-4">Start Recording</button>
                <button id="stopButton" class="focus:outline-none text-white bg-red-900 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 ml-4 hidden">Stop Recording</button>
            </div>
        </div>
        <div id="log"></div>
    </section>


    <!-- FOOTER: DEBUG INFO + COPYRIGHTS -->

    <footer>

        <div class="copyrights">

            <p>&copy; <?= date('Y') ?> George Okumu</p>

        </div>

    </footer>

    <!-- SCRIPTS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script {csp-script-nonce}>
        // DOM elements
        const [video, captureButton, resultDiv, videoResult, imageInput, videoInput, latitudeInput, longitudeInput, locationNameInput, camDisplay, takePhotoButton, switchCameraButton, imagesContainer, recordVideoButton, startRecordingButton, stopRecordingButton] = ['#camera', '#capture', '#photoresult', '#videoresult', '#image', '#video', '#latitude', '#longitude', '#locationName', '#camdisplay', '#takephoto', '#switchcamera', '#imagesContainer', '#recordvideo', '#startrecording', '#stoprecording']
        .map(id => document.querySelector(id));

        let locationFetched = false;
        let locationData = {};
        let photosTaken = 0;
        let videoStream;
        let useFrontCamera = true;

        const constraints = {
            video: {
                width: {
                    min: 1280,
                    ideal: 1920,
                    max: 2560
                },
                height: {
                    min: 720,
                    ideal: 1080,
                    max: 1440
                }
            }
        };

        const stopVideoStream = () => videoStream?.getTracks().forEach(track => track.stop());

        const initializeCamera = async () => {
            if (!navigator.mediaDevices?.getUserMedia) {
                alert("Camera API is not available in your browser");
                return;
            }
            stopVideoStream();
            constraints.video.facingMode = useFrontCamera ? "user" : "environment";
            try {
                videoStream = await navigator.mediaDevices.getUserMedia(constraints);
                video.srcObject = videoStream;
            } catch (err) {
                alert("Could not access the camera");
            }
        };

        takePhotoButton.addEventListener('click', () => {
            camDisplay.classList.remove('hidden');
            document.getElementById('videocontainer').classList.add('hidden');
            initializeCamera();
        });

        switchCameraButton.addEventListener('click', () => {
            useFrontCamera = !useFrontCamera;
            initializeCamera();
        });

        const getLocation = async (callback, localDivToDisplay) => {
            if (!locationFetched && navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(async ({
                    coords: {
                        latitude,
                        longitude
                    }
                }) => {
                    latitudeInput.value = latitude;
                    longitudeInput.value = longitude;
                    try {
                        const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${latitude}&lon=${longitude}`);
                        const {
                            display_name: locationName
                        } = await response.json();
                        locationNameInput.value = locationName;
                        locationData = {
                            latitude,
                            longitude,
                            locationName
                        };
                        locationFetched = true;
                        localDivToDisplay.innerHTML = `
                    <div class="max-w-sm p-6 bg-white border border-gray-200 rounded-lg shadow mt-4">
                        <p class="mb-3 font-normal text-gray-700 dark:text-gray-400">Latitude: ${latitude}</p>
                        <p class="mb-3 font-normal text-gray-700 dark:text-gray-400">Longitude: ${longitude}</p>
                        <p class="mb-3 font-normal text-gray-700 dark:text-gray-400">Location: ${locationName}</p>
                    </div>`;
                        callback();
                        Swal.close();
                    } catch (error) {
                        resultDiv.innerHTML = `<p>Error getting location name: ${error.message}</p>`;
                    }
                }, error => {
                    resultDiv.innerHTML = `<p>Error getting location: ${error.message}</p>`;
                });
            } else {
                callback();
            }
        };

        const captureImage = () => {
            Swal.fire({
                title: 'Processing...',
                text: 'Capturing photo and getting location',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            const canvas = document.createElement('canvas');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
            const imageDataURL = canvas.toDataURL('image/png');
            imageInput.value = imageDataURL;

            const displayCapturedImage = (imageDataURL) => {
                const imgElement = document.createElement('img');
                imgElement.src = imageDataURL;
                imgElement.className = 'p-4 h-40 w-40';
                imagesContainer.appendChild(imgElement);
                Swal.close();
            };

            getLocation(() => displayCapturedImage(imageDataURL), resultDiv);
        };

        captureButton.addEventListener('click', () => {
            if (photosTaken < 4) {
                captureImage();
                photosTaken++;
            } else {
                Swal.fire({
                    icon: 'info',
                    title: 'Limit reached',
                    text: 'You can only capture up to 4 photos.'
                });
                stopVideoStream(); // Stop the video stream after

            }
        });

        recordVideoButton.addEventListener('click', () => {
            stopVideoStream(); // Stop the image photoincase on.

            document.getElementById('videocontainer').classList.remove('hidden');
            document.getElementById('form').classList.remove('hidden');

            camDisplay.classList.add('hidden');
            recordVideo();
        });

        const recordVideo = () => {
            const preview = document.getElementById("preview");
            const recording = document.getElementById("recording");
            const startButton = document.getElementById("startButton");
            const stopButton = document.getElementById("stopButton");
            const switchCameraButton = document.getElementById("switchCameraButton");
            const logElement = document.getElementById("log");

            let recorder;
            let recordedChunks = [];
            let currentCamera = 'user';

            const log = msg => logElement.innerText += `${msg}\n`;

            const startPreview = () => {
                navigator.mediaDevices.getUserMedia({
                        video: {
                            facingMode: currentCamera
                        },
                        audio: true
                    })
                    .then(stream => {
                        preview.srcObject = stream;
                        preview.classList.remove('hidden');
                        recording.classList.add('hidden');
                    })
                    .catch(error => log(`Error: ${error.message}`));
            };

            startButton.addEventListener('click', () => {
                stopButton.classList.remove('hidden');
                switchCameraButton.classList.add('hidden');
                startPreview();
                navigator.mediaDevices.getUserMedia({
                        video: {
                            facingMode: currentCamera
                        },
                        audio: true
                    })
                    .then(stream => {
                        preview.srcObject = stream;
                        recorder = new MediaRecorder(stream);
                        recordedChunks = [];
                        recorder.ondataavailable = event => event.data.size > 0 && recordedChunks.push(event.data);
                        recorder.onstop = () => {

                            const recordedBlob = new Blob(recordedChunks, {
                                type: "video/webm"
                            });
                            getLocation(() => {}, videoResult);

                            recording.src = URL.createObjectURL(recordedBlob);

                            // am saving the video as the blob string, this wont work when you retrieve videos back
                            videoInput.value = URL.createObjectURL(recordedBlob);
                            log(`Successfully recorded ${recordedBlob.size} bytes of ${recordedBlob.type} media.`);
                        };
                        recorder.start();
                        log("Recording started...");
                    })
                    .catch(error => log(`Error: ${error.message}`));
            }, false);

            stopButton.addEventListener('click', () => {
                preview.classList.add('hidden');
                recording.classList.remove('hidden');
                if (recorder?.state === "recording") {
                    recorder.stop();
                    preview.srcObject.getTracks().forEach(track => track.stop());
                    log("Recording stopped.");
                }
            }, false);

            switchCameraButton.addEventListener('click', () => {
                currentCamera = currentCamera === 'user' ? 'environment' : 'user';
                startPreview();
                log(`Switched to ${currentCamera === 'user' ? 'front' : 'rear'} camera.`);
            }, false);
        };
    </script>


    <!-- -->

</body>

</html>