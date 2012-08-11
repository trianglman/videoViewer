<?php

namespace videoViewer\views;

use \Mockery as m;

/**
 * 
 */
class CreateAliasViewTest extends \PHPUnit_Framework_TestCase {
    
    public $values = array();
    
    public $di=null;
    
    public $tpl = '';

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->values['video'] = 'My Video';
        $series = m::mock('\videoViewer\Entities\Series');
        $series->shouldReceive('getId')->andReturn(1,1,2,2,3,3);
        $series->shouldReceive('getName')->andReturn('Series1','Series2','Series3');
        $this->values['series'] = array($series,$series,$series);
        $this->values['defaultSeries']=2;
        $this->values['defaultAlias']='test alias';
        $this->values['videoId']=1;
        $this->di = m::mock('\videoViewer\DIContainer');
        $head = file_get_contents(dirname(__FILE__).'/../../../templates/head.tpl');
        $nav = file_get_contents(dirname(__FILE__).'/../../../templates/nav.tpl');
        $this->di->shouldReceive('loadTemplate')->with('head')->andReturn($head);
        $this->di->shouldReceive('loadTemplate')->with('nav')->andReturn($nav);
        $this->tpl = file_get_contents(dirname(__FILE__).'/../../../templates/CreateAlias.tpl');
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    public function teardown() {
        m::close();
    }

    public function testCreateAliasNoErrorNoAlias() {
        $expected = file_get_contents(dirname(__FILE__).'/../../assets/html/CreateAlias_noerror_woalias.html');
        $this->di->shouldReceive('loadTemplate')->with('CreateAlias')->andReturn($this->tpl);
        $view = new \videoViewer\views\CreateAliasView($this->di);
        $view->video = $this->values['video'];
        $view->defaultAlias = $this->values['defaultAlias'];
        $view->defaultSeries = $this->values['defaultSeries'];
        $view->videoId = $this->values['videoId'];
        $view->setSeries($this->values['series']);
        $this->assertEquals($expected,$view->render());
    }

    /**
     * @depends testCreateAliasNoErrorNoAlias
     */
    public function testCreateAliasErrorNoAlias() {
        $expected = file_get_contents(dirname(__FILE__).'/../../assets/html/CreateAlias_error_woalias.html');
        $this->di->shouldReceive('loadTemplate')->with('CreateAlias')->andReturn($this->tpl);
        $view = new \videoViewer\views\CreateAliasView($this->di);
        $view->video = $this->values['video'];
        $view->defaultAlias = $this->values['defaultAlias'];
        $view->defaultSeries = $this->values['defaultSeries'];
        $view->videoId = $this->values['videoId'];
        $view->setSeries($this->values['series']);
        $view->error = 'Video file name could not be parsed.';
        $this->assertEquals($expected,$view->render());
    }

    /**
     * @depends testCreateAliasNoErrorNoAlias
     */
    public function testCreateAliasNoErrorAlias() {
        $expected = file_get_contents(dirname(__FILE__).'/../../assets/html/CreateAlias_noerror_walias.html');
        $this->di->shouldReceive('loadTemplate')->with('CreateAlias')->andReturn($this->tpl);
        $view = new \videoViewer\views\CreateAliasView($this->di);
        $view->video = $this->values['video'];
        $view->defaultAlias = $this->values['defaultAlias'];
        $view->defaultSeries = $this->values['defaultSeries'];
        $view->videoId = $this->values['videoId'];
        $view->setSeries($this->values['series']);
        $view->seriesHasAlias = true;
        $this->assertEquals($expected,$view->render());
    }

}