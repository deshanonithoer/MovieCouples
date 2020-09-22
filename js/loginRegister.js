$(".message a").click(function () {
    $('form').removeClass('removed loaded');
    $(this).parents("form").addClass('removed');
    $('form').not('.removed').addClass('loaded');
});

$('.register-form').on('submit', function(e){
    e.preventDefault();
    let error = false;
    $(this).find('input').each(function(){
        if($(this).val() == ""){
            $(this).css({ backgroundColor: "#dca2a2" }, "slow");
            error = true;
        }
    });

    if(error == false){ 
        $.ajax({
            url: '../php/loginRegister.php',
            type: 'post',
            data: $(this).serialize(),
            success: function(response){
                location.reload();
            }
        });
    }
});