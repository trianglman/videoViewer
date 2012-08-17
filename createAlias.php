<?php
namespace videoViewer;

session_start();
require_once('includes/config.php');
$page = new controllers\CreateAliasController($_GET, $_POST, $_SESSION, $_COOKIE, $di);
$page->fileNameParser = new \videoViewer\FileNameParser();
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
?>