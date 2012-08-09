<?php

namespace videoViewer\views;

/**
 * View for the full series listing
 *
 * @author John Judy <john.a.judy@gmail.com>
 */
class ListAllSeriesView extends PageView {
    
    public $seriesOpt=array();

    /**
     * Initializes the Index page view
     * @param \videoViewer\DIContainer $di 
     * 
     * @return void
     */
    public function __construct(\videoViewer\DIContainer $di) 
    {
        $template = $di->loadTemplate('ListAllSeries');
        parent::__construct($di, $template);
    }
    
    public function addSeries(\videoViewer\Entities\Series $series)
    {
        $opt = new \stdClass();
        $opt->unmatched = $series->getId()==1;
        $opt->seriesId = $series->getId();
        $opt->seriesName = $series->getName();
        $this->seriesOpt[]=$opt;
    }

}

?>