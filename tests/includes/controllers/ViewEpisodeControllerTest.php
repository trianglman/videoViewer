<?php
namespace videoViewer\controllers;

use \Mockery as m;

/**
 * 
 */
class ViewEpisodeControllerTest extends \PHPUnit_Framework_TestCase {
    
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
        $this->view = m::mock('\videoViewer\views\ViewEpisodeView');
        $this->di->shouldReceive('getView')->with('ViewEpisode')->andReturn($this->view);
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
        $get = array('ajax'=>'1','episode'=>'1');
        $obj = new \videoViewer\controllers\ViewEpisodeController($get,array(),$this->session,array(),$this->di);
        $obj->processRequest();
    }

    public function testProcessesGetNotAdmin()
    {
        $expected = file_get_contents(dirname(__FILE__)
                .'/../../assets/html/UserVideoList_twoUnwatched.html');
        
        $vid = m::mock('\videoViewer\Entities\Video');
        $series = m::mock('\videoViewer\Entities\Series');
        $series->shouldReceive('getName')->andReturn('Series 1');
        $vid->shouldReceive('getEpisodeName')->andReturn('Episode 1');
        $vid->shouldReceive('getSeries')->andReturn($series);
        $vid->shouldReceive('getWebPath')->with('ogg')->andReturn('videos/video.1.ogv');
        $vid->shouldReceive('getWebPath')->with('mp4')->andReturn('videos/video.1.mp4');
        $this->user->shouldReceive('isAdmin')->andReturn(false);
        $vid->shouldReceive('getId')->andReturn(1);
        $this->user->shouldReceive('addWatchedVideo')->with($vid);
        $this->em->shouldReceive('find')->with('videoViewer\Entities\Video',1)->andReturn($vid);
        $this->em->shouldReceive('flush');
        $this->user->shouldReceive('canAccessSeries')->with($series)->andReturn(true);
        
        $this->view->shouldReceive('render')->andReturn($expected);
        
        $obj = new \videoViewer\controllers\ViewEpisodeController(array('episode'=>'1'),array(),
                $this->session,array(),$this->di);
        $this->assertEquals($expected,$obj->processRequest());
    }
    
    /**
     * @depends testProcessesGetNotAdmin
     * @expectedException \videoViewer\PageRedirectException
     * @expectedExceptionCode 403
     */
    public function testRejectsNoAccess()
    {
        $vid = m::mock('\videoViewer\Entities\Video');
        $series = m::mock('\videoViewer\Entities\Series');
        $vid->shouldReceive('getSeries')->andReturn($series);
        $this->user->shouldReceive('isAdmin')->andReturn(false);
        $this->em->shouldReceive('find')->with('videoViewer\Entities\Video',1)->andReturn($vid);
        $this->user->shouldReceive('canAccessSeries')->with($series)->andReturn(false);
        
        $obj = new \videoViewer\controllers\ViewEpisodeController(array('episode'=>'1'),array(),
                $this->session,array(),$this->di);
        $obj->processRequest();
    }
    
    /**
     * @expectedException \videoViewer\PageRedirectException
     * @expectedExceptionCode 501
     */
    public function testRejectsPost()
    {
        $post = array('action'=>'anything','episode'=>'1');
        $obj = new \videoViewer\controllers\ViewEpisodeController(array(),$post,$this->session,array(),$this->di);
        $obj->processRequest();
    }
    
    /**
     * @expectedException \videoViewer\PageRedirectException
     * @expectedExceptionCode 403
     */
    public function testRejectsNotLoggedIn() 
    {
        $obj = new \videoViewer\controllers\ViewEpisodeController(array(),array(),
                array(),array(),$this->di);
        $obj->processRequest();
    }
    
}