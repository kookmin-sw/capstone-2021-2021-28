<!DOCTYPE html>
<html style="height: -webkit-fill-available;"> <!-- 화면 비율 꽉 채우기 -->
<head lang="ko">
    <meta charset="utf-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-eOJMYsd53ii+scO/bJGFsiCZc+5NDVN2yr8+0RDqr0Ql0h+rP48ckxlpbzKgwra6" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/js/bootstrap.bundle.min.js" integrity="sha384-JEW9xMcG8R+pH31jmWH6WWP0WintQrMb4s7ZOdauHnUtxwoG2vI5DkLtS3qm9Ekf" crossorigin="anonymous"></script>
    <script src="js/webcam-easy.js" type="text/javascript"></script>
    <script src="js/download.js" type="text/javascript"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <title></title>
</head>



<body style="height: -webkit-fill-available;">
<div class="container" style="height: -webkit-fill-available; display: flex; align-items: center; justify-content: center; flex-direction: column;">
    <div class="row w-100 mb-5">
        <div class="col-md-12">
            <div class="d-grid gap-2 d-md-block">
                <button class="btn btn-success" type="button" id="start">웹캠 On</button>
                <button class="btn btn-danger" type="button" id="stop">웹캠 Off</button>
                <button class="btn btn-primary" type="button" id="snapshot">캡쳐</button>
                <button class="btn btn-info" type="button" id="save">저장</button>
            </div>
        </div>
    </div>
    <div class="row w-100 mb-5">
        <div class="col-md-6">
            <video id="webcam" autoplay playsinline width="640" height="480" style="border: 3px solid black;"></video>
            <canvas id="canvas" class="d-none"></canvas>

        </div>
        <div class="col-md-6">
            <div id="preview" style="overflow: scroll">

            </div>
        </div>
    </div>
    <div class="row w-100 mb-5">
        <div class="col-md-12">
            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <?php
                    echo "<form action='".$action."' method='post'>";
                    echo "<input name='total_classes' type='text' value='".$_POST['total_classes']."'>";
                    echo "<input name='current_page' type='text' value='".($current_page+1)."'>";
                    for($i=0; $i<$current_page; ++$i){
                        $k = "images_".strval($i+1);
                        echo "<input type='text' id='".$k."' name='".$k."' value='".$_POST[$k]."'>";
                    }
                ?>
                <button class="btn btn-primary" type="submit">Next</button>
                <?php
                    echo "</form>";
                ?>
            </div>
        </div>
    </div>
</div>
</body>
</html>
