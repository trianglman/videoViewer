<?php
namespace videoViewer\controllers;

use \Mockery as m;

/**
 * 
 */
class RegisterControllerTest extends \PHPUnit_Framework_TestCase {
    
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
        $this->view = m::mock('\videoViewer\views\RegisterView');
        $this->di->shouldReceive('getView')->with('Register')->andReturn($this->view);
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
        $obj = new \videoViewer\controllers\RegisterController(array('ajax'=>'1'),array(),
                $this->session,array(),$this->di);
        $obj->processRequest();
    }

    public function testProcessesGet()
    {
        $expected = file_get_contents(dirname(__FILE__).'/../../assets/html/Register_noError.html');
        
        $this->view->shouldReceive('render')->andReturn($expected);
        
        $obj = new \videoViewer\controllers\RegisterController(array(),array(),
                array(),array(),$this->di);
        $this->assertEquals($expected,$obj->processRequest());
    }
    
    /**
     * @expectedException \videoViewer\PageRedirectException
     * @expectedExceptionCode 303
     */
    public function testProcessesValidPost()
    {
        $post = array('name'=>'user name','pass'=>'123456','pass2'=>'123456','login'=>'username');
        
        $user = m::mock('videoViewer\entities\User');
        $user->shouldReceive('setName')->with('user name')->andReturn($user);
        $user->shouldReceive('hashAndSetPassword')->with('123456')->andReturn($user);
        $user->shouldReceive('setUserName')->with('username')->andReturn($user);
        $user->shouldReceive('getId')->andReturn(1);
        $this->di->shouldReceive('getEntity')->with('User')->andReturn($user);
        
        $this->em->shouldReceive('persist')->with($user);
        $this->em->shouldReceive('flush');
        
        $obj = new \videoViewer\controllers\RegisterController(array(),$post,array(),array(),$this->di);
        $obj->processRequest();
    }
    
    /**
     * @depends testProcessesValidPost
     */
    public function testProcessesPostInvalidEntries()
    {
        $post = array('name'=>'','pass'=>'123456','pass2'=>'654321','login'=>'');
        $expected = file_get_contents(dirname(__FILE__).'/../../assets/html/Register_error.html');
        
        $this->view->shouldReceive('render')->andReturn($expected);
        
        $obj = new \videoViewer\controllers\RegisterController(array(),$post,array(),array(),$this->di);
        $this->assertEquals($expected,$obj->processRequest());
    }
    
    /**
     * @depends testProcessesValidPost
     */
    public function testProcessesPostInsecurePassword()
    {
        $post = array('name'=>'user name','pass'=>'123','pass2'=>'123','login'=>'username');
        $expected = file_get_contents(dirname(__FILE__).'/../../assets/html/Register_weakPassword.html');
        
        $this->view->shouldReceive('render')->andReturn($expected);
        
        $obj = new \videoViewer\controllers\RegisterController(array(),$post,array(),array(),$this->di);
        $this->assertEquals($expected,$obj->processRequest());
    }
    
    /**
     * @depends testProcessesValidPost
     */
    public function testProcessesValidPostDatabaseError()
    {
        $post = array('name'=>'user name','pass'=>'123456','pass2'=>'123456','login'=>'username');
        $expected = file_get_contents(dirname(__FILE__).'/../../assets/html/Register_databaseFail.html');
        
        $user = m::mock('videoViewer\entities\User');
        $user->shouldReceive('setName')->with('user name')->andReturn($user);
        $user->shouldReceive('hashAndSetPassword')->with('123456')->andReturn($user);
        $user->shouldReceive('setUserName')->with('username')->andReturn($user);
        $user->shouldReceive('getId')->andReturn(1);
        $this->di->shouldReceive('getEntity')->with('User')->andReturn($user);
        
        $this->em->shouldReceive('persist')->with($user);
        $err = new \Doctrine\ORM\ORMException('There was a database error.');
        $this->em->shouldReceive('flush')->andThrow($err);
        
        $this->view->shouldReceive('render')->andReturn($expected);
        
        $obj = new \videoViewer\controllers\RegisterController(array(),$post,array(),array(),$this->di);
        $this->assertEquals($expected,$obj->processRequest());
    }
    
}