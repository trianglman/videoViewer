<?php

namespace videoViewer\views;

use \Mockery as m;

/**
 * 
 */
class ViewUnmatchedVideosViewTest extends \PHPUnit_Framework_TestCase {
    
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
        $this->tpl = file_get_contents(dirname(__FILE__).'/../../../templates/ViewUnmatchedVideos.tpl');
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    public function teardown() {
        m::close();
    }

    public function testViewUnmatchedVideosTwoEpisodes() 
    {
        $expected = file_get_contents(dirname(__FILE__).'/../../assets/html/ViewUnmatchedVideos_twoEpisodes.html');
        $this->di->shouldReceive('loadTemplate')->with('ViewUnmatchedVideos')->andReturn($this->tpl);
        $ep = m::mock('\videoViewer\Entities\Video');
        $ep->shouldReceive('getFileNameBase')->andReturn('File_1.something','File_2.something');
        $ep->shouldReceive('getNotes')->andReturn('Here are some notes.','These are other notes.');
        $ep->shouldReceive('getId')->andReturn(1,2);
        $view = new \videoViewer\views\ViewUnmatchedVideosView($this->di);
        $view->addVideo($ep);
        $view->addVideo($ep);
        $this->assertEquals($expected,$view->render());
    }

}