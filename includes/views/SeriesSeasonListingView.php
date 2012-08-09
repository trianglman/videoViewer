<?php
namespace videoViewer\views;

/**
 * Page view for the index page
 *
 * @author John Judy <john.a.judy@gmail.com>
 */
class SeriesSeasonListingView extends PageView{
    public $seriesName = '';
    public $seriesId = 0;
    public $bannerURL = '';
    public $seriesDesc = '';
    public $seasons = array();
     
    /**
     * Initializes the Index page view
     * @param \videoViewer\DIContainer $di 
     * 
     * @return void
     */
    public function __construct(\videoViewer\DIContainer $di){
        $template = $di->loadTemplate('SeriesSeasonListing');
        parent::__construct($di,$template);
    }
}

?>
