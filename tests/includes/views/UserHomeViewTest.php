<?php

namespace videoViewer\views;

use \Mockery as m;

/**
 * 
 */
class UserHomeViewTest extends \PHPUnit_Framework_TestCase {
    
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
        $this->tpl = file_get_contents(dirname(__FILE__).'/../../../templates/userHome.tpl');
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    public function teardown() {
        m::close();
    }

    public function testUserHome_noSeries_noAdmin_noRoku() 
    {
        $expected = file_get_contents(dirname(__FILE__).'/../../assets/html/UserHome_noSeries_noAdmin_noRoku.html');
        $this->di->shouldReceive('loadTemplate')->with('userHome')->andReturn($this->tpl);
        $view = new \videoViewer\views\UserHomeView($this->di);
        $view->name = 'SoAndSo';
        $this->assertEquals($expected,$view->render());
    }

    /**
     * @depends testUserHome_noSeries_noAdmin_noRoku
     */
    public function testUserHome_twoSeries_noAdmin_noRoku() 
    {
        $expected = file_get_contents(dirname(__FILE__).'/../../assets/html/UserHome_twoSeries_noAdmin_noRoku.html');
        $this->di->shouldReceive('loadTemplate')->with('userHome')->andReturn($this->tpl);
        $series = m::mock('\videoViewer\Entities\Series');
        $series->shouldReceive('getId')->andReturn(1,2);
        $series->shouldReceive('getName')->andReturn('Series 1','Series 2');
        $ep = m::mock('\videoViewer\Entities\Video');
        $user = m::mock('\videoViewer\Entities\User');
        $container = m::mock();
        $container->shouldReceive('contains')->with($ep)->andReturn(true,false,true,false,false,false,false,false);
        $user->shouldReceive('getWatchedVideos')->andReturn($container);
        $series->shouldReceive('getEpisodes')->andReturn(array($ep,$ep,$ep),array($ep,$ep,$ep,$ep,$ep));
        $view = new \videoViewer\views\UserHomeView($this->di);
        $view->name = 'SoAndSo';
        $view->setSeries(array($series,$series), $user);
        $this->assertEquals($expected,$view->render());
    }

    /**
     * @depends testUserHome_noSeries_noAdmin_noRoku
     */
    public function testUserHome_twoSeries_admin_noRoku() 
    {
        $expected = file_get_contents(dirname(__FILE__).'/../../assets/html/UserHome_twoSeries_admin_noRoku.html');
        $this->di->shouldReceive('loadTemplate')->with('userHome')->andReturn($this->tpl);
        $series = m::mock('\videoViewer\Entities\Series');
        $series->shouldReceive('getId')->andReturn(1,2);
        $series->shouldReceive('getName')->andReturn('Series 1','Series 2');
        $ep = m::mock('\videoViewer\Entities\Video');
        $user = m::mock('\videoViewer\Entities\User');
        $container = m::mock();
        $container->shouldReceive('contains')->with($ep)->andReturn(true,false,true,false,false,false,false,false);
        $user->shouldReceive('getWatchedVideos')->andReturn($container);
        $series->shouldReceive('getEpisodes')->andReturn(array($ep,$ep,$ep),array($ep,$ep,$ep,$ep,$ep));
        $view = new \videoViewer\views\UserHomeView($this->di);
        $view->name = 'SoAndSo';
        $view->admin=true;
        $view->setSeries(array($series,$series), $user);
        $this->assertEquals($expected,$view->render());
    }

    /**
     * @depends testUserHome_noSeries_noAdmin_noRoku
     */
    public function testUserHome_twoSeries_noAdmin_roku() 
    {
        $expected = file_get_contents(dirname(__FILE__).'/../../assets/html/UserHome_twoSeries_noAdmin_roku.html');
        $this->di->shouldReceive('loadTemplate')->with('userHome')->andReturn($this->tpl);
        $series = m::mock('\videoViewer\Entities\Series');
        $series->shouldReceive('getId')->andReturn(1,2);
        $series->shouldReceive('getName')->andReturn('Series 1','Series 2');
        $ep = m::mock('\videoViewer\Entities\Video');
        $user = m::mock('\videoViewer\Entities\User');
        $container = m::mock();
        $container->shouldReceive('contains')->with($ep)->andReturn(true,false,true,false,false,false,false,false);
        $user->shouldReceive('getWatchedVideos')->andReturn($container);
        $series->shouldReceive('getEpisodes')->andReturn(array($ep,$ep,$ep),array($ep,$ep,$ep,$ep,$ep));
        $view = new \videoViewer\views\UserHomeView($this->di);
        $view->name = 'SoAndSo';
        $view->hasRoku=true;
        $view->rokuUrl = '/userxml/hexnumber.xml';
        $view->setSeries(array($series,$series), $user);
        $this->assertEquals($expected,$view->render());
    }

}