// Create modal overlay
document.addEventListener('DOMContentLoaded', () => {
    const link = document.createElement("link");
    link.href = "https://fonts.googleapis.com/css?family=Plus+Jakarta+Sans";
    link.rel = "stylesheet";
    document.head.appendChild(link);
    const toolbar = document.querySelector('.group-wrapper.breakpoints');

    if(toolbar === null) return;

    const modalOverlay = document.createElement("div");
    modalOverlay.style.display = "none";
    modalOverlay.style.position = "fixed";
    modalOverlay.style.top = "0";
    modalOverlay.style.left = "0";
    modalOverlay.style.width = "100%";
    modalOverlay.style.gap = "10px";
    modalOverlay.style.height = "100%";
    modalOverlay.style.backgroundColor = "rgba(0, 0, 0, 0.5)";
    modalOverlay.style.zIndex = "999";
    document.body.appendChild(modalOverlay);

    // Create modal container
    const modal = document.createElement("div");
    modal.style.display = "none";
    modal.style.flexWrap = "wrap";
    modal.style.position = "fixed";
    modal.style.top = "50%";
    modal.style.left = "50%";
    modal.style.transform = "translate(-50%, -50%)";
    modal.style.width = "470px";
    modal.style.padding = "20px";
    modal.style.gap = "10px";
    modal.style.backgroundColor = "#fff";
    modal.style.boxShadow = "0 2px 2px rgba(0, 0, 0, 0.2)";
    modal.style.borderRadius = "5px";
    modal.style.justifyContent = "center";
    modal.style.zIndex = "1000";
    document.body.appendChild(modal);

    const closeIcon = document.createElement("div");
    const svgString = `
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M5.63623 5.63672L18.3642 18.3646" stroke="#1A1A1A" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        <path d="M5.63623 18.3633L18.3642 5.63536" stroke="#1A1A1A" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        `;
    closeIcon.innerHTML = svgString;
    closeIcon.style.position = 'absolute';
    closeIcon.style.top = '10px';
    closeIcon.style.right = '10px';
    closeIcon.style.cursor = "pointer";

    modal.appendChild(closeIcon);

    let currentAjaxCall = null;
    const timeoutIDs = [];

    const closeModal = () => {
        modal.style.display = "none";
        modalOverlay.style.display = "none";
        inputField.innerText = "";
        submitBtn.innerText = "Upload Images to WordPress";

        if (currentAjaxCall) currentAjaxCall.abort();

        // Clear all timeouts if they exist
        timeoutIDs.forEach(id => clearTimeout(id));
    };

    closeIcon.addEventListener("click", closeModal)

    const icon = document.createElement("div");
    icon.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" viewBox="0 0 60 60"><path d="M57.6682 27.162V44.1891C57.8349 45.6269 57.6742 47.0838 57.1983 48.4507C56.7225 49.8177 55.9436 51.0594 54.9202 52.0829C53.8967 53.1064 52.655 53.8852 51.288 54.3611C49.921 54.837 48.4641 54.9976 47.0263 54.8309H12.9722C11.5344 54.9976 10.0775 54.837 8.71056 54.3611C7.34358 53.8852 6.1019 53.1064 5.07841 52.0829C4.05491 51.0594 3.27609 49.8177 2.80021 48.4507C2.32433 47.0838 2.1637 45.6269 2.33035 44.1891V15.8107C2.1637 14.3729 2.32433 12.916 2.80021 11.549C3.27609 10.182 4.05491 8.94033 5.07841 7.91683C6.1019 6.89333 7.34358 6.11451 8.71056 5.63863C10.0775 5.16275 11.5344 5.00213 12.9722 5.16878H35.675C36.2394 5.16878 36.7808 5.39302 37.1799 5.79217C37.5791 6.19131 37.8033 6.73268 37.8033 7.29716C37.8033 7.86164 37.5791 8.403 37.1799 8.80215C36.7808 9.2013 36.2394 9.42554 35.675 9.42554H12.9722C8.49698 9.42554 6.58711 11.3354 6.58711 15.8107V42.0607L13.7952 34.8526C14.331 34.3209 15.0553 34.0225 15.8101 34.0225C16.5649 34.0225 17.2891 34.3209 17.825 34.8526L20.4925 37.5201C20.7578 37.7801 21.1144 37.9257 21.4858 37.9257C21.8572 37.9257 22.2138 37.7801 22.479 37.5201L36.4979 23.5012C37.0337 22.9695 37.758 22.6712 38.5128 22.6712C39.2676 22.6712 39.9919 22.9695 40.5277 23.5012L53.4114 36.385V27.162C53.4114 26.5975 53.6357 26.0562 54.0348 25.657C54.434 25.2579 54.9753 25.0336 55.5398 25.0336C56.1043 25.0336 56.6457 25.2579 57.0448 25.657C57.444 26.0562 57.6682 26.5975 57.6682 27.162ZM18.6281 17.939C17.6859 17.9417 16.7832 18.3182 16.1184 18.9858C15.4536 19.6534 15.0809 20.5576 15.0822 21.4998C15.0835 22.442 15.4587 23.3452 16.1254 24.0109C16.7921 24.6767 17.6958 25.0507 18.638 25.0507C19.5802 25.0507 20.4839 24.6767 21.1506 24.0109C21.8173 23.3452 22.1925 22.442 22.1938 21.4998C22.1951 20.5576 21.8224 19.6534 21.1576 18.9858C20.4928 18.3182 19.5901 17.9417 18.6479 17.939H18.6281ZM48.5304 10.2201L49.1547 9.59864V15.8107C49.1547 16.3752 49.3789 16.9165 49.7781 17.3157C50.1772 17.7148 50.7186 17.939 51.2831 17.939C51.8475 17.939 52.3889 17.7148 52.7881 17.3157C53.1872 16.9165 53.4114 16.3752 53.4114 15.8107V9.59864L54.0358 10.2201C54.4392 10.5961 54.9729 10.8008 55.5243 10.791C56.0757 10.7813 56.6018 10.5579 56.9917 10.168C57.3817 9.77802 57.605 9.25193 57.6148 8.70053C57.6245 8.14914 57.4198 7.61549 57.0439 7.21202L52.7871 2.95527C52.3877 2.55738 51.8469 2.33398 51.2831 2.33398C50.7193 2.33398 50.1784 2.55738 49.779 2.95527L45.5223 7.21202C45.1463 7.61549 44.9416 8.14914 44.9514 8.70053C44.9611 9.25193 45.1845 9.77802 45.5744 10.168C45.9644 10.5579 46.4905 10.7813 47.0419 10.791C47.5932 10.8008 48.1269 10.5961 48.5304 10.2201Z" fill="#1E1E1E"/></svg>`;
    icon.style.textAlign = "center";
    icon.style.marginBottom = "10px";
    icon.style.opacity = "1";
    modal.appendChild(icon);

    // Create input field
    const inputField = document.createElement("textarea");
    inputField.type = "text";
    inputField.placeholder = "Paste Content Here & Click the button below";
    const style = document.createElement('style');
    style.innerHTML = `
        textarea::placeholder {
            color: black;
            opacity: 0.5;
        }
    `;
    // Append the style block to the document head
    document.head.appendChild(style);
    inputField.style.width = "100%";
    inputField.style.height = "104px";
    inputField.style.borderRadius = "5px";
    inputField.style.padding = "10px";
    inputField.style.textAlign = "start";
    inputField.style.fontFamily = "Plus Jakarta Sans"
    inputField.style.border = '0.5px solid #7A7A7A';
    inputField.style.marginBottom = "10px"; // Add margin for spacing
    inputField.style.resize = "none";
    modal.appendChild(inputField);

    // Styles for the button
    const submitButtonStyles = `
        background-color: #4B22CC;
        color: white;
        border: none;
        padding: 10px 20px;
        gap: 4px;
        font-size: 16px;
        border-radius: 5px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background-color 0.3s;
        margin: 5px;
        width: 100%;
        font-family: 'Plus Jakarta Sans';
        font-weight: 'normal ;
    `;

    // Create submit button
    const submitBtn = document.createElement("button");
    submitBtn.innerText = "Upload Images to WordPress";
    submitBtn.style.cssText = submitButtonStyles;
    modal.appendChild(submitBtn);

    const copyButtonStyles = `
        background-color: #28a745;
        color: white;
        border: none;
        padding: 10px 20px;
        font-size: 16px;
        font-family: Plus Jakarta Sans;
        border-radius: 5px;
        cursor: pointer;
        align-items: center;
        justify-content: center;
        transition: background-color 0.3s;
    `;
    // Create Copy Now button
    const copyNowBtn = document.createElement("button");
    copyNowBtn.innerText = "Copy Now";
    copyNowBtn.style.cssText = copyButtonStyles;
    copyNowBtn.style.display = "none"; // Initially hidden
    modal.appendChild(copyNowBtn);

    const buttonStyles = `
        background-color: #E3ED5D;
        color: white;
        border: none;
        padding: 5px 5px;
        font-size: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 5px;
        cursor: pointer;
        font-family: 'Plus Jakarta Sans';
        font-weight: 600;
        gap: 8px; /* Space between icon and text */
        margin: 5px;
        pointerEvents: 'none';
        color: black;
    `;
    // Create button to open modal
    const openModalBtn = document.createElement("button");
    openModalBtn.style.cssText = buttonStyles;

    const logoImg = document.createElement("img");
    logoImg.setAttribute("src", "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABMAAAASCAYAAAC5DOVpAAAACXBIWXMAABCcAAAQnAEmzTo0AAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAFCSURBVHgBxVRLToRAEO1uJgRISFjyWcgR5ga4dCc3cG4w3EA9geMN5ih4Atm6sk1I2GIghJAQfG3QtNjMQFz4kkrTVa8f1UUV1HXdkDH2SkYMw8Bhx7ZtH0uAKOB53h5LQikNJfeRTYmCAPE70zSfHUAh9ADOYSL0iV9isqhlWXvZh1tE8CdzZ2bFRvw4qGlafIp8TszBtS6+Nqil8xexVdiQdeCoWaoKwJ/RaWtMgauFRVG8kQX4v2v6vn+NZauK9X2fra1ZrOv6vSrQdd3tWjHCAZUfWS+qWUIWYmMYRokUZwlifNC4ovMzfFllvURHYOEMWZcgpeQExJzCYtjcBERoryc2kg9kAVQvFVnh/C7P81QTjqqqXmzbFo+XZ/Su6rp+l4SiMZGd8FOZGQTBjQjAtoqh5rBU2ot41jTN90/0AyMtajDXKkJTAAAAAElFTkSuQmCC")

    logoImg.style.width = "15px";
    logoImg.style.height = "15px";
    openModalBtn.appendChild(logoImg);

    // Add the text to the button
    const text = document.createElement("span");
    text.innerText = "Upload Media & Paste";
    openModalBtn.appendChild(text);

    openModalBtn.addEventListener('mouseenter', () => {
        openModalBtn.style.backgroundColor = '#B2BA45';
    });

    openModalBtn.addEventListener('mouseleave', () => {
        openModalBtn.style.backgroundColor = '#E3ED5D';
    });

    const li = document.createElement("li")
    li.className = 'preview-dimension';
    li.setAttribute('data-balloon', 'Upload Media & Paste Using UiChemy')
    li.setAttribute('data-balloon-pos','bottom')
    li.appendChild(openModalBtn)
    
    toolbar.appendChild(li);
  
    // Open modal on button click
    openModalBtn.addEventListener("click", () => {
        modal.style.display = "flex";
        modalOverlay.style.display = "block";
        inputField.value = ""; 
    });

    // Close modal when overlay is clicked
    modalOverlay.addEventListener("click", closeModal)

    // Submit data with AJAX on button click
    submitBtn.addEventListener("click", () => {

        const inputData = inputField.value;

        if (inputData) {

            // Update button text to indicate the upload process
            submitBtn.innerText = "Uploading Media...";
            
            // AJAX request
            currentAjaxCall = jQuery.ajax({
                url: uich_ajax_object_data.ajax_url,
                method: "POST",
                data: {
                    nonce: uich_ajax_object_data.nonce,
                    action: "uich_bricks_import_media",
                    inputData: inputData
                },
            })
            .done(function(response) {
                if (response.success) {
                    const contentToCopy = response.data;

                    if (contentToCopy) {
                        // Format content as JSON for readability
                        const formattedContent = JSON.stringify(contentToCopy, null, 2);

                        inputField.innerText = formattedContent;

                        // Change button text to indicate copying is available
                        submitBtn.innerText = "Media Uploaded, Copying to Clipboard..";

                        // Add the text to clipboard data attribute
                        submitBtn.setAttribute("data-clipboard-text", formattedContent);

                        // Copy content to clipboard
                        navigator.clipboard.writeText(formattedContent)
                            .then(() => {
                                
                                const t1 = setTimeout(() => {
                                    submitBtn.innerText = "Copied Updated Content To Your Clipboard!";

                                    const t2 = setTimeout(() => {
                                        submitBtn.innerHTML = "You can paste it with <b> CTRL/CMD + V </b> now";

                                        const t3 = setTimeout(() => {
                                            submitBtn.innerText = "Upload Images to WordPress";
                                        }, 45000);

                                        timeoutIDs.push(t3);
                                    }, 4000);

                                    timeoutIDs.push(t2);

                                }, 2000);

                                timeoutIDs.push(t1);
                            })
                            .catch((err) => {
                                submitBtn.innerText = "Failed while copying to your Clipboard";
                            });
                    }
                } else {
                    submitBtn.innerText = "Failed to import media";
                }
            })
            .fail(function(jqXHR, textStatus, errorThrown) {
                    submitBtn.innerText = "Failed to import media";
            });
        }
    });
});