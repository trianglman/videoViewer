<?php
namespace videoViewer;
/**
 * Holds a Series record from TVDB
 *
 * @author John Judy <john.a.judy@gmail.com>
 */
class TvDBSeries implements TvDBSeriesInterface{
    /**
     *
     * The TVDB series ID
     * @var int
     */
    public $seriesid;
    /**
     *
     * @var string
     */
    public $desc;
    /**
     *
     * @var string
     */
    public $name;
    /**
     *
     * An array of all the series's episodes
     * @var array[int]TvDBEpisode
     */
    public $episodes=array();
    
    /**
     * The connection to the TvDB API
     * @var \videoViewer\TvDBApiConn
     */
    protected $conn = null;

    public function __construct()
    {

    }

    /**
     * Gets the TVDB series ID for the series
     * 
     * @return int
     */
    public function getSeriesId()
    {
        return $this->seriesid;
    }
    
    /**
     * Gets the TVDB.com URL for the series
     * 
     * @return string
     */
    public function getTVDBUrl()
    {
        return "http://www.thetvdb.com/?tab=series&id=".$this->seriesid;
    }

    /**
     * Gets the full name of the series
     * 
     * @return string
     */
    public function getSeriesName()
    {
        return $this->name;
    }
    
    /**
     * Gets the description of the series
     * 
     * @return string
     */
    public function getDescription()
    {
        return $this->desc;
    }
    
    /**
     * Gets an episode by the season and episode number
     * 
     * @param int $season
     * @param int $episode
     * 
     * @return \videoViewer\TvDBEpisodeInterface
     */
    public function getEpisodeByEpisodeNumber($season,$episode)
    {
        if(is_null($this->conn)){
            return null;
        }
        $ep = $this->conn->getEpisode($this->seriesid, $season, $episode);
        $episode = new TvDBEpisode();
        $episode->populateFromEpisode($ep);
        return $episode;
    }
    
    /**
     * Gets an episode by its air date
     * 
     * @param \DateTime $airDate
     * 
     * @return \videoViewer\TvDBEpisodeInterface
     */
    public function getEpisodeByAirDate(\DateTime $airDate)
    {
        if(is_null($this->conn)){
            return null;
        }
        $allEps = $this->conn->getSerieEpisodes($this->seriesid);
        foreach($allEps['episodes'] as $ep){
            if($airDate->format('Y-m-d')===$ep->firstAired->format('Y-m-d')){
                $episode = new TvDBEpisode();
                $episode->populateFromEpisode($ep);
                return $episode;
            }
        }
        return null;
    }
    
    /**
     * Sets the TVDB API connection
     * 
     * @param TvDBApiConn $conn 
     * 
     * @return void
     */
    public function setConn(TvDBApiConn $conn)
    {
        $this->conn = $conn;
    }
    
    /**
     * Initializes the Series based on a TvDb Serie object
     * 
     * @param \TvDb\Serie $series 
     * 
     * @return void
     */
    public function populateFromSeries(\TvDb\Serie $series)
    {
        $this->seriesid = $series->id;
        $this->desc = $series->overview;
        $this->name = $series->name;
    }

}
?>
