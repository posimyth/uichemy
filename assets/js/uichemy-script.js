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

    // Select for User.
    $('#uichemy-user-select').change(function(e) {
        
        jQuery.ajax({
            url: uichemy_ajax_object.ajax_url,
            method: "POST",
            data: {
                action: 'uichemy_select_user',
                nonce: uichemy_ajax_object.nonce,
                new_user: e.target.value,
            },
            success: function(res){
            },
            error: function(){
            },
            complete: function() {
            },
        });
    });
    
    /**Accodion Welcome page */
    $('.uich-accordion-box:first').addClass('uich-active')
    $('.uich-accordion-box:first').children('.uich-acc-trigger').children('i').addClass('fa-minus')
    $('.uich-accordion-box:first').children('.uich-acc-trigger').addClass('selected').next('.uich-acc-container').show()

    $('.uich-acc-trigger').click(function(event) {
        var minus = '<svg class="uich-minus-icon" width="15" height="15" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5 12H19" stroke="#020202" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',
            plus = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 5V19" stroke="#020202" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M5 12H19" stroke="#020202" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
        
        if ($(this).hasClass('selected')) {
            $(this).removeClass('selected');
            $(this).children('i').removeClass('fa-plus').addClass('fa-minus');
            $(this).next().slideUp();
            $(this).parent().removeClass('uich-active');

            // Add Icon
            $(this).find('span').find('svg').remove();
            $(this).find('span').append(plus);    
        } else {
            $('.uich-acc-trigger').removeClass('selected');
            $(this).addClass('selected');
            $('.uich-acc-trigger').children('i').removeClass('fa-minus').addClass('fa-plus');
            $(this).children('i').removeClass('fa-plus').addClass('fa-minus');
            $('.uich-acc-trigger').next().slideUp();
            $(this).next().slideDown();
            $('.uich-accordion-box').removeClass('uich-active');
            $(this).parent().addClass('uich-active');

            var get_accordionarea = document.querySelectorAll('.uich-accordion-area .uich-accordion-box');
            if( get_accordionarea.length > 0 ){
                get_accordionarea.forEach(function(self) {
                    var GetSvg = self.querySelector('.uich-acc-trigger');

                        console.log( GetSvg );
                    if( self.classList.contains('uich-active') ){
                        $(GetSvg).find('span').find('svg').remove();
                        $(GetSvg).find('span').append(minus);
                    }else{
                        $(GetSvg).find('span').find('svg').remove();
                        $(GetSvg).find('span').append(plus);   
                    }

                } );
            }
        }
    });


    /************************************************* Install & Active ***********************************************/
    var loader_html = '<span class="uich-loader-main" style="display: flex;flex-direction: row;"><span class="uich-install-loader"></span><span class="uich-install-loader"></span><span class="uich-install-loader"></span></span>';

    /**Elementor Page Builder*/
    var elementorButton = document.querySelectorAll('.uich-install-elementor');
    if( elementorButton.length > 0 ){
        function installelementorHandler() {
            var $this = this;

            jQuery.ajax({
                url: uichemy_ajax_object.ajax_url,
                method: "POST",
                data: {
                    action: 'uich_uichemy',
                    nonce: uichemy_ajax_object.nonce,
                    type: 'install_elementor',
                },
                beforeSend: function() {
                    $this.classList.add('uich-hide');
                    $this.insertAdjacentHTML("afterend", loader_html);
                },
                success: function(res){

                    if( res ){
                        if( $this.classList.contains('uich-uninstalled') ){
                            $this.classList.remove('uich-uninstalled');
                        }

                        if( $this.classList.contains('uich-install-elementor') ){
                            $this.classList.remove('uich-install-elementor');
                        }

                        $this.classList.add('uich-installed');
                        $this.innerHTML = 'Activate';

                        elementorButton[0].removeEventListener('click', installelementorHandler);

                        Toast_message( res.success, res.message, res.description );
                    }

                    $this.classList.remove('uich-hide');
                    $this.nextElementSibling.remove()
                },
                error: function(jq, status, err){
                },
                complete: function() {
                },
            });
        }

        elementorButton[0].addEventListener('click', installelementorHandler);
    }

    var flexboxcontainer = document.querySelectorAll('.uich-active-flexboxcontainer');
    if( flexboxcontainer.length > 0 ){
        function flexboxcontainerHandler() {
            var $this = this;
            
            jQuery.ajax({
                url: uichemy_ajax_object.ajax_url,
                method: "POST",
                data: {
                    action: 'uich_uichemy',
                    nonce: uichemy_ajax_object.nonce,
                    type: 'flexbox_container',
                },
                beforeSend: function () {
                    $this.classList.add('uich-hide');
                    $this.insertAdjacentHTML("afterend", loader_html);
                },
                success: function (res) {
                    if (res) {
                        if ($this.classList.contains('uich-uninstalled')) {
                            $this.classList.remove('uich-uninstalled');
                        }

                        $this.classList.add('uich-installed');
                        $this.innerHTML = 'Activate';

                        // Remove click event after success
                        flexboxcontainer[0].removeEventListener('click', flexboxcontainerHandler);

                        Toast_message( res.success, res.message, res.description );
                    }else{
                        Toast_message( res.success, res.message, res.description );
                    }

                    $this.classList.remove('uich-hide');
                    $this.nextElementSibling.remove();
                },
                error: function (jq, status, err) {
                    // Handle error if needed
                },
                complete: function () {
                    // Any code you want to execute after success or error
                },
            });
        }

        // Attach the click event using the named function
        flexboxcontainer[0].addEventListener('click', flexboxcontainerHandler);
    }

    var fileuploads = document.querySelectorAll('.uich-active-fileuploads');
    if( fileuploads.length > 0 ){
        function fileuploadsHandler() {
            var $this = this;
            
            jQuery.ajax({
                url: uichemy_ajax_object.ajax_url,
                method: "POST",
                data: {
                    action: 'uich_uichemy',
                    nonce: uichemy_ajax_object.nonce,
                    type: 'elementor_file_uploads',
                },
                beforeSend: function () {
                    $this.classList.add('uich-hide');
                    $this.insertAdjacentHTML("afterend", loader_html);
                },
                success: function (res) {
                    
                    if (res) {
                        if ($this.classList.contains('uich-uninstalled')) {
                            $this.classList.remove('uich-uninstalled');
                        }

                        $this.classList.add('uich-installed');
                        $this.innerHTML = 'Activate';

                        // Remove click event after success
                        fileuploads[0].removeEventListener('click', fileuploadsHandler);

                        Toast_message( res.success, res.message, res.description );
                    }else{
                        Toast_message( res.success, res.message, res.description );
                    }

                    $this.classList.remove('uich-hide');
                    $this.nextElementSibling.remove();
                },
                error: function (jq, status, err) {
                    // Handle error if needed
                },
                complete: function () {
                    // Any code you want to execute after success or error
                },
            });
        }

        // Attach the click event using the named function
        fileuploads[0].addEventListener('click', fileuploadsHandler);
    }

});
