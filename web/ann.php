<?php
    $current_page = 1;
    $action = 'ann.php';
    if(isset($_POST['ann_current_page'])){
        $current_page = $_POST['ann_current_page'];
    }
    if($current_page==$_POST['total_classes']){
        $action = 'train.php';
    }
?>
<!DOCTYPE html>
<html style="height: -webkit-fill-available;">
<head lang="ko">
    <meta charset="utf-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-eOJMYsd53ii+scO/bJGFsiCZc+5NDVN2yr8+0RDqr0Ql0h+rP48ckxlpbzKgwra6" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/js/bootstrap.bundle.min.js" integrity="sha384-JEW9xMcG8R+pH31jmWH6WWP0WintQrMb4s7ZOdauHnUtxwoG2vI5DkLtS3qm9Ekf" crossorigin="anonymous"></script>
    <script type="text/javascript" src="https://unpkg.com/webcam-easy/dist/webcam-easy.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="js/bbox-annotator.js" type="text/javascript"></script>
    <title></title>
</head>
<script>

</script>
<body style="height: -webkit-fill-available;">
<div class="container" style="height: -webkit-fill-available; display: flex; align-items: center; justify-content: center; flex-direction: column;">
    <div class="row w-100 mb-5">
        <div class="col-md-12">
            <div id="preview" style="overflow: scroll">
            </div>
        </div>
    </div>
    <div class="row w-100 mb-5" style="height: 200px;">
        <div class="col-md-12">
<!--            <img id="main" width="640" height="480" style="border: 3px solid black;"></img>-->
            <div id="bbox_annotator"></div>
        </div>
    </div>
    <div class="row w-100 mb-5">
        <div class="col-md-12">
            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
            </div>
        </div>
    </div>
</div>
</body>
</html>
