<?php
namespace videoViewer\Repositories;

use \Doctrine\ORM\EntityRepository;
use \Doctrine\ORM\EntityNotFoundException;


/**
 * An entity repository that manages User object lookups
 *
 * @author John Judy <john.a.judy@gmail.com>
 */
class UserRepository extends EntityRepository{
    
    /**
     *
     * A cached version of the getUserByHash query to reduce overhead of 
     * compiling it every query
     * 
     * @var Doctrine\ORM\NativeQuery
     */
    protected $_hashQuery=null;

    /**
     * Gets a user object based on a supplied username/password hash
     * 
     * @param string $hash
     * @return \videoViewer\Entities\User
     */
    public function getUserByHash($hash){
        if(is_null($this->_hashQuery)){
            $rawMapper = new \Doctrine\ORM\Query\ResultSetMapping();
            $rawMapper->addEntityResult('videoViewer\Entities\User', 'u');
            $rawMapper->addFieldResult('u', 'id', 'id');
            $rawMapper->addFieldResult('u', 'user_name', 'userName');
            $rawMapper->addFieldResult('u', 'password', 'password');
            $rawMapper->addFieldResult('u', 'name', 'name');
            $rawMapper->addFieldResult('u', 'admin', 'admin');
            $sql = 'SELECT id,user_name,password, name,admin FROM `user` '
                    .'WHERE MD5(CONCAT(name,password))=?';
            $this->_hashQuery = $this->_em->createNativeQuery($sql,$rawMapper);
        }
        $this->_hashQuery->setParameter(1,$hash);
        $users = $this->_hashQuery->getResult();
        if(count($users)!==1){
            throw new EntityNotFoundException('No user with that hash');
        }
        return $users[0];
    }
}

?>
