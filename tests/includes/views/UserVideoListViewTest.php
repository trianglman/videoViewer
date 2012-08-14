<?php

namespace videoViewer\views;

use \Mockery as m;

/**
 * 
 */
class UserVideoListViewTest extends \PHPUnit_Framework_TestCase {
    
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
        $this->tpl = file_get_contents(dirname(__FILE__).'/../../../templates/UserVideoList.tpl');
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    public function teardown() {
        m::close();
    }

    public function testUserVideoListTwoUnwatched() 
    {
        $expected = file_get_contents(dirname(__FILE__).'/../../assets/html/UserVideoList_twoUnwatched.html');
        $this->di->shouldReceive('loadTemplate')->with('UserVideoList')->andReturn($this->tpl);
        $ep = m::mock('\videoViewer\Entities\Video');
        $ep->shouldReceive('getId')->andReturn(1,2);
        $ep->shouldReceive('getEpisodeName')->andReturn('Episode 1','Episode the Other');
        $ep->shouldReceive('getAirDate')->andReturn(new \DateTime('May 3, 1980'),new \DateTime('May 3, 1980'),
                                                    new \DateTime('November 18, 1980'),new \DateTime('November 18, 1980'));
        
        $series = m::mock('\videoViewer\Entities\Series');
        $series->shouldReceive('getName')->andReturn('Series 1','Series 2');
        $ep->shouldReceive('getSeries')->andReturn($series);
        $view = new \videoViewer\views\UserVideoListView($this->di);
        $view->addVideo($ep);
        $view->addVideo($ep);
        $this->assertEquals($expected,$view->render());
    }

    /**
     * @depends testUserVideoListTwoUnwatched
     */
    public function testUserVideoListTwoUnwatchedResort() 
    {
        $expected = file_get_contents(dirname(__FILE__).'/../../assets/html/UserVideoList_twoUnwatched.html');
        $this->di->shouldReceive('loadTemplate')->with('UserVideoList')->andReturn($this->tpl);
        $ep = m::mock('\videoViewer\Entities\Video');
        $ep->shouldReceive('getId')->andReturn(2,1);
        $ep->shouldReceive('getAirDate')->andReturn(new \DateTime('November 18, 1980'),new \DateTime('November 18, 1980'),
                                                    new \DateTime('May 3, 1980'),new \DateTime('May 3, 1980'));
        $ep->shouldReceive('getEpisodeName')->andReturn('Episode the Other','Episode 1');
        
        $series = m::mock('\videoViewer\Entities\Series');
        $series->shouldReceive('getName')->andReturn('Series 2','Series 1');
        $ep->shouldReceive('getSeries')->andReturn($series);
        $view = new \videoViewer\views\UserVideoListView($this->di);
        $view->addVideo($ep);
        $view->addVideo($ep);
        $this->assertEquals($expected,$view->render());
    }

    /**
     * @depends testUserVideoListTwoUnwatched
     */
    public function testUserVideoListNoUnwatched() 
    {
        $expected = file_get_contents(dirname(__FILE__).'/../../assets/html/UserVideoList_noUnwatched.html');
        $this->di->shouldReceive('loadTemplate')->with('UserVideoList')->andReturn($this->tpl);
        $view = new \videoViewer\views\UserVideoListView($this->di);
        $this->assertEquals($expected,$view->render());
    }

}