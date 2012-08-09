<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

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
    foreach(explode('\\', $name) as $piece){$filename.=lcfirst($piece).'/';}
    $filename = \str_replace("apacheLogParser",
            dirname(__FILE__).'/../tools/apacheLogParser', $filename);
    if(file_exists($filename.".php")){require_once($filename.".php");}
},true,true);

?>
