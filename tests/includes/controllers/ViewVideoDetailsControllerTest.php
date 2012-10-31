<?php
namespace videoViewer\controllers;

use \Mockery as m;

/**
 * 
 */
class ViewVideoDetailsControllerTest extends \PHPUnit_Framework_TestCase {
    
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
        $this->view = m::mock('\videoViewer\views\ViewVideoDetailsView');
        $this->di->shouldReceive('getView')->with('ViewVideoDetails')->andReturn($this->view);
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
        $get = array('ajax'=>'1','id'=>'1');
        $obj = new \videoViewer\controllers\ViewVideoDetailsController($get,array(),$this->session,array(),$this->di);
        $obj->processRequest();
    }

    public function testProcessesGet()
    {
        $expected = file_get_contents(dirname(__FILE__)
                .'/../../assets/html/ViewVideoDetails.html');
        $video = m::mock('\videoViewer\Entities\Video');
        $this->em->shouldReceive('find')->with('videoViewer\Entities\Video','1')->andReturn($video);
        
        $this->view->shouldReceive('render')->andReturn($expected);
        $obj = new \videoViewer\controllers\ViewVideoDetailsController(array('id'=>'1'),array(),
                $this->session,array(),$this->di);
        $this->assertEquals($expected,$obj->processRequest());
    }
    
    /**
     * @expectedException \videoViewer\PageRedirectException
     * @expectedExceptionCode 501
     */
    public function testRejectsPost()
    {
        $post = array('action'=>'anything','id'=>'1');
        $obj = new \videoViewer\controllers\ViewVideoDetailsController(array(),$post,$this->session,array(),$this->di);
        $obj->processRequest();
    }
    
    /**
     * @expectedException \videoViewer\PageRedirectException
     * @expectedExceptionCode 403
     */
    public function testRejectsNotLoggedIn() 
    {
        $obj = new \videoViewer\controllers\ViewVideoDetailsController(array('id'=>'1'),array(),
                array(),array(),$this->di);
        $obj->processRequest();
    }//        $this->user->shouldReceive('isAdmin')->andReturn(true);

    /**
     * @expectedException \videoViewer\PageRedirectException
     * @expectedExceptionCode 403
     */
    public function testRejectsNoId() 
    {
        $obj = new \videoViewer\controllers\ViewVideoDetailsController(array(),array(),
                $this->session,array(),$this->di);
        $obj->processRequest();
    }//        $this->user->shouldReceive('isAdmin')->andReturn(true);

}