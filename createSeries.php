<?php
namespace videoViewer;
session_start();
require_once('includes/config.php');
$page = new controllers\CreateSeriesController($_GET, $_POST, $_SESSION, $_COOKIE, $di);
$page->setTvdbConn(new TvDBApiConn());
$page->setParser(new FileNameParser());
try{
    echo $page->processRequest();
}
catch(PageRedirectException $e){
    exit();
    if($e->getCode()==403){
        $newError = new PageRedirectException(303, 'index.php',$e);
        $newError->sendHeader();
        exit();
    }
    $e->sendHeader();
    exit();
}
?>
