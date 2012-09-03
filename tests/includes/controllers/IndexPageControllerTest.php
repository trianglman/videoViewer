<?php
namespace videoViewer\controllers;

use \Mockery as m;

/**
 * 
 */
class IndexPageControllerTest extends \PHPUnit_Framework_TestCase {
    
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
        $this->view = m::mock('\videoViewer\views\IndexView');
        $this->di->shouldReceive('getView')->with('index')->andReturn($this->view);
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
        $obj = new \videoViewer\controllers\IndexPageController(array('ajax'=>'1'),array(),
                array(),array(),$this->di);
        $obj->processRequest();
    }

    /**
     * @expectedException \videoViewer\PageRedirectException
     * @expectedExceptionCode 303
     */
    public function testRedirectsLoggedIn() 
    {
        $obj = new \videoViewer\controllers\IndexPageController(array(),array(),
                $this->session,array(),$this->di);
        $obj->processRequest();
    }
    
    public function testProcessesGet()
    {
        $expected = file_get_contents(dirname(__FILE__).'/../../assets/html/Index_noError.html');
        
        $this->view->shouldReceive('render')->andReturn($expected);
        
        $obj = new \videoViewer\controllers\IndexPageController(array(),array(),
                array(),array(),$this->di);
        $this->assertEquals($expected,$obj->processRequest());
    }
    
    /**
     * @expectedException \videoViewer\PageRedirectException
     * @expectedExceptionCode 303
     */
    public function testProcessesValidPost()
    {
        $post = array('login'=>'user1','pass'=>'123');
        
        $user = m::mock('\videoViewer\Entities\User');
        $user->shouldReceive('getId')->andReturn(1);
        $repo = m::mock('videoViewer\Repositories\UserRepository');
        $repo->shouldReceive('getUserByNameAndPassword')->with('user1','123')->andReturn($user);
        $this->em->shouldReceive('getRepository')->with('videoViewer\Entities\User')->andReturn($repo);
        
        $obj = new \videoViewer\controllers\IndexPageController(array(),$post,array(),array(),$this->di);
        $obj->processRequest();
    }
    
    /**
     * @depends testProcessesValidPost
     */
    public function testProcessesInvalidPost()
    {
        $post = array('login'=>'user1','pass'=>'456');
        
        $user = m::mock('\videoViewer\Entities\User');
        $user->shouldReceive('getId')->andReturn(1);
        $repo = m::mock('videoViewer\Repositories\UserRepository');
        $e = new \Doctrine\ORM\NoResultException();
        $repo->shouldReceive('getUserByNameAndPassword')->with('user1','456')->andThrow($e);
        $this->em->shouldReceive('getRepository')->with('videoViewer\Entities\User')->andReturn($repo);
        
        $expected = file_get_contents(dirname(__FILE__).'/../../assets/html/Index_error.html');
        
        $this->view->shouldReceive('render')->andReturn($expected);
        
        $obj = new \videoViewer\controllers\IndexPageController(array(),$post,array(),array(),$this->di);
        $this->assertEquals($expected,$obj->processRequest());
    }
    
}