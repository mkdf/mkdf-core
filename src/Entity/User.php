<?php


namespace MKDF\Core\Entity;


class User extends Bucket
{
    // User status constants.
    const STATUS_ACTIVE       = 1; // Active user.
    const STATUS_RETIRED      = 2; // Retired user.

    protected $id;
    protected $email;
    protected $full_name;
    protected $password;
    protected $status;
    protected $is_admin;
    protected $date_created;
    protected $pwd_reset_token;
    protected $pwd_reset_token_creation_date;

    /**
     * Returns user ID.
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
    /**
     * Sets user ID.
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }
    /**
     * Returns email.
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }
    /**
     * Sets email.
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * Returns full name.
     * @return string
     */
    public function getFullName()
    {
        return $this->full_name;
    }
    /**
     * Sets full name.
     * @param string $full_name
     */
    public function setFullName($full_name)
    {
        $this->full_name = $full_name;
    }

    /**
     * Returns status.
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }
    /**
     * Returns possible statuses as array.
     * @return array
     */
    public static function getStatusList()
    {
        return [
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_RETIRED => 'Retired'
        ];
    }

    /**
     * Returns user status as string.
     * @return string
     */
    public function getStatusAsString()
    {
        $list = self::getStatusList();
        if (isset($list[$this->status]))
            return $list[$this->status];

        return 'Unknown';
    }

    /**
     * Sets status.
     * @param int $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * Sets admin flag for user
     * @param bool $admin
     */
    public function setIsAdmin($admin)
    {
        $this->is_admin = $admin;
    }

    /**
     * Gets admin flag for user
     * @return bool
     */
    public function getIsAdmin()
    {
        return $this->is_admin;
    }

    /**
     * Returns password.
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Sets password.
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * Returns the date of user creation.
     * @return string
     */
    public function getDateCreated()
    {
        return $this->date_created;
    }

    /**
     * Sets the date when this user was created.
     * @param string $date_created
     */
    public function setDateCreated($date_created)
    {
        $this->date_created = $date_created;
    }

    /**
     * Returns password reset token.
     * @return string
     */
    public function getPasswordResetToken()
    {
        return $this->pwd_reset_token;
    }

    /**
     * Sets password reset token.
     * @param string $token
     */
    public function setPasswordResetToken($token)
    {
        $this->pwd_reset_token = $token;
    }

    /**
     * Returns password reset token's creation date.
     * @return string
     */
    public function getPasswordResetTokenCreationDate()
    {
        return $this->pwd_reset_token_creation_date;
    }

    /**
     * Sets password reset token's creation date.
     * @param string $date
     */
    public function setPasswordResetTokenCreationDate($date)
    {
        $this->pwd_reset_token_creation_date = $date;
    }
    
    public function isAnonymous(){
        return $this->getId() > 0;
    }
    
    private static $anonymous;
    public final static function anonymous(){
        if(self::$anonymous == null){
            $u = new User();
            $u->setProperties([
                'id' => -1,
                'email' => null,
                'full_name' => 'Anonymous',
                'is_admin' => false
            ]);
            self::$anonymous = $u;
        }
        return self::$anonymous;
    }
}