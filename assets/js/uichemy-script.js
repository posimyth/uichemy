function copyToClipboard(content){
    window.navigator.clipboard.writeText(content);
}

jQuery(document).ready(function($) {

    function attachCopyLogic(button_id, input_id){

        const copy_btn = $(button_id)

        copy_btn.on('click', function(event) {
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

    // Check if Site URL is reachable
    const site_url_input = $('#uichemy-site-url-input')

    if(site_url_input.length > 0){
        const site_rest_url = site_url_input.val() + 'index.php?rest_route=/'

        fetch(site_rest_url , { credentials: "omit" }).then(res => {
            if(!res.ok) throw new Error(`Not OK Response Recieved for: ${site_rest_url}`)
    
            // console.info("URL Reachable")
            // Toast_message( true, "URL Reachable", "Site URL can be successfully accessed from Outside" );
        }).catch(err => {
            console.warn("Site REST URL: ", site_rest_url)
            console.warn("URL Unreachable: ", err)
    
            Toast_message(
                false,
                "URL Unreachable",
                "Site's REST API cannot be successfully accessed from Outside<br/>"
                + "Your WordPress REST API Must be disabled or inaccessible.<br/>"
                + "Please Enable it, before using it with UiChemy LiveImport.<br/>"
                + "URL: " + site_rest_url + "<br/>"
            );
        })
    }


    // Regenerate Button.
    $('#uichemy-regenerate-btn').on('click', function() {
        var secondSpan = this.querySelector('span:nth-child(2)')

        jQuery.ajax({
            url: uich_ajax_object_data.ajax_url,
            method: "POST",
            data: {
                action: 'uich_regenerate_token',
                nonce: uich_ajax_object_data.nonce,
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
    $('#uichemy-user-select').on('change', function(e) {
        
        jQuery.ajax({
            url: uich_ajax_object_data.ajax_url,
            method: "POST",
            data: {
                action: 'uich_select_user',
                nonce: uich_ajax_object_data.nonce,
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

    $('.uich-acc-trigger').on('click', function(event) {
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
    var success = '<svg width="14" height="14" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M5 0C7.76142 0 10 2.23858 10 5C10 7.76142 7.76142 10 5 10C2.23858 10 0 7.76142 0 5C0 2.23858 2.23858 0 5 0ZM7.52014 3.68435C7.71473 3.48841 7.71362 3.17183 7.51768 2.97725C7.32174 2.78267 7.00516 2.78377 6.81058 2.97971L4.1862 5.62245L3.18681 4.61608C2.99223 4.42014 2.67565 4.41904 2.47971 4.61362C2.28377 4.8082 2.28267 5.12478 2.47725 5.32072L3.83142 6.68435C3.92529 6.77887 4.05299 6.83203 4.1862 6.83203C4.31941 6.83203 4.44712 6.77887 4.54099 6.68435L7.52014 3.68435Z" fill="#00A31B"/></svg>';
    var error = '<svg width="14" height="14" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5 0C2.24 0 0 2.24 0 5C0 7.76 2.24 10 5 10C7.76 10 10 7.76 10 5C10 2.24 7.76 0 5 0ZM5 8C4.63333 8 4.33333 7.7 4.33333 7.33333C4.33333 6.96667 4.63333 6.66667 5 6.66667C5.36667 6.66667 5.66667 6.96667 5.66667 7.33333C5.66667 7.7 5.36667 8 5 8ZM5.76334 2.82999L5.54 5.50334C5.51667 5.78334 5.28333 6 5 6C4.71667 6 4.48333 5.78334 4.46 5.50334L4.23666 2.82999C4.2 2.38334 4.54999 2 5 2C5.42667 2 5.76667 2.34667 5.76667 2.76667C5.76667 2.78667 5.76667 2.80999 5.76334 2.82999Z" fill="#FF1E1E"/></svg>';

    /**Elementor Page Builder*/
    var elementorButton = document.querySelectorAll('.uich-install-elementor');
    if( elementorButton.length > 0 ){
        function installelementorHandler() {
            var $this = this,
                get_tooltip = $this.closest('.uich-listing-strip').querySelector('.uich-sm-icon');

            jQuery.ajax({
                url: uich_ajax_object_data.ajax_url,
                method: "POST",
                data: {
                    action: 'uich_uichemy',
                    nonce: uich_ajax_object_data.nonce,
                    type: 'install_elementor',
                },
                beforeSend: function() {
                    $this.innerHTML = '<span class="uich-round-loader"></span>';
                },
                complete: function() {
                },
                success: function(res){
                    if( res ){
                        $this.innerHTML = 'No Action Needed';
                        get_tooltip.innerHTML = success;

                        if( $this.classList.contains('uich-install-elementor') ){
                            $this.classList.remove('uich-install-elementor');
                        }

                        if ( $this.classList.contains('uich-activation-btn') ) {
                            $this.classList.remove('uich-activation-btn');
                        }

                        elementorButton[0].removeEventListener('click', installelementorHandler);

                        Toast_message( res.success, res.message, res.description );
                    }else{
                        get_tooltip.innerHTML = error;

                        Toast_message( res.success, res.message, res.description );
                    }
                },
                error: function(jq, status, err){
                },
            });
        }

        elementorButton[0].addEventListener('click', installelementorHandler);
    }

    var flexboxcontainer = document.querySelectorAll('.uich-active-flexboxcontainer');
    if( flexboxcontainer.length > 0 ){
        function flexboxcontainerHandler() {
            var $this = this,
                get_tooltip = $this.closest('.uich-listing-strip').querySelector('.uich-sm-icon');

            jQuery.ajax({
                url: uich_ajax_object_data.ajax_url,
                method: "POST",
                data: {
                    action: 'uich_uichemy',
                    nonce: uich_ajax_object_data.nonce,
                    type: 'flexbox_container',
                },
                beforeSend: function () {
                    $this.innerHTML = '<span class="uich-round-loader"></span>';
                },
                complete: function () {
                },
                success: function (res) {
                    if (res) {
                        $this.innerHTML = 'No Action Needed';
                        get_tooltip.innerHTML = success;

                        if ( $this.classList.contains('uich-activation-btn') ) {
                            $this.classList.remove('uich-activation-btn');
                        }

                        // Remove click event after success
                        flexboxcontainer[0].removeEventListener('click', flexboxcontainerHandler);

                        Toast_message( res.success, res.message, res.description );
                    }else{
                        get_tooltip.innerHTML = error;

                        Toast_message( res.success, res.message, res.description );
                    }
                },
                error: function (jq, status, err) {
                    $this.closest('.uich-listing-strip').querySelector('.uich-sm-icon').innerHTML = error;
                },
            });
        }

        // Attach the click event using the named function
        flexboxcontainer[0].addEventListener('click', flexboxcontainerHandler);
    }

    var tpgbButton = document.querySelectorAll('.uich-install-gutenberg');
    if( tpgbButton.length > 0 ){
        function installtpgbOnbording() {
            var $this = this,
                get_tooltip = $this.closest('.uich-listing-strip').querySelector('.uich-sm-icon');


                jQuery.ajax({
                    url: uich_ajax_object_data.ajax_url,
                    method: "POST",
                    data: {
                        action: 'uich_uichemy',
                        nonce: uich_ajax_object_data.nonce,
                        type: 'install_tpgb',
                    },
                    beforeSend: function() {
                        $this.innerHTML = '<span class="uich-round-loader"></span>';
                    },
                    success: function(res){
                        if( res ){
                            $this.innerHTML = 'No Action Needed';
                            get_tooltip.innerHTML = success;
    
                            if( $this.classList.contains('uich-install-gutenberg') ){
                                $this.classList.remove('uich-install-gutenberg');
                            }
    
                            if ( $this.classList.contains('uich-activation-btn') ) {
                                $this.classList.remove('uich-activation-btn');
                            }
    
                            tpgbButton[0].removeEventListener('click', installelementorHandler);
    
                            Toast_message( res.success, res.message, res.description );
                        }else{
                            get_tooltip.innerHTML = error;
    
                            Toast_message( res.success, res.message, res.description );
                        }
                    },
                    error: function(jq, status, err){
                    },
                    complete: function() {
                    },
                });
        }

        tpgbButton[0].addEventListener('click', installtpgbOnbording);
    }

    var fileuploads = document.querySelectorAll('.uich-active-fileuploads');
    if( fileuploads.length > 0 ){
        function fileuploadsHandler() {
            var $this = this,
                get_tooltip = $this.closest('.uich-listing-strip').querySelector('.uich-sm-icon');

            jQuery.ajax({
                url: uich_ajax_object_data.ajax_url,
                method: "POST",
                data: {
                    action: 'uich_uichemy',
                    nonce: uich_ajax_object_data.nonce,
                    type: 'elementor_file_uploads',
                },
                beforeSend: function () {
                    $this.innerHTML = '<span class="uich-round-loader"></span>';
                },
                complete: function () {
                },
                success: function (res) {
                    if (res) {
                        $this.innerHTML = 'No Action Needed';
                        get_tooltip.innerHTML = success;

                        if ( $this.classList.contains('uich-activation-btn') ) {
                            $this.classList.remove('uich-activation-btn');
                        }

                        // Remove click event after success
                        fileuploads[0].removeEventListener('click', fileuploadsHandler);

                        Toast_message( res.success, res.message, res.description );
                    }else{
                        get_tooltip.innerHTML = error;

                        Toast_message( res.success, res.message, res.description );
                    }
                },
                error: function (jq, status, err) {
                },
            });
        }

        // Attach the click event using the named function
        fileuploads[0].addEventListener('click', fileuploadsHandler);
    }

    const tabLinks = document.querySelectorAll(".uich-tablink");
    const tabContents = document.querySelectorAll(".uich-tabcontent");
    tabLinks.forEach(link => {
        link.addEventListener("click", function() {
            const tabId = this.getAttribute("data-tab");
            tabContents.forEach(content => {
                if (content.id === tabId) {
                    content.style.display = "block";
                } else {
                    content.style.display = "none";
                }
            });
            tabLinks.forEach(tablink => {
                tablink.classList.remove("active");
            });
            this.classList.add("active");
        });
    });

    var briuserBtn = document.querySelector('.uich-wel-bricks');
    if( briuserBtn ){
        briuserBtn.addEventListener("click", (e) => {
            var get_tooltip = e.target.closest('.uich-listing-strip').querySelector('.uich-sm-icon');
                e.target.innerHTML = `<span class="uich-round-loader"></span>`;
            jQuery.ajax({
                url: uich_ajax_object_data.ajax_url,
                method: "POST",
                data: {
                    action: 'uich_uichemy',
                    nonce: uich_ajax_object_data.nonce,
                    type: 'bricks_file_uploads',
                },
                beforeSend: function() {
                },
                success: function( data ){
                    if( data.success ){
                        e.target.innerHTML = 'No Action Needed';
                        e.target.setAttribute('disabled', 'disabled');
                        e.target.classList.remove('uich-activation-btn');
                        if ( get_tooltip.classList.contains('uich-error') ) {
                            get_tooltip.classList.remove('uich-error');
                        }
                        if ( !get_tooltip.classList.contains('uich-ob-success') ) {
                            get_tooltip.classList.add('uich-ob-success');
                        }
                        get_tooltip.querySelector('span:nth-child(1)').innerHTML = '<svg width="14" height="14" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M5 0C7.76142 0 10 2.23858 10 5C10 7.76142 7.76142 10 5 10C2.23858 10 0 7.76142 0 5C0 2.23858 2.23858 0 5 0ZM7.52014 3.68435C7.71473 3.48841 7.71362 3.17183 7.51768 2.97725C7.32174 2.78267 7.00516 2.78377 6.81058 2.97971L4.1862 5.62245L3.18681 4.61608C2.99223 4.42014 2.67565 4.41904 2.47971 4.61362C2.28377 4.8082 2.28267 5.12478 2.47725 5.32072L3.83142 6.68435C3.92529 6.77887 4.05299 6.83203 4.1862 6.83203C4.31941 6.83203 4.44712 6.77887 4.54099 6.68435L7.52014 3.68435Z" fill="#00A31B"/></svg>';
                        get_tooltip.querySelector('span:nth-child(2)').innerHTML = 'Activate';
                    }
                },
                error: function(jq, status, err){
                },
                complete: function() {
                },
            });
        })
    }

});
