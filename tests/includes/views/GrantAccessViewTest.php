<?php

namespace videoViewer\views;

use \Mockery as m;

/**
 * 
 */
class GrantAccessViewTest extends \PHPUnit_Framework_TestCase {
    
    public $values = array();
    
    public $di=null;
    
    public $tpl = '';

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $series = m::mock('\videoViewer\Entities\Series');
        $series->shouldReceive('getId')->andReturn(1);
        $series->shouldReceive('getName')->andReturn('Series 1');
        $series->shouldReceive('getSrc')->andReturn('http://localhost/banners/1234.jpg');
        $this->values['series'] = $series;
        
        $user = m::mock('\videoViewer\Entities\User');
        $user->shouldReceive('getId')->andReturn(1,2);
        $user->shouldReceive('canAccessSeries')->with($series)->andReturn(false,true);
        $user->shouldReceive('getName')->andReturn('User 1','User 2');
        $this->values['users'] = array($user,$user);

        $this->di = m::mock('\videoViewer\DIContainer');
        $head = file_get_contents(dirname(__FILE__).'/../../../templates/head.tpl');
        $nav = file_get_contents(dirname(__FILE__).'/../../../templates/nav.tpl');
        $this->di->shouldReceive('loadTemplate')->with('head')->andReturn($head);
        $this->di->shouldReceive('loadTemplate')->with('nav')->andReturn($nav);
        $this->tpl = file_get_contents(dirname(__FILE__).'/../../../templates/GrantAccess.tpl');
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    public function teardown() {
        m::close();
    }

    public function testCreateAliasNoErrorNoAlias() {
        $expected = file_get_contents(dirname(__FILE__).'/../../assets/html/GrantAccess_twoUsers.html');
        $this->di->shouldReceive('loadTemplate')->with('GrantAccess')->andReturn($this->tpl);
        $view = new \videoViewer\views\GrantAccessView($this->di);
        $view->series = $this->values['series'];
        foreach($this->values['users'] as $user){
            $view->addUser($user);
        }
        $this->assertEquals($expected,$view->render());
    }

}