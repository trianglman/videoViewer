<?php
namespace videoViewer;
session_start();
require_once('includes/config.php');
$page = new controllers\EditVideoController($_GET, $_POST, $_SESSION, $_COOKIE, $di);
$page->setFileNameParser(new FileNameParser());
$page->setTvdbConn(new TvDBApiConn());
try{
    echo $page->processRequest();
}
catch(PageRedirectException $e){
    if($e->getCode()==403){
        $newError = new PageRedirectException(303, 'seriesList.php',$e);
        $newError->sendHeader();
        exit();
    }
    $e->sendHeader();
    exit();
}
?>
