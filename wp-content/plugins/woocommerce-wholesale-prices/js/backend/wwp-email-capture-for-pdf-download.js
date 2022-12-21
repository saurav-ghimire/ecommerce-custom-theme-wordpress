/**
 * WWP Email Capture bow for PDF Download
 * 
 * @since 2.1.2
 */
jQuery(document).ready(function ($){
    var $options = email_capture_box_options, $validInputFields = false;


    /**
     * Send/Submit Email Capture box, if firstname and email input fields are valid.
     * 
     * We will send this in the drip account, or just forward it to a page, not yet clear where to pass this information. waiting for Josh final decision.
     * 
     * @since 2.1.2
     */
    $(document.body).on('click', '#wwp_email_capture_download_free_guide_button', function(){
        validateInputFields();
    });

    /**
     * Check first name input field if it has a valid entry, remember we only accept letters and space only.
     * 
     * @since 2.1.2
     */
    $('#wwp_email_capture_input_name').on('keyup change', function(){
        var regex = /^[a-zA-Z\s]*$/; // this regex will only allow letters and space.

        if(!regex.test($(this).val())){
            $(this).prev('span.error').text($options.i18n_wwp_email_capture_box_firstname_invalid_char);
            $(this).addClass('invalid-field');
            $validInputFields = false;
        }else{
            $(this).prev('span.error').text('');
            $(this).removeClass('invalid-field');
            $validInputFields = true;
        }

    });

    /**
     * Check email input field if its a valid email address
     * 
     * @since 2.1.2
     */
    $('#wwp_email_capture_input_email').on('change', function(){
        var regex = /(?=^.{10,30}$)([a-zA-Z\d*])+(\.?)([a-zA-Z\d*])*@{1}([a-zA-z\d*])+(\.){1}([a-zA-Z\d*]){2,}/gm; // valid email address

        if(!$(this).val().match(regex) && $(this).val().length > 0){
            $(this).prev('span.error').text($options.i18n_wwp_email_capture_box_email_invalid_char);
            $(this).addClass('invalid-field');
            $validInputFields = false;
        }else{
            $(this).prev('span.error').text('');
            $(this).removeClass('invalid-field');
            $validInputFields = true;
        }

    });

    /**
     * Validate input fields
     * 
     * @since 2.1.2
     */
    function validateInputFields(){
        var firstname, email;

        firstname = $('#wwp_email_capture_input_name');
        email     = $('#wwp_email_capture_input_email');

        // check if email and firstname is empty
        if(firstname.val() == '' && email.val() == ''){
            firstname.addClass('invalid-field');
            email.addClass('invalid-field');

            firstname.prev('span.error').text($options.i18n_wwp_email_capture_box_firstname_required);
            email.prev('span.error').text($options.i18n_wwp_email_capture_box_email_required);

            return false;
        }else if(firstname.val() == ''){
            firstname.addClass('invalid-field');
            email.removeClass('invalid-field');

            firstname.prev('span.error').text($options.i18n_wwp_email_capture_box_firstname_required);
            email.prev('span.error').text('');

            return false;
        }else if(email.val() == ''){
            firstname.removeClass('invalid-field');
            email.addClass('invalid-field');

            firstname.prev('span.error').text('');
            email.prev('span.error').text($options.i18n_wwp_email_capture_box_email_required);

            return false;
        }else{
            firstname.removeClass('invalid-field');
            email.removeClass('invalid-field');

            email.prev('span.error').text('');
            firstname.prev('span.error').text('');

            return true;
        }

    }

});