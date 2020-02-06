<?php


namespace MKDF\Core\Repository;

use MKDF\Core\Entity\Dataset;
use MKDF\Core\Entity\User;

interface MKDFCoreRepositoryInterface
{
    public function __construct($config);

    public function findAllUsers();
    public function findUser($id);
    public function findUserByEmail($email);
    public function updateUser(User $user);
    public function addUser(User $user);

}