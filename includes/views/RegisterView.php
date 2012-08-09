<?php

namespace videoViewer\views;

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * View for the register page
 *
 * @author John Judy <john.a.judy@gmail.com>
 */
class RegisterView extends PageView {
    
    /**
     *
     * @var boolean
     */
    public $hasError = false;
    
    /**
     *
     * @var string
     */
    public $errorMessage = '';

    /**
     * Initializes the Index page view
     * @param \videoViewer\DIContainer $di 
     * 
     * @return void
     */
    public function __construct(\videoViewer\DIContainer $di) {
        $template = $di->loadTemplate('Register');
        parent::__construct($di, $template);
    }

}

?>