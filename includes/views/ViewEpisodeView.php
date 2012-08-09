<?php

namespace videoViewer\views;

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of View
 *
 * @author John Judy <john.a.judy@gmail.com>
 */
class ViewEpisodeView extends PageView {
    
    public $episodeName='';
    public $seriesName='';
    public $oggPath='';
    public $mp4Path='';
    public $isAdmin=false;
    public $videoId=0;

    /**
     * Initializes the Index page view
     * @param \videoViewer\DIContainer $di 
     * 
     * @return void
     */
    public function __construct(\videoViewer\DIContainer $di) {
        $template = $di->loadTemplate('ViewEpisode');
        parent::__construct($di, $template);
    }

}

?>