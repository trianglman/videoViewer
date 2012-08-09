<?php
namespace videoViewer;
require_once(dirname(__FILE__).'/../includes/config.php');
use \apacheLogParser as a;
use \Doctrine\ORM\EntityNotFoundException;

//Allows testing or to be included elsewhere
if((isset($_SERVER['REQUEST_URL']) 
        && pathinfo(__FILE__,PATHINFO_BASENAME)==
        pathinfo($_SERVER['REQUEST_URL'],PATHINFO_BASENAME)) ||
    (isset($argv[0])) 
        && pathinfo(__FILE__,PATHINFO_BASENAME)==pathinfo($argv[0],PATHINFO_BASENAME)){
    echo runProcessApacheAccess($em);
}

function runProcessApacheAccess(\Doctrine\ORM\EntityManager $em){
    $lockfile = '/tmp/processApacheAccess.lock';
    if(file_exists($lockfile)){exit();}
    $logFile = '/usr/local/apache2/logs/videoViews.access_log';
    $logFormat = '%h %l %u %t \\"%r\\" %>s %b';
    
    if(file_exists($logFile)){
        copy($logFile,$lockfile);//move it so it's not in use while processing
        file_put_contents($logFile,'');
        $log = new a\ApacheLogFile(file_get_contents($lockfile),$logFormat);

        processLogFile($log,
                $em->getRepository('videoViewer\Entities\Video'),
                $em->getRepository('videoViewer\Entities\User'));
        unlink($lockfile);
    }
    $em->flush();
}

function processLogFile(\apacheLogParser\ApacheLogFile $log,
        Repositories\VideoRepository $videoRepo,
        Repositories\UserRepository $userRepo){
    $foundFiles = array();
    $loadedUsers = array();
    foreach($log->getRow() as $entry){
        /* @var $entry \apacheLogParser\ApacheLogRecord */
        if(strlen($entry->getRequest())==0 || 
            in_array($entry->getRequest(),$foundFiles) ||
            $entry->getLastStatus() <200 || $entry->getLastStatus() > 299){
            continue;
        }
        $foundFiles[]=$entry->getRequest();
        $req = explode(' ',$entry->getRequest());
        $reqFile = $req[1];
        $reqFileParts = explode('/',$reqFile);
        $partCount = count($reqFileParts);
        $filename = urldecode($reqFileParts[$partCount-1]);
        $userHash = $reqFileParts[$partCount-2];
        if(isset($loadedUsers[$userHash])){$user = $loadedUsers[$userHash];}
        else{
            /* @var $user \VideoViewer\Entities\User */
            try{
                $user = $userRepo->getUserByHash($userHash);
                $loadedUsers[$userHash]=$user;
            }
            catch(EntityNotFoundException $e){echo "invalid user\n";continue;}
        }
        /* @var $video \VideoViewer\Entities\Video */
        try{$video = $videoRepo->getByFilename(substr($filename,0,-4));}
        catch(EntityNotFoundException $e){echo "invalid video\n";continue;}
        $user->addWatchedVideo($video);
        echo "Adding ".$video->getSeries()->getName().': '.$video->getEpisodeName()
                .' to '.$user->getName()."'s watched list.\n";
    }
}
?>
