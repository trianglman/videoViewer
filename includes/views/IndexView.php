<?php
namespace videoViewer\views;

/**
 * Page view for the index page
 *
 * @author John Judy <john.a.judy@gmail.com>
 */
class IndexView extends PageView{
    
    /**
     * Whether there is an error message to display
     * @var boolean
     */
    public $hasError = false;
    
    /**
     * The error message to display
     * @var string
     */
    public $errorMessage = '';
    
    /**
     * Initializes the Index page view
     * @param \videoViewer\DIContainer $di 
     * 
     * @return void
     */
    public function __construct(\videoViewer\DIContainer $di){
        $template = $di->loadTemplate('index');
        parent::__construct($di,$template);
    }
}

?>
