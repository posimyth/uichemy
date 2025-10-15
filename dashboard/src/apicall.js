// Fetch Data From AJAX
export const uichdash_fetch_api = async (args) => {
    if (args) {
        try {
            const response = await fetch(uich_ajax_object.ajax_url, {
                method: 'POST',
                body: args
            });

            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }

            const data = await response.json();
            return data;
        } catch (error) {
            return error.message;
        }
    } else {
        return false;
    }
}

export const appendFormData = (form, data) => {
    for (const key in data) {
        if (data.hasOwnProperty(key)) {
            form.append(key, data[key]);
        }
    }
};

export const nexterBlockInstall = async (e,setInstalled, pluginName) => {
    let ajxData = new FormData(),
        formData = {};

    Object.assign(formData, {
        action: 'uich_uichemy',
        nonce: uich_ajax_object_data.nonce,
        type: 'install_tpgb',
        pluginName: pluginName,
    });
    appendFormData(ajxData, formData);

    pluginName === 'nexterBlock' ? e.target.innerHTML = 'Activating' : e.target.innerHTML = 'Installing';

    try {
        const response = await uichdash_fetch_api(ajxData);
        const data = await response;

        if (data) {

            // setTimeout(() => {
            //     e.target.innerHTML = 'Activated';
            // }, 2000); 
            
            window.location.reload();
            
            if (typeof setInstalled === 'function') {
                setInstalled(true);
                uich_ajax_object.dashData.nexterBlock= data.Sucees
            }
        }
    } catch (error) {
        console.error('Error in Cache :', error);
    }
}

export const plusAddonsInstall = async (e,setInstalled, pluginName) => {
    let ajxData = new FormData(),
        formData = {};

    Object.assign(formData, {
        action: 'uich_install_wdesign',
        slug : 'the-plus-addons-for-elementor-page-builder',
        security: uich_ajax_object.nonce,
        pluginName: pluginName,
    });
    appendFormData(ajxData, formData);

    pluginName === 'plusAddons' ? e.target.innerHTML = 'Activating' : e.target.innerHTML = 'Installing';
    try {
        const response = await uichdash_fetch_api(ajxData);
        const data = await response;

        if (data) {
            
            window.location.reload();            
            if (typeof setInstalled === 'function') {
                setInstalled(true);
                uich_ajax_object.dashData.plusAddons= data.Sucees
            }
        }
    } catch (error) {
        console.error('Error in Cache :', error);
    }
}

export const elementorProInstall = async (e,setInstalled, pluginName) => {
    let ajxData = new FormData(),
        formData = {};

    Object.assign(formData, {
        action: 'uich_activate_elementor_pro_plugin',
        slug : 'elementor-pro',
        security: uich_ajax_object.nonce,
        pluginName: pluginName,
    });
    appendFormData(ajxData, formData);

    pluginName === 'elementorPro' ? e.target.innerHTML = 'Activating' : ' ';

    try {
        const response = await uichdash_fetch_api(ajxData);
        const data = await response;

        if (data) {
            window.location.reload();            
             if (typeof setInstalled === 'function') {
                setInstalled(true);
                uich_ajax_object.dashData.elementorPro= data.data
            }
        }
    } catch (error) {
        console.error('Error in Cache :', error);
    }
}

export const elementorInstall = async (e,setInstalled, pluginName) => {
    let ajxData = new FormData(),
        formData = {};

    Object.assign(formData, {
        action: 'uich_uichemy',
        nonce: uich_ajax_object_data.nonce,
        type: 'install_elementor',
        pluginName: pluginName,
    });
    appendFormData(ajxData, formData);

    pluginName === 'elementor' ? e.target.innerHTML = 'Activating' : e.target.innerHTML = 'Installing';
    try {
        const response = await uichdash_fetch_api(ajxData);
        const data = await response;

        if (data) {

            // setTimeout(() => {
            //     e.target.innerHTML = 'Activated';
            // }, 2000);  

            console.log("data : "  ,data);

            window.location.reload();
            
             if (typeof setInstalled === 'function') {
                setInstalled(true);
                uich_ajax_object.dashData.elementor= data.Sucees
            }
        }
    } catch (error) {
        console.error('Error in Cache :', error);
    }
}

