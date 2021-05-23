<?php
$data = $_POST['data'];
$filepath = "images/".$_POST['filename'];
$filepath = str_replace('jpg', 'json', $filepath);
file_put_contents($filepath, $data);
?>