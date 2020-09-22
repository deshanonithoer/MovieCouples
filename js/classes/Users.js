class Users extends Form {
    constructor (uid){
        super();
        this.uid = uid;

        this.setEvents();
    }

    setEvents(){
        let global_scope = this;
        $('.friends-container').find('.friends-nav-button').on('click', function(){
            let value = $(this).val();
            if(value){
                global_scope[value]();
            }
        });

        let searching;
        $('#search-friends').on('keyup', function(){
            let searchBar = this;
            if(searching){
                clearInterval(searching);
            }

            searching = setTimeout(function(){
                if($(searchBar).val()){
                    global_scope.fetchUsers($(searchBar).val());
                } else {
                    global_scope.fetchUsers();
                }
            }, 300);
        });

        $(document).on('click', '.user-wrapper', function(){
            let inputs = $(this).find('input');
            let image = false;
            let userData = {};
            $(inputs).each(function(){
                userData[$(this).attr('name')] = $(this).val();
            });

            if($(this).find('img').not('.no-image').length){
                userData['image'] = $($(this).find('img')[0]).attr('src');
                image = true;
            }

            let username = userData.username.replace(/\b[a-z]/g, function(letter) {
                return letter.toUpperCase();
            });

            let element = '<div class="user-modal-content">' +
                    '<dl class="row">' +
                    '<dt class="col-sm-3">Name</dt>' +
                    '<dd class="col-sm-9">'+ username +'</dd>' +
                  
                    '<dt class="col-sm-3">Email</dt>' +
                    '<dd class="col-sm-9"><p>'+ userData.email +'</p></dd>';

            if(image == true){
                element += '<dt class="col-sm-3">Profile image</dt>' +
                '<dd class="col-sm-9"><p><img class="friend-image" src="'+ userData.image +'" alt="user-image"/></p></dd>';
            }

            element += '</dl>' +
                '</div>'
            ;

            $('#user-modal').html(element);
            $('#send-friend-request').attr('from_uid', parseInt(global_scope.uid));
            $('#send-friend-request').attr('to_uid', parseInt(userData.id));
            $('#myModal').on('shown.bs.modal', function () {
                $('#myInput').trigger('focus');
            });
        });

        $(document).on('click', '#send-friend-request', function(){
            let formData = new FormData();
            formData.append('action', 'sendFriendRequest');
            formData.append('from_uid', parseInt($(this).attr('from_uid')));
            formData.append('to_uid', parseInt($(this).attr('to_uid')));

            global_scope.ajaxCall('../../php/data/users.php', formData, async function(response){
                if(response == ''){
                    $("#user-modal-container").modal('hide');
                }
            });
        });

        $(document).on('click', '.accept-invite, .decline-invite', function(){
            let parent = $(this).parents('.request-wrapper')[0];
            let data = new FormData();
            data.append('action', 'editRequest');
            data.append('user_id', global_scope.uid);
            data.append('friend_id', parseInt($($(parent).find('input[name="id"]')[0]).val()));
            data.append('request_id', parseInt($($(parent).find('input[name="request_id"]')[0]).val()));
            
            global_scope.ajaxCall('../../php/data/users.php', data, async function(response){
                console.log(response);
                if(response && $.isNumeric(response) && parseInt(response) > 0){
                    $($('.friend-requests-counter')[0]).text(response);
                    global_scope.fetchRequests();
                } else if(parseInt(response) == 0){
                    $($('.friend-requests-counter')).hide();
                    global_scope.fetchRequests();
                }
            });
        });
    }

    fetchUsers (search = false){
        let data = new FormData();
        data.append('action', 'fetchUsers');
        if(search != false){
            data.append('search', search);
        }

        this.ajaxCall('../../php/data/users.php', data, async function(response){
            if(response){
                response = JSON.parse(response);
                $('#friends-content > .row').html(response);
            } else {
                $('#friends-content .row').text('0 Resultaten gevonden');
            }
        });
    }

    fetchRequests () {
        let data = new FormData();
        data.append('action', 'fetchRequests');
        this.ajaxCall('../../php/data/users.php', data, async function(response){
            console.log(response);
            if(response){
                response = JSON.parse(response);
                if(response[0] == 'succes'){
                    $('#users-wrapper').html(response[1]);
                }
            } else {
                $('#users-wrapper').html('');
            }
        });
    }
}

if($('#uid').val()){
    let users = new Users(parseInt($('#uid').val()));
    $('.users-button').click();
}