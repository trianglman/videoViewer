<?php
namespace videoViewer\controllers;

use \Mockery as m;

/**
 * 
 */
class EditVideoControllerTest extends \PHPUnit_Framework_TestCase {
    
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
        $this->view = m::mock('\videoViewer\views\EditVideoView');
        $this->di->shouldReceive('getView')->with('EditVideo')->andReturn($this->view);
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
    public function testRejectsInvalidAjax() 
    {
        $video = m::mock('videoViewer\Entities\Video');
        $this->em->shouldReceive('find')->with('videoViewer\Entities\Video',1)->andReturn($video);
        $obj = new \videoViewer\controllers\EditVideoController(array('ajax'=>'1','id'=>1),array(),
                $this->session,array(),$this->di);
        $obj->processRequest();
    }

    public function testGetsCheckSeriesAjax() 
    {
        $expected = array('Season'=>1,'Episode'=>1,'Date'=>'11/18/1980','Details'=>'Some Details.','Name'=>'Episode 1');
        $req = array('ajax'=>'1','id'=>1,'req'=>'checkSeries','seriesId'=>1);
        
        $series = m::mock('videoViewer\Entities\Series');
        $video = m::mock('videoViewer\Entities\Video');
        $series->shouldReceive('getSeriesId')->andReturn(1);
        $video->shouldReceive('getFileNameBase')->andReturn('video');
        $this->em->shouldReceive('find')->with('videoViewer\Entities\Series',1)->andReturn($series);
        $this->em->shouldReceive('find')->with('videoViewer\Entities\Video',1)->andReturn($video);
        
        $tvdb = m::mock('\videoViewer\TvDBApiConn');
        $series = m::mock('\videoViewer\TvDBSeries');
        $episode = m::mock('videoViewer\TvDBEpisode');
        $tvdb->shouldReceive('getFullSeriesInformation')->with(1)->andReturn($series);
        $series->shouldReceive('getEpisodeByEpisodeNumber')->with(1,1)->andReturn($episode);
        $episode->shouldReceive('getSeason')->andReturn(1);
        $episode->shouldReceive('getEpisode')->andReturn(1);
        $episode->shouldReceive('getAirDate')->andReturn(new \DateTime('1980-11-18'));
        $episode->shouldReceive('getDesc')->andReturn('Some Details.');
        $episode->shouldReceive('getName')->andReturn('Episode 1');
        
        $filenameParser = m::mock('\videoViewer\FileNameParser');
        $filenameParser->shouldReceive('parseFileName')->with('video');
        $filenameParser->episode = 1;
        $filenameParser->season = 1;
        
        $obj = new \videoViewer\controllers\EditVideoController($req,array(), $this->session,array(),$this->di);
        $obj->setFileNameParser($filenameParser);
        $obj->setTvdbConn($tvdb);
        $this->assertEquals(json_encode($expected),$obj->processRequest());
    }

    /**
     * @depends testGetsCheckSeriesAjax
     */
    public function testGetsCheckSeriesAjaxAirDateFile() 
    {
        $expected = array('Season'=>1,'Episode'=>1,'Date'=>'11/18/1980','Details'=>'Some Details.','Name'=>'Episode 1');
        $req = array('ajax'=>'1','id'=>1,'req'=>'checkSeries','seriesId'=>1);
        
        $series = m::mock('videoViewer\Entities\Series');
        $video = m::mock('videoViewer\Entities\Video');
        $series->shouldReceive('getSeriesId')->andReturn(1);
        $video->shouldReceive('getFileNameBase')->andReturn('video');
        $this->em->shouldReceive('find')->with('videoViewer\Entities\Series',1)->andReturn($series);
        $this->em->shouldReceive('find')->with('videoViewer\Entities\Video',1)->andReturn($video);
        
        $airDate = new \DateTime('1980-11-18');
        $tvdb = m::mock('\videoViewer\TvDBApiConn');
        $series = m::mock('\videoViewer\TvDBSeries');
        $episode = m::mock('videoViewer\TvDBEpisode');
        $tvdb->shouldReceive('getFullSeriesInformation')->with(1)->andReturn($series);
        $series->shouldReceive('getEpisodeByAirDate')->with($airDate)->andReturn($episode);
        $episode->shouldReceive('getSeason')->andReturn(1);
        $episode->shouldReceive('getEpisode')->andReturn(1);
        $episode->shouldReceive('getAirDate')->andReturn($airDate);
        $episode->shouldReceive('getDesc')->andReturn('Some Details.');
        $episode->shouldReceive('getName')->andReturn('Episode 1');
        
        $filenameParser = m::mock('\videoViewer\FileNameParser');
        $filenameParser->shouldReceive('parseFileName')->with('video');
        $filenameParser->airDate = $airDate;
        
        $obj = new \videoViewer\controllers\EditVideoController($req,array(), $this->session,array(),$this->di);
        $obj->setFileNameParser($filenameParser);
        $obj->setTvdbConn($tvdb);
        $this->assertEquals(json_encode($expected),$obj->processRequest());
    }

