function copyToClipboard(content){
    window.navigator.clipboard.writeText(content);
}

jQuery(document).ready(function($) {

    function attachCopyLogic(button_id, input_id){

        const copy_btn = $(button_id)

        copy_btn.click(function() {
            // CTRL + C.
            copyToClipboard($(input_id).val())
    
            // Change the Icon to done
            copy_btn.find('.done-icon').first().removeClass('hidden')
            copy_btn.find('.copy-icon').first().addClass('hidden')

            // Change it back
            setTimeout(function(){
                copy_btn.find('.copy-icon').first().removeClass('hidden')
                copy_btn.find('.done-icon').first().addClass('hidden')
            }, 1500)
        })
    }

    attachCopyLogic('#uichemy-url-copy-btn', '#uichemy-site-url-input')
    attachCopyLogic('#uichemy-token-copy-btn', '#uichemy-token-input')

    // Regenerate Button.
    $('#uichemy-regenerate-btn').click(function() {
        jQuery.ajax({
            url: uichemy_ajax_object.ajax_url,
            method: "POST",
            data: {
                action: 'uichemy_regenerate_token',
                nonce: uichemy_ajax_object.nonce,
            },
            success: function(res){
                document.querySelector('#uichemy-token-input').value = res.data.token
            },
            error: function(jq, status, err){
                // console.log("It errored..", jq, status, err)
            },
            complete: function() {
            },
        });
    });
});
