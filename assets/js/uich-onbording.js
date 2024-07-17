jQuery(document).ready(function($) {

    /**
     * Next Button event
     * 
     * @since 1.2.2
     * */
    var next_btn = document.querySelectorAll('.uich-btn-finish');
    if( next_btn.length > 0 ){
        next_btn[0].addEventListener("click", (e) => {
            var btn_main = e.target.closest('.uichemy-btn');
            var getstyle = document.querySelector('.uich-btn-finish');
            var btn_ddd = document.querySelector('.uichemy-btn');

            set_builder_setting();

            if( btn_main.classList.contains('uich-btn-step-1') ){
                document.querySelector('.uich-popup-container.uich-step-1').style.display = 'none';
                document.querySelector('.uich-popup-container.uich-step-2').style.display = 'flex';
                document.querySelector('.uichemy-page-count').innerHTML = '<span class="uich-pagination-first">02</span>/06';
                
                btn_ddd.classList.remove('uich-btn-step-1');
                btn_ddd.classList.add('uich-btn-step-2');

                e.target.innerHTML = 'Next';
                
                return;
            }

            if( btn_main.classList.contains('uich-btn-step-2') ){
                document.querySelector('.uich-popup-container.uich-step-2').style.display = 'none';
                document.querySelector('.uich-popup-container.uich-step-3').style.display = 'flex';
                document.querySelector('.uichemy-page-count').innerHTML = '<span class="uich-pagination-first">03</span>/06';

                btn_ddd.classList.remove('uich-btn-step-2');
                btn_ddd.classList.add('uich-btn-step-3');

                e.target.innerHTML = 'Next';

                return;
            }

            if( btn_main.classList.contains('uich-btn-step-3') ){
                document.querySelector('.uich-popup-container.uich-step-3').style.display = 'none';
                document.querySelector('.uich-popup-container.uich-step-4').style.display = 'flex';
                document.querySelector('.uichemy-page-count').innerHTML = '<span class="uich-pagination-first">04</span>/06';

                btn_ddd.classList.remove('uich-btn-step-3');
                btn_ddd.classList.add('uich-btn-step-4');

                e.target.innerHTML = 'Next';

                return;
            }

            if( btn_main.classList.contains('uich-btn-step-4') ){
                document.querySelector('.uich-popup-container.uich-step-4').style.display = 'none';
                document.querySelector('.uich-popup-container.uich-step-5').style.display = 'flex';
                document.querySelector('.uichemy-page-count').innerHTML = '<span class="uich-pagination-first">05</span>/06';

                btn_ddd.classList.remove('uich-btn-step-4');
                btn_ddd.classList.add('uich-btn-step-5');

                e.target.innerHTML = 'Next';

                return;
            }

            if( btn_main.classList.contains('uich-btn-step-5') ){
                document.querySelector('.uich-popup-container.uich-step-5').style.display = 'none';
                document.querySelector('.uich-popup-container.uich-step-6').style.display = 'flex';
                document.querySelector('.uichemy-page-count').innerHTML = '<span class="uich-pagination-first">06</span>/06';

                btn_ddd.classList.remove('uich-btn-step-5');
                btn_ddd.classList.add('uich-btn-step-6');

                e.target.innerHTML = 'Finish';

                return;
            }

            if( btn_main.classList.contains('uich-btn-step-6') ){

                e.target.disabled = true;
                e.target.innerHTML = '<span class="uich-round-loader"></span>'; 
                
                jQuery.ajax({
                    url: uich_onboarding_ajax.ajax_url,
                    method: "POST",
                    data: {
                        action: 'uich_boarding_store',
                        nonce: uich_onboarding_ajax.nonce,
                    },
                    beforeSend: function() {
                    },
                    success: function(res){

                        if( btn_main.classList.contains('uich-btn-step-6') ){
                            document.querySelector('.uich-popup-container.uich-step-6').style.display = 'none';
                        }

                        document.querySelector('.uich-main').classList.add('uich-onbording-hide');

                        Toast_message( 1, res.message, res.description );
                    },
                    error: function(jq, status, err){
                        document.querySelector('.uich-main').classList.add('uich-onbording-hide');
                    },
                    complete: function() {
                    },
                });

            }
            
        });
    }

    /**
     * Back Button event
     * 
     * @since 1.2.2
     * */
    var back_btn = document.querySelectorAll('.uich-btn-back');
    if( back_btn.length > 0 ){
        back_btn[0].addEventListener("click", (e) => {
            var btn_main = e.target.closest('.uichemy-btn');
            var btn_ddd = document.querySelector('.uichemy-btn');

            set_builder_setting();

            document.querySelector('.uich-btn-finish').innerHTML = 'Next';

            if( btn_main.classList.contains('uich-btn-step-2') ){
                document.querySelector('.uich-popup-container.uich-step-1').style.display = 'flex';
                document.querySelector('.uich-popup-container.uich-step-2').style.display = 'none';
                document.querySelector('.uichemy-page-count').innerHTML = '<span class="uich-pagination-first">01</span>/06';

                btn_ddd.classList.remove('uich-btn-step-2');
                btn_ddd.classList.add('uich-btn-step-1');

                return;
            }

            if( btn_main.classList.contains('uich-btn-step-3') ){
                document.querySelector('.uich-popup-container.uich-step-2').style.display = 'flex';
                document.querySelector('.uich-popup-container.uich-step-3').style.display = 'none';
                document.querySelector('.uichemy-page-count').innerHTML = '<span class="uich-pagination-first">02</span>/06';

                btn_ddd.classList.remove('uich-btn-step-3');
                btn_ddd.classList.add('uich-btn-step-2');

                return;
            }

            if( btn_main.classList.contains('uich-btn-step-4') ){
                document.querySelector('.uich-popup-container.uich-step-3').style.display = 'flex';
                document.querySelector('.uich-popup-container.uich-step-4').style.display = 'none';
                document.querySelector('.uichemy-page-count').innerHTML = '<span class="uich-pagination-first">03</span>/06';

                btn_ddd.classList.remove('uich-btn-step-4');
                btn_ddd.classList.add('uich-btn-step-3');

                return;
            }

            if( btn_main.classList.contains('uich-btn-step-5') ){
                document.querySelector('.uich-popup-container.uich-step-4').style.display = 'flex';
                document.querySelector('.uich-popup-container.uich-step-5').style.display = 'none';
                document.querySelector('.uichemy-page-count').innerHTML = '<span class="uich-pagination-first">04</span>/06';

                btn_ddd.classList.remove('uich-btn-step-5');
                btn_ddd.classList.add('uich-btn-step-4');

                return;
            }

            if( btn_main.classList.contains('uich-btn-step-6') ){
                document.querySelector('.uich-popup-container.uich-step-5').style.display = 'flex';
                document.querySelector('.uich-popup-container.uich-step-6').style.display = 'none';
                document.querySelector('.uichemy-page-count').innerHTML = '<span class="uich-pagination-first">05</span>/06';

                btn_ddd.classList.remove('uich-btn-step-6');
                btn_ddd.classList.add('uich-btn-step-5');

                return;
            }
            
        }); 
    }

    /**
     * Skip Button event
     * 
     * @since 1.2.2
     * */
    var skip_btn = document.querySelectorAll('.uich-btn-skip');
    if( skip_btn.length > 0 ){
        skip_btn[0].addEventListener("click", (e) => {
            var btn_ddd = document.querySelector('.uichemy-btn');

            set_builder_setting();

            document.querySelector('.uich-popup-container.uich-step-1').style.display = 'none';
            document.querySelector('.uich-popup-container.uich-step-2').style.display = 'none';
            document.querySelector('.uich-popup-container.uich-step-3').style.display = 'none';
            document.querySelector('.uich-popup-container.uich-step-4').style.display = 'none';
            document.querySelector('.uich-popup-container.uich-step-5').style.display = 'none';

            document.querySelector('.uich-popup-container.uich-step-6').style.display = 'flex';
            document.querySelector('.uichemy-page-count').innerHTML = '<span class="uich-pagination-first">06</span>/06';
            document.querySelector('.uich-btn-finish').innerHTML = 'Finish';
            
            btn_ddd.classList.remove('uich-btn-step-1');
            btn_ddd.classList.remove('uich-btn-step-2');
            btn_ddd.classList.remove('uich-btn-step-3');
            btn_ddd.classList.remove('uich-btn-step-4');
            btn_ddd.classList.remove('uich-btn-step-5');

            btn_ddd.classList.add('uich-btn-step-6');

            return;
            
        }); 
    }

    /**
     * Check ELementor and Brick Every Page
     * 
     * @since 1.2.2
     * */
    function set_builder_setting(){
        var checkbox = document.querySelector('.uich-latest-builder input:checked');
            
        if( checkbox.value ){
            if ( 'elementor' === checkbox.value ){
                let get_builder = document.querySelectorAll('.uichemy-info .uich-box');

                    document.querySelector('.uich-cover-bricks').style.display = "none";
                    document.querySelector('.uich-cover-gutenberg').style.display = "none";
                    document.querySelector('.uich-cover-elementor').style.display = "flex";

                    get_builder.forEach(function(self) {
                        if( self.classList.contains('uich-page-elementor') ){
                            self.style.display = "flex";
                        }else{
                            self.style.display = "none";
                        }
                    });
            }else if ( 'bricks' === checkbox.value ) {
                let get_builder = document.querySelectorAll('.uichemy-info .uich-box');

                    document.querySelector('.uich-cover-elementor').style.display = "none";
                    document.querySelector('.uich-cover-gutenberg').style.display = "none";
                    document.querySelector('.uich-cover-bricks').style.display = "flex";

                    get_builder.forEach(function(self) {
                        if( self.classList.contains('uich-page-bricks') ){
                            self.style.display = "flex";
                        }else{
                            self.style.display = "none";
                        }
                    });
            }else if ( 'gutenberg' === checkbox.value ){
                let get_builder = document.querySelectorAll('.uichemy-info .uich-box');

                    document.querySelector('.uich-cover-bricks').style.display = "none";
                    document.querySelector('.uich-cover-elementor').style.display = "none";
                    document.querySelector('.uich-cover-gutenberg').style.display = "flex";

                    get_builder.forEach(function(self) {
                        if( self.classList.contains('uich-page-gutenberg') ){
                            self.style.display = "flex";
                        }else{
                            self.style.display = "none";
                        }
                    });
            }else{
                let get_builder = document.querySelectorAll('.uichemy-info .uich-box');
                    get_builder.forEach(function(self) {
                        self.style.display = "none";
                    });
            }
        }
    }

    /************************************************* Page 5 Active Buttons ***********************************************/
    var success_svg = '<svg width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M8.33366 2.5L3.75033 7.08333L1.66699 5" stroke="white" stroke-linecap="round" stroke-linejoin="round"></path></svg>';
    var error_svg = '<svg width="14" height="14" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M5.00016 1.33301C2.97512 1.33301 1.3335 2.97463 1.3335 4.99967C1.3335 7.02472 2.97512 8.66634 5.00016 8.66634C7.02521 8.66634 8.66683 7.02472 8.66683 4.99967C8.66683 2.97463 7.02521 1.33301 5.00016 1.33301ZM0.333496 4.99967C0.333496 2.42235 2.42283 0.333008 5.00016 0.333008C7.57749 0.333008 9.66683 2.42235 9.66683 4.99967C9.66683 7.577 7.57749 9.66634 5.00016 9.66634C2.42283 9.66634 0.333496 7.577 0.333496 4.99967ZM5 2.83301C5.27614 2.83301 5.5 3.05687 5.5 3.33301V4.99967C5.5 5.27582 5.27614 5.49967 5 5.49967C4.72386 5.49967 4.5 5.27582 4.5 4.99967V3.33301C4.5 3.05687 4.72386 2.83301 5 2.83301ZM5 6.16699C4.72386 6.16699 4.5 6.39085 4.5 6.66699C4.5 6.94313 4.72386 7.16699 5 7.16699H5.00417C5.28031 7.16699 5.50417 6.94313 5.50417 6.66699C5.50417 6.39085 5.28031 6.16699 5.00417 6.16699H5Z" fill="white"></path></svg>';

    /**
     * Elementor Page Builder Install
     * 
     * @since 1.2.2
     * */
    var elementorButton = document.querySelectorAll('.uich-onbording-elementor');
    if( elementorButton.length > 0 ){
        function installelementorOnbording() {
            var $this = this,
                get_tooltip = $this.closest('.uich-box').querySelector('.uich-tooltip');

                $this.innerHTML = `<span class="uich-round-loader"></span>`;

                jQuery.ajax({
                    url: uichemy_ajax_object.ajax_url,
                    method: "POST",
                    data: {
                        action: 'uich_uichemy',
                        nonce: uichemy_ajax_object.nonce,
                        type: 'install_elementor',
                    },
                    beforeSend: function() {
                    },
                    success: function(res){
                        console.log( res );
                        if ( res && res.success ) {
                            $this.innerHTML = `No Action Needed`;

                            if ( $this.classList.contains('uich-success') ) {
                                $this.classList.remove('uich-success');
                            }

                            if ( $this.classList.contains('uich-onbording-fc') ) {
                                $this.classList.remove('uich-onbording-fc');
                            }

                            if ( get_tooltip.classList.contains('uich-error') ) {
                                get_tooltip.classList.remove('uich-error');
                            }

                            if ( !get_tooltip.classList.contains('uich-ob-success') ) {
                                get_tooltip.classList.add('uich-ob-success');
                            }

                            $this.classList.add('uich-ob-active');

                            get_tooltip.querySelector('span:nth-child(1)').innerHTML = success_svg;
                            get_tooltip.querySelector('span:nth-child(2)').innerHTML = 'Activate';

                            // Remove click event after success
                            elementorButton[0].removeEventListener('click', installelementorOnbording);
                        }else{
                            $this.innerHTML = `Install & Activate`;

                            get_tooltip.querySelector('span:nth-child(1)').innerHTML = error_svg;
                            get_tooltip.querySelector('span:nth-child(2)').innerHTML = 'Activate';
                        }
                    },
                    error: function(jq, status, err){
                    },
                    complete: function() {
                    },
                });
        }

        elementorButton[0].addEventListener('click', installelementorOnbording);
    }

    /**
     * The Plus Blocks for Block Editor Install
     * 
     * @since 1.2.2
     * */
    var tpgbButton = document.querySelectorAll('.uich-onbording-gutenberg');
    if( tpgbButton.length > 0 ){
        function installtpgbOnbording() {
            var $this = this,
                get_tooltip = $this.closest('.uich-box').querySelector('.uich-tooltip');

                $this.innerHTML = `<span class="uich-round-loader"></span>`;

                jQuery.ajax({
                    url: uichemy_ajax_object.ajax_url,
                    method: "POST",
                    data: {
                        action: 'uich_uichemy',
                        nonce: uichemy_ajax_object.nonce,
                        type: 'install_tpgb',
                    },
                    beforeSend: function() {
                    },
                    success: function(res){
                        if ( res && res.success ) {
                            $this.innerHTML = `No Action Needed`;

                            if ( $this.classList.contains('uich-success') ) {
                                $this.classList.remove('uich-success');
                            }

                            if ( $this.classList.contains('uich-onbording-fc') ) {
                                $this.classList.remove('uich-onbording-fc');
                            }

                            if ( get_tooltip.classList.contains('uich-error') ) {
                                get_tooltip.classList.remove('uich-error');
                            }

                            if ( !get_tooltip.classList.contains('uich-ob-success') ) {
                                get_tooltip.classList.add('uich-ob-success');
                            }

                            $this.classList.add('uich-ob-active');

                            get_tooltip.querySelector('span:nth-child(1)').innerHTML = success_svg;
                            get_tooltip.querySelector('span:nth-child(2)').innerHTML = 'Activate';

                            // Remove click event after success
                            elementorButton[0].removeEventListener('click', installtpgbOnbording);
                        }else{
                            $this.innerHTML = `Install & Activate`;

                            get_tooltip.querySelector('span:nth-child(1)').innerHTML = error_svg;
                            get_tooltip.querySelector('span:nth-child(2)').innerHTML = 'Activate';
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

    /**
     * Elementor Flex Contener Active
     * 
     * @since 1.2.2
     * */
    var get_flexboxcontainer = document.querySelectorAll('.uich-onbording-fc');
    if( get_flexboxcontainer.length > 0 ){
        function bording_flexboxcontainer() {
            var $this = this,
                get_tooltip = $this.closest('.uich-box').querySelector('.uich-tooltip');

                $this.innerHTML = `<span class="uich-round-loader"></span>`;

            jQuery.ajax({
                url: uichemy_ajax_object.ajax_url,
                method: "POST",
                data: {
                    action: 'uich_uichemy',
                    nonce: uichemy_ajax_object.nonce,
                    type: 'flexbox_container',
                },
                beforeSend: function () {
                },
                success: function (res) {
                    
                    if (res.success) {
                        $this.innerHTML = `No Action Needed`;

                        if ( $this.classList.contains('uich-success') ) {
                            $this.classList.remove('uich-success');
                        }
                        
                        if ( $this.classList.contains('uich-onbording-fc') ) {
                            $this.classList.remove('uich-onbording-fc');
                        }

                        if ( get_tooltip.classList.contains('uich-error') ) {
                            get_tooltip.classList.remove('uich-error');
                        }

                        if ( !get_tooltip.classList.contains('uich-ob-success') ) {
                            get_tooltip.classList.add('uich-ob-success');
                        }

                        $this.classList.add('uich-ob-active');

                        get_tooltip.querySelector('span:nth-child(1)').innerHTML = success_svg;
                        get_tooltip.querySelector('span:nth-child(2)').innerHTML = 'Activate';

                        // Remove click event after success
                        get_flexboxcontainer[0].removeEventListener('click', bording_flexboxcontainer);
                    }else{
                        $this.innerHTML = `Install & Activate`;

                        get_tooltip.querySelector('span:nth-child(1)').innerHTML = error_svg;
                        get_tooltip.querySelector('span:nth-child(2)').innerHTML = 'Inactive';
                    }
                },
                error: function (jq, status, err) {
                    // Handle error if needed
                },
                complete: function () {
                    // Any code you want to execute after success or error
                },
            });
        }

        get_flexboxcontainer[0].addEventListener('click', bording_flexboxcontainer);
    }

    /**
     * Elementor SVG Upload Enable
     * 
     * @since 1.2.2
     * */
    var fileuploads = document.querySelectorAll('.uich-onbording-fu');
    if( fileuploads.length > 0 ){
        function fileuploadsHandler() {
            var $this = this,
                get_tooltip = $this.closest('.uich-box').querySelector('.uich-tooltip');

                $this.innerHTML = `<span class="uich-round-loader"></span>`;

            jQuery.ajax({
                url: uichemy_ajax_object.ajax_url,
                method: "POST",
                data: {
                    action: 'uich_uichemy',
                    nonce: uichemy_ajax_object.nonce,
                    type: 'elementor_file_uploads',
                },
                beforeSend: function () {
                },
                success: function (res) {
                    if (res.success) {
                        $this.innerHTML = `No Action Needed`;

                        if ( $this.classList.contains('uich-success') ) {
                            $this.classList.remove('uich-success');
                        }
                        
                        if ( $this.classList.contains('uich-onbording-fc') ) {
                            $this.classList.remove('uich-onbording-fc');
                        }

                        if ( get_tooltip.classList.contains('uich-error') ) {
                            get_tooltip.classList.remove('uich-error');
                        }

                        if ( !get_tooltip.classList.contains('uich-ob-success') ) {
                            get_tooltip.classList.add('uich-ob-success');
                        }

                        $this.classList.add('uich-ob-active');

                        get_tooltip.querySelector('span:nth-child(1)').innerHTML = success_svg;
                        get_tooltip.querySelector('span:nth-child(2)').innerHTML = 'Activate';

                        // Remove click event after success
                        get_flexboxcontainer[0].removeEventListener('click', bording_flexboxcontainer);
                    }else{
                        $this.innerHTML = `Install & Activate`;

                        get_tooltip.querySelector('span:nth-child(1)').innerHTML = error_svg;
                        get_tooltip.querySelector('span:nth-child(2)').innerHTML = 'Inactive';
                    }
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

    /**
     * Bricks Page Builder
     * 
     * @since 1.2.2
     * */
    var briuserBtn = document.querySelector('.uich-onbording-bricks');
    if( briuserBtn ){
        briuserBtn.addEventListener("click", (e) => {
            var get_tooltip = e.target.closest('.uich-box').querySelector('.uich-tooltip');

                e.target.innerHTML = `<span class="uich-round-loader"></span>`;

            jQuery.ajax({
                url: uich_onboarding_ajax.ajax_url,
                method: "POST",
                data: {
                    action: 'uich_uichemy',
                    nonce: uichemy_ajax_object.nonce,
                    type: 'bricks_file_uploads',
                },
                beforeSend: function() {
                },
                success: function( data ){
                    if( data.success ){
                        e.target.innerHTML = 'No Action Needed';
                        e.target.setAttribute('disabled', 'disabled');
                        e.target.classList.add('uich-ob-active');

                        if ( get_tooltip.classList.contains('uich-error') ) {
                            get_tooltip.classList.remove('uich-error');
                        }

                        if ( !get_tooltip.classList.contains('uich-ob-success') ) {
                            get_tooltip.classList.add('uich-ob-success');
                        }

                        get_tooltip.querySelector('span:nth-child(1)').innerHTML = success_svg;
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