export const flexboxContainer = async (e,setInstalled) => {
    let ajxData = new FormData(),
        formData = {};

    Object.assign(formData, {
        action: 'uich_uichemy',
        nonce: uich_ajax_object_data.nonce,
        type: 'flexbox_container',
    });
    appendFormData(ajxData, formData);

    e.target.innerHTML = 'Activating';

    try {
        const response = await uichdash_fetch_api(ajxData);
        const data = await response;

        if (data) {

            window.location.reload();
            
             if (typeof setInstalled === 'function') {
                setInstalled(true);
                uich_ajax_object.dashData.flexboxCon= data.Sucees
            }
        }
    } catch (error) {
        console.error('Error in Cache :', error);
    }
}

export const elementorFileUploads = async (e,setInstalled) => {
    let ajxData = new FormData(),
        formData = {};

    Object.assign(formData, {
        action: 'uich_uichemy',
        nonce: uich_ajax_object_data.nonce,
        type: 'elementor_file_uploads',
    });
    appendFormData(ajxData, formData);
    
    e.target.innerHTML = 'Activating';

    try {
        const response = await uichdash_fetch_api(ajxData);
        const data = await response;

        if (data) {

            // setTimeout(() => {
            //     e.target.innerHTML = 'Activated';
            // }, 2000);  
            window.location.reload();
            
             if (typeof setInstalled === 'function') {
                setInstalled(true);
                uich_ajax_object.dashData.eleFileLoad= data.Sucees
            }
        }
    } catch (error) {
        console.error('Error in Cache :', error);
    }
}

export const bricksSvgUploads = async (e,setInstalled) => {
    let ajxData = new FormData(),
        formData = {};

    Object.assign(formData, {
        action: 'uich_uichemy',
        nonce: uich_ajax_object_data.nonce,
        type: 'bricks_file_uploads',
    });
    appendFormData(ajxData, formData);

    e.target.innerHTML = 'Activating';

    try {
        const response = await uichdash_fetch_api(ajxData);
        const data = await response;

        if (data) {

            // setTimeout(() => {
            //     e.target.innerHTML = 'Activated';
            // }, 2000);  
            window.location.reload();

            console.log("data :", data);
            
             if (typeof setInstalled === 'function') {
                setInstalled(true);
                uich_ajax_object.dashData.bricksSvgLoad= data.Sucees
            }
        }
    } catch (error) {
        console.error('Error in Cache :', error);
    }
}

// export const gutenbergInstall = async (e,setInstalled) => {
//     let ajxData = new FormData(),
//         formData = {};

//     Object.assign(formData, {
//         action: 'uich_install_wdesign',
//         slug : 'gutenberg',
//         security: uich_ajax_object.nonce,
//     });
//     appendFormData(ajxData, formData);

//     e.target.innerHTML = 'Installing Gutenberg';
//     try {
//         const response = await uichdash_fetch_api(ajxData);
//         const data = await response;

//         if (data) {
            
//             window.location.reload();            
//             if (typeof setInstalled === 'function') {
//                 setInstalled(true);
//                 uich_ajax_object.dashData.gutenberg= data.Sucees
//             }
//         }
//     } catch (error) {
//         console.error('Error in Cache :', error);
//     }
// }

export const bricksActive = async (e,setInstalled) => {
    let ajxData = new FormData(),
        formData = {};

    Object.assign(formData, {
        action: 'uich_install_wdesign',
        slug : 'bricks',
        security: uich_ajax_object.nonce,
    });
    appendFormData(ajxData, formData);

    e.target.innerHTML = 'Activating';

    try {
        const response = await uichdash_fetch_api(ajxData);
        const data = await response;

        if (data) {
            
            window.location.reload();            
            if (typeof setInstalled === 'function') {
                setInstalled(true);
                uich_ajax_object.dashData.activeTheme= data.Sucees
            }
        }
    } catch (error) {
        console.error('Error in Cache :', error);
    }
}


export const addcusOption = async (e, setInstalled, key ) => {
    let ajxData = new FormData(),
        formData = {};

    Object.assign(formData, {
        action: 'uich_uichemy',
        nonce: uich_ajax_object_data.nonce,
        type: 'add_custom_option',
        key: key
    });

    appendFormData(ajxData, formData);

    // Update button text during activation/deactivation
    if (e.target.innerHTML === 'Deactivate') {
        e.target.innerHTML = 'Deactivating';
    } else {
        e.target.innerHTML = 'Activating';
    }

    try {
        const response = await uichdash_fetch_api(ajxData);
        const data = await response;

        if (data.success) {
            window.location.reload();
            if (typeof setInstalled === 'function') {
                setInstalled(true);
                uich_ajax_object.dashData[key] = data.success; // Updated dynamically
            }
        }
    } catch (error) {
        console.error('Error in Cache:', error);
    }
};