<?php

namespace videoViewer\views;

use \Mockery as m;

/**
 * 
 */
class EditVideoViewTest extends \PHPUnit_Framework_TestCase {
    
    public $values = array();
    
    public $di=null;
    
    public $tpl = '';

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() 
    {
        $video = m::mock('\videoViewer\Entities\Video');
        $video->shouldReceive('getId')->andReturn(1);
        $video->shouldReceive('getEpisodeName')->andReturn('Episode');
        $video->shouldReceive('getSeasonNumber')->andReturn(1);
        $video->shouldReceive('getEpisodeNumber')->andReturn(1);
        $video->shouldReceive('getAirDate')->andReturn(new \DateTime('11/18/1980'));
        $video->shouldReceive('getDetails')->andReturn('Details on the episode.');
        $video->shouldReceive('getNotes')->andReturn('A couple notes.');
        $video->shouldReceive('__toString')->andReturn('My Video');
        $this->values['videoObj']= $video;
        $series = m::mock('\videoViewer\Entities\Series');
        $series->shouldReceive('getId')->andReturn(1,3);
        $series->shouldReceive('getName')->andReturn('Series 1','Series 3');
        $selSeries = m::mock('\videoViewer\Entities\Series');
        $selSeries->shouldReceive('getId')->andReturn(2);
        $selSeries->shouldReceive('getName')->andReturn('Series 2');
        $this->values['series'] = array($series,$selSeries,$series);
        $video->shouldReceive('getSeries')->andReturn($selSeries);

        $this->di = m::mock('\videoViewer\DIContainer');
        $head = file_get_contents(dirname(__FILE__).'/../../../templates/head.tpl');
        $nav = file_get_contents(dirname(__FILE__).'/../../../templates/nav.tpl');
        $this->di->shouldReceive('loadTemplate')->with('head')->andReturn($head);
        $this->di->shouldReceive('loadTemplate')->with('nav')->andReturn($nav);
        $this->tpl = file_get_contents(dirname(__FILE__).'/../../../templates/EditVideo.tpl');
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    public function teardown() {
        m::close();
    }

    public function testEditVideoNoError() {
        $expected = file_get_contents(dirname(__FILE__).'/../../assets/html/EditVideo_noError.html');
        $this->di->shouldReceive('loadTemplate')->with('EditVideo')->andReturn($this->tpl);
        $view = new \videoViewer\views\EditVideoView($this->di);
        $view->video = $this->values['videoObj'];
        foreach($this->values['series'] as $series){
            $view->addSeries($series);
        }
        $this->assertEquals($expected,$view->render());
    }

    /**
     * @depends testEditVideoNoError
     */
    public function testEditVideoError() {
        $expected = file_get_contents(dirname(__FILE__).'/../../assets/html/EditVideo_error.html');
        $this->di->shouldReceive('loadTemplate')->with('EditVideo')->andReturn($this->tpl);
        $view = new \videoViewer\views\EditVideoView($this->di);
        $view->video = $this->values['videoObj'];
        foreach($this->values['series'] as $series){
            $view->addSeries($series);
        }
        $view->hasError = true;
        $view->errorMessage = 'You must supply an episode name. Date must be a valid date.';
        $this->assertEquals($expected,$view->render());
    }

}