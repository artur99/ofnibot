<?php
$url = $_GET['url'];
if(substr($url, 0, 4)!='http')die();
echo file_get_contents($url);