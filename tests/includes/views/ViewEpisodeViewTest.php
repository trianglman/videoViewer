<?php

namespace videoViewer\views;

use \Mockery as m;

/**
 * 
 */
class ViewEpisodeViewTest extends \PHPUnit_Framework_TestCase {
    
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
        $this->tpl = file_get_contents(dirname(__FILE__).'/../../../templates/ViewEpisode.tpl');
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    public function teardown() {
        m::close();
    }

    public function testViewEpisodeAdmin() 
    {
        $expected = file_get_contents(dirname(__FILE__).'/../../assets/html/ViewEpisode_admin.html');
        $this->di->shouldReceive('loadTemplate')->with('ViewEpisode')->andReturn($this->tpl);
        $view = new \videoViewer\views\ViewEpisodeView($this->di);
        $view->episodeName = 'Episode 1';
        $view->mp4Path = 'videos/video.1.mp4';
        $view->oggPath = 'videos/video.1.ogv';
        $view->seriesName = 'Series 1';
        $view->videoId = 1;
        $view->isAdmin = true;
        $this->assertEquals($expected,$view->render());
    }

    /**
     * @depends testViewEpisodeAdmin
     */
    public function testViewEpisodeNoAdmin() 
    {
        $expected = file_get_contents(dirname(__FILE__).'/../../assets/html/ViewEpisode_notAdmin.html');
        $this->di->shouldReceive('loadTemplate')->with('ViewEpisode')->andReturn($this->tpl);
        $view = new \videoViewer\views\ViewEpisodeView($this->di);
        $view->episodeName = 'Episode 1';
        $view->mp4Path = 'videos/video.1.mp4';
        $view->oggPath = 'videos/video.1.ogv';
        $view->seriesName = 'Series 1';
        $view->videoId = 1;
        $view->isAdmin = false;
        $this->assertEquals($expected,$view->render());
    }

}