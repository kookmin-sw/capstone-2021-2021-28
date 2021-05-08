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
            guide: false,
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

    function save_json(){ // train 단계에서, train size가 동적으로 변화함으로써 좌표 대신 비율로 annotate하여 bbox를 지정해준다.
        for(let i=0; i<images.length; ++i){
            let data = images_obj[images[i]][0];
            let output = [{}];
            output[0]["x"] = math_round(data["left"]/640); // 웹캠의 사이즈를 기준으로 bbox를 비율로 환산. 이 때 x, y 는 top left이다.
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
                type:"post",
                complete:function(){
                    alert("ok");
                }
            });
        }
    }

    $(document).ready(function() {
    });
</script>
<body style="height: -webkit-fill-available;">
<div class="container" style="height: -webkit-fill-available; display: flex; align-items: center; justify-content: center; flex-direction: column;">
    <div class="row w-100 mb-5">
        <div class="col-md-12">
            <div id="preview" style="overflow: scroll">
                <?php
                    $k = "images_".strval($current_page);
                    $images = explode(",", $_POST[$k]);
                    for($i=0; $i<count($images); ++$i){
                        echo "<img src='images/".$images[$i]."' id='".$images[$i]."' height='100px' width='100px' onclick='start_ann(this)'>";
                    }
                ?>
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
                <?php
                echo "<form action='".$action."' method='post'>";
                echo "<input name='total_classes' type='text' value='".$_POST['total_classes']."'>";
                echo "<input name='ann_current_page' type='text' value='".($current_page+1)."'>";
                for($i=0; $i<$_POST['total_classes']; ++$i){
                    $k = "images_".strval($i+1);
                    echo "<input type='text' id='".$k."' name='".$k."' value='".$_POST[$k]."'>";
                }
                ?>
                <button class="btn btn-info" type="button" onclick="save_json()">저장</button>
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
