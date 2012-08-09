<?php

namespace videoViewer\views;

/**
 * Description of View
 *
 * @author John Judy <john.a.judy@gmail.com>
 */
class CreateSeriesView extends PageView {
    
    public $seriesOpt=array();
    public $videoId = 0;

    /**
     * Initializes the Index page view
     * @param \videoViewer\DIContainer $di 
     * 
     * @return void
     */
    public function __construct(\videoViewer\DIContainer $di) {
        $template = $di->loadTemplate('CreateSeries');
        parent::__construct($di, $template);
    }
    
    public function addSeriesOpt($opt){
        $seriesOpt = new \stdClass();
        $seriesOpt->seriesId = $opt->seriesid;
        $seriesOpt->TVDBUrl = $opt->getTVDBUrl();
        $seriesOpt->name = $opt->name;
        $this->seriesOpt[]=$seriesOpt;
    }

}

?>