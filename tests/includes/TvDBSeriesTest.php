<?php

namespace videoViewer;

use \Mockery as m;

/**
 * 
 */
class TvDBSeriesTest extends \PHPUnit_Framework_TestCase {

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    public function teardown() {
        m::close();
    }

    public function testLoadsFromSeries() 
    {
        $ser = m::mock('\TvDb\Serie');
        $ser->overview = 'A description';
        $ser->id = 1234;
        $ser->name = 'Series Name';
        
        $series = new TvDBSeries();
        $series->populateFromSeries($ser);
        $this->assertEquals(1234,$series->getSeriesId());
        $this->assertEquals('A description',$series->getDescription());
        $this->assertEquals('Series Name',$series->getSeriesName());
    }

    /**
     * @depends testLoadsFromSeries
     */
    public function testGetTVDBUrl() 
    {
        $ser = m::mock('\TvDb\Serie');
        $ser->overview = 'A description';
        $ser->id = 1234;
        $ser->name = 'Series Name';
        
        $series = new TvDBSeries();
        $series->populateFromSeries($ser);
        $this->assertEquals("http://www.thetvdb.com/?tab=series&id=1234",$series->getTVDBUrl());
    }
    
    /**
     * @depends testLoadsFromSeries
     */
    public function testGetEpisodeByEpisodeNumber()
    {
        $ser = m::mock('\TvDb\Serie');
        $ser->overview = 'A description';
        $ser->id = 1234;
        $ser->name = 'Series Name';
        
        $conn = m::mock('\videoViewer\TvDBApiConn');
        $ep = m::mock('\TvDb\Episode');
        $airDate = new \DateTime('1980-11-18');
        $ep->firstAired = $airDate;
        $ep->overview = 'A description';
        $ep->number = 1;
        $ep->id = 1234;
        $ep->name = 'Ep Name';
        $ep->season = 1;
        $conn->shouldReceive('getEpisode')->with(1234,1,1)->andReturn($ep);
        
        $series = new TvDBSeries();
        $series->populateFromSeries($ser);
        $series->setConn($conn);
        $this->assertEquals('Ep Name',
                            $series->getEpisodeByEpisodeNumber(1,1)->getName());
    }
    
    /**
     * @depends testGetEpisodeByEpisodeNumber
     */
    public function testGetEpisodeByAirDate()
    {
        $ser = m::mock('\TvDb\Serie');
        $ser->overview = 'A description';
        $ser->id = 1234;
        $ser->name = 'Series Name';
        
        $conn = m::mock('\videoViewer\TvDBApiConn');
        $ep = m::mock('\TvDb\Episode');
        $airDate = new \DateTime('1980-11-18');
        $ep->firstAired = $airDate;
        $ep->overview = 'A description';
        $ep->number = 1;
        $ep->id = 1234;
        $ep->name = 'Ep Name';
        $ep->season = 1;
        $ep2 = m::mock('\TvDb\Episode');
        $airDate2 = new \DateTime('1980-05-03');
        $ep2->firstAired = $airDate2;
        $ep2->overview = 'A description';
        $ep2->number = 1;
        $ep2->id = 1234;
        $ep2->name = 'Right Ep Name';
        $ep2->season = 1;
        $conn->shouldReceive('getSerieEpisodes')->with(1234)->andReturn(array('serie'=>$ser,'episodes'=>array($ep,$ep2)));
        
        $series = new TvDBSeries();
        $series->populateFromSeries($ser);
        $series->setConn($conn);
        $this->assertEquals('Right Ep Name',
                            $series->getEpisodeByAirDate(new \DateTime('1980-05-03'))->getName());
    }

}