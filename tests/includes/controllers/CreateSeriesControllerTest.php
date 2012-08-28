<?php
namespace videoViewer\controllers;

use \Mockery as m;

/**
 * 
 */
class CreateSeriesControllerTest extends \PHPUnit_Framework_TestCase {
    
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
        $this->fileParser = m::mock('\videoViewer\FileNameParser');
        $this->view = m::mock('\videoViewer\views\CreateSeriesView');
        $this->di->shouldReceive('getView')->with('CreateSeries')->andReturn($this->view);
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
        $obj = new \videoViewer\controllers\CreateSeriesController(array('ajax'=>'1','videoId'=>1),array(),
                $this->session,array(),$this->di);
        $obj->processRequest();
    }

    /**
     * @expectedException \videoViewer\PageRedirectException
     * @expectedExceptionCode 303
     */
    public function testRejectsNoVideoId() 
    {
        $obj = new \videoViewer\controllers\CreateSeriesController(array(),array(),
                $this->session,array(),$this->di);
        $obj->processRequest();
    }

    /**
     * @expectedException \videoViewer\PageRedirectException
     * @expectedExceptionCode 403
     */
    public function testRejectsNotLoggedIn() 
    {
        $obj = new \videoViewer\controllers\CreateSeriesController(array('videoId'=>1),array(),
                array(),array(),$this->di);
        $obj->processRequest();
    }
    
    public function testGetTwoSeries()
    {
        $expected = file_get_contents(dirname(__FILE__).'/../../assets/html/CreateSeries_twoSeriesOpts.html');
        $obj = new \videoViewer\controllers\CreateSeriesController(array('videoId'=>1),array(),
                $this->session,array(),$this->di);
        $video = m::mock('videoViewer\Entities\Video');
        $video->shouldReceive('getId')->andReturn(1);
        $video->shouldReceive('getFileNameBase')->andReturn('video');
        $this->em->shouldReceive('find')->with('videoViewer\Entities\Video',1)->andReturn($video);
        
        $fileParser = m::mock('\videoViewer\FileNameParser');
        $fileParser->shouldReceive('parseFileName')->with('video');
        $fileParser->series = 'Series 1';
        
        $series = m::mock('\videoViewer\TvDBSeries');
        $series->seriesid = 1;
        $series->bannerUrl = 'http://tvdb.com/image.jpg';
        $tvdb = m::mock('\videoViewer\TvDBApiConn');
        $tvdb->shouldReceive('findSeries')->with('Series 1')->andReturn(array($series,$series));
        $tvdb->shouldReceive('getBanner')->with('http://tvdb.com/image.jpg')->andReturn('123');
        
        $this->di->shouldReceive('fileSystem')->with('file_exists',m::type('array'))->andReturn(false);
        $this->di->shouldReceive('fileSystem')->with('file_put_contents',m::type('array'));
        
        $this->view->shouldReceive('addSeriesOpt')->with($series);
        $this->view->shouldReceive('render')->andReturn($expected);
        
        $obj->setParser($fileParser);
        $obj->setTvdbConn($tvdb);
        
        $this->assertEquals($expected,$obj->processRequest());
    }

    /**
     * @expectedException \videoViewer\PageRedirectException
     * @expectedExceptionCode 403
     */
    public function testHandlesInvalidPost()
    {
        $obj = new \videoViewer\controllers\CreateSeriesController(array(),array('videoId'=>'1','seriesId'=>'notASeries'),
                $this->session,array(),$this->di);
        $obj->processRequest();
    }

    public function testHandlesPostSuccess()
    {
        $obj = new \videoViewer\controllers\CreateSeriesController(array(),array('videoId'=>'1','seriesId'=>'1'),
                $this->session,array(),$this->di);
        $airDate = new \DateTime('1980-11-18');
        $video = m::mock('videoViewer\Entities\Video');
        $video->shouldReceive('getFileNameBase')->andReturn('video');
        $video->shouldReceive('setAirDate')->with($airDate)->andReturn($video)->once();
        $video->shouldReceive('setDetails')->with('Episode desc.')->andReturn($video)->once();
        $video->shouldReceive('setEpisodeNumber')->with(1)->andReturn($video)->once();
        $video->shouldReceive('setSeasonNumber')->with(1)->andReturn($video)->once();
        $video->shouldReceive('setNotes')->with('')->andReturn($video)->once();
        $this->em->shouldReceive('find')->with('videoViewer\Entities\Video',1)->andReturn($video);
        
        $fileParser = m::mock('\videoViewer\FileNameParser');
        $fileParser->shouldReceive('parseFileName')->with('video');
        $fileParser->series = 'Series 1';
        $fileParser->episode = 1;
        $fileParser->season = 1;
        
        $episode = m::mock('\videoViewer\TvDBEpisode');
        $episode->airDate = $airDate;
        $episode->desc = 'Episode desc.';
        $episode->episode = 1;
        $episode->season = 1;
        $tvdbSeries = m::mock('\videoViewer\TvDBSeries');
        $tvdbSeries->seriesid = 1;
        $tvdbSeries->desc = 'A description.';
        $tvdbSeries->name = 'Series 1';
        $tvdbSeries->shouldReceive('getEpisodeByEpisodeNumber')->with(1,1)->andReturn($episode);
        $tvdb = m::mock('\videoViewer\TvDBApiConn');
        $tvdb->shouldReceive('getFullSeriesInformation')->with(1)->andReturn($tvdbSeries);
        
        $series = m::mock('videoViewer\Entities\Series');
        $mainAlias = m::mock('videoViewer\Entities\SeriesAlias');
        $mainAlias->shouldReceive('setAlias')->with('Series 1')->andReturn($mainAlias);
        $series->shouldReceive('setDescription')->with('A description.')->andReturn($series)->once();
        $series->shouldReceive('setImage')->with('/1\.jpg$/')->andReturn($series)->once();
        $series->shouldReceive('setName')->with('Series 1')->andReturn($series)->once();
        $series->shouldReceive('setSeriesId')->with('1')->andReturn($series)->once();
        $series->shouldReceive('addAlias')->with($mainAlias)->andReturn($series)->once();
        $series->shouldReceive('addEpisode')->with($video)->andReturn($series)->once();
        $this->di->shouldReceive('getEntity')->with('Series')->andReturn($series)->once();
        $this->di->shouldReceive('getEntity')->with('SeriesAlias')->andReturn($mainAlias)->once();
        
        $this->di->shouldReceive('fileSystem')->with('rename',m::type('array'));
        $this->em->shouldReceive('persist')->with($series)->once();
        $this->em->shouldReceive('flush');
        
        $obj->setParser($fileParser);
        $obj->setTvdbConn($tvdb);
        $obj->processRequest();
    }

    /**
     * @depends testHandlesPostSuccess
     */
    public function testHandlesPostSuccessDiffAlias()
    {
        $obj = new \videoViewer\controllers\CreateSeriesController(array(),array('videoId'=>'1','seriesId'=>'1'),
                $this->session,array(),$this->di);
        $airDate = new \DateTime('1980-11-18');
        $video = m::mock('videoViewer\Entities\Video');
        $video->shouldReceive('getFileNameBase')->andReturn('video');
        $video->shouldReceive('setAirDate')->with($airDate)->andReturn($video)->once();
        $video->shouldReceive('setDetails')->with('Episode desc.')->andReturn($video)->once();
        $video->shouldReceive('setEpisodeNumber')->with(1)->andReturn($video)->once();
        $video->shouldReceive('setSeasonNumber')->with(1)->andReturn($video)->once();
        $video->shouldReceive('setNotes')->with('')->andReturn($video)->once();
        $this->em->shouldReceive('find')->with('videoViewer\Entities\Video',1)->andReturn($video);
        
        $fileParser = m::mock('\videoViewer\FileNameParser');
        $fileParser->shouldReceive('parseFileName')->with('video');
        $fileParser->series = 'My Series 1';
        $fileParser->airDate = $airDate;
        
        $episode = m::mock('\videoViewer\TvDBEpisode');
        $episode->airDate = $airDate;
        $episode->desc = 'Episode desc.';
        $episode->episode = 1;
        $episode->season = 1;
        $tvdbSeries = m::mock('\videoViewer\TvDBSeries');
        $tvdbSeries->seriesid = 1;
        $tvdbSeries->desc = 'A description.';
        $tvdbSeries->name = 'Series 1';
        $tvdbSeries->shouldReceive('getEpisodeByAirDate')->with($airDate)->andReturn($episode);
        $tvdb = m::mock('\videoViewer\TvDBApiConn');
        $tvdb->shouldReceive('getFullSeriesInformation')->with(1)->andReturn($tvdbSeries);
        
        $series = m::mock('videoViewer\Entities\Series');
        $mainAlias = m::mock('videoViewer\Entities\SeriesAlias');
        $mainAlias->shouldReceive('setAlias')->with('My Series 1')->andReturn($mainAlias);
        $altAlias = m::mock('videoViewer\Entities\SeriesAlias');
        $altAlias->shouldReceive('setAlias')->with('Series 1')->andReturn($altAlias);
        $series->shouldReceive('setDescription')->with('A description.')->andReturn($series)->once();
        $series->shouldReceive('setImage')->with('/1\.jpg$/')->andReturn($series)->once();
        $series->shouldReceive('setName')->with('Series 1')->andReturn($series)->once();
        $series->shouldReceive('setSeriesId')->with('1')->andReturn($series)->once();
        $series->shouldReceive('addAlias')->with($mainAlias)->andReturn($series)->once();
        $series->shouldReceive('addAlias')->with($altAlias)->andReturn($series)->once();
        $series->shouldReceive('addEpisode')->with($video)->andReturn($series)->once();
        $this->di->shouldReceive('getEntity')->with('Series')->andReturn($series)->once();
        $this->di->shouldReceive('getEntity')->with('SeriesAlias')->andReturn($mainAlias)->once();
        $this->di->shouldReceive('getEntity')->with('SeriesAlias')->andReturn($altAlias)->once();
        
        $this->di->shouldReceive('fileSystem')->with('rename',m::type('array'));
        $this->em->shouldReceive('persist')->with($series)->once();
        $this->em->shouldReceive('flush');
        
        $obj->setParser($fileParser);
        $obj->setTvdbConn($tvdb);
        $obj->processRequest();
    }

    /**
     * @depends testHandlesPostSuccess
     * @expectedException \RuntimeException
     */
    public function testHandlesPostUnfoundEpisode()
    {
        $obj = new \videoViewer\controllers\CreateSeriesController(array(),array('videoId'=>'1','seriesId'=>'1'),
                $this->session,array(),$this->di);
        $airDate = new \DateTime('1980-11-18');
        $video = m::mock('videoViewer\Entities\Video');
        $video->shouldReceive('getFileNameBase')->andReturn('video');
        $this->em->shouldReceive('find')->with('videoViewer\Entities\Video',1)->andReturn($video);
        
        $fileParser = m::mock('\videoViewer\FileNameParser');
        $fileParser->shouldReceive('parseFileName')->with('video');
        $fileParser->series = 'My Series 1';
        $fileParser->airDate = $airDate;
        
        $tvdbSeries = m::mock('\videoViewer\TvDBSeries');
        $tvdbSeries->seriesid = 1;
        $tvdbSeries->desc = 'A description.';
        $tvdbSeries->name = 'Series 1';
        $tvdbSeries->shouldReceive('getEpisodeByAirDate')->with($airDate)->andReturn(null);
        $tvdb = m::mock('\videoViewer\TvDBApiConn');
        $tvdb->shouldReceive('getFullSeriesInformation')->with(1)->andReturn($tvdbSeries);
        
        $series = m::mock('videoViewer\Entities\Series');
        $mainAlias = m::mock('videoViewer\Entities\SeriesAlias');
        $mainAlias->shouldReceive('setAlias')->with('My Series 1')->andReturn($mainAlias);
        $altAlias = m::mock('videoViewer\Entities\SeriesAlias');
        $altAlias->shouldReceive('setAlias')->with('Series 1')->andReturn($altAlias);
        $series->shouldReceive('setDescription')->with('A description.')->andReturn($series)->once();
        $series->shouldReceive('setImage')->with('/1\.jpg$/')->andReturn($series)->once();
        $series->shouldReceive('setName')->with('Series 1')->andReturn($series)->once();
        $series->shouldReceive('setSeriesId')->with('1')->andReturn($series)->once();
        $series->shouldReceive('addAlias')->with($mainAlias)->andReturn($series)->once();
        $series->shouldReceive('addAlias')->with($altAlias)->andReturn($series)->once();
        $this->di->shouldReceive('getEntity')->with('Series')->andReturn($series)->once();
        $this->di->shouldReceive('getEntity')->with('SeriesAlias')->andReturn($mainAlias)->once();
        $this->di->shouldReceive('getEntity')->with('SeriesAlias')->andReturn($altAlias)->once();
        
        $this->di->shouldReceive('fileSystem')->with('rename',m::type('array'));
        $this->em->shouldReceive('persist')->with($series)->once();
        $this->em->shouldReceive('flush')->once();
        
        $obj->setParser($fileParser);
        $obj->setTvdbConn($tvdb);
        $obj->processRequest();
    }

}