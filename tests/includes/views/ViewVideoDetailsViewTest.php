<?php

namespace videoViewer\views;

use \Mockery as m;

/**
 * 
 */
class ViewVideoDetailsViewTest extends \PHPUnit_Framework_TestCase {
    
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
        $this->tpl = file_get_contents(dirname(__FILE__).'/../../../templates/ViewVideoDetails.tpl');
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    public function teardown() {
        m::close();
    }

    public function testViewVideoDetails() 
    {
        $expected = file_get_contents(dirname(__FILE__).'/../../assets/html/ViewVideoDetails.html');
        $this->di->shouldReceive('loadTemplate')->with('ViewVideoDetails')->andReturn($this->tpl);
        $series = m::mock('\videoViewer\Entities\Series');
        $series->shouldReceive('getName')->andReturn('Series 1');
        $ep = m::mock('\videoViewer\Entities\Video');
        $ep->shouldReceive('getWebPath')->with('ogg',$this->di)->andReturn('videos/video.1.ogv');
        $ep->shouldReceive('getWebPath')->with('mp4',$this->di)->andReturn('videos/video.1.mp4');
        $ep->shouldReceive('getSeries')->andReturn($series);
        $ep->shouldReceive('getSeasonNumber')->andReturn(1);
        $ep->shouldReceive('getEpisodeNumber')->andReturn(1);
        $ep->shouldReceive('getAirDate')->andReturn(new \DateTime('November 18, 1980'));
        $ep->shouldReceive('getDetails')->andReturn('These are some details about the video.');
        $ep->shouldReceive('getNotes')->andReturn('These are my notes.');
        $ep->shouldReceive('getId')->andReturn(1);
        $ep->shouldReceive('__toString')->andReturn('Episode 1');
        $view = new \videoViewer\views\ViewVideoDetailsView($this->di);
        $view->video = $ep;
        $this->assertEquals($expected,$view->render());
    }

}