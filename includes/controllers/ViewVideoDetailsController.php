<?php

namespace videoViewer\controllers;

use videoViewer as v;

/**
 * Description of Controller
 *
 * @author John Judy <john.a.judy@gmail.com>
 */
class ViewVideoDetailsController extends PageController {

    /**
     * Processes an AJAX request this page knows how to handle
     * 
     * Should generally return a json string
     * 
     * @return string
     */
    protected function _processAjax() {
        throw new v\PageRedirectException(501);
    }

    /**
     * Processes a standard GET request for the page
     * 
     * @return string
     */
    protected function _processGet() {
        $view = $this->_di->getView('ViewVideoDetails');
        $view->video = $this->_di['em']->find('videoViewer\Entities\Video',$this->_get['id']);
        return $view->render();
    }

    /**
     * Processes a POST form submission for this page
     * 
     * @return string
     */
    protected function _processPost() {
        throw new v\PageRedirectException(501);
    }

    /**
     * Verifies that the logged in user has access to perform the supplied action
     * 
     * @param string $action [Optional] Defaults to checking if the user can load the page
     * 
     * @return boolean
     */
    protected function _verifyAccess($action='load') {
        if ($action == 'load') {
            if (is_null($this->_user) || 
                    ((empty($this->_get['id']) ||!is_numeric($this->_get['id'])) &&
                     (empty($this->_post['id']) ||!is_numeric($this->_post['id'])))
                ) {
                return false;
            }
        }
        return true;
    }

}

?>