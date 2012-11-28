<?php
namespace videoViewer;
/**
 * Holds an Episode record from TVDB
 *
 * @author John Judy <john.a.judy@gmail.com>
 */
interface TvDBEpisodeInterface
{
    
    /**
     * Gets the air date of the episode
     * 
     * @return \DateTime
     */
    public function getAirDate();
    
    /**
     * Gets the episode description
     * 
     * @return string
     */
    public function getDesc();
    
    /**
     * Gets the episode number
     * 
     * @return int
     */
    public function getEpisode();
    
    /**
     * Gets the season number
     * 
     * @return int
     */
    public function getSeason();
    
    /**
     * Gets the episode name
     * 
     * @return string
     */
    public function getName();
}
?>