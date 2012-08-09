<?php
namespace videoViewer;

session_start();
require_once('includes/config.php');
$page = new controllers\ViewVideoDetailsController($_GET, $_POST, $_SESSION, $_COOKIE, $di);
try{
    echo $page->processRequest();
}
catch(PageRedirectException $e){
    if($e->getCode()==403){
        $newError = new PageRedirectException(303, 'index.php',$e);
        $newError->sendHeader();
        exit();
    }
    $e->sendHeader();
    exit();
}
