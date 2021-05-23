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
<html>
<?php include 'header.php'?>
<script>
    let images_string = '<?php $k = "images_".strval($current_page); echo $_POST[$k];?>';
    let images = images_string.split(",");
    let images_obj = {};
    for(let i=0; i<images.length; ++i){
        images_obj[images[i]] = [];
    }

    function start_ann(img){
        $('.image_frame').remove();
        var annotator = new BBoxAnnotator({
            url: img.src,
            input_method: 'fixed',    // Can be one of ['text', 'select', 'fixed']
            labels: '<?php echo $current_page;?>',
            guide: true,
            onchange: function(entries) {
                // Input the text area on change. Use "hidden" input tag unless debugging.
                // <input id="annotation_data" name="annotation_data" type="hidden" />
                // $("#annotation_data").val(JSON.stringify(entries))
                images_obj[img.id] = entries;
            }
        });
        // Initialize the reset button.
        $("#reset_button").click(function(e) {
            annotator.clear_all();
        })
    }

    function math_round(num){
        return Math.round(num*100)/100;
    }

    function save_json(){ // train 단계에서, train size가 동적으로 변화함으로써 좌표 대신 비율로 annotate하여 bbox를 지정해준다
        for(let i=0; i<images.length; ++i){
            let data = images_obj[images[i]][0];
            let output = [{}];
            output[0]["x"] = math_round(data["left"]/640);  // 웹캠의 사이즈를 기준으로 bbox를 비율로 환산. 이 때 x, y 는 top left이다.

            output[0]["y"] = math_round(data["top"]/480);
            output[0]["width"] = math_round(data["width"]/640);
            output[0]["height"] = math_round(data["height"]/480);
            output[0]["label"] = parseInt(data["label"])-1; // configure 작성 시에 class가 배열로 생성되어 label은 인덱스로서 작동한다. 즉 direct Indexing의 개념.
            $.ajax({
                url:"upload_json.php",
                data:{
                    data: JSON.stringify(output),
                    filename: images[i]
                },
                type:"post"
            });
        }
        alert("Annotation이 저장되었습니다.")
    }

    $(document).ready(function() {
        document.getElementById("class_num").innerHTML = "Class "+"<?php echo $current_page;?>";
    });
</script>
<body>
<div class="container">
    <div class="row w-100 mb-5">
        <div class="col-md-12">
            <div id="preview" style="overflow: scroll">
                <h2 id="class_num"></h2>
                <br /><h3>Preview</h3><br />
                <?php
                    $k = "images_".strval($current_page);
                    $images = explode(",", $_POST[$k]);
                    for($i=0; $i<count($images); ++$i){
                        echo "<img src='images/".$images[$i]."' id='".$images[$i]."' height='100px' width='100px' style='border: 4px solid transparent; border-radius: 10px;' onclick='start_ann(this)'>";
                    }
                ?>
            </div>
        </div>
    </div>
    <div class="row w-100 mb-5" style="height: 480px;">
        <div class="col-md-12">
            <h3>Annotating</h3><br />
            <div id="bbox_annotator"></div>
        </div>
    </div>
    <div class="row w-100 mb-5">
        <div class="col-md-12">
            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <?php
                echo "<form action='".$action."' method='post'>";
                echo "<input name='total_classes' type='hidden' value='".$_POST['total_classes']."'>";
                echo "<input name='ann_current_page' type='hidden' value='".($current_page+1)."'>";
                for($i=0; $i<$_POST['total_classes']; ++$i){
                    $k = "images_".strval($i+1);
                    echo "<input type='hidden' id='".$k."' name='".$k."' value='".$_POST[$k]."'>";
                }
                ?>
                <a class="btn btn-warning" data-bs-toggle="modal" href="#exampleModalToggle" role="button">도움말</a>
                &nbsp;
                <button class="btn btn-info" type="button" onclick="save_json()">저장</button>
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
                            <h5 class="modal-title" id="exampleModalToggleLabel">Annotating</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <image src="direction/ann1.png" style="width:100%; height:100%; border:none"></image>
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
                            <h5 class="modal-title" id="exampleModalToggleLabel2">Annotating</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <image src="direction/ann2.png" style="width:100%; height:100%; border:none"></image>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-primary" data-bs-target="#exampleModalToggle" data-bs-toggle="modal" data-bs-dismiss="modal">이전으로</button>
                            <button class="btn btn-primary" data-bs-target="#exampleModalToggle3" data-bs-toggle="modal" data-bs-dismiss="modal">다음으로</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="exampleModalToggle3" aria-hidden="true" aria-labelledby="exampleModalToggleLabel3" tabindex="-1">
                <div class="modal-dialog modal-dialog-scrollable modal-xl modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalToggleLabel3">Annotating</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <image src="direction/ann3.png" style="width:100%; height:100%; border:none"></image>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-primary" data-bs-target="#exampleModalToggle2" data-bs-toggle="modal" data-bs-dismiss="modal">이전으로</button>
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
