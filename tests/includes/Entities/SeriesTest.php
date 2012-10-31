<?php

namespace videoViewer\Entities;

use \Mockery as m;

/**
 * Test class for Series.
 * Generated by PHPUnit on 2012-10-30 at 23:06:41.
 */
class SeriesTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var Series
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->object = new Series;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {
        
    }

    public function testGetEpisodes() {
        $video = m::mock('videoViewer\Entities\Video');
        $video->shouldReceive('setSeries')->with($this->object)->andReturn($video)->once();
        $this->assertEquals(0,$this->object->getEpisodes()->count());
        $this->object->addEpisode($video);
        $this->assertEquals(1,$this->object->getEpisodes()->count());
        $tmp = $this->object->getEpisodes();
        $this->assertEquals($video,$tmp[0]);
    }

    public function testGetAliases() {
        $alias = m::mock('videoViewer\Entities\SeriesAlias');
        $alias->shouldReceive('setSeries')->with($this->object)->andReturn($alias)->once();
        $this->assertEquals(0,$this->object->getAliases()->count());
        $this->object->addAlias($alias);
        $this->assertEquals(1,$this->object->getAliases()->count());
        $tmp = $this->object->getAliases();
        $this->assertEquals($alias,$tmp[0]);
    }

    public function testGetDescription() {
        $this->assertEquals('',$this->object->getDescription());
        $this->object->setDescription('a description');
        $this->assertEquals('a description',$this->object->getDescription());
    }

    public function testGetImage() {
        $this->assertEquals('',$this->object->getImage());
        $this->object->setImage('/path/to/image.jpg');
        $this->assertEquals('/path/to/image.jpg',$this->object->getImage());
    }

    public function testGetName() {
        $this->assertEquals('',$this->object->getName());
        $this->object->setName('Series Name');
        $this->assertEquals('Series Name',$this->object->getName());
    }

    public function testGetSeriesId() {
        $this->assertEquals(0,$this->object->getSeriesId());
        $this->object->setSeriesId(1);
        $this->assertEquals(1,$this->object->getSeriesId());
    }

    /**
     * @depends testGetImage
     */
    public function testGetSrc() {
        $_SERVER['DOCUMENT_ROOT'] = '/path/';
        $this->object->setImage('/path/to/image.jpg');
        $this->assertEquals('to/image.jpg',$this->object->getSrc());
    }

    /**
     * @depends testGetAliases
     */
    public function testHasAlias() {
        $alias = m::mock('videoViewer\Entities\SeriesAlias');
        $alias->shouldReceive('setSeries')->with($this->object)->andReturn($alias)->once();
        $alias->shouldReceive('getAlias')->andReturn('Does not exist');
        $this->assertFalse($this->object->hasAlias('Does not exist'));
        $this->object->addAlias($alias);
        $this->assertTrue($this->object->hasAlias('Does not exist'));
    }

}

?>
