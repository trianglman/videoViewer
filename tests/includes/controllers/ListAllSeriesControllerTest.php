<?php
namespace videoViewer\controllers;

use \Mockery as m;

/**
 * 
 */
class ListAllSeriesControllerTest extends \PHPUnit_Framework_TestCase {
    
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
        $this->view = m::mock('\videoViewer\views\ListAllSeriesView');
        $this->di->shouldReceive('getView')->with('ListAllSeries')->andReturn($this->view);
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
        $this->user->shouldReceive('isAdmin')->andReturn(true);
        $obj = new \videoViewer\controllers\ListAllSeriesController(array('ajax'=>'1'),array(),
                $this->session,array(),$this->di);
        $obj->processRequest();
    }

    public function testProcessesGet()
    {
        $expected = file_get_contents(dirname(__FILE__).'/../../assets/html/ListAllSeries_threeSeries.html');
        
        $this->user->shouldReceive('isAdmin')->andReturn(true);
        $series = m::mock('videoViewer\Entities\Series');
        
        $query = m::mock();
        $query->shouldReceive('getResult')->andReturn(array($series,$series,$series));
        $this->em->shouldReceive('createQuery')->with("SELECT s FROM videoViewer\Entities\Series s")->andReturn($query);
        
        $this->view->shouldReceive('addSeries')->with($series)->times(3);
        $this->view->shouldReceive('render')->andReturn($expected);
        
        $obj = new \videoViewer\controllers\ListAllSeriesController(array(),array(),
                $this->session,array(),$this->di);
        $this->assertEquals($expected,$obj->processRequest());
    }
    
    /**
     * @expectedException \videoViewer\PageRedirectException
     * @expectedExceptionCode 501
     */
    public function testRejectsPost()
    {
        $post = array('anything'=>'shouldFail');
        $this->user->shouldReceive('isAdmin')->andReturn(true);
        $obj = new \videoViewer\controllers\ListAllSeriesController(array(),$post,$this->session,array(),$this->di);
        $obj->processRequest();
    }
    
    /**
     * @expectedException \videoViewer\PageRedirectException
     * @expectedExceptionCode 403
     */
    public function testRejectsNotLoggedIn() 
    {
        $obj = new \videoViewer\controllers\ListAllSeriesController(array(),array(),
                array(),array(),$this->di);
        $obj->processRequest();
    }
    
    /**
     * @expectedException \videoViewer\PageRedirectException
     * @expectedExceptionCode 403
     */
    public function testRejectsNotAdmin() 
    {
        $this->user->shouldReceive('isAdmin')->andReturn(false);
        $obj = new \videoViewer\controllers\ListAllSeriesController(array(),array(),
                $this->session,array(),$this->di);
        $obj->processRequest();
    }
    
}