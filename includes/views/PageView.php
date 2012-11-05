<?php
namespace videoViewer\views;
use videoViewer as v;
/**
 * An abstract page view all other page views should inherit from
 *
 * @author John Judy <john.a.judy@gmail.com>
 */
abstract class PageView extends \Mustache{
    /**
     *
     * @var \videoViewer\DIContainer
     */
    protected $di;
    
    /**
     * Initializes the Page View
     * @param v\DIContainer $di
     * @param string $template
     * @param mixed $view
     * @param array $partials 
     * 
     * @return void
     */
    public function __construct(v\DIContainer $di=null,
            $template = null, $view = null, $partials = null){
        if(is_null($partials)){$partials = array();}
        $partials['head']=$di->loadTemplate('head');
        $partials['nav']=$di->loadTemplate('nav');
        $this->di = $di;
        parent::__construct($template,$this,$partials);
    }
    
    /**
     * Adds a partial to the view
     * @param string $tag
     * @param string $partial 
     * 
     * @return void
     */
    public function addPartial($tag,$partial){
        $this->_partials[$tag]=$partial;
    }
}

?>
