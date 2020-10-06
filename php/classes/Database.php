<?php
## File for all database queries and connections.

class Database {
    public $db = null;

    // Construct om de database connectie te maken 
    public function __construct(){
        try {
            if($_SERVER['HTTP_HOST'] == 'localhost'){
                $dbname = 'fasten_your_seatbelts';
                $username = 'root';
                $password = '';
            } else {
                $dbname = 'u45310p68148_moviematch';
                $username = 'u45310p68148_root';
                $password = 'kousjilia';
            }
            

            $this->db = new PDO("mysql:host=localhost;dbname=$dbname;charset=utf8;", $username, $password);
			$this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch(PDOException $e){
            throw new Exception($e);
        }
    }

    // Private function to execute a call to db
    public function dataCall($sql, $params, $response = false){
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            if($response){
                return $stmt->fetchAll();
            }
        } catch(PDOException $e){
            throw new Exception($e);
        }
    }
    
    public function findUserName($username) {
        $sql = "SELECT * FROM users WHERE username = ?";
        return $this->dataCall($sql, array($username), true);
    }

    public function insertNewUserDB($username, $password, $email){
        $sql = "INSERT INTO `users` (`username`, `password`, `email`, `created_at`) VALUES (?, ?, ?, NOW())";
        return $this->dataCall($sql, array($username, $password, $email), false);
    }

    public function fetchUserInfo($uid){
        $sql = "SELECT * FROM users WHERE id = ?";
        return $this->dataCall($sql, array($uid), true);
    }

    public function fetchAllUsers ($search = false){
        $where = "";
        if($search != false){
            $search = '%*' . str_replace(' ', '*', $search) . '*%';
            $where .= " AND MATCH (username) AGAINST ('%$search%' IN BOOLEAN MODE)";
        }

        $sql = "SELECT id, LOWER(username) as username, email, gender, image_path FROM users WHERE id != ? $where GROUP BY id ORDER BY id DESC ";
        return $this->dataCall($sql, array($_SESSION['uid']), true);
    }

    public function insertFriendRequest($from_uid, $to_uid){
        $sql = "INSERT IGNORE INTO `friend-requests` (`created_at`, `from_uid`, `to_uid`) VALUES (NOW(), ?, ?)";
        return $this->dataCall($sql, array($from_uid, $to_uid), false);
    }

    public function fetchFriendRequests($uid){
        $sql = "SELECT
                `users`.id,
                `users`.username,
                `users`.email,
                `users`.image_path,
                `friend-requests`.`created_at`,
                `friend-requests`.id as request_id
            FROM
                `friend-requests`
            JOIN `users` ON `users`.id = `friend-requests`.`from_uid` AND `friend-requests`.`to_uid` = ?
            WHERE `friend-requests`.`status` = 'pending'
            GROUP BY `users`.id
        ";

        return $this->dataCall($sql, array($uid), true);
    }

    public function fetchFriends($uid){
        $sql = "SELECT
                `users`.id,
                `users`.username,
                `users`.email,
                `users`.image_path,
                `friends`.`created_at`,
                `friends`.id as request_id
            FROM
                `friends`
            JOIN `users` ON `users`.id = `friends`.`friend_id`
            WHERE `friends`.`user_id` = ?
            GROUP BY `users`.id

            union

            SELECT
                `users`.id,
                `users`.username,
                `users`.email,
                `users`.image_path,
                `friends`.`created_at`,
                `friends`.id as request_id
            FROM
                `friends`
            JOIN `users` ON `users`.id = `friends`.`user_id`
            WHERE `friends`.`friend_id` = ?
            GROUP BY `users`.id
        ";

        return $this->dataCall($sql, array($uid, $uid), true);
    }

    public function insertNewFriend($user_id, $friend_id){
        $sql = "INSERT INTO `friends` (`created_at`, `user_id`, `friend_id`) VALUES (NOW(), ?, ?)";
        return $this->dataCall($sql, array($user_id, $friend_id), false);
    }

    public function editRequestDB($request_id){
        $sql = "UPDATE `friend-requests` SET `status` = 'accepted' WHERE id = ?";
        return $this->dataCall($sql, array($request_id), false);
    }

    public function insertMovie($movieData){
        $sql = "INSERT IGNORE INTO `movies` (`uid`, `release_date`, `title`, `description`, `poster_path`) VALUES (?, ?, ?, ?, ?)";
        return $this->dataCall($sql, array($movieData['id'], $movieData['release_date'], $movieData['title'], $movieData['overview'], $movieData['poster_path']), false);
    }

    public function insertSelected ($tabel, $movie_id, $user_id){
        $sql = "INSERT IGNORE INTO $tabel (`movie_id`, `user_id`) VALUES (?, ?)";
        return $this->dataCall($sql, array($movie_id, $user_id), false);
    }

    public function fetchTypeMoviesDB($user_id, $table){
        $sql = "SELECT *
            FROM `$table`
            JOIN `movies` ON `movies`.`uid` = `$table`.`movie_id`
            WHERE `user_id` = ?
            GROUP BY `$table`.`movie_id`
            ORDER BY `$table`.`created_at` DESC
        ";
        return $this->dataCall($sql, array($user_id), true);
    }

    public function validateMovieDB($movie_id, $user_id){
        $sql ="SELECT id
            FROM `liked_movies`
            WHERE `liked_movies`.`movie_id` = ?
            AND `liked_movies`.`user_id` = ?

            UNION

            SELECT id
            FROM `disliked_movies`
            WHERE `disliked_movies`.`movie_id` = ?
            AND `disliked_movies`.`user_id` = ?
        ";
        
        return $this->dataCall($sql, array($movie_id, $user_id, $movie_id, $user_id), true);
    }
}