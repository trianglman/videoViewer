<?php
namespace videoViewer;
/**
 * Holds an Episode record from TVDB
 *
 * @author John Judy <john.a.judy@gmail.com>
 */
class TvDBEpisode implements TvDBEpisodeInterface {
    /**
     *
     * @var int
     */
    public $id;
    /**
     *
     * @var int
     */
    public $season;
    /**
     *
     * @var int
     */
    public $episode;
    /**
     *
     * @var DateTime
     */
    public $airDate;
    /**
     *
     * @var string
     */
    public $name;
    /**
     *
     * @var string
     */
    public $desc;

    /**
     * Gets the air date of the episode
     * 
     * @return \DateTime
     */
    public function getAirDate()
    {
        return $this->airDate;
    }
    
    /**
     * Gets the episode description
     * 
     * @return string
     */
    public function getDesc()
    {
        return $this->desc;
    }
    
    /**
     * Gets the episode number
     * 
     * @return int
     */
    public function getEpisode()
    {
        return $this->episode;
    }
    
    /**
     * Gets the season number
     * 
     * @return int
     */
    public function getSeason()
    {
        return $this->season;
    }
    
    /**
     * Gets the episode name
     * 
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * Initializes the Episode based on a TvDb Episode object
     * 
     * @param \TvDb\Episode $ep 
     * 
     * @return void
     */
    public function populateFromEpisode(\TvDb\Episode $ep)
    {
        $this->airDate = $ep->firstAired;
        $this->desc = $ep->overview;
        $this->episode = $ep->number;
        $this->id = $ep->id;
        $this->name = $ep->name;
        $this->season = $ep->season;
    }

}
?>