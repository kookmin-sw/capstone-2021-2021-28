<?php
$data = $_POST['data'];
$images = explode(",", $data);
for($i=0; $i<count($images); ++$i){
    unlink("images/".$images[$i]);
    unlink("images/".str_replace("jpg", "json", $images[$i]));
}
?>