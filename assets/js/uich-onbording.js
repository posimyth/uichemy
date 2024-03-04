jQuery(document).ready(function($) {

    var next_btn = document.querySelectorAll('.uich-btn-finish');
    if( next_btn.length > 0 ){
        next_btn[0].addEventListener("click", (e) => {
            var btn_main = e.target.closest('.uichemy-btn');
            var getstyle = document.querySelector('.uich-btn-finish');
            var btn_ddd = document.querySelector('.uichemy-btn');

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
                    },
                    complete: function() {
                    },
                });

            }
            
        });
    }

    var back_btn = document.querySelectorAll('.uich-btn-back');
    if( back_btn.length > 0 ){
        back_btn[0].addEventListener("click", (e) => {
            var btn_main = e.target.closest('.uichemy-btn');
            var btn_ddd = document.querySelector('.uichemy-btn');

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

    var skip_btn = document.querySelectorAll('.uich-btn-skip');
    if( skip_btn.length > 0 ){
        skip_btn[0].addEventListener("click", (e) => {
            var btn_ddd = document.querySelector('.uichemy-btn');

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

    /************************************************* Page 5 Active Button ***********************************************/
    var success_svg = '<svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M12.3734 2.79289C12.764 3.18342 12.764 3.81658 12.3734 4.20711L5.95678 10.6238C5.56626 11.0143 4.93309 11.0143 4.54257 10.6238L1.6259 7.70711C1.23538 7.31658 1.23538 6.68342 1.6259 6.29289C2.01643 5.90237 2.64959 5.90237 3.04011 6.29289L5.24967 8.50245L10.9592 2.79289C11.3498 2.40237 11.9829 2.40237 12.3734 2.79289Z" fill="#33C598"/></svg>';
    var error_svg = '<svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg"><g clip-path="url(#clip0_168_67022)"><path d="M7.00033 12.8337C10.222 12.8337 12.8337 10.222 12.8337 7.00033C12.8337 3.77866 10.222 1.16699 7.00033 1.16699C3.77866 1.16699 1.16699 3.77866 1.16699 7.00033C1.16699 10.222 3.77866 12.8337 7.00033 12.8337Z" stroke="#FF3D31" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M7 4.66699V7.00033" stroke="#FF3D31" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M7 9.33301H7.00583" stroke="#FF3D31" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></g><defs><clipPath id="clip0_168_67022"><rect width="14" height="14" fill="white"/></clipPath></defs></svg>';

    /**Elementor Page Builder*/
    var elementorButton = document.querySelectorAll('.uich-onbording-elementor');
    if( elementorButton.length > 0 ){
        function installelementorOnbording() {
            var $this = this;

            this.insertAdjacentHTML("beforeend", `<span class="uich-round-loader" style="margin-left: 5px;"></span>`);

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
                    if( res ){
                        if ( $this.classList.contains('uich-danger') ) {
                            $this.classList.remove('uich-danger');
                        }

                        $this.classList.add('uich-success');
                        $this.querySelector('span:nth-child(1)').innerHTML = success_svg;
                        $this.querySelector('span:nth-child(2)').innerHTML = 'Activate';

                        elementorButton[0].removeEventListener('click', installelementorOnbording);
                    }else{
                        if ( $this.classList.contains('uich-success') ) {
                            $this.classList.remove('uich-success');
                        }

                        $this.classList.add('uich-danger');
                        $this.querySelector('span:nth-child(1)').innerHTML = error_svg;
                        $this.querySelector('span:nth-child(2)').innerHTML = 'Installed';
                    }

                    $this.querySelector('.uich-round-loader').remove();
                },
                error: function(jq, status, err){
                },
                complete: function() {
                },
            });
        }

        elementorButton[0].addEventListener('click', installelementorOnbording);
    }

    var get_flexboxcontainer = document.querySelectorAll('.uich-onbording-fc');
    if( get_flexboxcontainer.length > 0 ){
        function bording_flexboxcontainer() {
            var $this = this;
            
            this.insertAdjacentHTML("beforeend", `<span class="uich-round-loader" style="margin-left: 5px;"></span>`);

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
                    if (res) {
                        if ( $this.classList.contains('uich-danger') ) {
                            $this.classList.remove('uich-danger');
                        }

                        $this.classList.add('uich-success');
                        $this.querySelector('span:nth-child(1)').innerHTML = success_svg;
                        $this.querySelector('span:nth-child(2)').innerHTML = 'Activate';

                        // Remove click event after success
                        get_flexboxcontainer[0].removeEventListener('click', bording_flexboxcontainer);
                    }else{
                        if ( $this.classList.contains('uich-success') ) {
                            $this.classList.remove('uich-success');
                        }

                        $this.classList.add('uich-danger');
                        $this.querySelector('span:nth-child(1)').innerHTML = error_svg;
                        $this.querySelector('span:nth-child(2)').innerHTML = 'Installed';
                    }

                    $this.querySelector('.uich-round-loader').remove();
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

    var fileuploads = document.querySelectorAll('.uich-onbording-fu');
    if( fileuploads.length > 0 ){
        function fileuploadsHandler() {
            var $this = this;

            this.insertAdjacentHTML("beforeend", `<span class="uich-round-loader" style="margin-left: 5px;"></span>`);

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
                    if (res) {
                        if ( $this.classList.contains('uich-danger') ) {
                            $this.classList.remove('uich-danger');
                        }

                        $this.classList.add('uich-success');
                        $this.querySelector('span:nth-child(1)').innerHTML = success_svg;
                        $this.querySelector('span:nth-child(2)').innerHTML = 'Activate';

                        // Remove click event after success
                        fileuploads[0].removeEventListener('click', fileuploadsHandler);
                    }else{
                        if ( $this.classList.contains('uich-success') ) {
                            $this.classList.remove('uich-success');
                        }

                        $this.classList.add('uich-danger');
                        $this.querySelector('span:nth-child(1)').innerHTML = error_svg;
                        $this.querySelector('span:nth-child(2)').innerHTML = 'Installed';
                    }

                    $this.querySelector('.uich-round-loader').remove();
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
