<?php

namespace app\models;

use app\components\PDOConnection;
use PDO;
use Respect\Validation\Validator as v;
use Respect\Validation\Exceptions\ValidationException;

/**
 * Class User
 * @package app\models
 */
class User
{
    private $email;
    private $username;
    private $name;
    private $password;
    private $birth;
    private $countryId;

    /**
     * User constructor.
     * @param $email
     * @param $username
     * @param $name
     * @param $password
     * @param $birth
     * @param $countryId
     */
    public function __construct($email, $username, $name, $password, $birth, $countryId)
    {
        $this->email = $email;
        $this->username = $username;
        $this->name = $name;
        $this->password = $password;
        $this->birth = $birth;
        $this->countryId = $countryId;
    }

    /**
     * Login to the site
     * @param $login user login
     * @param $password user password
     * @return bool login result
     */
    public static function login($login, $password)
    {
       $db = PDOConnection::getConnection();

       $sql = 'SELECT username, password FROM user WHERE username = :login OR email = :login';

       $result = $db->prepare($sql);
       $result->bindParam(":login", $login, PDO::PARAM_STR);
       $result->setFetchMode(PDO::FETCH_ASSOC);
       $result->execute();
       $user = $result->fetch();
       if(empty($user)) {
           return false;
       }

       $dbPassword = $user['password'];

       //comparing passwords
       if(password_verify($password, $dbPassword)) {
           return static::auth($user['username']);
       } else {
           return false;
       }

    }

    /**
     * Getting user data (email and name)
     * @param $login login of the required user
     * @return array user email and name
     */
    public static function getUserData($login)
    {
        $db = PDOConnection::getConnection();

        $sql = 'SELECT email, name FROM user '
            . 'WHERE username = :username';

        $result = $db->prepare($sql);
        $result->bindParam(":username", $login, PDO::PARAM_STR);
        $result->execute();
        $userData = $result->fetch();
        return $userData;
    }

    /**
     * Saving new user into DB
     * @return bool|array result of saving
     */
    public function save()
    {
        $result = $this->validate();//
        if($result !== true) {//if errors occurred during the validation
            $errors = $result;
            return $errors;
        } else {
            $db = PDOConnection::getConnection();

            $sql = 'INSERT INTO user (username, email, password, name, birth_date, country_id, registration_date) '
                . 'VALUES(:username, :email, :password, :name, :birth_date, :country_id, :registration_date)';

            $result = $db->prepare($sql);

            $passwordHash = password_hash($this->password, PASSWORD_DEFAULT);//getting password hash
            $registrationDate = time();//getting current timestamp

            $result->bindParam(':username', $this->username, PDO::PARAM_STR);
            $result->bindParam(':email', $this->email, PDO::PARAM_STR);
            $result->bindParam(':password', $passwordHash, PDO::PARAM_STR);
            $result->bindParam(':name', $this->name, PDO::PARAM_STR);
            $result->bindParam(':birth_date', $this->birth);
            $result->bindParam(':country_id', $this->countryId, PDO::PARAM_INT);
            $result->bindParam(':registration_date', $registrationDate);
            return $result->execute() && self::auth($this->username);
        }

    }

    /**
     * Fields validation before saving to DB
     * @return array|bool
     */
    private function validate()
    {
        $emailValidator = v::email()->setName("Email");
        $usernameValidator = v::alnum()->noWhitespace()->length(3, 70)->setName("Username");
        $nameValidator = v::notEmpty()->setName("Full Name");
        $passwordValidator = v::alnum()->noWhitespace()->length(3)->setName("Password");
        $birthValidator = v::optional(v::date('Y-m-d')->setName("Date Of Birth"));

        $errors = [];

        //checking email
        try {
            $emailValidator->check($this->email);
        } catch (ValidationException $e) {
            $errors[] = $e->getMainMessage();
        }

        if(!$this->checkEmailUnique()) {
            $errors[] = 'This email is already in use';
            return $errors;
        }

        //checking username
        try {
            $usernameValidator->check($this->username);
        } catch (ValidationException $e) {
            $errors[] = $e->getMainMessage();
        }

        if(!$this->checkUsernameUnique()) {
            $errors[] = 'This username is already in use';
            return $errors;
        }

        //checking user full name
        try {
            $nameValidator->check($this->name);
        } catch (ValidationException $e) {
            $errors[] = $e->getMainMessage();
        }

        //checking password
        try {
            $passwordValidator->check($this->password);
        } catch (ValidationException $e) {
            $errors[] = $e->getMainMessage();
        }

        //checking user date of birth
        try {
            $birthValidator->check($this->birth);
        } catch (ValidationException $e) {
            $errors[] = $e->getMainMessage();
        }

        //checking country
        if(!$this->checkCountry()) {
            $errors[] = '"Country" contains invalid data';
        }

        if(empty($errors)) {
            return true;//successful validation
        } else {
            return $errors;//If errors are found
        }

    }

    /**
     * User authorization on the site
     * @param $username user to authorize
     * @return bool result of authorization
     */
    private static function auth($username)
    {
        //Write the username to the session
        $_SESSION['user'] = $username;
        return true;
    }

    /**
     * Checking username uniqueness
     * @return bool result of checking
     */
    private function checkUsernameUnique()
    {
        $db = PDOConnection::getConnection();

        $sql = 'SELECT * FROM user WHERE username = :username';

        $result = $db->prepare($sql);
        $result->bindParam(":username", $this->username);
        $result->execute();
        return $result->fetch() ? false : true;
    }

    /**
     * Checking email uniqueness
     * @return bool result of checking
     */
    private function checkEmailUnique()
    {
        $db = PDOConnection::getConnection();

        $sql = 'SELECT * FROM user WHERE email = :email';

        $result = $db->prepare($sql);
        $result->bindParam(":email", $this->email);
        $result->execute();
        return $result->fetch() ? false : true;
    }

    /**
     * Checking the existence of the country in the database
     * @return bool result of checking
     */
    private function checkCountry()
    {
        $db = PDOConnection::getConnection();

        $sql = 'SELECT * FROM country WHERE id = :id';

        $result = $db->prepare($sql);
        $result->bindParam(":id", $this->countryId, PDO::PARAM_INT);
        $result->execute();
        return $result->fetch() ? true : false;
    }

}