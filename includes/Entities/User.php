<?php
namespace videoViewer\Entities;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Users of the site
 *
 * @author johnj
 * @Entity(repositoryClass="videoViewer\Repositories\UserRepository")
 * @Table(name="`user`")
 * 
 */
class User {
    /**
     *
     * @var int
     * @Column(type="integer")
     * @Id
     * @GeneratedValue
     */
    private $id;
    /**
     *
     * @var string
     * @column(length=100,name="user_name")
     */
    private $userName;
    /**
     *
     * @var string
     * @column(length=64)
     */
    private $password;
    /**
     *
     * @var string
     * @column(length=255)
     */
    private $name;
    /**
     *
     * @var Doctrine\Common\Collections\ArrayCollection
     * @ManyToMany(targetEntity="videoViewer\Entities\Video", inversedBy="usersWhoViewed")
     */
    private $watchedVideos;
    /**
     *
     * @var Doctrine\Common\Collections\ArrayCollection
     * @ManyToMany(targetEntity="videoViewer\Entities\Series", inversedBy="authorizedUsers")
     */
    private $authorizedSeries;
    /**
     *
     * @var \boolean
     * @Column(type="boolean")
     */
    private $admin=0;
    
    const SALT = "V9Fw\"(uX3up&viNkCag'xxGTkU]uTIIUh~/eI(Wdpg<\$e5i.F$~f,Rb&Z](-AjM";

    public function  __construct() {
        $this->watchedVideos = new ArrayCollection();
        $this->authorizedSeries = new ArrayCollection();
    }

    public function getWatchedVideos(){
        return $this->watchedVideos;
    }
    
    public function addWatchedVideo($video){
        if(!$this->watchedVideos->contains($video)){$this->watchedVideos[]=$video;}
    }
    
    public function getAuthorizedSeries(){
        return $this->authorizedSeries;
    }
    
    public function setName($name){
        $this->name = $name;
    }
    
    public function setPassword($pass){
        $this->password = $pass;
    }
    
    public function setUserName($name){
        $this->userName = $name;
    }
    
    public function getName(){
        return $this->name;
    }
    
    public function hashAndSetPassword($pass)
    {
        $this->password = md5(self::SALT.$pass);
    }
    
    public function getPassword(){
        return $this->password;
    }
    
    public function getUserName(){
        return $this->userName;
    }
    
    public function getId(){
        return $this->id;
    }
    
    public function isAdmin(){
        return $this->admin;
    }
    
    public function canAccessSeries($series){
        return $this->authorizedSeries->contains($series);
    }
    
    public function addAuthorizedSeries($series){
        if(!$this->authorizedSeries->contains($series))
        {
            $this->authorizedSeries->add($series);
        }
    }
    
    public function removeAuthorizedSeries($series)
    {
        if($this->authorizedSeries->contains($series))
        {
            $this->authorizedSeries->removeElement($series);
        }
    }
    
    public function removeWatchedVideo($video){
        if($this->watchedVideos->contains($video)){
            $this->watchedVideos->removeElement($video);
        }
    }
    
    public function getUserHash(){
        return md5($this->getName().$this->getPassword());
    }
    
    public function getRokuXML(){
        return '/userxml/'.$this->getUserHash().'.xml';
    }

}
?>
