<?php
namespace videoViewer;
/**
 * Holds a Series record from TVDB
 *
 * @author John Judy <john.a.judy@gmail.com>
 */
class TvDBSeries {
    /**
     *
     * The TVDB series ID
     * @var int
     */
    public $seriesid;
    /**
     *
     * An array of actor names
     * @var array[int]string
     */
    public $actors=array();
    /**
     *
     * The first date this show aired
     * @var DateTime
     */
    public $firstAired;
    /**
     *
     * An array of genres this show is in
     * @var arrray[int]string
     */
    public $genres=array();
    /**
     *
     * The IMDB ID of the show
     * @var string
     */
    public $IMDBID;
    /**
     *
     * @var string
     */
    public $language;
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
     * The last part of the URL of the series banner on theTVDB.com
     * @var string
     */
    public $bannerUrl;
    /**
     *
     * An array of all the series's episodes
     * @var array[int]TvDBEpisode
     */
    public $episodes=array();

    public function __construct(){

    }

    /**
     *
     * Populates the object based on a SimpleXMLElement from the TVDB XML
     * @param \SimpleXMLElement $xml
     * @param boolean $abridged Whether to load the actors and genres or not
     * @return TvDBSeries
     */
    public function loadFromSimpleXML(\SimpleXMLElement $xml,$abridged){
        $this->seriesid = (string)$xml->seriesid;
        if(empty($this->seriesid)){$this->seriesid = (string)$xml->SeriesID;}
        $this->firstAired = new \DateTime($xml->FirstAired);
        $this->IMDBID = (string)$xml->IMDB_ID;
        $this->language = (string)$xml->language;
        if(empty($this->language)){$this->language = (string)$xml->Language;}
        $this->desc = (string)$xml->Overview;
        $this->name = (string)$xml->SeriesName;
        $this->bannerUrl = (string)$xml->banner;
        if(!$abridged){
            $actors = \explode('|', $xml->Actors);
            foreach(\array_filter($actors) as $actor){$this->actors[]=$actor;}
            $genres = \explode('|', $xml->Genre);
            foreach(\array_filter($genres) as $genre){$this->genres[]=$genre;}
        }
        return $this;
    }

    public function getTVDBUrl(){
        return "http://www.thetvdb.com/?tab=series&id=".$this->seriesid;
    }

    /**
     *
     * Searches through the set eiposodes for an episode with an air date within 24 hours of a given air date
     * @param \DateTime $airDate
     * @return TvDBEpisode
     */
    public function getEpisodeByAirDate(\DateTime $airDate){
        foreach($this->episodes as $setEpisode){
            if($airDate->diff($setEpisode->airDate, true)->format('%a') <1){
                return $setEpisode;
            }
        }
        return null;
    }

    /**
     *
     * Searches through the set episodes for an episode with the matching Season and Episode numbers
     * 
     * @param int $season
     * @param int $episode
     * @return TvDBEpisode
     */
    public function getEpisodeByEpisodeNumber($season,$episode){
        foreach($this->episodes as $setEpisode){
            if($setEpisode->season == $season && $setEpisode->episode==$episode){
                return $setEpisode;
            }
        }
        return null;
    }
    
    public function getSeriesId()
    {
        return $this->seriesid;
    }
    
    public function getSeriesName()
    {
        return $this->name;
    }
}
?>
