<?php

namespace videoViewer\views;

use \Mockery as m;

/**
 * 
 */
class ListAllSeriesViewTest extends \PHPUnit_Framework_TestCase {
    
    public $values = array();
    
    public $di=null;
    
    public $tpl = '';

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $series = m::mock('\videoViewer\Entities\Series');
        $series->shouldReceive('getId')->andReturn(1,1,2,2,3,3);
        $series->shouldReceive('getName')->andReturn('Series 1','Series 2','Series 3');
        $this->values['series'] = array($series,$series,$series);

        $this->di = m::mock('\videoViewer\DIContainer');
        $head = file_get_contents(dirname(__FILE__).'/../../../templates/head.tpl');
        $nav = file_get_contents(dirname(__FILE__).'/../../../templates/nav.tpl');
        $this->di->shouldReceive('loadTemplate')->with('head')->andReturn($head);
        $this->di->shouldReceive('loadTemplate')->with('nav')->andReturn($nav);
        $this->tpl = file_get_contents(dirname(__FILE__).'/../../../templates/ListAllSeries.tpl');
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    public function teardown() {
        m::close();
    }

    public function testCreateAliasNoErrorNoAlias() {
        $expected = file_get_contents(dirname(__FILE__).'/../../assets/html/ListAllSeries_threeSeries.html');
        $this->di->shouldReceive('loadTemplate')->with('ListAllSeries')->andReturn($this->tpl);
        $view = new \videoViewer\views\ListAllSeriesView($this->di);
        foreach($this->values['series'] as $series){
            $view->addSeries($series);
        }
        $this->assertEquals($expected,$view->render());
    }

}