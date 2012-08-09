<?php
namespace videoViewer\Entities;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * An entire series of episodes
 *
 * @author johnj
 * @Entity
 * @table(name="videoSeries")
 */
class Series {
    /**
     *
     * @var int
     * @Column(type="integer")
     * @Id
     * @GeneratedValue
     */
    private $id;
    /**
     * The TV DB's series ID number
     * @var int
     * @Column(type="integer"),name="series_id"
     */
    private $seriesId;
    /**
     *
     * @var string
     * @column(type="text")
     */
    private $description;
    /**
     *
     * @var string
     * @column(length=255)
     */
    private $name;
    /**
     * The URL of the stored image
     * @var string
     * @column(length=255)
     */
    private $image;
    /**
     *
     * @var Doctrine\Common\Collections\ArrayCollection
     * @OneToMany(targetEntity="videoViewer\Entities\Video", mappedBy="series", cascade={"persist", "remove"})
     * @OrderBy ({"seasonNumber"="ASC","episodeNumber"="ASC"})
     */
    private $episodes;
    /**
     *
     * @var Doctrine\Common\Collections\ArrayCollection
     * @OneToMany(targetEntity="videoViewer\Entities\SeriesAlias", mappedBy="series", cascade={"persist", "remove"})
     */
    private $aliases;
    /**
     *
     * @var Doctrine\Common\Collections\ArrayCollection
     * @ManyToMany(targetEntity="videoViewer\Entities\User",mappedBy="authorizedSeries")
     * @JoinTable(name="userAuthorizedSeries")
     */
    private $authorizedUsers;

    public function  __construct() {
        $this->episodes = new ArrayCollection();
        $this->aliases = new ArrayCollection();
        $this->authorizedUsers = new ArrayCollection();
    }

    public function getEpisodes(){
        return $this->episodes;
    }

    public function getAliases(){
        return $this->aliases;
    }

    public function addEpisode(Video $video){
        $video->setSeries($this);
        $this->episodes[]=$video;
        return $this;
    }

    public function addAlias(SeriesAlias $alias){
        $this->aliases[]=$alias;
        $alias->setSeries($this);
        return $this;
    }

    public function setDescription($desc){
        $this->description = $desc;
        return $this;
    }

    public function setImage($path){
        $this->image = $path;
        return $this;
    }

    public function setName($name){
        $this->name = $name;
        return $this;
    }

    public function setSeriesId($id){
        $this->seriesId = $id;
        return $this;
    }

    public function getId(){
        return $this->id;
    }

    public function getDescription(){
        return $this->description;
    }

    public function getImage(){
        return $this->image;
    }
    
    public function getSrc(){
        return str_replace($_SERVER['DOCUMENT_ROOT'], '', $this->image);
    }

    public function getName(){
        return $this->name;
    }

    public function getSeriesId(){
        return $this->seriesId;
    }
    
    public function hasAlias($aliasName){
        return $this->aliases->exists(function($key,$element) use($aliasName){
            return $element->getAlias()==$aliasName;
        });
    }
    
}
?>
