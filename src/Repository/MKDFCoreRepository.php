<?php

namespace MKDF\Core\Repository;

use MKDF\Core\Entity\Dataset;
use MKDF\Core\Entity\User;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\ResultSet;


class MKDFCoreRepository implements MKDFCoreRepositoryInterface
{
    private $_config;
    private $_adapter;
    private $_queries;

    public function __construct($config)
    {
        $this->_config = $config;
        $this->_adapter = new Adapter([
            'driver'   => 'Pdo_Mysql',
            'database' => $this->_config['db']['dbname'],
            'username' => $this->_config['db']['user'],
            'password' => $this->_config['db']['password'],
            'host'     => $this->_config['db']['host'],
            'port'     => $this->_config['db']['port']
        ]);
        $this->buildQueries();
    }

    private function fp($param) {
        return $this->_adapter->driver->formatParameterName($param);
    }

    private function qi($param) {
        return $this->_adapter->platform->quoteIdentifier($param);
    }

    private function buildQueries(){
        $this->_queries = [
            'isReady'           => 'SELECT ID FROM user LIMIT 1',
            'allUsers'          => 'SELECT * FROM user WHERE id > 0',
            'oneUser'           => 'SELECT * FROM user WHERE id = ' . $this->fp('id'),
            'oneUserByEmail'    => 'SELECT * FROM user WHERE email = ' . $this->fp('email'),
            'updateUser'        => 'UPDATE user '
                .'SET '
                .'email ='.$this->fp("email") .', '
                .'full_name='.$this->fp("full_name") .', '
                .'status ='.$this->fp("status") .', '
                .'is_admin ='.$this->fp("is_admin")
                .'  WHERE '.$this->qi("id").'='.$this->fp('id'),
            'addUser'           => 'INSERT INTO user ('
                .$this->qi("email").', '
                .$this->qi("full_name").', '
                .$this->qi("password").', '
                .$this->qi("status").', '
                .$this->qi("date_created").') VALUES('
                .$this->fp("email").','
                .$this->fp("full_name").','.
                $this->fp("password").','.
                $this->fp("status").','.
                $this->fp("date_created").')',
            'updateUserPassword'    => 'UPDATE user SET password = '.$this->fp("password")
                .' WHERE id = '.$this->fp("id"),
            'updateUserPasswordResetToken' => 'UPDATE user SET pwd_reset_token = '.$this->fp("resetToken")
                .', pwd_reset_token_creation_date = '.$this->fp("resetTokenDate").' WHERE id = '.$this->fp("id"),
            'addRole'           => 'INSERT INTO role (id) VALUES ('.$this->fp("id").')',
        ];
    }

    private function getQuery($query){
        return $this->_queries[$query];
    }


/*
 * USER FUNCTIONS
 */
    public function findAllUsers() {
        $userCollection = [];
        $statement = $this->_adapter->createStatement($this->getQuery('allUsers'));
        $result    = $statement->execute();
        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet = new ResultSet;
            $resultSet->initialize($result);
            foreach ($resultSet as $row) {
                $user = new User();
                $user->setProperties($row);
                array_push($userCollection, $user);
            }
            return $userCollection;
        }
        return [];
    }

    public function findUser($id) {
        $parameters = [
            'id'   => $id
        ];
        $statement = $this->_adapter->createStatement($this->getQuery('oneUser'));
        $result    = $statement->execute($parameters);
        $user = new User();
        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            if ($result->count() > 0) {
                $user->setProperties($result->current());
            }
            else {
                $user = null;
            }
        }
        return $user;
    }

    public function findUserByEmail($email) {
        $parameters = [
            'email'   => $email
        ];
        $statement = $this->_adapter->createStatement($this->getQuery('oneUserByEmail'));
        $result    = $statement->execute($parameters);
        $user = new User();
        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            if ($result->count() > 0) {
                $user->setProperties($result->current());
            }
            else {
                $user = null;
            }
        }
        return $user;
    }

    public function updateUser(User $user) {
        $parameters = [
            'id'        => $user->getID(),
            'email'     => $user->getEmail(),
            'full_name' => $user->getFullName(),
            'status'    => $user->getStatus(),
            'is_admin'  => $user->getIsAdmin(),
        ];
        $statement = $this->_adapter->createStatement($this->getQuery('updateUser'));
        $result    = $statement->execute($parameters);
        if ($result->getAffectedRows() > 0) {
            return true;
        }
        return false;
    }

    public function addUser(User $user){
        $parameters = [
            'email'     => $user->getEmail(),
            'full_name' => $user->getFullName(),
            'password'    => $user->getPassword(),
            'status'    => $user->getStatus(),
            'date_created'    => $user->getDateCreated(),
        ];
        $statement = $this->_adapter->createStatement($this->getQuery('addUser'));
        $result    = $statement->execute($parameters);
        if ($result->getAffectedRows() > 0) {
            $id = $this->_adapter->getDriver()->getLastGeneratedValue();
            return $id;
        }
        return false;
    }

    public function addRole(int $id) {
        $parameters = [
            'id'    => $id
        ];
        $statement = $this->_adapter->createStatement($this->getQuery('addRole'));
        $result    = $statement->execute($parameters);
        if ($result->getAffectedRows() > 0) {
            $id = $this->_adapter->getDriver()->getLastGeneratedValue();
            return $id;
        }
        return false;
    }

    public function updateUserPassword (User $user) {
        $parameters = [
            'id'        => $user->getID(),
            'password' => $user->getPassword()
        ];
        $statement = $this->_adapter->createStatement($this->getQuery('updateUserPassword'));
        $result    = $statement->execute($parameters);
        if ($result->getAffectedRows() > 0) {
            return true;
        }
        return false;
    }

    public function setUserPasswordResetToken(User $user) {
        $parameters = [
            'id'        => $user->getID(),
            'resetToken' => $user->getPasswordResetToken(),
            'resetTokenDate' => $user->getPasswordResetTokenCreationDate(),
        ];
        $statement = $this->_adapter->createStatement($this->getQuery('updateUserPasswordResetToken'));
        $result    = $statement->execute($parameters);
        if ($result->getAffectedRows() > 0) {
            return true;
        }
        return false;
    }
    
    public function init(){
        try {
            $statement = $this->_adapter->createStatement($this->getQuery('isReady'));
            $result    = $statement->execute();
            return false;
        } catch (\Exception $e) {
            // XXX Maybe raise a warning here?
        }
        $sql = file_get_contents(dirname(__FILE__) . '/../../sql/setup.sql');
        $this->_adapter->getDriver()->getConnection()->execute($sql);
        $sql = file_get_contents(dirname(__FILE__) . '/../../sql/admin-user.sql');
        $this->_adapter->getDriver()->getConnection()->execute($sql);
        return true;
    }
/*
 * END OF USER FUNCTIONS
 */
}