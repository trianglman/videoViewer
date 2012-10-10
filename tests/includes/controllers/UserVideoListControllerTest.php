<?php
namespace videoViewer\controllers;

use \Mockery as m;

/**
 * 
 */
class UserVideoListControllerTest extends \PHPUnit_Framework_TestCase {
    
    public $di = null;
    public $session = array();
    public $em = null;
    public $user = null;
    public $view = null;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() 
    {
        $this->session = array('userId'=>1);
        $this->user = m::mock('\videoViewer\Entities\User');
        $this->em = m::mock();
        $this->em->shouldReceive('find')->with('videoViewer\Entities\User',$this->session['userId'])
                 ->andReturn($this->user);
        $this->di = m::mock('\videoViewer\DIContainer');
        $this->di->shouldReceive('offsetGet')->with('em')->andReturn($this->em);
        $this->view = m::mock('\videoViewer\views\UserVideoListView');
        $this->di->shouldReceive('getView')->with('UserVideoList')->andReturn($this->view);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    public function teardown() 
    {
        m::close();
    }

    /**
     * @expectedException \videoViewer\PageRedirectException
     * @expectedExceptionCode 501
     */
    public function testRejectsAjax() 
    {
        $get = array('ajax'=>'1');
        $obj = new \videoViewer\controllers\UserVideoListController($get,array(),$this->session,array(),$this->di);
        $obj->processRequest();
    }

    public function testProcessesGetTwoSeriesRoku()
    {
        $expected = file_get_contents(dirname(__FILE__)
                .'/../../assets/html/UserVideoList_twoUnwatched.html');
        $series =  m::mock('\videoViewer\Entities\Series');
        $unwatched = m::mock('\videoViewer\Entities\Video');
        $watched = m::mock('\videoViewer\Entities\Video');
        $container = m::mock();
        $this->user->shouldReceive('getWatchedVideos')->andReturn($container);
        $container->shouldReceive('contains')->with($unwatched)->andReturn(false);
        $container->shouldReceive('contains')->with($watched)->andReturn(true);
        $series->shouldReceive('getEpisodes')->andReturn(array($unwatched,$watched,$unwatched));
        $this->user->shouldReceive('getAuthorizedSeries')->andReturn(array($series));
        
        $this->view->shouldReceive('addVideo')->with($unwatched)->twice();
        $this->view->shouldReceive('render')->andReturn($expected);
        
        $obj = new \videoViewer\controllers\UserVideoListController(array(),array(),
                $this->session,array(),$this->di);
        $this->assertEquals($expected,$obj->processRequest());
    }
    
    /**
     * @expectedException \videoViewer\PageRedirectException
     * @expectedExceptionCode 501
     */
    public function testRejectsPost()
    {
        $post = array('action'=>'anything');
        $obj = new \videoViewer\controllers\UserVideoListController(array(),$post,$this->session,array(),$this->di);
        $obj->processRequest();
    }
    
    /**
     * @expectedException \videoViewer\PageRedirectException
     * @expectedExceptionCode 403
     */
    public function testRejectsNotLoggedIn() 
    {
        $obj = new \videoViewer\controllers\UserVideoListController(array(),array(),
                array(),array(),$this->di);
        $obj->processRequest();
    }
    
}