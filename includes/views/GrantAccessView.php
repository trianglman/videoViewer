<?php

namespace videoViewer\views;

/**
 * View for the grant access page
 *
 * @author John Judy <john.a.judy@gmail.com>
 */
class GrantAccessView extends PageView {
    
    /**
     *
     * @var \videoViewer\entities\Series
     */
    public $series=null;
    /**
     *
     * @var array[int]\stdClass
     */
    public $users=array();

    /**
     * Initializes the Index page view
     * @param \videoViewer\DIContainer $di 
     * 
     * @return void
     */
    public function __construct(\videoViewer\DIContainer $di) 
    {
        $template = $di->loadTemplate('GrantAccess');
        parent::__construct($di, $template);
    }
    
    /**
     * Adds a user option to the list
     * 
     * @param \videoViewer\Entities\User $user 
     * 
     * @return void
     */
    public function addUser(\videoViewer\Entities\User $user)
    {
        $opt = new \stdClass();
        $opt->userId=$user->getId();
        $opt->userHasAccess = $user->canAccessSeries($this->series);
        $opt->userName = $user->getName();
        $this->users[]=$opt;
    }
    
    /**
     * Gets the set series's ID
     * @return int
     */
    public function seriesId()
    {
        return $this->series->getId();
    }
    
    /**
     * Gets the set series's image path
     * @return string
     */
    public function seriesImage()
    {
        return $this->series->getSrc();
    }
    
    /**
     * Gets the set series's name
     * @return string
     */
    public function seriesName()
    {
        return $this->series->getName();
    }

}

?>