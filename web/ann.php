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

    function save_json(){
        for(let i=0; i<images.length; ++i){
            let data = images_obj[images[i]][0];
            let output = [{}];
            output[0]["x"] = math_round(data["left"]/640);
            output[0]["y"] = math_round(data["top"]/480);
            output[0]["width"] = math_round(data["width"]/640);
            output[0]["height"] = math_round(data["height"]/480);
            output[0]["label"] = parseInt(data["label"])-1;
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
<body>
<div class="container">
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
    <div class="row w-100 mb-5" style="height: 480px;">
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
                echo "<input name='total_classes' type='hidden' value='".$_POST['total_classes']."'>";
                echo "<input name='ann_current_page' type='hidden' value='".($current_page+1)."'>";
                for($i=0; $i<$_POST['total_classes']; ++$i){
                    $k = "images_".strval($i+1);
                    echo "<input type='hidden' id='".$k."' name='".$k."' value='".$_POST[$k]."'>";
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
