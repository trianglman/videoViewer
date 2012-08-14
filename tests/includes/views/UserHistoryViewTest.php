<?php

namespace videoViewer\views;

use \Mockery as m;

/**
 * 
 */
class UserHistoryViewTest extends \PHPUnit_Framework_TestCase {
    
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
        $this->tpl = file_get_contents(dirname(__FILE__).'/../../../templates/UserHistory.tpl');
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    public function teardown() {
        m::close();
    }

    public function testUserHistoryTwoWatched() {
        $expected = file_get_contents(dirname(__FILE__).'/../../assets/html/UserHistory_twoWatched.html');
        $this->di->shouldReceive('loadTemplate')->with('UserHistory')->andReturn($this->tpl);
        $ep = m::mock('\videoViewer\Entities\Video');
        $ep->shouldReceive('getId')->andReturn(1,2);
        $ep->shouldReceive('getSeasonNumber')->andReturn(1,2);
        $ep->shouldReceive('getEpisodeNumber')->andReturn(3,7);
        $ep->shouldReceive('getEpisodeName')->andReturn('Episode 1','Episode the Other');
        
        $series = m::mock('\videoViewer\Entities\Series');
        $series->shouldReceive('getName')->andReturn('Series 1','Series 2');
        $ep->shouldReceive('getSeries')->andReturn($series);
        $view = new \videoViewer\views\UserHistoryView($this->di);
        $view->addVideo($ep);
        $view->addVideo($ep);
        $this->assertEquals($expected,$view->render());
    }

    public function testUserHistoryNoWatched() {
        $expected = file_get_contents(dirname(__FILE__).'/../../assets/html/UserHistory_noWatched.html');
        $this->di->shouldReceive('loadTemplate')->with('UserHistory')->andReturn($this->tpl);
        $view = new \videoViewer\views\UserHistoryView($this->di);
        $this->assertEquals($expected,$view->render());
    }

}