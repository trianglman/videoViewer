<?php
namespace videoViewer\controllers;
use videoViewer as v;

/**
 * A generic page controller all pages should extend
 *
 * @author John Judy <john.a.judy@gmail.com>
 */
abstract class PageController {
    /**
     * Supplied GET variables
     * @var array[string]string
     */
    protected $_get=array();
    /**
     * Supplied POST variables
     * @var array[string]string
     */
    protected $_post=array();
    /**
     * Supplied session variables
     * @var array[string]string
     */
    protected $_session=array();
    /**
     * Supplied cookie variables
     * @var array[string]string
     */
    protected $_cookie=array();
    /**
     * Dependency injection container used to load templates, etc.
     * @var \Pimple
     */
    protected $_di=null;
    /**
     * The requesting user, if any
     * @var \videoViewer\Entities\User
     */
    protected $_user=null;
    
    public function __construct($get, $post, $session, $cookie, \Pimple $di){
        $this->_get = $get;
        $this->_post = $post;
        $this->_session = $session;
        $this->_cookie = $cookie;
        $this->_di = $di;
        if(!empty ($this->_session['userId'])){
            $this->_user = $this->_di['em']->find('videoViewer\Entities\User',
                    $this->_session['userId']);
        }
    }
    
    public function __destruct(){
        $_SESSION=$this->_session;
        foreach($this->_cookie as $cookie=>$val){
            setcookie($cookie, $val, 60*60*24*7);
        }
    }
    
    /**
     * Processes a user request
     * 
     * @return string
     */
    public function processRequest(){
        if(!$this->_verifyAccess()){
            throw new v\PageRedirectException(403);
        }
        if(isset($this->_get['ajax']) || isset ($this->_post['ajax'])){
            return $this->_processAjax();
        }
        if(!empty($this->_post)){
            return $this->_processPost();
        }
        return $this->_processGet();
    }
    
    /**
     * Processes an AJAX request this page knows how to handle
     * 
     * Should generally return a json string
     * 
     * @return string
     */
    abstract protected function _processAjax();
    
    /**
     * Processes a standard GET request for the page
     * 
     * @return string
     */
    abstract protected function _processGet();
    
    /**
     * Processes a POST form submission for this page
     * 
     * @return string
     */
    abstract protected function _processPost();
    
    /**
     * Verifies that the logged in user has access to perform the supplied action
     * 
     * @param string $action [Optional] Defaults to checking if the user can load the page
     * 
     * @return boolean
     */
    abstract protected function _verifyAccess($action='load');
}

?>
