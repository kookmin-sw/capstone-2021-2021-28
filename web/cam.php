<?php
    $current_page = 1;
    $action = 'cam.php';
    if(isset($_POST['current_page'])){
        $current_page = $_POST['current_page'];
    }
    if($current_page==$_POST['total_classes']){
        $action = 'ann.php'; // 클래스의 개수에 따라서 모든 클래스에 대한 캡처를 완료했으면 다음 annotation 페이지로 이동
    }
    if($current_page == 1){
        $_POST["images_1"] = "";
    }
?>
<!DOCTYPE html>
<html style="height: -webkit-fill-available;"> <!-- 화면 비율 꽉 채우기 -->
<head lang="ko">
    <meta charset="utf-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-eOJMYsd53ii+scO/bJGFsiCZc+5NDVN2yr8+0RDqr0Ql0h+rP48ckxlpbzKgwra6" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/js/bootstrap.bundle.min.js" integrity="sha384-JEW9xMcG8R+pH31jmWH6WWP0WintQrMb4s7ZOdauHnUtxwoG2vI5DkLtS3qm9Ekf" crossorigin="anonymous"></script>
    <script src="js/webcam-easy.js" type="text/javascript"></script> <!-- 웹캠 오픈 소스 활용 -->
    <script src="js/download.js" type="text/javascript"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <title></title>
</head>
<script>
    $(document).ready(function() { // 페이지 로딩 기다림
        let images = []; // 이미지 배열
        const webcamElement = document.getElementById('webcam'); //웹캠 활성화
        const canvasElement = document.getElementById('canvas'); // 캔버스 활성화
        const webcam = new Webcam(webcamElement, 'user', canvasElement); // 웹캠 객체 생성
        document.getElementById('start').onclick = function() { // 웹캠 시작
            webcam.start()
                .then(result =>{
                    console.log("webcam started");
                })
                .catch(err => {
                    console.log(err);
                });
        };
        document.getElementById('stop').onclick = function() {
            webcam.stop();
        };
        document.getElementById('snapshot').onclick = function() {
            let picture = webcam.snap(); // 웹캠 이지로 쉽게 캡쳐 후 저장
            images.push(picture); // 이미지 배열에 푸시
            var new_image = document.createElement("img"); // 동적 이미지 추가 
            new_image.src = picture;
            new_image.setAttribute('height', '100px'); // 프리뷰 이미지 특성 설정
            new_image.setAttribute('width', '100px');
            document.getElementById('preview').appendChild(new_image); // 프리뷰 이미지 배열 동적 추가
        };
        document.getElementById('save').onclick = function() {
            let i = 0;
            let image_filenames = [];
            for(i=0; i<images.length; ++i){ // for loop 돌면서 이미지 추가
                let today = new Date(); // 날짜와 밀리세컨 단위로 Key 값 생성 -> 데이터 베아스 사용안함.
                let date = today.getFullYear()+''+(today.getMonth()+1)+''+today.getDate();
                let time = today.getHours() + "" + today.getMinutes() + "" + today.getSeconds() + "" + today.getMilliseconds();
                let filename = date+''+time+'_'+(i+1).toString()+'.jpg';
                $.ajax({ // 비동기 식으로 처리하여 upload_image 페이지 로딩 필요없이 이미지 저장
                    url:"upload_image.php", // 
                    data:{
                        base64: images[i],
                        filename: filename
                    },
                    type:"post", // 포스트 방식으로 upload_image.php로 passing
                    complete:function(){
                        alert("ok"); //정상적으로 완료되었다면 알림 설정.
                    }
                });
                image_filenames.push(filename);
            }
            let key = 'images_'+'<?php echo $current_page?>';
            document.getElementById(key).value = image_filenames.toString();
        };
    });
</script>
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

