<?php
namespace videoViewer\views;

/**
 * Description of UserHomeView
 *
 * @author John Judy <john.a.judy@gmail.com>
 */
class CreateAliasView  extends PageView{
    public $video = '';
    public $series = array();
    public $videoId = 0;
    public $error = false;
    public $defaultSeries = 0;
    public $defaultAlias = '';
    public $seriesHasAlias=false;
    
    /**
     * Initializes the Index page view
     * @param \videoViewer\DIContainer $di 
     * 
     * @return void
     */
    public function __construct(\videoViewer\DIContainer $di){
        $template = $di->loadTemplate('CreateAlias');
        parent::__construct($di,$template);
    }
    
    public function setSeries($seriesArray){
        foreach($seriesArray as $series){
            $temp = new \stdClass();
            $temp->seriesId = $series->getId();
            $temp->seriesName = $series->getName();
            if($series->getId()==$this->defaultSeries){
                $temp->selected = 'selected="selcted"';
            }
            else{$temp->selected='';}
            $this->series[]=$temp;
        }
        
    }
}

?>
