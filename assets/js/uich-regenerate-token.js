jQuery(document).ready(function ($) {

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


    $(document).on("click", "#uichemy-regenerate-btn", function () {
        var secondSpan = this.querySelector("span:nth-child(2)");
        var firstSpan = this.querySelector("span:nth-child(1)");

        jQuery.ajax({
            url: uich_ajax_object_data.ajax_url,
            method: "POST",
            data: {
                action: "uich_regenerate_token",
                nonce: uich_ajax_object_data.nonce,
            },
            beforeSend: function () {
                secondSpan.style.display = "flex";
                firstSpan.style.display = "none";
            },
            success: function (res) {

                if (res.data.token) {
                    const tokenInput = document.querySelector("#uichemy-token-input");
                    tokenInput.setAttribute('data-original-token', res.data.token);
                
                    const parts = res.data.token.split('-');
                    const maskedParts = parts.map((part, index) => {
                        if (index === parts.length - 1) {
                            return part;
                        }
                        return 'X'.repeat(part.length);
                    });
                
                    const maskedToken = maskedParts.join('-');
                    tokenInput.value = maskedToken;
                }

            },
            error: function (jq, status, err) {},
            complete: function () {
                secondSpan.style.display = "none";
                firstSpan.style.display = "flex";
            },
        });
    });

    // Select for User.
    $(document).on("change", "#uichemy-user-select", function (e) {
        jQuery.ajax({
            url: uich_ajax_object_data.ajax_url,
            method: "POST",
            data: {
                action: "uich_select_user",
                nonce: uich_ajax_object_data.nonce,
                new_user: e.target.value,
            },
            success: function (res) { console.log("new_user",res); },
            error: function () {},
            complete: function () {},
        });
    });
});
