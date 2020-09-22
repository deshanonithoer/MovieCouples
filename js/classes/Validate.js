class Validate {

    constructor (input) {
        this.value = null;
        this.error = false;
        this.input = input;

        let validate_type = $(this.input).attr('type');
        if(validate_type){
            this[validate_type]();
        }
    }

    text (){
        this.value =  $(this.input).val().replace(/</g, "&lt;").replace(/>/g, "&gt;");
    }

    radio () {
        if(this.input.checked == true){
            this.value = $(this.input).val();
        }
    }
}