<?php
namespace videoViewer;
/**
 * A class to wrap the connection details with the TVDB
 *
 * @author John Judy <john.a.judy@gmail.com>
 */
class TvDBApiConn extends \TvDb\Client implements TvDBConnInterface
{
    /**
     * The API key for
     */
    const TVDB_API_KEY='AFF0D45A8B89163F';

    /**
     * Initializes the connection
     * 
     * @return void
     */
    public function __construct()
    {
        parent::__construct('http://thetvdb.com',self::TVDB_API_KEY);
    }

    /**
     *
     * Looks up a series from theTVDB, returning a set of possible matches
     * 
     * @param string $seriesName The name of the series to look up
     * 
     * @return array[int]TvDBSeriesInterface
     */
    public function findSeries($seriesName)
    {
        $returnArray = array();
        foreach($this->getSeries($seriesName) as $series){
            $temp = new TvDBSeries();
            $temp->setConn($this);
            $temp->populateFromSeries($series);
            $returnArray[] = $temp;
        }
        return $returnArray;
    }

    /**
     *
     * Gets the file contents of a TVDB banner
     * 
     * @param int $seriesId The ID of the series to retrieve the banner for
     * 
     * @return string
     */
    public function getBanner($seriesId)
    {
        $allBanners = parent::getBanners($seriesId);
        return file_get_contents($allBanners[0]);//we only care about the first
    }

    /**
     *
     * Gets a series including it's episodes
     * $serieId
     * @param int $seriesId
     * @return TvDBSeries
     */
    public function getFullSeriesInformation($seriesId)
    {
        $temp = $this->getSerie($seriesId);
        $series = new TvDBSeries();
        $series->setConn($this);
        $series->populateFromSeries($temp);
        return $series;
    }

}
?>
