<?php
namespace videoViewer;
require_once(dirname(__FILE__).'/../includes/config.php');
if(file_exists('/tmp/storeNewVideo.lock')){exit();}
touch('/tmp/storeNewVideo.lock');


//check for .done files in ./videos/
$videoPath = dirname(__FILE__).'/../'.Entities\Video::VIDEO_DIR;
$videoDir = \opendir($videoPath);
$newVideos = array();
while(($file=  \readdir($videoDir))!==false){
    if(\pathinfo($file, \PATHINFO_EXTENSION)=='done'){
        try{$parsedFileName = new FileNameParser($file);}
        catch(\Exception $e){
            //log the video as an unmatched video
            logUnmatchedVideo($em,$file);unlink($videoPath.$file);
            unlink($videoPath.$file);
            continue;
        }
        $query = $em->createQuery("SELECT s FROM videoViewer\Entities\Series s
            JOIN s.aliases a WHERE a.alias=:alias");
        $query->setParameter('alias', $parsedFileName->series);
        $series = $query->getResult();
        if(count($series)!=1){
            //log the video as an unmatched video
            logUnmatchedVideo($em,$file,$parsedFileName);
        }
        else{
            //it's a matched series, locate the episode information
            processNewVideo($em,$file,$series[0],$parsedFileName);
        }

        \unlink($videoPath.$file);//remove it even if there's a problem so it doesn't keep getting processed
    }
}
unlink('/tmp/storeNewVideo.lock');

function logUnmatchedVideo(\Doctrine\ORM\EntityManager $em,$fileName,  FileNameParser $fileParser=null){
    $video = new Entities\Video();
    $video->setFileNameBase(pathinfo($fileName,\PATHINFO_FILENAME));
    if(\is_null($fileParser)){
        $video->setNotes('The file name could not be parsed. Please add a parser method for this format.');
    }
    else{
        $video->setNotes('This series could not be found. Please create a new alias or series.'
                ."\nParsed Series Name: ".$fileParser->series);
    }
    $query = $em->createQuery("SELECT s FROM videoViewer\Entities\Series s WHERE s.name=:name");
    $query->setParameter('name', 'Unmatched Videos');
    $series = $query->getSingleResult();
    $series->addEpisode($video);

    $em->persist($video);
    $em->flush();
}

function processNewVideo(\Doctrine\ORM\EntityManager $em,$fileName,
                        Entities\Series $series,
                        FileNameParser $parsedFileName){
    //check for the episode details in the series
    $tvdb = new TvDBApiConn();
    $tvdbSeries = $tvdb->getFullSeriesInformation($series->getSeriesId());
    if(!empty($parsedFileName->episode)){
        $episode = $tvdbSeries->getEpisodeByEpisodeNumber($parsedFileName->season,
                $parsedFileName->episode);
    }
    elseif(!empty($parsedFileName->airDate)){
        $episode = $tvdbSeries->getEpisodeByAirDate($parsedFileName->airDate);
    }
    //if the details are found, apply them
    if(!empty($episode)){
        $video = new Entities\Video();
        $video->setFileNameBase(pathinfo($fileName,\PATHINFO_FILENAME));
        $video->setAirDate($episode->airDate);
        $video->setDetails($episode->desc);
        $video->setEpisodeName($episode->name);
        $video->setEpisodeNumber($episode->episode);
        $video->setNotes($parsedFileName->extraInfo);
        $video->setSeasonNumber($episode->season);
        $series->addEpisode($video);

        $em->persist($video);
        $em->flush();
    }
    //otherwise return details not matched
    else{return logUnmatchedVideo($em,$fileName,$fileParser);}
}

?>
