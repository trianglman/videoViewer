<?php

namespace videoViewer;

use \Mockery as m;

/**
 * 
 */
class TvDBEpisodeTest extends \PHPUnit_Framework_TestCase {

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

    public function testLoadsFromEpisode() {
        $ep = m::mock('\TvDb\Episode');
        $airDate = new \DateTime('1980-11-18');
        $ep->firstAired = $airDate;
        $ep->overview = 'A description';
        $ep->number = 1;
        $ep->id = 1234;
        $ep->name = 'Ep Name';
        $ep->season = 1;
        
        $episode = new TvDBEpisode();
        $episode->populateFromEpisode($ep);
        $this->assertEquals($airDate,$episode->getAirDate());
        $this->assertEquals('A description',$episode->getDesc());
        $this->assertEquals(1,$episode->getEpisode());
        $this->assertEquals('Ep Name',$episode->getName());
        $this->assertEquals(1,$episode->getSeason());
    }

}