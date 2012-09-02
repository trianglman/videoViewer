<?php
namespace videoViewer\controllers;

use \Mockery as m;

/**
 * 
 */
class GrantAccessControllerTest extends \PHPUnit_Framework_TestCase {
    
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
        $this->view = m::mock('\videoViewer\views\GrantAccessView');
        $this->di->shouldReceive('getView')->with('GrantAccess')->andReturn($this->view);
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
        $obj = new \videoViewer\controllers\GrantAccessController(array('ajax'=>'1','series'=>1),array(),
                $this->session,array(),$this->di);
        $obj->processRequest();
    }

    /**
     * @expectedException \videoViewer\PageRedirectException
     * @expectedExceptionCode 403
     */
    public function testRejectsNoSeriesId() 
    {
        $this->user->shouldReceive('isAdmin')->andReturn(true);
        $obj = new \videoViewer\controllers\GrantAccessController(array(),array(),
                $this->session,array(),$this->di);
        $obj->processRequest();
    }

    /**
     * @expectedException \videoViewer\PageRedirectException
     * @expectedExceptionCode 403
     */
    public function testRejectsNotLoggedIn() 
    {
        $obj = new \videoViewer\controllers\GrantAccessController(array('series'=>1),array(),
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
        $obj = new \videoViewer\controllers\GrantAccessController(array('series'=>1),array(),
                $this->session,array(),$this->di);
        $obj->processRequest();
    }
    
    public function testProcessesGet()
    {
        $expected = file_get_contents(dirname(__FILE__).'/../../assets/html/GrantAccess_twoUsers.html');
        $this->user->shouldReceive('isAdmin')->andReturn(true);
        
        $series = m::mock('videoViewer\Entities\Series');
        $this->em->shouldReceive('find')->with('videoViewer\Entities\Series',1)->andReturn($series);
        
        $user = m::mock('videoViewer\Entities\User');
        $query = m::mock();
        $query->shouldReceive('getResult')->andReturn(array($user,$user));
        $this->em->shouldReceive('createQuery')->with('SELECT u FROM videoViewer\Entities\User u')->andReturn($query);
        
        $this->view->shouldReceive('addUser')->with($user)->twice();
        $this->view->shouldReceive('render')->andReturn($expected);
        
        $obj = new \videoViewer\controllers\GrantAccessController(array('series'=>1),array(),
                $this->session,array(),$this->di);
        $this->assertEquals($expected,$obj->processRequest());
    }
    
    /**
     * @expectedException \videoViewer\PageRedirectException
     * @expectedExceptionCode 303
     */
    public function testProcessesValidPost()
    {
        $post = array('series'=>'1','authUser'=>array('2'));
        $this->user->shouldReceive('isAdmin')->andReturn(true);
        
        $series = m::mock('videoViewer\Entities\Series');
        $series->shouldReceive('getId')->andReturn(1);
        $this->em->shouldReceive('find')->with('videoViewer\Entities\Series',1)->andReturn($series);
        
        $auth = m::mock('videoViewer\Entities\User');
        $auth->shouldReceive('getId')->andReturn(2);
        $auth->shouldreceive('addAuthorizedSeries')->with($series)->andReturn($auth);
        $unauth = m::mock('videoViewer\Entities\User');
        $unauth->shouldReceive('getId')->andReturn(1);
        $unauth->shouldreceive('removeAuthorizedSeries')->with($series)->andReturn($unauth);
        $query = m::mock();
        $query->shouldReceive('getResult')->andReturn(array($unauth,$auth));
        $this->em->shouldReceive('createQuery')->with('SELECT u FROM videoViewer\Entities\User u')->andReturn($query);
        
        $this->em->shouldReceive('flush');
        
        $obj = new \videoViewer\controllers\GrantAccessController(array(),$post,$this->session,array(),$this->di);
        $obj->processRequest();
    }
    
}