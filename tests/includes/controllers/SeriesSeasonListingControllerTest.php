<?php
namespace videoViewer\controllers;

use \Mockery as m;

/**
 * 
 */
class SeriesSeasonListingControllerTest extends \PHPUnit_Framework_TestCase {
    
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
        $this->view = m::mock('\videoViewer\views\SeriesSeasonListingView');
        $this->di->shouldReceive('getView')->with('SeriesSeasonListing')->andReturn($this->view);
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
        $get = array('ajax'=>'1','series'=>'1');
        $obj = new \videoViewer\controllers\SeriesSeasonListingController($get,array(),$this->session,array(),$this->di);
        $obj->processRequest();
    }

    public function testProcessesGet()
    {
        $expected = file_get_contents(dirname(__FILE__).'/../../assets/html/SeriesSeasonListing_twoSeasons.html');
        $episode = m::mock('\videoViewer\Entities\Video');
        $episode->shouldReceive('getSeasonNumber')->andReturn(1);
        $episode2 = m::mock('\videoViewer\Entities\Video');
        $episode2->shouldReceive('getSeasonNumber')->andReturn(2);
        $series = m::mock('\videoViewer\Entities\Series');
        $series->shouldReceive('getId')->andReturn(1)->once();
        $series->shouldReceive('getName')->andReturn('Series 1')->once();
        $series->shouldReceive('getSrc')->andReturn('http://localhost/banners/1234.jpg')->once();
        $series->shouldReceive('getDescription')->andReturn('This is the first series\' description.')->once();
        $availEps = array();
        for($x=0;$x<10;$x++){
            //set up five episodes in the first season, 10 in the second
            if($x<5){
                $availEps[]=$episode;
            }
            $availEps[]=$episode2;
        }
        $series->shouldReceive('getEpisodes')->andReturn($availEps);
        $this->em->shouldReceive('find')->with('videoViewer\Entities\Series','1')->andReturn($series);
        
        $this->view->shouldReceive('render')->andReturn($expected);
        
        $this->user->shouldReceive('isAdmin')->andReturn(false);
        $this->user->shouldReceive('canAccessSeries')->with($series)->andReturn(true);
        $obj = new \videoViewer\controllers\SeriesSeasonListingController(array('series'=>'1'),array(),
                $this->session,array(),$this->di);
        $this->assertEquals($expected,$obj->processRequest());
    }
    
    /**
     * @expectedException \videoViewer\PageRedirectException
     * @expectedExceptionCode 501
     */
    public function testRejectsPost()
    {
        $post = array('anything'=>'shouldFail','series'=>'1');
        $obj = new \videoViewer\controllers\SeriesSeasonListingController(array(),$post,$this->session,array(),$this->di);
        $obj->processRequest();
    }
    
    /**
     * @expectedException \videoViewer\PageRedirectException
     * @expectedExceptionCode 403
     */
    public function testRejectsNotLoggedIn() 
    {
        $obj = new \videoViewer\controllers\SeriesSeasonListingController(array(),array(),
                array(),array(),$this->di);
        $obj->processRequest();
    }
    
    /**
     * @depends testProcessesGet
     * @expectedException \videoViewer\PageRedirectException
     * @expectedExceptionCode 303
     */
    public function testRejectsInvalidGet()
    {
        $obj = new \videoViewer\controllers\SeriesSeasonListingController(array(),array(),
                $this->session,array(),$this->di);
        $obj->processRequest();
    }
    
    /**
     * @depends testProcessesGet
     * @expectedException \videoViewer\PageRedirectException
     * @expectedExceptionCode 403
     */
    public function testRejectsUnauthorized()
    {
        $series = m::mock('\videoViewer\Entities\Series');
        $this->em->shouldReceive('find')->with('videoViewer\Entities\Series','1')->andReturn($series);
        $this->user->shouldReceive('isAdmin')->andReturn(false);
        $this->user->shouldReceive('canAccessSeries')->with($series)->andReturn(false);
        
        $obj = new \videoViewer\controllers\SeriesSeasonListingController(array('series'=>'1'),array(),
                $this->session,array(),$this->di);
        $obj->processRequest();
    }
    
}