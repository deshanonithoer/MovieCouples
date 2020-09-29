<?php
session_start();

require("../classes/Database.php");
require("../classes/User.php");

$db = new Database();
$user = new User();
$user->setUser($_SESSION['uid']);

class Users extends User {
    public function loadAction($action){  
        if(method_exists($this, $action)){
            $this->{$action}();
        }
    }

    public function fetchUsers() {
        $this->setUser($_SESSION['uid']);
        $friendsRequest = false;

        if(array_key_exists('friends', $_POST) && $_POST['friends'] == "true"){
            $allUsers = $this->friends;
            $friendsRequest = true;
        } else if(array_key_exists('search', $_POST) && $_POST['search'] == true){
            $allUsers = $this->fetchAllUsers($_POST['search']);
        } else {
            $allUsers = $this->fetchAllUsers();
        }
        
        if($allUsers){
            $layout = '<div class="row no-gutters col-md-12">';
            $counter = 0;
            foreach($allUsers as $key => $value){
                $layout .= '
                    <div class="col-md-4">
                        <div class="item-wrapper user-wrapper" data-toggle="modal" data-target="#user-modal-container">';
                        if($friendsRequest != true){
                            $noRequest = false;
                            foreach($this->friends as $friendInfo){
                                if($value['id'] == $friendInfo['id']){
                                    $noRequest = true;
                                }
                            }

                            if($noRequest == true){
                                $layout .= '<input type="hidden" name="noFriend" value="true" />';
                            }
                        }

                        foreach($value as $column => $columnValue){
                            $layout .= '<input type="hidden" name="'. $column .'" value="'. $columnValue .'" />';
                        }

                        if($value['image_path']){
                            $layout .= '
                                <img src="'. $value['image_path'] .'" alt="person-image" />';
                        } else {
                            $layout .= '<img class="no-image" src="/media/placeholder.png" alt="placeholder image" />';
                        }
                $layout .= '
                            <span>' . ucwords($value['username']) . '</span>

                            <div class="clear"></div>
                        </div>
                    </div>
                ';

                if(($counter + 1) % 3 == 0){
                    $layout .= '
                        </div>
                        <div class="row no-gutters col-md-12">
                    ';
                }
                $counter++;
            }
            $layout .= '</div>';

            echo json_encode($layout);
        }
    }

    public function sendFriendRequest() {
        return $this->insertFriendRequest(intval($_POST['from_uid']), intval($_POST['to_uid']));
    }

    public function fetchRequests(){
        $this->setUser($_SESSION['uid']);
        if ($this->friendRequests){
            $layout = '<div class="row no-gutters col-md-12">';
            foreach($this->friendRequests as $key => $value){
                $layout .= '
                    <div class="col-md-12 mr-auto">
                        <div class="item-wrapper request-wrapper">';
                            foreach($value as $column => $columnValue){
                                $layout .= '<input type="hidden" name="'. $column .'" value="'. $columnValue .'" />';
                            }
                            if($value['image_path']){
                                $layout .= '
                                    <img src="'. $value['image_path'] .'" alt="person-image" />';
                            } else {
                                $layout .= '<img class="no-image" src="/media/placeholder.png" alt="placeholder image" />';
                            }

                $layout .= '    
                            <span class="request-span">'.ucwords($value['username']).'</span>
                            <span class="request-span time-elapsed-span">'.ucwords($this->humanTiming(strtotime($value['created_at']))).' ago</span>
                            <div class="ml-auto col-md-3 mr-3">
                                <button type="button" value="accepted" class="btn btn-success accept-invite">
                                    Accept 
                                </button>
                                <button type="button" value="declined" class="btn btn-danger decline-invite">
                                    Decline 
                                </button>
                            </div>
                        </div>
                    </div>
                ';
            }
            $layout .= '</div>';

            echo json_encode(array('succes', $layout));
        }
    }

    public function editRequest(){
        $this->setUser($_SESSION['uid']);
        $this->editRequestDB(intval($_POST['request_id']));
        $this->insertNewFriend(intval($_POST['user_id']), intval($_POST['friend_id']));
        echo (count($this->friendRequests) - 1);
    }
    
    private function humanTiming ($time){
        $time = time() - $time; // to get the time since that moment
        $time = ($time<1)? 1 : $time;
        $tokens = array (
            31536000 => 'year',
            2592000 => 'month',
            604800 => 'week',
            86400 => 'day',
            3600 => 'hour',
            60 => 'minute',
            1 => 'second'
        );
    
        foreach ($tokens as $unit => $text) {
            if ($time < $unit) continue;
            $numberOfUnits = floor($time / $unit);
            return $numberOfUnits.' '.$text.(($numberOfUnits>1)?'s':'');
        }
    }
}

if($_POST){
    if(array_key_exists('action', $_POST)){
        $users = new Users();
        $users->loadAction($_POST['action']);
    }
}
?>