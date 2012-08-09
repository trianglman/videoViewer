<?php
namespace videoViewer\Repositories;

use \Doctrine\ORM\EntityRepository;
use \Doctrine\ORM\EntityNotFoundException;


/**
 * An entity repository that manages Video object lookups
 *
 * @author John Judy <john.a.judy@gmail.com>
 */
class VideoRepository extends EntityRepository{
    
    /**
     *
     * A cached version of the getUserByHash query to reduce overhead of 
     * compiling it every query
     * 
     * @var Doctrine\ORM\NativeQuery
     */
    protected $_filenameQuery=null;

    /**
     * Gets a video object based on a supplied filename
     * 
     * @param string $filename
     * @return \videoViewer\Entities\User
     */
    public function getByFilename($filename){
        if(is_null($this->_filenameQuery)){
            $dql = 'SELECT v FROM videoViewer\Entities\Video v '
                    .'WHERE v.fileNameBase=?1';
            $this->_filenameQuery = $this->_em->createQuery($dql);
        }
        $this->_filenameQuery->setParameter(1,$filename);
        $videos = $this->_filenameQuery->getResult();
        if(count($videos)!==1){
            throw new EntityNotFoundException('No video with that filename');
        }
        return $videos[0];
    }
}

?>