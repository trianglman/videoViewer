<?php
namespace videoViewer;
/**
 * All of the bootstrap code needed to initialize a page
 */
define('TVDB_API_KEY','');

//load objects from this project
spl_autoload_register(function ($name){
    $filename = \str_replace("\\", "/", $name);
    $filename = \str_replace("videoViewer", dirname(__FILE__), $filename);
    if(file_exists($filename.".php")){
            require_once($filename.".php");
    }
},true,true);

//load third party tools
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

require 'doctrine/lib/Doctrine/ORM/Tools/Setup.php';

$lib = 'doctrine';
\Doctrine\ORM\Tools\Setup::registerAutoloadGit($lib);

//configure doctrine
use Doctrine\ORM\EntityManager,
    Doctrine\ORM\Configuration;

$cache = new \Doctrine\Common\Cache\ArrayCache;
$config = new Configuration;
$config->setMetadataCacheImpl($cache);
$driverImpl = $config->newDefaultAnnotationDriver(dirname(__FILE__).'/Entities');
$config->setMetadataDriverImpl($driverImpl);
$config->setQueryCacheImpl($cache);
$config->setProxyDir(dirname(__FILE__).'/Proxies');
$config->setProxyNamespace('videoViewer\Proxies');
//see example in Doctrine\DBAL\Logging\EchoSQLLogger
//$config->setSQLLogger($logger);

$config->setAutoGenerateProxyClasses(true);

$connectionOptions = array(
    'driver'   => 'pdo_mysql',
    'user'     => 'doctrine',
    'password' => 'doctrinepass',
    'host'     => 'localhost',
    'port'     => '3306',
    'dbname'   => 'videoViewer'
);

$em = EntityManager::create($connectionOptions, $config);
$di = new DIContainer($em);


?>