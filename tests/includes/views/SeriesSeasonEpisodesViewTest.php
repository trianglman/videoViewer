<?php

namespace videoViewer\views;

use \Mockery as m;

/**
 * 
 */
class SeriesSeasonEpisodesViewTest extends \PHPUnit_Framework_TestCase {
    
    public $values = array();
    
    public $di=null;
    
    public $tpl = '';

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {

        $this->di = m::mock('\videoViewer\DIContainer');
        $head = file_get_contents(dirname(__FILE__).'/../../../templates/head.tpl');
        $nav = file_get_contents(dirname(__FILE__).'/../../../templates/nav.tpl');
        $this->di->shouldReceive('loadTemplate')->with('head')->andReturn($head);
        $this->di->shouldReceive('loadTemplate')->with('nav')->andReturn($nav);
        $this->tpl = file_get_contents(dirname(__FILE__).'/../../../templates/SeriesSeasonEpisodes.tpl');
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    public function teardown() {
        m::close();
    }

    public function testSeriesSeasonEpisodesTwoEpisodes() {
        $expected = file_get_contents(dirname(__FILE__).'/../../assets/html/SeriesSeasonEpisodes_twoEpisodes.html');
        $this->di->shouldReceive('loadTemplate')->with('SeriesSeasonEpisodes')->andReturn($this->tpl);
        $view = new \videoViewer\views\SeriesSeasonEpisodesView($this->di);
        $view->seriesName = 'Series 1';
        $view->selectedSeason = 1;
        $view->seriesImage = 'http://localhost/banners/1234.jpg';
        $ep = m::mock('\videoViewer\Entities\Video');
        $ep->shouldReceive('getId')->andReturn(1);
        $ep->shouldReceive('getEpisodeName')->andReturn('Episode 1');
        $ep->shouldReceive('getEpisodeNumber')->andReturn(1);
        $ep->shouldReceive('getAirDate')->andReturn(new \DateTime('May 3, 1980'));
        $view->addEpisode($ep);
        $ep = m::mock('\videoViewer\Entities\Video');
        $ep->shouldReceive('getId')->andReturn(2);
        $ep->shouldReceive('getEpisodeName')->andReturn('Episode 2');
        $ep->shouldReceive('getEpisodeNumber')->andReturn(2);
        $ep->shouldReceive('getAirDate')->andReturn(new \DateTime('November 18, 1980'));
        $view->addEpisode($ep);
        $this->assertEquals($expected,$view->render());
    }

    public function testSeriesSeasonEpisodesTwoEpisodesResort() {
        $expected = file_get_contents(dirname(__FILE__).'/../../assets/html/SeriesSeasonEpisodes_twoEpisodes.html');
        $this->di->shouldReceive('loadTemplate')->with('SeriesSeasonEpisodes')->andReturn($this->tpl);
        $view = new \videoViewer\views\SeriesSeasonEpisodesView($this->di);
        $view->seriesName = 'Series 1';
        $view->selectedSeason = 1;
        $view->seriesImage = 'http://localhost/banners/1234.jpg';
        $ep = m::mock('\videoViewer\Entities\Video');
        $ep->shouldReceive('getId')->andReturn(2);
        $ep->shouldReceive('getEpisodeName')->andReturn('Episode 2');
        $ep->shouldReceive('getEpisodeNumber')->andReturn(2);
        $ep->shouldReceive('getAirDate')->andReturn(new \DateTime('November 18, 1980'));
        $view->addEpisode($ep);
        $ep = m::mock('\videoViewer\Entities\Video');
        $ep->shouldReceive('getId')->andReturn(1);
        $ep->shouldReceive('getEpisodeName')->andReturn('Episode 1');
        $ep->shouldReceive('getEpisodeNumber')->andReturn(1);
        $ep->shouldReceive('getAirDate')->andReturn(new \DateTime('May 3, 1980'));
        $view->addEpisode($ep);
        $this->assertEquals($expected,$view->render());
    }

}