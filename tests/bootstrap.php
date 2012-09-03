<?php

require_once 'Mockery/Loader.php';
require_once 'Hamcrest/Hamcrest.php';
/**
 * @author John Judy <john.a.judy@gmail.com>
 */
// TODO: check include path
ini_set('include_path', ini_get('include_path').PATH_SEPARATOR.dirname(__FILE__).'/../../../../../usr/local/lib/php/doctrine');

// put your code here
spl_autoload_register(function ($name){
    $filename = \str_replace("\\", "/", $name);
    $filename = \str_replace("videoViewer", dirname(__FILE__).'/../includes', 
            $filename);
    if(file_exists($filename.".php")){
            require_once($filename.".php");
    }
},true,true);

spl_autoload_register(function($name){
    $filename = '';
    $fileParts = array_map(function($piece){return lcfirst($piece);},
            explode('\\',$name));
    if($fileParts[0]=='pimple'){
        $filename = dirname(__FILE__).'/../tools/Pimple/lib/Pimple';
    }
    elseif($fileParts[0]=='mustache'){
        $filename = dirname(__FILE__).'/../tools/mustache/Mustache';
    }
    else{
        $filename = implode('/', $fileParts);
        $filename = \str_replace("apacheLogParser",
                dirname(__FILE__).'/../tools/apacheLogParser', $filename);
    }
    if(file_exists($filename.".php")){require_once($filename.".php");}
},true,true);
$loader = new \Mockery\Loader;
$loader->register();

require 'doctrine/lib/Doctrine/ORM/Tools/Setup.php';

$lib = 'doctrine';
\Doctrine\ORM\Tools\Setup::registerAutoloadGit($lib);


?>
