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
<html>
<?php include 'header.php'?>
<script>
    $(document).ready(function() { // 페이지 로딩 기다림

        let images = []; // 이미지 배열
        const webcamElement = document.getElementById('webcam'); // 웹캠 활성화
        const canvasElement = document.getElementById('canvas'); // 캔버스 활성화
        // const snapSoundElement = document.getElementById('snapSound');
        // const webcam = new Webcam(webcamElement, 'user', canvasElement, snapSoundElement);
        const webcam = new Webcam(webcamElement, 'user', canvasElement); // 웹캠 객체 생성
        document.getElementById("class_num").innerHTML = "Class "+"<?php echo $current_page;?>";

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
            new_image.setAttribute('style', 'border: 4px solid transparent; border-radius: 10px;');
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
                    url:"upload_image.php",
                    data:{
                        base64: images[i],
                        filename: filename
                    },
                    type:"post"// 포스트 방식으로 upload_image.php로 passing

                });
                image_filenames.push(filename);
            }
            alert("저장되었습니다.");
            let key = 'images_'+'<?php echo $current_page?>';
            document.getElementById(key).value = image_filenames.toString();
        };
    });
</script>
<body>
<div class="container" style="height:auto; width:auto;">
    <div class="row w-100 mb-5">
        <div class="col-md-12">
            <br />
            <h2 id="class_num"></h2>
            <div class="d-grid gap-2 d-md-block">
                <br />
                <button class="btn btn-success" type="button" id="start">웹캠 시작</button>
                <button class="btn btn-danger" type="button" id="stop">웹캠 끄기</button>
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
                    echo "<input name='total_classes' type='hidden' value='".$_POST['total_classes']."'>";
                    echo "<input name='current_page' type='hidden' value='".($current_page+1)."'>";
                    for($i=0; $i<$current_page; ++$i){
                        $k = "images_".strval($i+1);
                        echo "<input type='hidden' id='".$k."' name='".$k."' value='".$_POST[$k]."'>";
                    }
                ?>
                <a class="btn btn-warning" data-bs-toggle="modal" href="#exampleModalToggle" role="button">도움말</a>
                &nbsp;
                <button class="btn btn-primary" type="submit">Next</button>
                <?php
                    echo "</form>";
                ?>
            </div>
            <!-- Modal -->
            <div class="modal fade" id="exampleModalToggle" aria-hidden="true" aria-labelledby="exampleModalToggleLabel" tabindex="-1">
                <div class="modal-dialog modal-dialog-scrollable modal-xl modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalToggleLabel">Get Data</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <image src="direction/capture1.png" style="width:100%; height:100%; border:none"></image>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-primary" data-bs-target="#exampleModalToggle2" data-bs-toggle="modal" data-bs-dismiss="modal">다음으로</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="exampleModalToggle2" aria-hidden="true" aria-labelledby="exampleModalToggleLabel2" tabindex="-1">
                <div class="modal-dialog modal-dialog-scrollable modal-xl modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalToggleLabel2">Get Data</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <image src="direction/capture2.png" style="width:100%; height:100%; border:none"></image>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-primary" data-bs-target="#exampleModalToggle" data-bs-toggle="modal" data-bs-dismiss="modal">이전으로</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">닫기</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
