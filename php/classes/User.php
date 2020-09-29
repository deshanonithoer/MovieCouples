<?php 

/**
 * Class om acties te verrichten met gebruikers accounts
 * 
 * @author Shano Nithoer
 */
class User extends Database {
    public function setUser ($uid) {
        $userArray = $this->fetchUserInfo($uid);
        if(array_key_exists(0, $userArray)){
            $userInfo = $this->fetchUserInfo($uid)[0];
        } else {
            header('location: /login');
        }
        
        $this->uid = $userInfo['id'];
        $this->name = $userInfo['username'];
        $this->email = $userInfo['email'];
        $this->password = $userInfo['password'];
        $this->image_path = $userInfo['image_path'];
        $this->gender = $userInfo['gender'];

        $this->friendRequests = $this->fetchFriendRequests($this->uid);
        if(count($this->friendRequests)){
            $this->totalRequests = count($this->friendRequests);
        }

        $this->friends = $this->fetchFriends($this->uid);
    }

    public function validateUsername ($username){
        return (count($this->findUserName($username))) ? $this->findUserName($username) : false;
    }

    public function insertNewUser ($username, $password, $email) {
        $password = password_hash($password, PASSWORD_DEFAULT);
        $this->insertNewUserDB($username, $password, $email);
    }

    public function clearPassword($value){
        $value = trim($value); //remove empty spaces
        $value = strip_tags($value); //remove html tags
        $value = htmlentities($value, ENT_QUOTES,'UTF-8'); //for major security transform some other chars into html corrispective...
        return $value;
    }

    public function clearText($value){
        $value = trim($value); //remove empty spaces
        $value = strip_tags($value); //remove html tags
        $value = filter_var($value, FILTER_SANITIZE_MAGIC_QUOTES); //addslashes();
        $value = filter_var($value, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW); //remove /t/n/g/s
        $value = filter_var($value, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH); //remove é à ò ì ` ecc...
        $value = htmlentities($value, ENT_QUOTES,'UTF-8'); //for major security transform some other chars into html corrispective...
        return $value;
    }
}