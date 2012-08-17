<?php
namespace videoViewer\controllers;

use \Mockery as m;

/**
 * 
 */
class CreateAliasControllerTest extends \PHPUnit_Framework_TestCase {
    
    public $di = null;
    public $session = array();
    public $em = null;
    public $user = null;
    public $video = null;
    public $fileParser = null;
    public $view = null;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() 
    {
        $this->session = array('userId'=>1);
        $this->user = m::mock('\videoViewer\Entities\User');
        $this->video = m::mock('\videoViewer\Entities\Video');
        $this->em = m::mock();
        $this->em->shouldReceive('find')->with('videoViewer\Entities\User',$this->session['userId'])
                 ->andReturn($this->user);
        $this->em->shouldReceive('find')->with('videoViewer\Entities\Video',$this->session['userId'])
                 ->andReturn($this->video);
        $this->di = m::mock('\videoViewer\DIContainer');
        $this->di->shouldReceive('offsetGet')->with('em')->andReturn($this->em);
        $this->fileParser = m::mock('\videoViewer\FileNameParser');
        $this->view = m::mock('\videoViewer\views\CreateAliasView');
        $this->di->shouldReceive('getView')->with('CreateAlias')->andReturn($this->view);
        $vidSeries = m::mock('\videoViewer\Entities\Series');
        $vidSeries->shouldReceive('getId')->andReturn(1);
        $vidSeries->shouldReceive('hasAlias')->with('Series Alias true')->andReturn(true);
        $vidSeries->shouldReceive('hasAlias')->with(stringValue())->andReturn(false);
        $this->video->shouldReceive('getSeries')->andReturn($vidSeries);
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
        $obj = new \videoViewer\controllers\CreateAliasController(array('ajax'=>'1','videoId'=>1),array(),
                $this->session,array(),$this->di);
        $obj->processRequest();
    }

    /**
     * @expectedException \videoViewer\PageRedirectException
     * @expectedExceptionCode 303
     */
    public function testRejectsNoVideoId() 
    {
        $obj = new \videoViewer\controllers\CreateAliasController(array(),array(),
                $this->session,array(),$this->di);
        $obj->processRequest();
    }

    /**
     * @expectedException \videoViewer\PageRedirectException
     * @expectedExceptionCode 403
     */
    public function testRejectsNotLoggedIn() 
    {
        $obj = new \videoViewer\controllers\CreateAliasController(array('videoId'=>1),array(),
                array(),array(),$this->di);
        $obj->processRequest();
    }
    
    public function testGetNoErrorNoAlias()
    {
        $expected = file_get_contents(dirname(__FILE__).'/../../assets/html/CreateAlias_noerror_woalias.html');
        $obj = new \videoViewer\controllers\CreateAliasController(array('videoId'=>1),array(),
                $this->session,array(),$this->di);
        $obj->fileNameParser = $this->fileParser;
        $this->video->shouldReceive('getFileName')->with('mp4')->andReturn('video.mp4');
        $this->video->shouldReceive('__toString')->andReturn('Episode 1');
        $this->video->shouldReceive('getId')->andReturn(1);
        $this->fileParser->shouldReceive('parseFileName');
        $this->fileParser->series = 'not set alias';
        $emRs = m::mock();
        $emRs->shouldReceive('getResult')->andReturn(array());
        $this->em->shouldReceive('createQuery')->with('SELECT s FROM videoViewer\Entities\Series s')
                ->andReturn($emRs);
        $this->view->shouldReceive('setSeries')->with(array());
        $this->view->shouldReceive('render')->andReturn($expected);
        
        $this->assertEquals($expected,$obj->processRequest());
    }

    public function testGetErrorNoAlias()
    {
        $expected = file_get_contents(dirname(__FILE__).'/../../assets/html/CreateAlias_error_woalias.html');
        $obj = new \videoViewer\controllers\CreateAliasController(array('videoId'=>1),array(),
                $this->session,array(),$this->di);
        $obj->fileNameParser = $this->fileParser;
        $this->video->shouldReceive('getFileName')->with('mp4')->andReturn('video.mp4');
        $this->video->shouldReceive('__toString')->andReturn('Episode 1');
        $this->video->shouldReceive('getId')->andReturn(1);
        $this->fileParser->shouldReceive('parseFileName')->andThrow(new \Exception());
        $this->fileParser->series = '';
        $emRs = m::mock();
        $emRs->shouldReceive('getResult')->andReturn(array());
        $this->em->shouldReceive('createQuery')->with('SELECT s FROM videoViewer\Entities\Series s')
                ->andReturn($emRs);
        $this->view->shouldReceive('setSeries')->with(array());
        $this->view->shouldReceive('render')->andReturn($expected);
        
        $this->assertEquals($expected,$obj->processRequest());
    }
    
    /**
     * @expectedException \videoViewer\PageRedirectException
     * @expectedExceptionCode 303
     */
    public function testHandlesPostSameSeriesAndFound()
    {
        $obj = new \videoViewer\controllers\CreateAliasController(array(),array('videoId'=>1,'series'=>1,'alias'=>'Alias 1'),
                $this->session,array(),$this->di);
        $this->em->shouldReceive('find')->with('videoViewer\Entities\Series',1)
                 ->andReturn($this->video->getSeries());
        $alias = m::mock('videoViewer\Entities\SeriesAlias');
        $alias->shouldReceive('setAlias')->with('Alias 1')->andReturn($alias);
        $alias->shouldReceive('getAlias')->andReturn('Alias 1');
        $this->di->shouldReceive('getEntity')->with('SeriesAlias')->andReturn($alias);
        $this->video->getSeries()->shouldReceive('getAliases')->andReturn(array($alias));
        $this->em->shouldReceive('flush');
        $obj->processRequest();
    }

    /**
     * @expectedException \videoViewer\PageRedirectException
     * @expectedExceptionCode 303
     */
    public function testHandlesPostDiffSeriesNotFound()
    {
        $obj = new \videoViewer\controllers\CreateAliasController(array(),array('videoId'=>1,'series'=>2,'alias'=>'Alias 2'),
                $this->session,array(),$this->di);
        $series = m::mock('videoViewer\Entities\Series');
        $this->em->shouldReceive('find')->with('videoViewer\Entities\Series',2)
                 ->andReturn($series);
        $alias = m::mock('videoViewer\Entities\SeriesAlias');
        $alias->shouldReceive('setAlias')->with('Alias 2')->andReturn($alias);
        $alias->shouldReceive('getAlias')->andReturn('Alias 2');
        $this->di->shouldReceive('getEntity')->with('SeriesAlias')->andReturn($alias);
        $otherAlias = m::mock('videoViewer\Entities\SeriesAlias');
        $otherAlias->shouldReceive('getAlias')->andReturn('Alias 1');
        $series->shouldReceive('addAlias')->with($alias)->andReturn($series);
        $this->video->shouldReceive('setSeries')->with($series)->andReturn($this->video);
        $series->shouldReceive('getAliases')->andReturn(array($otherAlias));
        $this->em->shouldReceive('persist')->with($alias);
        $this->em->shouldReceive('flush');
        $obj->processRequest();
    }

}