<?php

namespace videoViewer\views;

use \Mockery as m;

/**
 * 
 */
class IndexViewTest extends \PHPUnit_Framework_TestCase {
    
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
        $this->tpl = file_get_contents(dirname(__FILE__).'/../../../templates/index.tpl');
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    public function teardown() {
        m::close();
    }

    public function testIndexNoError() {
        $expected = file_get_contents(dirname(__FILE__).'/../../assets/html/Index_noError.html');
        $this->di->shouldReceive('loadTemplate')->with('index')->andReturn($this->tpl);
        $view = new \videoViewer\views\IndexView($this->di);
        $this->assertEquals($expected,$view->render());
    }

    /**
     * @depends testIndexNoError
     */
    public function testIndexError() {
        $expected = file_get_contents(dirname(__FILE__).'/../../assets/html/Index_error.html');
        $this->di->shouldReceive('loadTemplate')->with('index')->andReturn($this->tpl);
        $view = new \videoViewer\views\IndexView($this->di);
        $view->hasError=true;
        $view->errorMessage = 'User name or Password not recognized.';
        $this->assertEquals($expected,$view->render());
    }

}