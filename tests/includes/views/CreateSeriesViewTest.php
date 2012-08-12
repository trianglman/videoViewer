<?php

namespace videoViewer\views;

use \Mockery as m;

/**
 * 
 */
class CreateSeriesViewTest extends \PHPUnit_Framework_TestCase {
    
    public $values = array();
    
    public $di=null;
    
    public $tpl = '';

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->values['videoId'] = 1;
        $series = m::mock('\videoViewer\TvDBSeries');
        $series->shouldReceive('getTVDBUrl')->andReturn(
                'http://www.thetvdb.com/?tab=series&id=73388&lid=17',
                'http://www.thetvdb.com/?tab=series&id=73389&lid=18'
                );
        $series->shouldReceive('getSeriesId')->andReturn(123,456);
        $series->shouldReceive('getSeriesName')->andReturn('Series1','Series2');
        $series->shouldReceive('getName')->andReturn('Series1','Series2','Series3');
        $this->values['seriesOpt'] = array($series,$series);

        $this->di = m::mock('\videoViewer\DIContainer');
        $head = file_get_contents(dirname(__FILE__).'/../../../templates/head.tpl');
        $nav = file_get_contents(dirname(__FILE__).'/../../../templates/nav.tpl');
        $this->di->shouldReceive('loadTemplate')->with('head')->andReturn($head);
        $this->di->shouldReceive('loadTemplate')->with('nav')->andReturn($nav);
        $this->tpl = file_get_contents(dirname(__FILE__).'/../../../templates/CreateSeries.tpl');
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    public function teardown() {
        m::close();
    }

    public function testCreateSeriesTwoSeries() {
        $expected = file_get_contents(dirname(__FILE__).'/../../assets/html/CreateSeries_twoSeriesOpts.html');
        $this->di->shouldReceive('loadTemplate')->with('CreateSeries')->andReturn($this->tpl);
        $view = new \videoViewer\views\CreateSeriesView($this->di);
        $view->videoId = $this->values['videoId'];
        foreach($this->values['seriesOpt'] as $series){
            $view->addSeriesOpt($series);
        }
        $this->assertEquals($expected,$view->render());
    }

}