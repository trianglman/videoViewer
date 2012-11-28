<?php
namespace videoViewer;

/**
 * An interface to connect to theTvDB.com data
 * 
 * @author John Judy <john.a.judy@gmail.com>
 */
interface TvDBConnInterface
{
    /**
     *
     * Looks up a series from theTVDB, returning a set of possible matches
     * 
     * @param string $seriesName The name of the series to look up
     * 
     * @return array[int]TvDBSeries
     */
    public function findSeries($seriesName);
    
    /**
     *
     * Gets the file contents of a TVDB banner
     * 
     * @param int $seriesId The ID of the series to retrieve the banner for
     * 
     * @return string
     */
    public function getBanner($seriesId);
    
    /**
     *
     * Gets a series including it's episodes
     * 
     * @param int $seriesId
     * 
     * @return TvDBSeries
     */
    public function getFullSeriesInformation($seriesId);
    
}
?>
