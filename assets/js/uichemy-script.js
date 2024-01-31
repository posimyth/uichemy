function copyToClipboard(content){
    window.navigator.clipboard.writeText(content);
}

jQuery(document).ready(function($) {

    function attachCopyLogic(button_id, input_id){

        const copy_btn = $(button_id)

        copy_btn.click(function(event) {
            event.preventDefault();
            // Stop the event from propagating up or down the DOM tree
            event.stopPropagation();
            
            // CTRL + C.
            copyToClipboard($(input_id).val())
    
            // Change the Icon to done
            copy_btn.find('.done-icon').first().removeClass('hidden')
            copy_btn.find('.copy-icon').first().addClass('hidden')

            // Change it back.
            setTimeout(function(){
                copy_btn.find('.copy-icon').first().removeClass('hidden')
                copy_btn.find('.done-icon').first().addClass('hidden')
            }, 1500)
        });
    }

    attachCopyLogic('#uichemy-url-copy-btn', '#uichemy-site-url-input')
    attachCopyLogic('#uichemy-token-copy-btn', '#uichemy-token-input')

    // Regenerate Button.
    $('#uichemy-regenerate-btn').click(function() {
        var secondSpan = this.querySelector('span:nth-child(2)')

        jQuery.ajax({
            url: uichemy_ajax_object.ajax_url,
            method: "POST",
            data: {
                action: 'uichemy_regenerate_token',
                nonce: uichemy_ajax_object.nonce,
            },
            beforeSend: function() {
                secondSpan.style.display = 'flex';
            },
            success: function(res){
                if( res.data.token ){
                    document.querySelector('#uichemy-token-input').value = res.data.token
                }
            },
            error: function(jq, status, err){
            },
            complete: function() {
                secondSpan.style.display = 'none';
            },
        });
    });

    /**Accodion Welcome page */
    $('.uich-accordion-box:first').addClass('uich-active')
    $('.uich-accordion-box:first').children('.uich-acc-trigger').children('i').addClass('fa-minus')
    $('.uich-accordion-box:first').children('.uich-acc-trigger').addClass('selected').next('.uich-acc-container').show()

    $('.uich-acc-trigger').click(function(event) {
        if ($(this).hasClass('selected')) {
            $(this).removeClass('selected');
            $(this).children('i').removeClass('fa-plus').addClass('fa-minus');
            $(this).next().slideUp();
            $(this).parent().removeClass('uich-active');
        } else {
            $('.uich-acc-trigger').removeClass('selected');
            $(this).addClass('selected');
            $('.uich-acc-trigger').children('i').removeClass('fa-minus').addClass('fa-plus');
            $(this).children('i').removeClass('fa-plus').addClass('fa-minus');
            $('.uich-acc-trigger').next().slideUp();
            $(this).next().slideDown();
            $('.uich-accordion-box').removeClass('active');
            $(this).parent().addClass('active');
        }
    });
});
