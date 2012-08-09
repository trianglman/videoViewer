<?php
namespace videoViewer;
/**
 * Parses out a supplied file name and stores it into the appropriate properties
 *
 * @author johnj
 */
class FileNameParser {
    /**
     * The full name of the supplied file
     * @var string
     */
    public $fullName;
    /**
     * The name of the series this is from
     * @var string
     */
    public $series;
    /**
     * The timestamp of the date this episode was aired
     * @var DateTime
     */
    public $airDate;
    /**
     * The season number of the episode
     * @var int
     */
    public $season;
    /**
     * The episode number of the episode
     * @var int
     */
    public $episode;
    /**
     * Any extra information that was in the file name
     * @var string
     */
    public $extraInfo;

    /**
     * Creates the object
     *
     *
     * @return void
     */
    public function __construct()
    {
    }
    
    /**
     * Parses a file name into the pieces
     * 
     * @param string $filename
     * @return void
     * @throws \RuntimeException If no pattern is matched 
     */
    public function parseFileName($filename)
    {
        $this->fullName=$filename;
        if($this->_attemptNameDateExtraFormat($filename)){return;}
        if($this->_attemptNameLabeledEpisodeExtraFormat($filename)){return;}
        if($this->_attemptNameUnlabeledEpisodeExtraFormat($filename)){return;}
        throw new \RuntimeException('File name did not match any patterns');
    }

    /**
     * Attempts to extract the file name parts
     * based on the format {Series}.{air date}.{extras}
     *
     * @param string $string
     * @return boolean
     */
    public function _attemptNameDateExtraFormat($string)
    {
        $pattern = "/^(.*)\.([0-9]{2,4})[\.-]([0-9]{2})[\.-]([0-9]{2,4})\.(.*)$/U";
        $matches = array();
        if(preg_match($pattern,$string,$matches)>0){
            $this->series = str_replace("."," ",$matches[1]);
            if(strlen($matches[2])==4){
                try{$this->airDate = new \DateTime($matches[3].'/'.$matches[4].'/'.$matches[2]);}
                catch(\Exception $e){
                    try{$this->airDate = new \DateTime($matches[4].'/'.$matches[3].'/'.$matches[2]);}
                    catch(\Exception $e){return false;}
                }
            }
            else{
                try{$this->airDate = new \DateTime($matches[2].'/'.$matches[3].'/'.$matches[4]);}
                catch(\Exception $e){
                    try{$this->airDate = new \DateTime($matches[3].'/'.$matches[2].'/'.$matches[4]);}
                    catch(\Exception $e){return false;}
                }
            }
            $this->extraInfo=str_replace('.', " ", $matches[5]);
            return true;
        }
        else{return false;}
    }

    protected function _attemptNameLabeledEpisodeExtraFormat($string)
    {
        $pattern = "/^(.*)(\.|\W|_){1,3}([se][0-9]+)(\.|\W|x)?([se][0-9]+)(.*)$/i";
        $matches = array();
        if(preg_match($pattern,$string,$matches)>0){
            $this->series = str_replace("."," ",$matches[1]);
            if(strtolower(substr($matches[3], 0,1))=='e'){
                $this->episode=substr($matches[3],1);
                $this->season=substr($matches[5],1);
            }
            else{
                $this->episode=preg_replace("/\D/",'',$matches[5]);
                $this->season=preg_replace("/\D/",'',$matches[3]);
            }
            $this->extraInfo=trim(str_replace('.', " ", $matches[6]));
            return true;
        }
        else{return false;}
    }
    
    protected function _attemptNameUnlabeledEpisodeExtraFormat($string)
    {
        $pattern = "/^(.*)(\.|\W|_){1,3}([0-9]+)(\.|\W|x)([0-9]+)(.*)$/i";
        $matches = array();
        if(preg_match($pattern,$string,$matches)>0){
            $this->series = str_replace("."," ",$matches[1]);
            $this->episode=preg_replace("/\D/",'',$matches[5]);
            $this->season=preg_replace("/\D/",'',$matches[3]);
            $this->extraInfo=trim(str_replace('.', " ", $matches[6]));
            return true;
        }
        else{return false;}
    }
}
?>
