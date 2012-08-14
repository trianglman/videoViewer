<?php
namespace videoViewer\views;

/**
 * A view listing all the episodes in a season
 *
 * @author John Judy <john.a.judy@gmail.com>
 */
class SeriesSeasonEpisodesView extends PageView {
    
    /**
     * The series name
     * @var string
     */
    public $seriesName='';
    
    /**
     * The season selected to be listed
     * @var int
     */
    public $selectedSeason=0;
    
    /**
     * The path to the series's banner image
     * @var string
     */
    public $seriesImage='';
    
    /**
     * The set of episodes from this season
     * @var array[int]\stdClass
     */
    public $episodes=array();

    /**
     * Initializes the Index page view
     * @param \videoViewer\DIContainer $di 
     * 
     * @return void
     */
    public function __construct(\videoViewer\DIContainer $di) {
        $template = $di->loadTemplate('SeriesSeasonEpisodes');
        parent::__construct($di, $template);
    }
    
    /**
     * Override the parent render() in order to sort the episodes for display
     * 
     * @return string
     */
    public function render($template = NULL, $view = NULL, $partials = NULL)
    {
        usort($this->episodes, function($a,$b)
        {
            if($a->sortDate==$b->sortDate)
            {
                return 0;
            }
            return $a->sortDate>$b->sortDate?1:-1;
        });
        return parent::render($template,$view,$partials);
    }
    
    /**
     * Adds an episode to the season listing
     * 
     * @param \videoViewer\Entities\Video $episode 
     * 
     * @return void
     */
    public function addEpisode(\videoViewer\Entities\Video $episode)
    {
        $opt = new \stdClass();
        $opt->episodeNumber = $episode->getEpisodeNumber();
        $opt->videoId = $episode->getId();
        $opt->episodeName = $episode->getEpisodeName();
        $opt->airDate = $episode->getAirDate()->format('F j, Y');
        $opt->sortDate = $episode->getAirDate()->getTimestamp();
        $this->episodes[] = $opt;
    }

}

?>