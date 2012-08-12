<?php

namespace videoViewer\views;

use \Mockery as m;

/**
 * 
 */
class RegisterViewTest extends \PHPUnit_Framework_TestCase {
    
    public $values = array();
    
    public $di=null;
    
    public $tpl = '';

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {

        $this->di = m::mock('\videoViewer\DIContainer');
        $head = file_get_contents(dirname(__FILE__).'/../../../templates/head.tpl');
        $nav = file_get_contents(dirname(__FILE__).'/../../../templates/nav.tpl');
        $this->di->shouldReceive('loadTemplate')->with('head')->andReturn($head);
        $this->di->shouldReceive('loadTemplate')->with('nav')->andReturn($nav);
        $this->tpl = file_get_contents(dirname(__FILE__).'/../../../templates/Register.tpl');
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    public function teardown() {
        m::close();
    }

    public function testRegisterNoError() {
        $expected = file_get_contents(dirname(__FILE__).'/../../assets/html/Register_noError.html');
        $this->di->shouldReceive('loadTemplate')->with('Register')->andReturn($this->tpl);
        $view = new \videoViewer\views\RegisterView($this->di);
        $this->assertEquals($expected,$view->render());
    }

    public function testRegisterError() {
        $expected = file_get_contents(dirname(__FILE__).'/../../assets/html/Register_error.html');
        $this->di->shouldReceive('loadTemplate')->with('Register')->andReturn($this->tpl);
        $view = new \videoViewer\views\RegisterView($this->di);
        $view->hasError=true;
        $view->errorMessage='Passwords do not match. You must set a name. You must set a log in.';
        $this->assertEquals($expected,$view->render());
    }

}