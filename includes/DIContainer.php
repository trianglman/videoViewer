<?php
namespace videoViewer;
/**
 * A dependency injector container
 *
 * @author John Judy <john.a.judy@gmail.com>
 */
class DIContainer extends \Pimple{
    
    public function __construct($em=null){
        $this['em']=$em;
    }
    
    /**
     * Loads a timplate file's contents from the templates/ directory
     * @param string $templateName
     * @return string 
     */
    public function loadTemplate($templateName){
        $templateDir = dirname(__FILE__).'/../templates/';
        if(file_exists($templateDir.$templateName.'.tpl')){
            return file_get_contents($templateDir.$templateName.'.tpl');
        }
        return '';
    }
    
    /**
     * Gets a view class
     * @param string $page
     * @return PageView
     */
    public function getView($page){
        $class = '\\videoViewer\\views\\'.ucfirst($page).'View';
        if(class_exists($class, true)){
            return new $class($this);
        }
        else{
            throw new \RuntimeException('Class not found: '.$class);
        }
    }
    
    public function getEntity($name){
        $class = '\\videoViewer\\entities\\'.$name;
        if(class_exists($class,true))
        {
            return new $class();
        }
        else
        {
            throw new \RuntimeException('Class not found: '.$class);
        }
    }
    
    /**
     * Runs an approved file system command
     * Allowed commands: 
     *      rename, if the files are moving in the videoViewer scope
     *      file_exists
     *      file_put_contents, if the target file iss in the videoViewer scope
     * 
     * @param string $cmd
     * @param array $params 
     * 
     * @return mixed
     * 
     * @throws \InvalidArgumentException if the command is not called in an accepted way
     */
    public function fileSystem($cmd,$params)
    {
        $validPath = realpath(dirname(__FILE__).'/..');
        switch($cmd)
        {
            case 'rename':
                if(count($params)!==2)
                {
                    throw new \InvalidArgumentException('Rename must be called with two arguments, '.count($params).' supplied.');
                }
                $from = realpath(dirname($params[0])).'/'.pathinfo($params[0],PATHINFO_BASENAME);
                $to = realpath(dirname($params[1])).'/'.pathinfo($params[1],PATHINFO_BASENAME);
                if(substr($from, 0, strlen($validPath))!==substr($to, 0, strlen($validPath)) && 
                        substr($from, 0, strlen($validPath))!==$validPath)
                {
                    throw new \InvalidArgumentException('Files can only be moved in the local context');
                }
                return rename($from, $to);
            case 'file_exists':
                if(count($params)!==1)
                {
                    throw new \InvalidArgumentException('file_exists must be called with one argument, '
                            .count($params).' supplied.');
                }
                return file_exists($params[0]);
            case 'file_put_contents':
                if(count($params)!==2)
                {
                    throw new \InvalidArgumentException('file_put_contents must be called with two arguments, '
                            .count($params).' supplied.');
                }
                $to = realpath($params[0]);
                if(substr($to, 0, strlen($validPath))!==$validPath)
                {
                    throw new \InvalidArgumentException('Files can only be modified in the local context');
                }
                return file_put_contents($to, $params[1]);
            default:
                throw new \InvalidArgumentException('Not a valid command.');
        }
    }
}

?>
