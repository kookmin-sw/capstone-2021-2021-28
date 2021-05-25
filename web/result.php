<?php
    $total_classes = $_POST['total_classes'];
    $labels_string = $_POST['labels'];
    $images_string = $_POST['images'];
    $action = 'train.php';
?>
<!DOCTYPE html>
<html>
<?php include 'header.php'?>
<script>
    $(document).ready(function() {
        const webcamElement = document.getElementById('webcam');
        const canvasElement = document.getElementById('canvas');
        const webcam = new Webcam(webcamElement, 'user', canvasElement);
        let total_classses = parseInt('<?php echo $total_classes; ?>');
        let labels_string = '<?php echo $labels_string; ?>'
        let labels = labels_string.split(",");
        let images_string = '<?php echo $images_string; ?>'
        let images = images_string.split(",");
        document.getElementById('start').onclick = function() { // weight 파일을 업로드하고 'start'버튼을 눌러 테스트 시작
            webcam.start()
                .then(result =>{
                    console.log("webcam started");
                    if(document.getElementById("weight_file").value != "") {
                        // you have a file
                        setInterval(start_ann, 10);
                    }
                })
                .catch(err => {
                    console.log(err);
                });
        };
        document.getElementById('stop').onclick = function() {
            webcam.stop();
        };

        document.getElementById('reset').onclick = function() { // 모든 이미지를 unlink하고 처음으로 돌아감
            $.ajax({
                url:"delete.php",
                data:{
                    data: images_string
                },
                type:"post",
                complete:function(){
                    alert("모든 이미지가 삭제 되었습니다!");
                }
            });
            let form = document.createElement("form");
            form.method = "POST";
            form.action = "index.php";
            document.body.appendChild(form);
            form.submit();
        };

        async function start_ann(){
            var reader = new FileReader();
            reader.readAsArrayBuffer(document.getElementById("weight_file").files[0]);
            reader.onload = async function(e) {
                let weights = new Float32Array(e.target.result);
                tf = yolo.tf;
                // continue training at epoch
                const startEpoch = 0;
                // optimizer used for loss minimization (I found adam with default parameters to work out)
                const learningRate = 0.001; // 0.001
                window.optimizer = tf.train.adam(learningRate, 0.9, 0.999, 1e-8);
                // ignore boxes if min dimension is smaller than 'minBoxSize'
                window.minBoxSize = 32;
                // save weights every 'saveEveryNthSample' images
                window.saveEveryNthSample = 500;
                // image sizes at which each image is backward passed
                window.trainSizes = [160,224];
                // finish training after 'maxEpoch' epochs
                window.maxEpoch = 200;
                const lossReporter = createLossReporter(trainSizes)
                const log = (str, ...args) => console.log(`[${[(new Date()).toTimeString().substr(0, 8)]}] ${str || ''}`, ...args)
                window.config = {
                    // use separable convolutions over regular convolutions
                    "withSeparableConvs": true,
                    // iou threshold for nonMaxSuppression
                    "iouThreshold": 0.5,
                    // instructions for how to determine anchors is given below
                    "anchors": [
                        { "x": 1.08, "y": 1.19 },
                        { "x": 3.42, "y": 4.41 },
                        { "x": 6.63, "y": 11.38 },
                        { "x": 9.42, "y": 5.11 },
                        { "x": 16.62, "y": 10.52 }
                    ],
                    // whatever kind of objects you are training your object detector on
                    "classes": labels,
                    // optionally you can compute the mean RGB value for your dataset and
                    // pass it in the config for performing mean value subtraction on your
                    // input images
                    //"meanRgb": [100,100,100],
                    // scale factors for each loss term (only required for training),
                    // explained below
                    "objectScale": 10,
                    "noObjectScale": 1,
                    "coordScale": 1,
                    "classScale": 1
                }
                window.net = new yolo.TinyYolov2Trainable(window.config, window.optimizer)

                await window.net.load(weights);
                const forwardParams = {
                    inputSize: 224,
                    scoreThreshold: 0.6
                }
                let video = document.querySelector("#webcam");
                const detections = await net.detect(video, forwardParams);
                let canvas = document.querySelector("#canvas");
                canvas.width = 640;
                canvas.height = 480;
                let ctx = canvas.getContext('2d');
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                for(let i=0; i<detections.length; ++i){
                    detections[i]._box._x = canvas.width - detections[i]._box._x - detections[i]._box._width;
                }
                yolo.drawDetection(canvas, detections.map(det => det.forSize(640, 480)));


            }
        }
    });
</script>
<body>
<div class="container">
    <div class="row w-100 mb-5">
        <div class="col-md-6">
            <input type="file" id="weight_file">
            <a class="btn btn-warning" data-bs-toggle="modal" href="#testModalToggle" role="button">도움말</a>
            <button class="btn btn-success" type="button" id="start">Start</button>
            <button class="btn btn-danger" type="button" id="stop">Stop</button>
            <button class="btn btn-danger" type="button" id="reset">초기화</button>
        </div>
        <div class="col-md-6">
            <video id="webcam" autoplay playsinline width="640" height="480" style="border: 3px solid black; position: absolute; top: 20%;"></video>
            <canvas id="canvas" style="position: absolute; top: 20%;"></canvas>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="testModalToggle" aria-hidden="true" aria-labelledby="testModalToggleLabel" tabindex="-1">
                <div class="modal-dialog modal-dialog-scrollable modal-xl modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="testModalToggleLabel">Test</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <image src="direction/test1.png" style="width:100%; height:100%; border:none"></image>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-primary" data-bs-target="#testModalToggle2" data-bs-toggle="modal" data-bs-dismiss="modal">다음으로</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="testModalToggle2" aria-hidden="true" aria-labelledby="testModalToggleLabel2" tabindex="-1">
                <div class="modal-dialog modal-dialog-scrollable modal-xl modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="testModalToggleLabel2">Test</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <image src="direction/test2.png" style="width:100%; height:100%; border:none"></image>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-primary" data-bs-target="#testModalToggle" data-bs-toggle="modal" data-bs-dismiss="modal">이전으로</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">닫기</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</div>
</body>
</html>
