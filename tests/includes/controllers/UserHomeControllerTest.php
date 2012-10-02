<?php
namespace videoViewer\controllers;

use \Mockery as m;

/**
 * 
 */
class UserHomeControllerTest extends \PHPUnit_Framework_TestCase {
    
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
        $this->view = m::mock('\videoViewer\views\UserHomeView');
        $this->di->shouldReceive('getView')->with('UserHome')->andReturn($this->view);
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
        $obj = new \videoViewer\controllers\UserHomeController($get,array(),$this->session,array(),$this->di);
        $obj->processRequest();
    }

    public function testProcessesGetNoSeries()
    {
        $expected = file_get_contents(dirname(__FILE__)
                .'/../../assets/html/UserHome_noSeries_noAdmin_noRoku.html');
        $this->user->shouldReceive('getName')->andReturn('SoAndSo');
        $this->user->shouldReceive('isAdmin')->andReturn(false);
        $this->user->shouldReceive('getRokuXML')->andReturn('nosuchfile.xml');
        $this->user->shouldReceive('getAuthorizedSeries')->andReturn(array());
        $this->di->shouldReceive('fileSystem')->with('file_exists',m::type('array'))->andReturn(false);
        
        $this->view->shouldReceive('setSeries')->with(array(),$this->user);
        $this->view->shouldReceive('render')->andReturn($expected);
        $obj = new \videoViewer\controllers\UserHomeController(array(),array(),
                $this->session,array(),$this->di);
        $this->assertEquals($expected,$obj->processRequest());
    }
    
    public function testProcessesGetTwoSeriesRoku()
    {
        $expected = file_get_contents(dirname(__FILE__)
                .'/../../assets/html/UserHome_twoSeries_noAdmin_roku.html');
        $series =  m::mock('\videoViewer\Entities\Series');
        $this->user->shouldReceive('getName')->andReturn('SoAndSo');
        $this->user->shouldReceive('isAdmin')->andReturn(false);
        $this->user->shouldReceive('getRokuXML')->andReturn('/userxml/hexnumber.xml');
        $this->user->shouldReceive('getAuthorizedSeries')->andReturn(array($series,$series));
        $this->di->shouldReceive('fileSystem')->with('file_exists',m::type('array'))->andReturn(true);
        
        $this->view->shouldReceive('setSeries')->with(array($series,$series),$this->user);
        $this->view->shouldReceive('render')->andReturn($expected);
        $obj = new \videoViewer\controllers\UserHomeController(array(),array(),
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
        $obj = new \videoViewer\controllers\UserHomeController(array(),$post,$this->session,array(),$this->di);
        $obj->processRequest();
    }
    
    /**
     * @expectedException \videoViewer\PageRedirectException
     * @expectedExceptionCode 403
     */
    public function testRejectsNotLoggedIn() 
    {
        $obj = new \videoViewer\controllers\UserHomeController(array(),array(),
                array(),array(),$this->di);
        $obj->processRequest();
    }
    
}