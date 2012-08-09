<?php
namespace videoViewer;
/**
 * A class to wrap the connection details with the TVDB
 *
 * @author John Judy <john.a.judy@gmail.com>
 */
class TvDBApiConn {
    /**
     *
     * @var string
     */
    protected $mirrorList;
    /**
     *
     * @var DateTime
     */
    protected $serverTime;
    /**
     * The API key for
     */
    const TVDB_API_KEY='AFF0D45A8B89163F';

    /**
     * Checks that the API is available and gets some basic information needed in future requests
     *
     * @return void
     */
    public function __construct(){
        $mirrorListPath = 'http://www.thetvdb.com/api/'.self::TVDB_API_KEY.'/mirrors.xml';
        $mirrorXML = \file_get_contents($mirrorListPath);
        if($mirrorXML === false){
            throw new Exception("The TVDB API appears to be unavailable. Please try again later.");
        }
        $parsedMirrorXML = \simplexml_load_string($mirrorXML);
        $this->mirrorList = $parsedMirrorXML->Mirror->mirrorpath;
        $remoteServerTimeXML = \simplexml_load_file('http://www.thetvdb.com/api/Updates.php?type=none');
        $this->serverTime = new \DateTime();
        $this->serverTime->setTimestamp((int)$remoteServerTimeXML->Time);
    }

    /**
     *
     * Looks up a series from theTVDB, returning a set of possible matches
     * 
     * @param string $seriesName The name of the series to look up
     * @return array[int]TvDBSeries
     */
    public function findSeries($seriesName){
        $seriesLookupPath = 'http://www.thetvdb.com/api/GetSeries.php?language=en&seriesname='.$seriesName;
        $listing = \simplexml_load_file($seriesLookupPath);
        $returnArray = array();
        foreach($listing->Series as $series){
            $obj = new TvDBSeries();
            $obj->loadFromSimpleXML($series,true);
            $returnArray[]=$obj;
        }
        return $returnArray;
    }

    /**
     *
     * Gets the file contents of a TVDB banner
     * @param string $bannerPath The final part of the banner URL
     * @return string
     */
    public function getBanner($bannerPath){
        return \file_get_contents($this->mirrorList.'/banners/'.$bannerPath);
    }

    /**
     *
     * Loads the full series information for a given series
     * @param int $seriesId
     * @return TvDBSeries
     */
    public function getSeriesInformation($seriesId){
        $seriesXML = \simplexml_load_string($this->_getSeriesXML($seriesId));
        $series = new TvDBSeries();
        return $series->loadFromSimpleXML($seriesXML->Series, false);
    }

    /**
     *
     * Gets an array of all the series's episodes
     * @param int $seriesId
     * @return array[int]TvDBEpisode
     */
    public function getSeriesEpisodes($seriesId){
        $seriesXML = \simplexml_load_string($this->_getSeriesXML($seriesId));
        $returnArray = array();
        foreach($seriesXML->Episode as $epXml){
            $ep = new TvDBEpisode();
            $ep->loadFromSimpleXML($epXml);
            $returnArray[]=$ep;
        }
        return $returnArray;
    }

    /**
     *
     * Gets a series including it's episodes
     * 
     * @param int $seriesId
     * @return TvDBSeries
     */
    public function getFullSeriesInformation($seriesId){
        $seriesXML = \simplexml_load_string($this->_getSeriesXML($seriesId));
        $series = new TvDBSeries();
        $series->loadFromSimpleXML($seriesXML->Series, false);
        foreach($seriesXML->Episode as $epXml){
            $ep = new TvDBEpisode();
            $ep->loadFromSimpleXML($epXml);
            $series->episodes[]=$ep;
        }
        return $series;
    }

    /**
     *
     * Gets the full series XML string either from the cached file or loads it from theTVDB
     * @param int $seriesId
     * @return string
     */
    protected function _getSeriesXML($seriesId){
        $xmlCachePath = dirname(__FILE__).'/../tvdbxml/'.$seriesId.'.xml';
        if(!\file_exists($xmlCachePath)){
            $seriesXML = $this->_getNewSeriesXML($seriesId, $xmlCachePath);
        }
        else{
            $lastUpdated = new \DateTime();
            $lastUpdated->setTimestamp(\filectime($xmlCachePath));
            $diff = $this->serverTime->diff($lastUpdated, true);
            if(true || $diff->format('%a') >= 2){//if the difference is more than 2 days
                $updates = \file_get_contents($this->mirrorList.'/api/Updates.php?type=series&time='.$this->serverTime->format('U'));
                if($updates===false){
                    throw new Exception('Failed to check for updates');
                }
                $updateXML = \simplexml_load_string($updates);
                $check = false;
                if(count($updateXML->Series)==0){$check=true;}
                elseif(count($updateXML->Series)<1000){
                    foreach($updateXML->Series as $series){if($series==$seriesId){$check=true;break;}}
                }
                else{$check=true;}//if there are 1000 values, some updates won't be listed
                if($check){$seriesXML = $this->_getNewSeriesXML($seriesId, $xmlCachePath);}
                else{$seriesXML = \file_get_contents($xmlCachePath);}
            }
        }
        return $seriesXML;
    }

    /**
     *
     * Gets and saves the newest version of the series's XML listing
     * @param string $seriesId The ID of the series to look up
     * @param string $cachePath The path to cache the file to
     *
     * @return string The downloaded XML string
     *
     * @throws Exception If there was an error downloading the XML file
     */
    protected function _getNewSeriesXML($seriesId,$cachePath){
        $seriesXML = \file_get_contents($this->mirrorList.'/api/'.self::TVDB_API_KEY.'/series/'.$seriesId.'/all/en.xml');
        if($seriesXML===false){
            throw new Exception("Failed to locate the series: ".$seriesId);
        }
        else{
            \file_put_contents($cachePath, $seriesXML);
            //sets the modified time to the timestamp retrieved from the remote server
            //used later for update requests
            \touch($cachePath, $this->serverTime->getTimestamp());
        }
        return $seriesXML;
    }
}
?>
