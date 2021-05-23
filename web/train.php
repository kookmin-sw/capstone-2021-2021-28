<?php
    $total_classes = $_POST['total_classes'];
    $images = "";
    for($i=0; $i<$total_classes; ++$i){
        $k = "images_".strval($i+1);
        $images = $images.$_POST[$k];
        $images = $images.",";
    }
?>
<!DOCTYPE html>
<html>
<?php include 'header.php'?>
<script>
    function submit(){
        let total_classses = parseInt('<?php echo $total_classes; ?>');
        let labels = [];
        let labels_string = "";
        let form = document.createElement("form");
        var element1 = document.createElement("input");
        var element2 = document.createElement("input");
        form.method = "POST";
        form.action = "result.php";
        for(let i=0; i<total_classses; ++i){
            let k = 'label_'+(i+1).toString();
            labels.push(document.getElementById(k).value);
        }
        labels_string = labels.join(",");
        element1.value=total_classses;
        element1.name="total_classses";
        form.appendChild(element1);

        element2.value=labels_string;
        element2.name="labels";
        form.appendChild(element2);

        document.body.appendChild(form);
        form.submit();

    }
    $(document).ready(function() {
        let chart = Highcharts.chart('container', { // Loss 그래프 차트 생성
            chart: {
                type: 'spline',
                animation: Highcharts.svg, // don't animate in old IE
                marginRight: 10,
                events: {
                    load: function () {

                    }
                }
            },
            title:{
                text: 'Loss Graph'
            },
            xAxis:{
                title:{
                    text : 'seconds'
                }
            },
            yAxis:{
                title:{
                    text : 'loss'
                }
            },
            legend: {
                enabled: false
            },
            exporting: {
                enabled: false
            },
            series: [{
                name: 'total loss',
                data: [0]
            }]
        });
        let images_string = '<?php echo $images; ?>';
        let images = images_string.split(",");
        let json_uris = [];
        let total_classses = parseInt('<?php echo $total_classes; ?>');
        let model_weights_name = "initial_glorot_1_classes.weights";
        let labels = [];
        let data = [];
        for (let i=0; i<total_classses; ++i){
            let k = "label_"+(i+1).toString();
            if(document.getElementById(k).value){
                labels.push(document.getElementById(k).value);
            }else{
                labels.push(i.toString());
            }
        }
        for(let i=0; i<images.length; ++i){
            if(images[i] !== ""){
                json_uris.push("http://localhost/images/"+images[i].replace("jpg", "json"));
            }
        }
        if(total_classses == 2){
            model_weights_name = "initial_glorot_2_classes.weights";
        }else if(total_classses == 3){
            model_weights_name = "initial_glorot_3_classes.weights";
        }else if(total_classses == 4){
            model_weights_name = "initial_glorot_4_classes.weights";
        }

        function saveWeights(net, filename = 'train_tmp') {
            saveAs(new Blob([net.serializeParams()]), filename)
        }

        function onBackwardComplete(epoch, dataIdx, inputSize) {
            const idx = dataIdx + 1

            if (inputSize === window.trainSizes[window.trainSizes.length - 1] && (idx % window.saveEveryNthSample) === 0) {
                saveWeights(window.net, `voc_e${epoch}_${idx}.weights`)
                saveAs(new Blob([JSON.stringify(lossReporter.summary())]), `voc_e${epoch}_${idx}.json`)
            }
        }

        async function loadNetWeights(uri) {
            return new Float32Array(await (await fetch(uri)).arrayBuffer())
        }

        document.getElementById('train').onclick = async function() {
            let e = document.getElementById("epoch");
            if(parseInt(e.value)<0){
                alert("Select Epoch!");
            }
            tf = yolo.tf;
            // uri to weights file of last checkpoint
            const modelCheckpoint = 'http://localhost/weights/'+model_weights_name;
            console.log("model check point file path is "+modelCheckpoint);
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
            window.maxEpoch = parseInt(e.value);
            const lossReporter = createLossReporter(trainSizes)
            const log = (str, ...args) => console.log(`[${[(new Date()).toTimeString().substr(0, 8)]}] ${str || ''}`, ...args)
            console.log(json_uris);
            window.boxJsonUris = json_uris;
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
            const weights = await loadNetWeights(modelCheckpoint)
            await window.net.load(weights);
            let series = chart.series[0];
            for (let epoch = startEpoch; epoch < window.maxEpoch; epoch++) {
                // if (epoch !== startEpoch && epoch % 100 == 0) {
                //   saveWeights(window.net, `voc_epoch${epoch - 1}.weights`)
                //   saveAs(new Blob([JSON.stringify(lossReporter.summary())]), `voc_losses_epoch${epoch - 1}.json`)
                // }
                lossReporter.resetLosses()
                const shuffledInputs = yolo.shuffleArray(window.boxJsonUris)
                for (let dataIdx = 0; dataIdx < shuffledInputs.length; dataIdx++) {
                    const boxJsonUri = shuffledInputs[dataIdx]
                    // fetch image and ground truth bounding boxes
                    const img = await yolo.bufferToImage(await fetchImage(boxJsonUri.replace('.json', '.jpg')))
                    const groundTruth = (await fetchJson(boxJsonUri)).map(gt => ({ ...gt, label: gt.label }))

                    for (let sizeIdx = 0; sizeIdx < window.trainSizes.length; sizeIdx++) {

                        const inputSize = window.trainSizes[sizeIdx]

                        const backwardOptions = {
                            minBoxSize: window.minBoxSize,
                            reportLosses: function({ losses, numBoxes, inputSize }) {
                                if(dataIdx === 0){
                                    series.addPoint(yolo.round(losses.totalLoss, 4));
                                }
                                lossReporter.reportLosses({ losses, numBoxes, inputSize })
                            }
                        }

                        const ts = Date.now()
                        const loss = await net.backward(img, groundTruth, inputSize, backwardOptions)

                        onBackwardComplete(epoch, dataIdx, inputSize)

                        if (!loss) {
                            log(`no boxes for input size ${inputSize}, ${groundTruth.length} boxes were too small`)
                            continue
                        }

                        loss.dispose()
                        log(`trainStep time for epoch ${epoch}, dataIdx ${dataIdx} (${inputSize}): ${Date.now() - ts} ms`)
                        await tf.nextFrame()
                    }
                }
                document.getElementById("notice_epoch").innerHTML = "현재 Epoch : " + epoch;
            }
            console.log("train Ended");
            saveWeights(window.net, 'train.weights');
        }
    });
