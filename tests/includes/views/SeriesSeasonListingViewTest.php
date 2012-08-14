<?php

namespace videoViewer\views;

use \Mockery as m;

/**
 * 
 */
class SeriesSeasonListingViewTest extends \PHPUnit_Framework_TestCase {
    
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
        $this->tpl = file_get_contents(dirname(__FILE__).'/../../../templates/SeriesSeasonListing.tpl');
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    public function teardown() {
        m::close();
    }

    public function testSeriesSeasonListingTwoEpisodes() {
        $expected = file_get_contents(dirname(__FILE__).'/../../assets/html/SeriesSeasonListing_twoSeasons.html');
        $this->di->shouldReceive('loadTemplate')->with('SeriesSeasonListing')->andReturn($this->tpl);
        $view = new \videoViewer\views\SeriesSeasonListingView($this->di);
        $view->bannerURL = 'http://localhost/banners/1234.jpg';
        $view->seasons = array(array('seasonNumber'=>1,'epCount'=>5),array('seasonNumber'=>2,'epCount'=>10));
        $view->seriesDesc = "This is the first series' description.";
        $view->seriesId = 1;
        $view->seriesName = 'Series 1';
        $this->assertEquals($expected,$view->render());
    }

}