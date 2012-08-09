<?php

namespace videoViewer;
require_once 'Mockery/Loader.php';
require_once 'Hamcrest/Hamcrest.php';
require_once(dirname(__FILE__).'/../processApacheAccess.php');
use \Mockery as m;
$loader = new m\Loader;
$loader->register();

/**
 * Test class for the processApacheAccess script
 *
 * @author John Judy <john.a.judy@gmail.com>
 */
class ProcessApacheAccessTest extends \PHPUnit_Framework_TestCase{
    public function testProcessLogFile(){
        //prepare mocks
        require_once('../includes/Repositories/UserRepository.php');
        $userRepoMock = m::mock('\videoViewer\Repositories\UserRepository');
        require_once('../includes/Repositories/VideoRepository.php');
        $videoRepoMock = m::mock('\videoViewer\Repositories\VideoRepository');
        require_once('../includes/Entities/User.php');
        $userMock = m::mock('\VideoViewer\Entities\User');
        require_once('../includes/Entities/Video.php');
        $videoMock = m::mock('\VideoViewer\Entities\Video');
        require_once('../includes/Entities/Series.php');
        $seriesMock = m::mock('\VideoViewer\Entities\Series');
        require_once('../tools/apacheLogParser/apacheLogFile.php');
        $logMock = m::mock('\apacheLogParser\ApacheLogFile');
        require_once('../tools/apacheLogParser/apacheLogRecord.php');
        $entryMock = m::mock('\apacheLogParser\ApacheLogRecord');
        $userRepoMock->shouldReceive('getUserByHash')
                ->with('82f01b4c7ca9f8c94a12b59ae67c7541')->andReturn($userMock);
        $videoRepoMock->shouldReceive('getByFilename')
                ->with('video')->andReturn($videoMock);
        $userMock->shouldReceive('addWatchedVideo')
                ->with($videoMock)->mock()
                ->shouldReceive('getName')->andReturn('Test User');
        $videoMock->shouldReceive('getSeries')->andReturn($seriesMock)->mock()
                ->shouldReceive('getEpisodeName')->andReturn('Test Video');
        $seriesMock->shouldReceive('getName')->andReturn('Test Series');
        
        $logMock->shouldReceive('getRow')->andReturn(array($entryMock));
        $entryMock->shouldReceive('getRequest')
                ->andReturn('GET /vidPath/82f01b4c7ca9f8c94a12b59ae67c7541/video.mp4 HTTP/1.1')
                ->mock()
                ->shouldReceive('getLastStatus')->andReturn('206');
        
        //test the function
        ob_start();
        processLogFile($logMock,$videoRepoMock,$userRepoMock);
        $this->assertEquals("Adding Test Series: Test Video to Test User's "
                ."watched list.\n",  ob_get_contents());
    }

    public function teardown(){
        m::close();
    }

}

?>
