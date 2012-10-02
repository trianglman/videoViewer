<?php
namespace videoViewer\controllers;

use \Mockery as m;

/**
 * 
 */
class UserHistoryControllerTest extends \PHPUnit_Framework_TestCase {
    
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
        $this->view = m::mock('\videoViewer\views\UserHistoryView');
        $this->di->shouldReceive('getView')->with('UserHistory')->andReturn($this->view);
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
        $obj = new \videoViewer\controllers\UserHistoryController($get,array(),$this->session,array(),$this->di);
        $obj->processRequest();
    }

    public function testProcessesGetNoHistory()
    {
        $expected = file_get_contents(dirname(__FILE__).'/../../assets/html/UserHistory_noWatched.html');
        $this->user->shouldReceive('getWatchedVideos')->andReturn(array());
        $this->view->shouldReceive('render')->andReturn($expected);
        $obj = new \videoViewer\controllers\UserHistoryController(array(),array(),
                $this->session,array(),$this->di);
        $this->assertEquals($expected,$obj->processRequest());
    }
    
    public function testProcessesGetTwoWatched()
    {
        $expected = file_get_contents(dirname(__FILE__).'/../../assets/html/UserHistory_twoWatched.html');
        $vid = m::mock('\videoViewer\Entities\Video');
        $this->view->shouldReceive('addVideo')->with($vid)->twice();
        $this->view->shouldReceive('render')->andReturn($expected);
        $this->user->shouldReceive('getWatchedVideos')->andReturn(array($vid,$vid));
        $obj = new \videoViewer\controllers\UserHistoryController(array(),array(),
                $this->session,array(),$this->di);
        $this->assertEquals($expected,$obj->processRequest());
    }
    
    /**
     * @expectedException \videoViewer\PageRedirectException
     * @expectedExceptionCode 501
     */
    public function testRejectsInvalidPost()
    {
        $post = array('action'=>'invalid');
        $obj = new \videoViewer\controllers\UserHistoryController(array(),$post,$this->session,array(),$this->di);
        $obj->processRequest();
    }
    
    /**
     * @expectedException \videoViewer\PageRedirectException
     * @expectedExceptionCode 303
     */
    public function testProcessesAdd()
    {
        $post = array('action'=>'add','episode'=>'1');
        $vid = m::mock('\videoViewer\Entities\Video');
        $this->em->shouldReceive('find')->with('videoViewer\Entities\Video','1')->andReturn($vid);
        $this->em->shouldReceive('flush');
        $this->user->shouldReceive('addWatchedVideo')->with($vid);
        $obj = new \videoViewer\controllers\UserHistoryController(array(),$post,$this->session,array(),$this->di);
        $obj->processRequest();
    }
    
    /**
     * @expectedException \videoViewer\PageRedirectException
     * @expectedExceptionCode 303
     */
    public function testProcessesDelete()
    {
        $post = array('action'=>'del','episode'=>'1');
        $vid = m::mock('\videoViewer\Entities\Video');
        $this->em->shouldReceive('find')->with('videoViewer\Entities\Video','1')->andReturn($vid);
        $this->em->shouldReceive('flush');
        $this->user->shouldReceive('removeWatchedVideo')->with($vid);
        $obj = new \videoViewer\controllers\UserHistoryController(array(),$post,$this->session,array(),$this->di);
        $obj->processRequest();
    }
    
    /**
     * @expectedException \videoViewer\PageRedirectException
     * @expectedExceptionCode 403
     */
    public function testRejectsNotLoggedIn() 
    {
        $obj = new \videoViewer\controllers\UserHistoryController(array(),array(),
                array(),array(),$this->di);
        $obj->processRequest();
    }
    
}