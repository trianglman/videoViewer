<?php

namespace videoViewer;
require_once('includes/config.php');
session_start();
$page = new controllers\IndexPageController($_GET,$_POST,$_SESSION,$_COOKIE,$di);
try{
    echo $page->processRequest();
}
catch(PageRedirectException $e){
    $e->sendHeader();
    exit();
}
?>