</script>
<body>
<div class="container" style="height:auto; width:auto;">
    <div class="row w-100 mb-5">
        <div class="col-md-12">
            <br />
            <div class="d-grid gap-2 d-md-flex">
                <h2>Training</h2>
                &nbsp;
                <a class="btn btn-warning" data-bs-toggle="modal" href="#trainModalToggle" role="button">도움말</a>
            </div>
            <!-- Modal -->
            <div class="modal fade" id="trainModalToggle" aria-hidden="true" aria-labelledby="trainModalToggleLabel" tabindex="-1">
                <div class="modal-dialog modal-dialog-scrollable modal-xl modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="trainModalToggleLabel">Train</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <image src="direction/train1.png" style="width:100%; height:100%; border:none"></image>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-primary" data-bs-target="#trainModalToggle2" data-bs-toggle="modal" data-bs-dismiss="modal">다음으로</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="trainModalToggle2" aria-hidden="true" aria-labelledby="trainModalToggleLabel2" tabindex="-1">
                <div class="modal-dialog modal-dialog-scrollable modal-xl modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="trainModalToggleLabel2">Train</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <image src="direction/train2.png" style="width:100%; height:100%; border:none"></image>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-primary" data-bs-target="#trainModalToggle" data-bs-toggle="modal" data-bs-dismiss="modal">이전으로</button>
                            <button class="btn btn-primary" data-bs-target="#trainModalToggle3" data-bs-toggle="modal" data-bs-dismiss="modal">다음으로</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="trainModalToggle3" aria-hidden="true" aria-labelledby="trainModalToggleLabel3" tabindex="-1">
                <div class="modal-dialog modal-dialog-scrollable modal-xl modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="trainModalToggleLabel3">Train</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <image src="direction/train3.png" style="width:100%; height:100%; border:none"></image>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-primary" data-bs-target="#trainModalToggle2" data-bs-toggle="modal" data-bs-dismiss="modal">이전으로</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">닫기</button>
                        </div>
                    </div>
                </div>
            </div>
            <br /><br />
            <?php
                for($i=0; $i<$total_classes; ++$i){
                    if($i == 0){
                        $num = "첫 번째 class명";
                    }
                    elseif($i == 1){
                        $num = "두 번째 class명";
                    }
                    elseif($i == 2){
                        $num = "세 번째 class명";
                    }
                    else{
                        $num = "네 번째 class명";
                    }
                    echo "<h4>Class name ".($i+1)."</h4>";
                    echo "<input class='form-control' name='label_".strval($i+1)."' id='label_".strval($i+1)."' type='text' placeholder='".strval($num)."을 입력해주세요.'>";
                    echo "<br />";
                }
            ?>
            <br />
            <div class="d-grid gap-2 d-md-flex">
                <h3>epoch 설정</h3>
                &nbsp;
                <a class="btn btn-warning" data-bs-toggle="modal" href="#epochModalToggle" role="button">도움말</a>
            </div>
            <br />
            <!-- Modal -->
            <div class="modal fade" id="epochModalToggle" aria-hidden="true" aria-labelledby="epochModalToggleLabel" tabindex="-1">
                <div class="modal-dialog modal-dialog-scrollable modal-xl modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="epochModalToggleLabel">Epoch</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <image src="direction/epoch1.png" style="width:100%; height:100%; border:none"></image>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-primary" data-bs-target="#epochModalToggle2" data-bs-toggle="modal" data-bs-dismiss="modal">다음으로</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="epochModalToggle2" aria-hidden="true" aria-labelledby="epochModalToggleLabel2" tabindex="-1">
                <div class="modal-dialog modal-dialog-scrollable modal-xl modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="epochModalToggleLabel2">Epoch</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <image src="direction/epoch2.png" style="width:100%; height:100%; border:none"></image>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-primary" data-bs-target="#epochModalToggle" data-bs-toggle="modal" data-bs-dismiss="modal">이전으로</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">닫기</button>
                        </div>
                    </div>
                </div>
            </div>
            <input class="form-control" name="epoch" id="epoch" type="number">
            <br />
            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <a class="btn btn-warning" data-bs-toggle="modal" href="#lossModalToggle" role="button">Loss 설명</a>
                &nbsp;
                <button class="btn btn-primary" type="button" id="train">Train</button>
                &nbsp;
                <button class="btn btn-info" type="button" id="next" onclick="submit()">Next</button>
            </div>
            <!-- Modal -->
            <div class="modal fade" id="lossModalToggle" aria-hidden="true" aria-labelledby="lossModalToggleLabel" tabindex="-1">
                <div class="modal-dialog modal-dialog-scrollable modal-xl modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="lossModalToggleLabel">Loss</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <image src="direction/loss1.png" style="width:100%; height:100%; border:none"></image>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-primary" data-bs-target="#lossModalToggle2" data-bs-toggle="modal" data-bs-dismiss="modal">다음으로</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="lossModalToggle2" aria-hidden="true" aria-labelledby="lossModalToggleLabel2" tabindex="-1">
                <div class="modal-dialog modal-dialog-scrollable modal-xl modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="lossModalToggleLabel2">Loss</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <image src="direction/loss2.png" style="width:100%; height:100%; border:none"></image>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-primary" data-bs-target="#lossModalToggle" data-bs-toggle="modal" data-bs-dismiss="modal">이전으로</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">닫기</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row w-100 mb-20">
        <div class="col-md-12">
            <figure class="highcharts-figure">
                <div id="container"></div>
                <div id="notice_epoch">현재 Epoch : </div>
            </figure>
        </div>

    </div>
</div>
</body>
</html>