    /**
     * @depends testGetsCheckSeriesAjax
     */
    public function testGetsCheckSeriesAjaxUnfoundSeries() 
    {
        $expected = 'Invalid series';
        $req = array('ajax'=>'1','id'=>1,'req'=>'checkSeries','seriesId'=>99);
        
        $series = m::mock('videoViewer\Entities\Series');
        $video = m::mock('videoViewer\Entities\Video');
        $series->shouldReceive('getSeriesId')->andReturn(1);
        $video->shouldReceive('getFileNameBase')->andReturn('video');
        $this->em->shouldReceive('find')->with('videoViewer\Entities\Series',99)->andReturn(null);
        $this->em->shouldReceive('find')->with('videoViewer\Entities\Video',1)->andReturn($video);
        
        $obj = new \videoViewer\controllers\EditVideoController($req,array(), $this->session,array(),$this->di);
        $this->assertEquals(json_encode($expected),$obj->processRequest());
    }

    /**
     * @depends testGetsCheckSeriesAjax
     */
    public function testGetsCheckSeriesAjaxUnfoundEpisode() 
    {
        $expected = 'Episode not matched';
        $req = array('ajax'=>'1','id'=>1,'req'=>'checkSeries','seriesId'=>1);
        
        $series = m::mock('videoViewer\Entities\Series');
        $video = m::mock('videoViewer\Entities\Video');
        $series->shouldReceive('getSeriesId')->andReturn(1);
        $video->shouldReceive('getFileNameBase')->andReturn('video');
        $this->em->shouldReceive('find')->with('videoViewer\Entities\Series',1)->andReturn($series);
        $this->em->shouldReceive('find')->with('videoViewer\Entities\Video',1)->andReturn($video);
        
        $airDate = new \DateTime('1980-11-18');
        $tvdb = m::mock('\videoViewer\TvDBApiConn');
        $series = m::mock('\videoViewer\TvDBSeries');
        $tvdb->shouldReceive('getFullSeriesInformation')->with(1)->andReturn($series);
        $series->shouldReceive('getEpisodeByAirDate')->with($airDate)->andReturn(null);
        
        $filenameParser = m::mock('\videoViewer\FileNameParser');
        $filenameParser->shouldReceive('parseFileName')->with('video');
        $filenameParser->airDate = $airDate;
        
        $obj = new \videoViewer\controllers\EditVideoController($req,array(), $this->session,array(),$this->di);
        $obj->setFileNameParser($filenameParser);
        $obj->setTvdbConn($tvdb);
        $this->assertEquals(json_encode($expected),$obj->processRequest());
    }

    /**
     * @expectedException \videoViewer\PageRedirectException
     * @expectedExceptionCode 403
     */
    public function testRejectsNoVideoId() 
    {
        $obj = new \videoViewer\controllers\EditVideoController(array(),array(),
                $this->session,array(),$this->di);
        $obj->processRequest();
    }

    /**
     * @expectedException \videoViewer\PageRedirectException
     * @expectedExceptionCode 403
     */
    public function testRejectsNotLoggedIn() 
    {
        $obj = new \videoViewer\controllers\EditVideoController(array('id'=>1),array(),
                array(),array(),$this->di);
        $obj->processRequest();
    }
    
    public function testProcessesGet()
    {
        $expected = file_get_contents(dirname(__FILE__).'/../../assets/html/EditVideo_noError.html');
        
        $video = m::mock('videoViewer\Entities\Video');
        $this->em->shouldReceive('find')->with('videoViewer\Entities\Video',1)->andReturn($video);
        
        $series = m::mock('videoViewer\Entities\Series');
        $query = m::mock();
        $query->shouldReceive('getResult')->andReturn(array($series,$series));
        $this->em->shouldReceive('createQuery')->with('SELECT s FROM videoViewer\Entities\Series s')->andReturn($query);
        
        $this->view->shouldReceive('addSeries')->with($series)->twice();
        $this->view->shouldReceive('render')->andReturn($expected);
        
        $obj = new \videoViewer\controllers\EditVideoController(array('id'=>1),array(),
                $this->session,array(),$this->di);
        $this->assertEquals($expected,$obj->processRequest());
    }
    
