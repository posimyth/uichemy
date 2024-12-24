import ReactDOM from 'react-dom';
const { Modal } = wp.components;

( function( window, wp ){
	const copybuton = '<svg width="14" height="15" viewBox="0 0 14 15" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M8.15443 7.91705L8.14325 10.9265C8.14325 10.9265 5.60113 11.0735 5.60113 9.11541V4.12304C5.60071 3.162 5.21864 2.24047 4.53893 1.56107C3.85923 0.881661 2.93753 0.5 1.9765 0.5H3.17421e-07V10.4512C-0.000209837 10.9806 0.103934 11.5048 0.30647 11.9939C0.509006 12.483 0.805964 12.9274 1.18037 13.3016C1.55477 13.6759 1.99926 13.9727 2.48845 14.175C2.97763 14.3773 3.5019 14.4813 4.03128 14.4809C4.30471 14.5064 4.57992 14.5064 4.85335 14.4809H9.66757C10.7509 14.4809 11.7899 14.0505 12.556 13.2844C13.322 12.5184 13.7524 11.4794 13.7524 10.3961V7.91945L8.15443 7.91705ZM13.2674 10.3937C13.2655 11.3465 12.8862 12.2599 12.2124 12.9337C11.5386 13.6074 10.6252 13.9868 9.67235 13.9887H7.38669C7.62987 13.794 7.8426 13.564 8.01782 13.3065C8.55948 12.5076 8.62818 11.6136 8.62818 10.9257L8.63857 8.40117H13.2674V10.3937Z" fill="white"/><path d="M11.413 0.5C10.5485 0.5 9.71933 0.843391 9.10792 1.45465C8.49652 2.06591 8.15293 2.89498 8.15271 3.75954V5.71446H13.7546V0.5H11.413Z" fill="white"/></svg> Paste';

	const loadingbtn = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"  viewBox="0 0 24 24"><style>.spinner_ajPY{transform-origin:center;animation:spinner_AtaB .75s infinite linear}@keyframes spinner_AtaB{100%{transform:rotate(360deg)}}</style><path fill="#FFFFFF" d="M12,1A11,11,0,1,0,23,12,11,11,0,0,0,12,1Zm0,19a8,8,0,1,1,8-8A8,8,0,0,1,12,20Z" opacity=".25"/><path fill="#FFFFFF" d="M10.14,1.16a11,11,0,0,0-9,8.92A1.59,1.59,0,0,0,2.46,12,1.52,1.52,0,0,0,4.11,10.7a8,8,0,0,1,6.66-6.61A1.42,1.42,0,0,0,12,2.69h0A1.57,1.57,0,0,0,10.14,1.16Z" class="spinner_ajPY"/></svg> Uploading...';
    
    function uichemy_cross_cp() {
        if (!document.querySelector("#uich-paste-area-input")) {
            const modalContainer = document.querySelector("#uich-paste-clipboard");
            ReactDOM.render(renderModal(), modalContainer);
            modalContainer.innerHTML = copybuton;
            const inputArea = document.querySelector("#uich-paste-area-input");
            inputArea.focus();
            inputArea.addEventListener("paste", (async function(event) {
                event.preventDefault();
                closeModal()
                const pastedData = event.clipboardData.getData("text");

                if(pastedData){
                    const pattern = /<!--\s?\/?wp:tpgb\/[a-z-]+\s?.*?-->/g;
                    const result = pattern.test(pastedData);
                    if(result){
                        const block = wp.blocks.parse(pastedData);
                        const blockStringify = JSON.stringify(block);
                        const checkMedia = /\.(jpg|png|jpeg|gif|svg)/gi.test(blockStringify);
                        document.querySelector("#uich-paste-clipboard").innerHTML = loadingbtn
                        if (checkMedia) {
                            jQuery.ajax({
                            url: tpgb_blocks_load.ajax_url,
                            method: "POST",
                            data: {
                                nonce: tpgb_admin.tpgb_nonce,
                                action: "tpgb_cross_cp_import",
                                content: blockStringify
                            }
                            }).done((function(e) {
                                if (e.success) {
                                    document.querySelector("#uich-paste-clipboard").innerHTML = copybuton;
                                    const data = e.data[0];
                                    wp.data.dispatch('core/block-editor').insertBlocks(data);
                                }
                            })) 
                        } else {
                            document.querySelector("#uich-paste-clipboard").innerHTML = copybuton
                            wp.data.dispatch("core/block-editor").insertBlocks(block)
                        }
                    }
                } 
            }))
        }
    }
    wp.data.subscribe((function() {
        const e = document.querySelector(".edit-post-header-toolbar .editor-document-tools__left");
        e && setTimeout((() => {
            if (!e.querySelector("#uich-paste-clipboard")) {
                const t = document.createElement("div");
                t.classList.add("uich-paste-clipboard-wrap");
                const a = '<button id="uich-paste-clipboard" title="Paste">' + copybuton + "</button>";
                t.innerHTML = a, e.appendChild(t);
                let o = document.querySelector("#uich-paste-clipboard");
                o && o.addEventListener("click", uichemy_cross_cp)
            }
        }), 1)
    }));
   
    const renderModal = () => {
		return (
			<Modal className={"uich-clipboard-paste-data"} onRequestClose={closeModal}>
				<div className="uich-clip-pop-content">
                    <svg width="84" height="85" viewBox="0 0 84 85" fill="none" xmlns="http://www.w3.org/2000/svg">
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
					<div className="uich-clip-os-wrap">
						<div className="uich-os-tag">
							<label>{'For Mac :'}</label>
							<span className="os-icon"><svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="none" viewBox="0 0 32 32"><rect width="32" height="32" fill="#1C1C1C" rx="5"/><path fill="#fff" d="M20.8 17.6h-1.6v-3.2h1.6c1.765 0 3.2-1.436 3.2-3.2C24 9.436 22.565 8 20.8 8a3.203 3.203 0 0 0-3.2 3.2v1.6h-3.2v-1.6c0-1.764-1.435-3.2-3.2-3.2A3.203 3.203 0 0 0 8 11.2c0 1.764 1.435 3.2 3.2 3.2h1.6v3.2h-1.6A3.203 3.203 0 0 0 8 20.8c0 1.764 1.435 3.2 3.2 3.2 1.765 0 3.2-1.436 3.2-3.2v-1.6h3.2v1.6c0 1.764 1.435 3.2 3.2 3.2 1.765 0 3.2-1.436 3.2-3.2 0-1.765-1.435-3.2-3.2-3.2Zm-1.6-6.4c0-.882.718-1.6 1.6-1.6.882 0 1.6.718 1.6 1.6 0 .882-.718 1.6-1.6 1.6h-1.6v-1.6Zm-6.4 9.6c0 .882-.718 1.6-1.6 1.6-.882 0-1.6-.718-1.6-1.6 0-.882.718-1.6 1.6-1.6h1.6v1.6Zm0-8h-1.6c-.882 0-1.6-.718-1.6-1.6 0-.882.718-1.6 1.6-1.6.882 0 1.6.718 1.6 1.6v1.6Zm4.8 4.8h-3.2v-3.2h3.2v3.2Zm3.2 4.8c-.882 0-1.6-.718-1.6-1.6v-1.6h1.6c.882 0 1.6.718 1.6 1.6 0 .882-.718 1.6-1.6 1.6Z"/></svg>{' + V'}</span>
						</div>
						<div className="uich-clip-separator">|</div>
						<div className="uich-os-tag">
							<label>{'For Windows :'}</label>
							<span className="os-icon"><svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="none" viewBox="0 0 32 32"><rect width="32" height="32" fill="#1C1C1C" rx="5"/><path fill="#fff" d="M8 10.5v5h7V9.625L8 10.5ZM16 9.5v6h8v-7l-8 1ZM16 16.5v6l8 1v-7h-8ZM8 16.5v5l7 .875V16.5H8Z"/></svg>{' + V'}</span>
						</div>
					</div>
                    <div className='uich-head-clip-pop'>{'To paste with images from UiChemy'}</div>
					<input type="text" id={"uich-paste-area-input"} autoComplete={'off'} />
				</div>
			</Modal>
		);
	};

    function closeModal() {
		const modalContainer = document.querySelector("#uich-paste-clipboard");
		if(modalContainer){
			ReactDOM.unmountComponentAtNode(modalContainer);
			modalContainer.innerHTML = copybuton
		}
	}
} )( window, wp )