<?php
namespace videoViewer;
/**
 * Holds a Series record from TVDB
 *
 * @author John Judy <john.a.judy@gmail.com>
 */
interface TvDBSeriesInterface
{
    /**
     * Gets the TVDB series ID for the series
     * 
     * @return int
     */
    public function getSeriesId();
    
    /**
     * Gets the TVDB.com URL for the series
     * 
     * @return string
     */
    public function getTVDBUrl();
    
    /**
     * Gets the full name of the series
     * 
     * @return string
     */
    public function getSeriesName();
    
    /**
     * Gets the description of the series
     * 
     * @return string
     */
    public function getDescription();
    
    /**
     * Gets an episode by the season and episode number
     * 
     * @param int $season
     * @param int $episode
     * 
     * @return \videoViewer\TvDBEpisodeInterface
     */
    public function getEpisodeByEpisodeNumber($season,$episode);
    
    /**
     * Gets an episode by its air date
     * 
     * @param \DateTime $airDate
     * 
     * @return \videoViewer\TvDBEpisodeInterface
     */
    public function getEpisodeByAirDate(\DateTime $airDate);
}
?>