    /**
     * @expectedException \videoViewer\PageRedirectException
     * @expectedExceptionCode 303
     */
    public function testProcessesValidPost()
    {
        $post = array('id'=>'1','series'=>'1','name'=>'Episode 1','season'=>'1','episode'=>'1','date'=>'11/18/1980',
                    'details'=>'Some video details.','notes'=>'I had a note');
        
        $video = m::mock('videoViewer\Entities\Video');
        $video->shouldReceive('setEpisodeName')->with($post['name'])->andReturn($video);
        $video->shouldReceive('setSeasonNumber')->with($post['season'])->andReturn($video);
        $video->shouldReceive('setEpisodeNumber')->with($post['episode'])->andReturn($video);
        $video->shouldReceive('setAirDate')->with(m::type('\DateTime'))->andReturn($video);
        $video->shouldReceive('setDetails')->with($post['details'])->andReturn($video);
        $video->shouldReceive('setNotes')->with($post['notes'])->andReturn($video);
        $video->shouldReceive('getId')->andReturn(1);
        $series = m::mock('videoViewer\Entities\Series');
        $series->shouldReceive('addEpisode')->with($video)->andReturn($series);
        $this->em->shouldReceive('find')->with('videoViewer\Entities\Video',1)->andReturn($video);
        $this->em->shouldReceive('find')->with('videoViewer\Entities\Series',1)->andReturn($series);
        $this->em->shouldReceive('flush');
        
        $obj = new \videoViewer\controllers\EditVideoController(array(),$post,$this->session,array(),$this->di);
        $obj->processRequest();
    }
    
    /**
     * @depends testProcessesValidPost
     */
    public function testProcessesInvalidSeriesPost()
    {
        $post = array('id'=>'1','series'=>'1000','name'=>'Episode 1','season'=>'1','episode'=>'1','date'=>'11/18/1980',
                    'details'=>'Some video details.','notes'=>'I had a note');
        $expected = file_get_contents(dirname(__FILE__).'/../../assets/html/EditVideo_invalidSeriesError.html');
        
        $video = m::mock('videoViewer\Entities\Video');
        $this->em->shouldReceive('find')->with('videoViewer\Entities\Video',1)->andReturn($video);
        $this->em->shouldReceive('find')->with('videoViewer\Entities\Series',1000)->andReturn(null);
        $this->em->shouldReceive('flush');
        
        $series = m::mock('videoViewer\Entities\Series');
        $query = m::mock();
        $query->shouldReceive('getResult')->andReturn(array($series,$series));
        $this->em->shouldReceive('createQuery')->with('SELECT s FROM videoViewer\Entities\Series s')->andReturn($query);
        
        $this->view->shouldReceive('addSeries')->with($series)->twice();
        $this->view->shouldReceive('render')->andReturn($expected);
        
        $obj = new \videoViewer\controllers\EditVideoController(array(),$post,$this->session,array(),$this->di);
        $this->assertEquals($expected,$obj->processRequest());
    }
    
    /**
     * @depends testProcessesValidPost
     */
    public function testProcessesInvalidInputPost()
    {
        $post = array('id'=>'1','series'=>'1','name'=>'','season'=>'1','episode'=>'1','date'=>'not a date',
                    'details'=>'Some video details.','notes'=>'I had a note');
        $expected = file_get_contents(dirname(__FILE__).'/../../assets/html/EditVideo_error.html');
        
        $video = m::mock('videoViewer\Entities\Video');
        $series = m::mock('videoViewer\Entities\Series');
        $this->em->shouldReceive('find')->with('videoViewer\Entities\Video',1)->andReturn($video);
        $this->em->shouldReceive('find')->with('videoViewer\Entities\Series',1)->andReturn($series);
        $this->em->shouldReceive('flush');
        
        $series = m::mock('videoViewer\Entities\Series');
        $query = m::mock();
        $query->shouldReceive('getResult')->andReturn(array($series,$series));
        $this->em->shouldReceive('createQuery')->with('SELECT s FROM videoViewer\Entities\Series s')->andReturn($query);
        
        $this->view->shouldReceive('addSeries')->with($series)->twice();
        $this->view->shouldReceive('render')->andReturn($expected);
        
        $obj = new \videoViewer\controllers\EditVideoController(array(),$post,$this->session,array(),$this->di);
        $this->assertEquals($expected,$obj->processRequest());
    }
    
}