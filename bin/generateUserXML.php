<?php

namespace videoViewer;
require_once(dirname(__FILE__).'/../includes/config.php');
$query = $em->createQuery("SELECT u FROM videoViewer\Entities\User u");
$users = $query->getResult();

$outputPath = dirname(__FILE__).'/..';

foreach($users as $user){
    /* @var $user videoViewer\Entities\User */
    $viddb = new \SimpleXMLElement('<xml><viddb></viddb></xml>');
    foreach($user->getAuthorizedSeries() as $series){
        foreach($series->getEpisodes() as $episode){
            $currMovie = $viddb->viddb[0]->addChild('movie');
            $currMovie->addChild('origtitle', htmlentities($episode->getEpisodeName()));
            $currMovie->addChild('year', $episode->getAirDate()->format('Y'));
            $currMovie->addChild('description', $episode->getDetails());
            $path = 'http://trianglman.dyndns-ip.com/videoViewer/videos/'
                        .$user->getUserHash().'/'.$episode->getFileName('mp4');
            $currMovie->addChild('path', $path);
            $currMovie->addChild('videocodec', 'mp4');
            $currMovie->addChild('poster',$episode->getThumbnail($di));
            $genre = '[TV/'.$series->getName()
                    .'/Season '.$episode->getSeasonNumber().'],';
            if(!$user->getWatchedVideos()->contains($episode)){
                $genre.= '[Unwatched TV/'.$series->getName()
                        .'/Season '.$episode->getSeasonNumber().']';
            }
            else{
                $genre.= '[Watched TV/'.$series->getName()
                        .'/Season '.$episode->getSeasonNumber().']';
            }
            $currMovie->addChild('genre', $genre);
        }
    }
    $viddb->asXML($outputPath.$user->getRokuXML());
}

?>