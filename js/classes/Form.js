class Form {
    constructor (form) {
        this.form = form;
        this.form_data = new FormData();

        this.setEventHandlers();
    }

    ajaxCall (url, data, success) {
        var xhr = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP");
        xhr.open('POST', url);
        xhr.onreadystatechange = function () {
            if (xhr.readyState > 3 && xhr.status == 200) {
                success(xhr.responseText);
            }
        };
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.send(data);
        return xhr;
    }

    setEventHandlers () {
        let global_scope = this;
        $(this.form).on('submit', function(e){
            e.preventDefault();
            global_scope.submitForm(this);
        });

        $('#image-uploader').on('change', function(){
            $(this).next('label').text(this.files[0].name);
            global_scope.form_data.append('file', this.files[0]);
        });
    }

    submitForm (form) {
        let global_scope = this;
        this.form_data = new FormData();
        $(form).find('input').each(function(){
            if($(this).attr('type') != 'file'){
                let input = new Validate(this);
                if($(this).attr('type') == 'radio' && this.checked == false){ return; }

                if(global_scope.form_data[$(this).attr('name')] == null){
                    global_scope.form_data.append($(this).attr('name'), input.value);
                } else if(global_scope.form_data[$(this).attr('name')] != input.value) {
                    global_scope.form_data.append($(this).attr('name'), input.value);
                }
            }
        });

        this.form_data.append('submit', true);
        this.sendRequest();
    }

    sendRequest () {
        this.ajaxCall("/php/data/profile.php", this.form_data, async function(response){
            if(response == ""){
                location.reload();
            } else {
                // Error
                alert(response);
            }
        });
    }

}