<?php
session_start();

require("../classes/Database.php");
require("../classes/User.php");

$db = new Database();
$user = new User();
$user->setUser($_SESSION['uid']);

class Matching extends User {
    public function loadAction($action){  
        if(method_exists($this, $action)){
            $this->{$action}();
        }
    }

    public function insertLoadedMovie(){
        $movieInsert = $this->insertMovie($_POST);

        $tabel = ($_POST['type'] == '-1') ? 'liked_movies' : 'disliked_movies';
        $insertSelected = $this->insertSelected($tabel, $_POST['id'], $_SESSION['uid']);
    }

    public function fetchTypeMovies(){
        $liked_movies = $this->fetchTypeMoviesDB($_SESSION['uid'], $_POST['table']);
        if(count($liked_movies)){
            $layout = '<div class="row no-gutters nopadding col-md-12">';
            $counter = 0;
            foreach($liked_movies as $key => $value){
                $layout .= '<div class="col-md-4 card-item" style=\'background-image:url("http://image.tmdb.org/t/p/original/'. $value['poster_path'] .'"); \'>
                    <input type="hidden" json_data="'. urlencode(json_encode($value)) .'" />
                </div>';

                if(($counter + 1) % 3 == 0){
                    $layout .= '
                        </div>
                        <div class="row no-gutters col-md-12">
                    ';
                }
            }
            $layout .= '</div>';

            echo json_encode($layout);
        }
    }

    public function validateMovie(){
        $validate = $this->validateMovieDB($_POST['movie_id'], $_SESSION['uid']);
        if(count($validate) !== 0){
            echo 'error';
        }
    }
}

if($_POST){
    if(array_key_exists('action', $_POST)){
        $matching = new Matching();
        $matching->loadAction($_POST['action']);
    }
}
?>