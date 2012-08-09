<?php
namespace videoViewer\views;

/**
 * Description of UserHomeView
 *
 * @author John Judy <john.a.judy@gmail.com>
 */
class UserHomeView  extends PageView{
    public $name = '';
    public $series = array();
    public $admin = false;
    public $hasRoku = false;
    public $rokuUrl = '';
    
    /**
     * Initializes the Index page view
     * @param \videoViewer\DIContainer $di 
     * 
     * @return void
     */
    public function __construct(\videoViewer\DIContainer $di){
        $template = $di->loadTemplate('userHome');
        parent::__construct($di,$template);
    }
    
    public function setSeries($seriesArray,$user){
        foreach($seriesArray as $series){
            $temp = new \stdClass();
            $temp->seriesId = $series->getId();
            $temp->seriesName = $series->getName();
            $unwatched = 0;
            foreach($series->getEpisodes() as $episode){
                if(!$user->getWatchedVideos()->contains($episode)){
                    $unwatched++;
                }
            }
            $temp->unwatchedCount = $unwatched;
            $temp->pluralize = $unwatched!=1?'s':'';
            $this->series[]=$temp;
        }
    }
    
    public function noSeries(){
        return count($this->series)==0;
    }
}

?>
