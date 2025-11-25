// Gutenberg Paste Button
(function(window, wp) {
    const copyButton = '<svg width="14" height="15" viewBox="0 0 14 15" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M8.15443 7.91705L8.14325 10.9265C8.14325 10.9265 5.60113 11.0735 5.60113 9.11541V4.12304C5.60071 3.162 5.21864 2.24047 4.53893 1.56107C3.85923 0.881661 2.93753 0.5 1.9765 0.5H3.17421e-07V10.4512C-0.000209837 10.9806 0.103934 11.5048 0.30647 11.9939C0.509006 12.483 0.805964 12.9274 1.18037 13.3016C1.55477 13.6759 1.99926 13.9727 2.48845 14.175C2.97763 14.3773 3.5019 14.4813 4.03128 14.4809C4.30471 14.5064 4.57992 14.5064 4.85335 14.4809H9.66757C10.7509 14.4809 11.7899 14.0505 12.556 13.2844C13.322 12.5184 13.7524 11.4794 13.7524 10.3961V7.91945L8.15443 7.91705ZM13.2674 10.3937C13.2655 11.3465 12.8862 12.2599 12.2124 12.9337C11.5386 13.6074 10.6252 13.9868 9.67235 13.9887H7.38669C7.62987 13.794 7.8426 13.564 8.01782 13.3065C8.55948 12.5076 8.62818 11.6136 8.62818 10.9257L8.63857 8.40117H13.2674V10.3937Z" fill="white"/><path d="M11.413 0.5C10.5485 0.5 9.71933 0.843391 9.10792 1.45465C8.49652 2.06591 8.15293 2.89498 8.15271 3.75954V5.71446H13.7546V0.5H11.413Z" fill="white"/></svg> Paste';

    const loadingButton = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"><style>.spinner_ajPY{transform-origin:center;animation:spinner_AtaB .75s infinite linear}@keyframes spinner_AtaB{100%{transform:rotate(360deg)}}</style><path fill="#FFFFFF" d="M12,1A11,11,0,1,0,23,12,11,11,0,0,0,12,1Zm0,19a8,8,0,1,1,8-8A8,8,0,0,1,12,20Z" opacity=".25"/><path fill="#FFFFFF" d="M10.14,1.16a11,11,0,0,0-9,8.92A1.59,1.59,0,0,0,2.46,12,1.52,1.52,0,0,0,4.11,10.7a8,8,0,0,1,6.66-6.61A1.42,1.42,0,0,0,12,2.69h0A1.57,1.57,0,0,0,10.14,1.16Z" class="spinner_ajPY"/></svg> Uploading...';

    // Create modal HTML
    function createModal() {
        const modalOverlay = document.createElement('div');
        modalOverlay.className = 'uich-clipboard-modal-overlay';
        modalOverlay.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 999999;
        `;

        const modalContent = document.createElement('div');
        modalContent.className = 'uich-clipboard-paste-data';
        modalContent.style.cssText = `
            background: white;
            border-radius: 8px;
            padding: 30px;
            max-width: 400px;
            width: 90%;
            position: relative;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        `;

        modalContent.innerHTML = `
            <button class="uich-modal-close" type="button" style="position: absolute; top: 15px; right: 15px; background: none; border: none; cursor: pointer; padding: 5px; display: flex; align-items: center; justify-content: center; transition: opacity 0.2s; z-index: 10;" title="Close">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" style="pointer-events: none;">
                    <path d="M15 5L5 15M5 5L15 15" stroke="#666" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
            <div class="uich-clip-pop-content" style="text-align: center;">
                <svg width="84" height="85" viewBox="0 0 84 85" fill="none" xmlns="http://www.w3.org/2000/svg" style="margin-bottom: 20px;">
                    <g clipPath="url(#clip0_4829_3031)">
                    <path d="M36.75 41.625C36.75 35.353 41.853 30.25 48.125 30.25H59.5V20.625C59.5 15.3155 55.1845 11 49.875 11H9.625C4.3155 11 0 15.3155 0 20.625V64.375C0 69.6845 4.3155 74 9.625 74H36.75V41.625Z" fill="#1E1E1E"/>
                    <path d="M37.6641 7.88199L37.9528 8.9H39.011H42.875C43.5508 8.9 44.1 9.4492 44.1 10.125V17.125C44.1 19.7293 41.9793 21.85 39.375 21.85H20.125C17.5207 21.85 15.4 19.7293 15.4 17.125V10.125C15.4 9.4492 15.9492 8.9 16.625 8.9H20.489H21.5472L21.8359 7.88199C22.8143 4.43198 25.9974 1.9 29.75 1.9C33.5026 1.9 36.6857 4.43198 37.6641 7.88199Z" fill="#1E1E1E" stroke="white" strokeWidth="2.8"/>
                    <path d="M77.875 35.5H48.125C44.744 35.5 42 38.244 42 41.625V78.375C42 81.756 44.744 84.5 48.125 84.5H77.875C81.256 84.5 84 81.756 84 78.375V41.625C84 38.244 81.256 35.5 77.875 35.5Z" fill="#A5A5A5"/>
                    <path d="M70.4375 67H54.6875C53.2385 67 52.0625 65.824 52.0625 64.375C52.0625 62.926 53.2385 61.75 54.6875 61.75H70.4375C71.8865 61.75 73.0625 62.926 73.0625 64.375C73.0625 65.824 71.8865 67 70.4375 67Z" fill="white"/>
                    <path d="M70.4375 56.5H54.6875C53.2385 56.5 52.0625 55.324 52.0625 53.875C52.0625 52.426 53.2385 51.25 54.6875 51.25H70.4375C71.8865 51.25 73.0625 52.426 73.0625 53.875C73.0625 55.324 71.8865 56.5 70.4375 56.5Z" fill="white"/>
                    </g>
                    <defs>
                    <clipPath id="clip0_4829_3031">
                    <rect width="84" height="84" fill="white" transform="translate(0 0.5)"/>
                    </clipPath>
                    </defs>
                </svg>
                <div class="uich-clip-os-wrap" style="display: flex; align-items: center; justify-content: center; gap: 15px; margin-bottom: 20px; font-size: 14px;">
                    <div class="uich-os-tag" style="display: flex; align-items: center; gap: 22px; width: max-content;">
                        <label style="font-weight: 600;">For Mac:</label>
                        <span class="os-icon" style="display: flex; align-items: center; gap: 5px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="none" viewBox="0 0 32 32"><rect width="32" height="32" fill="#1C1C1C" rx="5"/><path fill="#fff" d="M20.8 17.6h-1.6v-3.2h1.6c1.765 0 3.2-1.436 3.2-3.2C24 9.436 22.565 8 20.8 8a3.203 3.203 0 0 0-3.2 3.2v1.6h-3.2v-1.6c0-1.764-1.435-3.2-3.2-3.2A3.203 3.203 0 0 0 8 11.2c0 1.764 1.435 3.2 3.2 3.2h1.6v3.2h-1.6A3.203 3.203 0 0 0 8 20.8c0 1.764 1.435 3.2 3.2 3.2 1.765 0 3.2-1.436 3.2-3.2v-1.6h3.2v1.6c0 1.764 1.435 3.2 3.2 3.2 1.765 0 3.2-1.436 3.2-3.2 0-1.765-1.435-3.2-3.2-3.2Zm-1.6-6.4c0-.882.718-1.6 1.6-1.6.882 0 1.6.718 1.6 1.6 0 .882-.718 1.6-1.6 1.6h-1.6v-1.6Zm-6.4 9.6c0 .882-.718 1.6-1.6 1.6-.882 0-1.6-.718-1.6-1.6 0-.882.718-1.6 1.6-1.6h1.6v1.6Zm0-8h-1.6c-.882 0-1.6-.718-1.6-1.6 0-.882.718-1.6 1.6-1.6.882 0 1.6.718 1.6 1.6v1.6Zm4.8 4.8h-3.2v-3.2h3.2v3.2Zm3.2 4.8c-.882 0-1.6-.718-1.6-1.6v-1.6h1.6c.882 0 1.6.718 1.6 1.6 0 .882-.718 1.6-1.6 1.6Z"/></svg>
                            + V
                        </span>
                    </div>
                    <div class="uich-clip-separator" style="color: #ccc;">|</div>
                    <div class="uich-os-tag" style="display: flex; align-items: center; gap: 8px; width: max-content;">
                        <label style="font-weight: 600;">For Windows:</label>
                        <span class="os-icon" style="display: flex; align-items: center; gap: 5px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="none" viewBox="0 0 32 32"><rect width="32" height="32" fill="#1C1C1C" rx="5"/><path fill="#fff" d="M8 10.5v5h7V9.625L8 10.5ZM16 9.5v6h8v-7l-8 1ZM16 16.5v6l8 1v-7h-8ZM8 16.5v5l7 .875V16.5H8Z"/></svg>
                            + V
                        </span>
                    </div>
                </div>
                <div class='uich-head-clip-pop' style="margin-bottom: 15px; font-size: 16px; font-weight: 600; color: #333;">To paste with images from UiChemy</div>
                <input type="text" id="uich-paste-area-input" autocomplete="off" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; outline: none;" />
            </div>
        `;

        modalOverlay.appendChild(modalContent);
        
        // Add close button functionality with proper event handling
        setTimeout(function() {
            const closeBtn = modalContent.querySelector('.uich-modal-close');
            if (closeBtn) {
                closeBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    closeModal();
                });
                closeBtn.addEventListener('mouseenter', function() {
                    this.style.opacity = '0.7';
                });
                closeBtn.addEventListener('mouseleave', function() {
                    this.style.opacity = '1';
                });
            }
        }, 0);
        
        return modalOverlay;
    }

    function closeModal() {
        const modal = document.querySelector('.uich-clipboard-modal-overlay');
        if (modal) {
            modal.remove();
        }
        const clipboardBtn = document.querySelector("#uich-paste-clipboard");
        if (clipboardBtn) {
            clipboardBtn.innerHTML = copyButton;
        }
    }

    function openModal() {
        if (document.querySelector('.uich-clipboard-modal-overlay')) {
            return;
        }

        const modal = createModal();
        document.body.appendChild(modal);

        const inputArea = modal.querySelector("#uich-paste-area-input");
        inputArea.focus();

        // Close on overlay click
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeModal();
            }
        });

        // Handle paste event
        inputArea.addEventListener("paste", async function(event) {
            event.preventDefault();
            closeModal();
            
            const pastedData = event.clipboardData.getData("text");

            if (pastedData) {
                const pattern = /<!--\s?\/?wp:[a-z-\/]+\s?.*?-->/g;
                const result = pattern.test(pastedData);
                
                if (result) {
                    const block = wp.blocks.parse(pastedData);
                    const blockStringify = JSON.stringify(block);
                    const isTPAG = block[0].name.startsWith("tpgb/");
                    const checkMedia = /\.(jpg|png|jpeg|gif|svg|avif|webp)/gi.test(blockStringify);
                    
                    const clipboardBtn = document.querySelector("#uich-paste-clipboard");
                    if (clipboardBtn) {
                        clipboardBtn.innerHTML = loadingButton;
                    }

                    if (checkMedia) {
                        jQuery.ajax({
                            url: isTPAG ? tpgb_blocks_load.ajax_url : uichemy_ajax_object.ajax_url,
                            method: "POST",
                            data: {
                                nonce: isTPAG ? tpgb_admin.tpgb_nonce : uichemy_ajax_object.nonce,
                                action: isTPAG ? "tpgb_cross_cp_import" : "uichemy_import_images",
                                content: blockStringify
                            }
                        }).done(function(e) {
                            if (e.success) {
                                if (clipboardBtn) {
                                    clipboardBtn.innerHTML = copyButton;
                                }
                                const data = e.data[0];
                                wp.data.dispatch('core/block-editor').insertBlocks(data);
                            }
                        });
                    } else {
                        if (clipboardBtn) {
                            clipboardBtn.innerHTML = copyButton;
                        }
                        wp.data.dispatch("core/block-editor").insertBlocks(block);
                    }
                }
            }
        });

        // Close on Escape key
        document.addEventListener('keydown', function escapeHandler(e) {
            if (e.key === 'Escape') {
                closeModal();
                document.removeEventListener('keydown', escapeHandler);
            }
        });
    }

    // Subscribe to WordPress data changes
    wp.data.subscribe(function() {
        const toolbar = document.querySelector(".edit-post-header-toolbar .editor-document-tools__left");
        
        if (toolbar) {
            setTimeout(function() {
                if (!toolbar.querySelector("#uich-paste-clipboard")) {
                    const wrapper = document.createElement("div");
                    wrapper.classList.add("uich-paste-clipboard-wrap");
                    
                    const button = document.createElement("button");
                    button.id = "uich-paste-clipboard";
                    button.title = "Paste";
                    button.innerHTML = copyButton;
                    button.style.cssText = `
                        background: #4B22CC;
                        border: none;
                        border-radius: 4px;
                        padding: 8px 12px;
                        cursor: pointer;
                        display: flex;
                        align-items: center;
                        gap: 5px;
                        transition: background 0.2s;
                    `;
                    
                    button.addEventListener('mouseenter', function() {
                        button.style.background = '#2e0d98';
                    });
                    
                    button.addEventListener('mouseleave', function() {
                        button.style.background = '#4B22CC';
                    });
                    
                    button.addEventListener("click", openModal);
                    
                    wrapper.appendChild(button);
                    toolbar.appendChild(wrapper);
                }
            }, 1);
        }
    });

})(window, wp);