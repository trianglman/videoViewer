<?php
namespace videoViewer;
/**
 * Holds an Episode record from TVDB
 *
 * @author John Judy <john.a.judy@gmail.com>
 */
class TvDBEpisode {
    /**
     *
     * @var int
     */
    public $id;
    /**
     *
     * @var int
     */
    public $seasonId;
    /**
     *
     * @var int
     */
    public $seriesId;
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
     * @var array[int]string
     */
    public $guestStars;

    /**
     *
     * Loads the object based on a supplied XML
     * 
     * @param \SimpleXMLElement $xml 
     */
    public function loadFromSimpleXML(\SimpleXMLElement $xml){
        $this->id = $xml->id;
        $this->seasonId = (int)$xml->seasonid;
        $this->seriesId = (int)$xml->seriesid;
        $this->season = (int)$xml->SeasonNumber;
        $this->episode = (int)$xml->EpisodeNumber;
        $this->airDate = new \DateTime($xml->FirstAired);
        $this->name = (string)$xml->EpisodeName;
        $this->IMDBID = (string)$xml->IMDB_ID;
        $this->language = (string)$xml->Language;
        $this->desc = (string)$xml->Overview;
        $guestStars = \array_filter(\explode('|', $xml->GuestStars));
        foreach($guestStars as $star){$this->guestStars[]=$star;}
    }

    /**
     * Gets the URL for the show on TVDB
     * 
     * @return string
     */
    public function getTVDBUrl(){
        return 'http://www.thetvdb.com/?tab=episode&seriesid='.$this->seriesId
                .'&seasonid='.$this->seasonId.'&id='.$this->id.'&lid=7';
    }
}
?>
