<?php

namespace app\controllers;

use app\components\Helper;
use app\models\Country;
use app\models\User;

class SiteController
{
    /**
     * Action for index page
     * @return bool
     */
    public function actionIndex()
    {

        if(!isset($_SESSION['user']) || empty($_SESSION['user'])) {
            //if user not authorized
            header("location: /login");
        }
        $userData = User::getUserData($_SESSION['user']);
        require_once ROOT . '/views/site/index.php';
        return true;
    }

    /**
     * Action for login page
     * @return bool
     */
    public function actionLogin()
    {
        if(isset($_SESSION['user']) || !empty($_SESSION['user'])) {
            header("location: /");
        }

        $error = '';

        //if login form was submitted
        if(isset($_POST['signin'])) {
            $login = Helper::safeInput($_POST['login']);
            $password = Helper::safeInput($_POST['password']);
            $result = User::login($login, $password);
            if($result !== false) {
                header("location: /");
            } else {
                $error = 'Username or password are incorrect';
                unset($_POST['password']);
            }
        }

        require_once ROOT . '/views/site/login.php';
        return true;
    }

    /**
     * User logout action
     * @return bool
     */
    public function actionLogout()
    {

        unset($_SESSION["user"]);

        //redirect user to the home page
        header("Location: /");
        return true;
    }

    /**
     * Action for register page
     * @return bool
     */
    public function actionRegister()
    {
        $errors = [];

        //if register form was submitted
        if(isset($_POST['register'])) {
            $email = Helper::safeInput($_POST['email']);
            $username = Helper::safeInput($_POST['username']);
            $name= Helper::safeInput($_POST['name']);
            $password = Helper::safeInput($_POST['password']);
            $birth = Helper::safeInput($_POST['birth']);
            $countryId = Helper::safeInput($_POST['country']);

            //creating new user
            $user = new User($email, $username, $name, $password, $birth, $countryId);
            $result = $user->save();
            if($result === true) {
                header("location: /");
            } else {
                $errors = $result;
            }
        }

        $countries = Country::getCountries();
        require_once ROOT . '/views/site/register.php';
        return true;
    }
}