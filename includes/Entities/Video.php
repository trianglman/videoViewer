<?php
namespace videoViewer\Entities;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * A class describing an available video
 *
 * @author johnj
 * @Entity(repositoryClass="videoViewer\Repositories\VideoRepository")
 * @Table(name="video")
 */
class Video {
    /**
     * The directory to store/lookup videos relative the the base directory of the project
     * @var string
     */
    const VIDEO_DIR='videos/';
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
     * @var int
     * @Column(type="integer",name="season_number")
     */
    private $seasonNumber=0;
    /**
     *
     * @var int
     * @Column(type="integer",name="episode_number")
     */
    private $episodeNumber=0;
    /**
     *
     * @var DateTime
     * @Column(type="date",name="air_date")
     */
    private $airDate;
    /**
     *
     * @var string
     * @Column(length=255,name="file_name_base")
     */
    private $fileNameBase='';
    /**
     *
     * @var string
     * @Column(length=255,name="episode_name")
     */
    private $episodeName='';
    /**
     *
     * @var string
     * @Column (type="text")
     */
    private $details='';
    /**
     *
     * @var string
     * @column (length=255)
     */
    private $notes='';
    /**
     *
     * @var Series
     * @ManyToOne(targetEntity="videoViewer\Entities\Series", inversedBy="episodes")
     */
    private $series;
    /**
     *
     * @var Doctrine\Common\Collections\ArrayCollection
     * @ManyToMany(targetEntity="videoViewer\Entities\User",mappedBy="watchedVideos")
     * @JoinTable(name="userWatched")
     */
    private $usersWhoViewed;
    
    public function  __construct() {
        $this->usersWhoViewed = new ArrayCollection();
        $this->airDate= new \DateTime();
    }

    /**
     *
     * @return Doctrine\Common\Collections\ArrayCollection 
     */
    public function getViewers(){
        return $this->usersWhoViewed;
    }

    /**
     *
     * @return int
     */
    public function getId(){
        return $this->id;
    }
    
    /**
     *
     * @return string
     */
    public function getEpisodeName(){
        return $this->episodeName;
    }
    
    /**
     *
     * @param string $name 
     * @return Video
     */
    public function setEpisodeName($name){
        $this->episodeName=$name;
        return $this;
    }

    /**
     *
     * @param int $number
     * @return Video
     */
    public function setSeasonNumber($number){
        if(!is_numeric($number)){throw new \InvalidArgumentException('Season must be a number');}
        $this->seasonNumber = $number;
        return $this;
    }

    /**
     *
     * @param \DateTime $date
     * @return Video
     */
    public function setAirDate(\DateTime $date){
        $this->airDate = $date;
        return $this;
    }

    /**
     *
     * @param string $details
     * @return Video
     */
    public function setDetails($details){
        $this->details = $details;
        return $this;
    }

    /**
     *
     * @param int $number
     * @return Video
     */
    public function setEpisodeNumber($number){
        if(!is_numeric($number)){throw new \InvalidArgumentException('Episode must be a number');}
        $this->episodeNumber = $number;
        return $this;
    }

    /**
     *
     * @param string $filename
     * @return Video 
     */
    public function setFileNameBase($filename){
        $this->fileNameBase = $filename;
        return $this;
    }

    /**
     *
     * @param string $notes
     * @return Video 
     */
    public function setNotes($notes){
        $this->notes = $notes;
        return $this;
    }

    /**
     *
     * @param Series $series
     * @return Video 
     */
    public function setSeries(Series $series){
        $this->series = $series;
        return $this;
    }

    /**
     *
     * @param User $user
     * @return Video 
     */
    public function addViewer(User $user){
        $this->usersWhoViewed[]=$user;
        return $this;
    }
    
    /**
     *
     * @return int
     */
    public function getSeasonNumber(){
        return $this->seasonNumber;
    }

    /**
     *
     * @return DateTime
     */
    public function getAirDate(){
        return $this->airDate;
    }

    /**
     * 
     * @return string
     */
    public function getDetails(){
        return $this->details;
    }

    /**
     *
     * @return int
     */
    public function getEpisodeNumber(){
        return $this->episodeNumber;
    }

    /**
     *
     * @return string
     */
    public function getFileNameBase(){
        return $this->fileNameBase;
    }

    /**
     *
     * Gives the path as can be reached via the web server
     * @param string $format
     * @param \videoViewer\DIContainer $di
     * @return string
     *
     * @throws Exception if the specified format can not be found
     */
    public function getWebPath($format, \videoViewer\DIContainer $di){
        $fileLoc = $this->getFilePath($format,$di);
        $pathBase = str_replace(\dirname(__FILE__).'/../../','',$fileLoc);
        return $pathBase;
    }

    /**
     *
     * Gives the path of the file based on a given format
     * @param string $format
     * @param \videoViewer\DIContainer $di
     * @return string
     *
     * @throws Exception if the specified format can not be found
     */
    public function getFilePath($format, \videoViewer\DIContainer $di){
        $filename = $this->getFileName($format);
        $dir = \dirname(__FILE__).'/../../'.self::VIDEO_DIR;
        if($format=='jpg'){
            $dir.='thumbs/';
        }
        if(!$di->fileSystem('file_exists', array($dir.$filename))){
            throw new \RuntimeException($dir.$filename.' does not exist in that format');
        }
        else{
            return $dir.$filename;
        }
    }
    
    public function getFileName($format){
        if($format=='ogg'){
            $filename = $this->fileNameBase.'.ogv';
        }
        elseif($format=='mp4'){
            $filename = $this->fileNameBase.'.mp4';
        }
        elseif($format=='jpg'){
            $filename = $this->fileNameBase.'.jpg';
        }
        else{
            throw new \RuntimeException('This file does not exist in that format');
        }
        return $filename;
    }
    
    /**
     * Gets the path of the video's thumbnail
     * 
     * @param \videoViewer\DIContainer $di
     * 
     * @return string
     */
    public function getThumbnail(\videoViewer\DIContainer $di){
        $fileLoc = $this->getFilePath('jpg',$di);
        $pathBase = str_replace(\dirname(__FILE__).'/../../','',$fileLoc);
        return $pathBase;
    }

    /**
     *
     * @return string
     */
    public function getNotes(){
        return $this->notes;
    }

    /**
     *
     * @return Series
     */
    public function getSeries(){
        return $this->series;
    }

    /**
     *
     * Converts the Video into a printable string
     * @return string
     */
    public function __toString(){
        return $this->series->getName().': Season '.$this->seasonNumber.', Episode '.$this->episodeNumber;
    }

}
?>
