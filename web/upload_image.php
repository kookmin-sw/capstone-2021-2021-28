<?php
$data = $_POST['base64'];
$data = str_replace('data:image/jpeg;base64,', '', $data);
$data = str_replace(' ', '+', $data);
$data = base64_decode($data);
$filepath = "images/".$_POST['filename'];
file_put_contents($filepath, $data);
?>