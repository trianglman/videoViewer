<?php
namespace videoViewer;

use \Mockery as m;

/**
 * 
 */
class DIContainerTest extends \PHPUnit_Framework_TestCase 
{

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() 
    {
        
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    public function teardown() 
    {
        m::close();
    }

    public function testLoadsTemplate() 
    {
        $expected = file_get_contents(dirname(__FILE__).'/../assets/testTemplate.tpl');
        $di = new \videoViewer\DIContainer();
        $this->assertEquals($expected,$di->loadTemplate('../tests/assets/testTemplate'));
    }

    /**
     * @depends testLoadsTemplate
     */
    public function testReturnsEmptyBadTemplate() 
    {
        $di = new \videoViewer\DIContainer();
        $this->assertEquals('',$di->loadTemplate('../tests/assets/doesntexist'));
    }
    
    public function testGetView()
    {
        require_once(dirname(__FILE__).'/../assets/TestView.php');
        $di = new \videoViewer\DIContainer();
        $this->assertInstanceOf('\videoViewer\views\TestView',$di->getView('Test'));
    }
    
    /**
     * @depends testGetView
     * @expectedException \RuntimeException
     */
    public function testExceptionOnUnknownView()
    {
        $di = new \videoViewer\DIContainer();
        $di->getView('doesntexist');
    }

    public function testGetEntity()
    {
        require_once(dirname(__FILE__).'/../assets/TestEntity.php');
        $di = new \videoViewer\DIContainer();
        $this->assertInstanceOf('\videoViewer\Entities\TestEntity',$di->getEntity('TestEntity'));
    }
    
    /**
     * @depends testGetEntity
     * @expectedException \RuntimeException
     */
    public function testExceptionOnUnknownEntity()
    {
        $di = new \videoViewer\DIContainer();
        $di->getEntity('doesntexist');
    }
    
    public function testFilesystemRenames()
    {
        $oldfile = dirname(__FILE__).'/../assets/filetorename';
        $newfile = dirname(__FILE__).'/../assets/filerenamed';
        file_put_contents($oldfile,'');
        $di = new \videoViewer\DIContainer();
        $di->fileSystem('rename',array($oldfile,$newfile));
        $this->assertTrue(file_exists($newfile));
        unlink($newfile);
    }
    
    /**
     * @depends testFilesystemRenames
     * @expectedException \InvalidArgumentException
     */
    public function testFilesystemFailsRenameTooFewArguments()
    {
        $di = new \videoViewer\DIContainer();
        $di->fileSystem('rename', array(dirname(__FILE__).'/../assets/filetorename'));
    }
    
    /**
     * @depends testFilesystemRenames
     * @expectedException \InvalidArgumentException
     */
    public function testFilesystemFailsRenameOutOfScope()
    {
        $di = new \videoViewer\DIContainer();
        $di->fileSystem('rename', array('/tmp/outofscope','/tmp/stilloutofscope'));
    }
    
    public function testFilesystemFileExists()
    {
        file_put_contents(dirname(__FILE__).'/../assets/filethatexists','');
        $di = new \videoViewer\DIContainer();
        $this->assertTrue($di->fileSystem('file_exists',array(dirname(__FILE__).'/../assets/filethatexists')));
        $this->assertFalse($di->fileSystem('file_exists',array(dirname(__FILE__).'/../assets/filethatdoesntexist')));
        unlink(dirname(__FILE__).'/../assets/filethatexists');
    }
    
    /**
     * @depends testFilesystemFileExists
     * @expectedException \InvalidArgumentException
     */
    public function testFilesystemFailsFileExistsTooFewArguments()
    {
        $di = new \videoViewer\DIContainer();
        $di->fileSystem('file_exists', array());
    }
    
    public function testFilesystemPutContents()
    {
        $string = 'the test succeeded';
        $file = dirname(__FILE__).'/../assets/writtenfile';
        $di = new \videoViewer\DIContainer();
        $di->fileSystem('file_put_contents',array($file,$string));
        $this->assertEquals($string,file_get_contents($file));
        unlink($file);
    }

    /**
     * @depends testFilesystemPutContents
     * @expectedException \InvalidArgumentException
     */
    public function testFilesystemFailsPutContentsTooFewArguments()
    {
        $di = new \videoViewer\DIContainer();
        $di->fileSystem('file_put_contents', array(dirname(__FILE__).'/../assets/writtenfile'));
    }
    
    /**
     * @depends testFilesystemPutContents
     * @expectedException \InvalidArgumentException
     */
    public function testFilesystemFailsPutContentsOutOfScope()
    {
        $di = new \videoViewer\DIContainer();
        $di->fileSystem('file_put_contents', array('/tmp/outofscope','hope this doesnt write'));
    }
    
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testFilesystemFailsInvalidRequest()
    {
        $di = new \videoViewer\DIContainer();
        $di->fileSystem('commanddoesntexist', array());
    }
    
}