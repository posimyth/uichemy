import './onboarding.scss';
import '../../style/main.scss';
import React, { Fragment, useState, useEffect } from 'react';
import { __ } from "@wordpress/i18n";
import { nexterBlockInstall, plusAddonsInstall, elementorInstall, bricksActive, nexterThemeActive, elementorProInstall } from '../../apicall.js';

const Onboarding = ({ onComplete = () => { }, dataSave, isChecked, setIsChecked }) => {

    const [onBoardingStep, setOnBoardingStep] = useState(() => {
        const savedStep = localStorage.getItem('onUichBoardingStep');
        return savedStep ? parseInt(savedStep) : 1;
    });

    const [nextstep, setNextstep] = useState(() => {
        const savedNextStep = localStorage.getItem('onUichBoardingNextStep');
        return savedNextStep || 'showTab';
    });

    useEffect(() => {
        localStorage.setItem('onUichBoardingStep', onBoardingStep.toString());
        localStorage.setItem('onUichBoardingNextStep', nextstep);
    }, [onBoardingStep, nextstep]);

    const [loading, setLoading] = useState(false);
    const [isEnabled, setIsEnabled] = useState(uich_ajax_object.dashData)

    var plugin_url = uich_ajax_object.uich_url;
    var active = uich_ajax_object.dashData;


    useEffect(() => {
        const shouldTrigger = sessionStorage.getItem('triggerOnComplete');
        if (shouldTrigger === 'yes') {
            sessionStorage.removeItem('triggerOnComplete');
            onComplete();
        }
    }, [onComplete]);

    const nav_array = () => {
        if ('Gutenberg' === nextstep || 'Elementor' === nextstep || 'Bricks' === nextstep || 'showTab' === nextstep) {
            const steps = [
                { step_number: 1, step_name: __('Select Page Builder', 'uichemy') },
                { step_number: 2, step_name: __('Install Figma Plugin', 'uichemy') },
            ];

            if ('Bricks' === nextstep) {
                steps.push(
                    { step_number: 3, step_name: __('Required Theme', 'uichemy') },
                    { step_number: 4, step_name: __('Complete', 'uichemy') }
                )
            } else {
                steps.push(
                    { step_number: 3, step_name: __('Required Plugins', 'uichemy') },
                    { step_number: 4, step_name: __('Complete', 'uichemy') }
                )

            }

            return steps;
        }
    };

    const select_page_builder = () => {

        const handleSelect = (e) => {
            setOnBoardingStep(onBoardingStep + 1);
        };

        const handleSelection = (selection) => {
            localStorage.setItem('onUichBoardingNextStep', selection);
            setNextstep(selection);


        };

        const isEnabled = nextstep === 'Elementor' || nextstep === 'Gutenberg' || nextstep === 'Bricks';

        return (
            <div className="uich-board-start-crd-main uich-board-onbsec-cover">
                <img src={plugin_url + 'assets/images/uichemy01.svg'} alt="Logo" />
                <h2 className="uich_section_heading">{__('Select Page Builder', 'uichemy')}</h2>

                <div className="uich-board-strt-crd-cover uich-board-whitebg-cover">
                    <div className={`uich-board-strat-card ${'Elementor' === nextstep ? 'uich-board-active-board-card' : ''} `} onClick={() => { handleSelection('Elementor') }}>
                        <div className='uich_plugin_icon elementor'>
                            <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="none" viewBox="0 0 10 10"><path fill="#fff" d="M2 10H.003V.003H2v9.999Zm7.998 0H4V8.003h6V10Zm0-4H4V4.003h6V6Zm0-4H4V.003h6v1.999Z" /></svg>
                        </div>
                        <div className='uich-board-crd-h'>
                            <h3>{__('Elementor', 'uichemy')}</h3>
                        </div>
                    </div>

                    <div className={`uich-board-strat-card ${'Gutenberg' === nextstep ? 'uich-board-active-board-card' : ''}`} onClick={() => { handleSelection('Gutenberg') }}>
                        <div className='uich_setting_icon wp'>
                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="14" fill="none" viewBox="0 0 13 14"><g clip-path="url(#a)"><path fill="#fff" d="M3.822 2.006C1.994 2.8 1.293 4.504 1.43 7.778c.077 1.666.175 2.271.505 2.877.837 1.59 2.218 2.272 4.241 2.101 1.44-.094 2.47-.643 2.938-1.514.194-.36.35-1.23.408-2.158.078-1.495.097-1.552.603-1.666.72-.151 1.965-1.173 1.965-1.628 0-.53-.525-.454-1.284.19-.428.36-1.012.624-1.77.776-2.16.416-2.549.568-3.152 1.23-.661.738-.758 1.174-.291 1.363.194.076.486-.114.875-.568.545-.625 1.576-1.173 1.81-.946.057.075.116.568.116 1.135 0 2.007-.837 2.897-2.685 2.897-1.128 0-2.062-.53-2.645-1.515-.35-.568-.409-.984-.409-3.028 0-2.707.292-3.56 1.44-4.297 1.4-.909 3.579-.398 4.085.984.428 1.098.817 1.325 1.186.644.176-.303.137-.55-.194-1.212-.778-1.59-3.424-2.29-5.35-1.438Z" /></g><defs><clipPath id="a"><path fill="#fff" d="M.333.783h12.343V13.55H.333z" /></clipPath></defs></svg>
                        </div>
                        <div className='uich-board-crd-h'>
                            <h3>{__('Gutenberg', 'uichemy')}</h3>
                        </div>
                    </div>

                    <div className={`uich-board-strat-card ${'Bricks' === nextstep ? 'uich-board-active-board-card' : ''}`} onClick={() => { handleSelection('Bricks') }}>
                        <div className='uich_plugin_icon uichmy'>
                            <svg xmlns="http://www.w3.org/2000/svg" width="11" height="13" fill="none" viewBox="0 0 11 13"><path fill="#212121" d="m3.33.145.215.133v3.724c.756-.5 1.592-.75 2.509-.75 1.321 0 2.416.461 3.285 1.384.857.922 1.286 2.06 1.286 3.41 0 1.357-.431 2.494-1.295 3.411-.869.923-1.96 1.384-3.276 1.384-1.15 0-2.131-.41-2.947-1.232v1.009H.714V.439L3.33.145ZM5.598 5.67c-.63 0-1.157.215-1.58.643-.423.44-.634 1.018-.634 1.732 0 .715.211 1.289.634 1.724.417.434.943.651 1.58.651.673 0 1.218-.226 1.634-.678.41-.447.616-1.012.616-1.697 0-.684-.208-1.253-.625-1.705-.416-.447-.958-.67-1.625-.67Z" /></svg>
                        </div>
                        <div className='uich-board-crd-h'>
                            <h3>{__('Bricks Builder', 'uichemy')}</h3>
                        </div>
                    </div>
                </div>
                <div className="uich-footer-actions-button">
                    <button className={`uich-board-purple-common-btn ${isEnabled ? '' : 'uich-board-disable-element'}`} onClick={handleSelect} disabled={!isEnabled}>{__('Continue', 'uichemy')}</button>
                </div>
            </div>
        )
    }

    const Install_figma_plugin = () => {

        const plugin_data = [
            {
                name: "Elementor",
                link: "https://www.figma.com/community/plugin/1265873702834050352",
                image : plugin_url + 'assets/images/image-3.png'
            },
            {
                name: "Gutenberg",
                link: " https://www.figma.com/community/plugin/1379733208974981538",
                image : plugin_url + 'assets/images/togutenberg.jpg'
            },
            {
                name: "Bricks",
                link: "https://www.figma.com/community/plugin/1344313361212431142",
                image : plugin_url + 'assets/images/tobricks.jpg'
            },
        ];

        return (
            <>
                {plugin_data.map((data, index) => (
                    (nextstep === data.name) ?
                        <div className='uich-install-figma-plugin' key={index}>
                            <div className="uich-top-bar">
                                <h2>{__('Install UiChemy Figma Plugin', 'uichemy')}</h2>
                                <a href="https://www.youtube.com/embed/eqLDo2xB_SI?si=ge0bRx4f8u9r-Cua&autoplay=1?utm_source=wpbackend&utm_medium=dashboard&utm_campaign=uichemy " className="uich-get-key-link" target="_blank" rel="noopener noreferrer">
                                    <span className="uich-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 20 20"><path fill="#4B22CC" d="M10 1.875C5.52 1.875 1.875 5.52 1.875 10S5.52 18.125 10 18.125 18.125 14.48 18.125 10 14.48 1.875 10 1.875Zm2.92 8.488-4.47 2.701a.421.421 0 0 1-.637-.364V7.3a.421.421 0 0 1 .637-.364l4.47 2.7a.426.426 0 0 1 0 .727Z" /></svg>
                                    </span>
                                    <span>{__('Get UiChemy Key', 'uichemy')}</span>
                                </a>
                            </div>
                            <div className="uich-install-plugin-container">
                                <div className="uich-steps-box">
                                    <div className="uich-step">
                                        <img src={plugin_url + 'assets/images/image-1.png'} alt="Logo" />
                                        <p>{__('Install UiChemy Figma to', 'uichemy')} {data.name} {__('Convertor', 'uichemy')}</p>
                                        <a href={data.link + '?utm_source=wpbackend&utm_medium=dashboard&utm_campaign=uichemy'} className="uich-btn-link" target="_blank" rel="noopener noreferrer">
                                            <span>{__('Install Figma Plugin', 'uichemy')} </span>
                                            <span><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 16 16"><path stroke="#4B22CC" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2" d="M10 2h4m0 0v4m0-4L6.667 9.333M12 8.667v4A1.334 1.334 0 0 1 10.667 14H3.333A1.334 1.334 0 0 1 2 12.667V5.333A1.333 1.333 0 0 1 3.333 4h4" /></svg></span>
                                        </a>
                                    </div>

                                    <div className="uich-step">
                                        <img src={plugin_url + 'assets/images/image-2.png'} alt="Logo" />
                                        <p>{__('Install & Run', 'uichemy')}<br />{__('The Figma Plugin', 'uichemy')}</p>
                                    </div>

                                    <div className="uich-step">
                                        <img src={data.image} alt="Logo" />
                                        <p>{__('Connect your UiChemy', 'uichemy')}<br /> {__('Key & Click Connect', 'uichemy')}</p>
                                        <a href="https://uichemy.com/pricing/?utm_source=wpbackend&utm_medium=dashboard&utm_campaign=uichemy" className="uich-btn-link" target="_blank" rel="noopener noreferrer">
                                            <span>{__('Get UiChemy Key', 'uichemy')} </span>
                                            <span><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 16 16"><path stroke="#4B22CC" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2" d="M10 2h4m0 0v4m0-4L6.667 9.333M12 8.667v4A1.334 1.334 0 0 1 10.667 14H3.333A1.334 1.334 0 0 1 2 12.667V5.333A1.333 1.333 0 0 1 3.333 4h4" /></svg></span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div className="uich-footer-actions-button">
                                <button className="uich-back-btn" onClick={() => { setOnBoardingStep(onBoardingStep - 1) }}>{__('Back', 'uichemy')}</button>
                                <button className="uich-board-purple-common-btn" onClick={() => { setOnBoardingStep(onBoardingStep + 1) }}>{__('Confirm & Proceed', 'uichemy')}</button>
                            </div>
                        </div> : ''
                ))}
            </>
        )
    }

    const gutenberginstall = () => {
        var active = uich_ajax_object.dashData;

        return (
            <>

                {/* Gutenberg is not available in the new version of WordPress */}
                {/* <div className={`uich_plugin_item ${active.gutenberg === true ? 'uich_activated' : ''}`}>
                    <div className='uich_plugin_info'>
                        <div className='uich_setting_icon wp'>
                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="12" fill="none" viewBox="0 0 13 12"><path fill="#fff" d="M3.394.396C1.476 1.252.741 3.086.884 6.613c.082 1.794.184 2.446.53 3.099.878 1.712 2.327 2.446 4.45 2.263 1.51-.102 2.59-.694 3.08-1.631.205-.388.368-1.325.429-2.324.082-1.61.102-1.672.633-1.794.755-.163 2.06-1.264 2.06-1.753 0-.571-.55-.49-1.346.204-.45.387-1.061.672-1.857.835-2.265.449-2.673.612-3.306 1.325-.694.795-.796 1.264-.306 1.468.204.082.51-.122.918-.611.572-.673 1.653-1.264 1.898-1.02.061.082.122.612.122 1.223 0 2.161-.877 3.12-2.816 3.12-1.183 0-2.163-.571-2.775-1.631-.367-.612-.428-1.06-.428-3.262 0-2.915.306-3.832 1.51-4.627 1.469-.979 3.754-.429 4.285 1.06.449 1.182.857 1.427 1.245.693.183-.327.143-.592-.204-1.305C8.189.233 5.414-.522 3.394.395Z" /></svg>
                        </div>
                        <span className='uich_plugin_icon_text'>{__('Gutenberg', 'uichemy')}</span>
                        <span className='uich_alert_button'>{__('Important', 'uichemy')}</span>
                    </div>

                    {active.gutenberg === true ?
                        <div className='uich_activated_label'>
                            <span className='uich_check_green_icon'>
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="12" fill="none" viewBox="0 0 14 12"><path stroke="#00A31B" strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.5" d="M1.545 7.638s1.228 0 2.864 2.864c0 0 4.548-7.5 8.591-9" /></svg>
                            </span>
                            <span>{__('Enable', 'uichemy')}</span>
                        </div> :
                        <button className='uich_secondary_button'>{__('Disable', 'uichemy')}</button>
                    }
                </div> */}

                <div className={`uich_plugin_item ${active.nexterBlock === true ? 'uich_activated' : ''}`}>
                    <div className='uich_plugin_info'>
                        <div className='uich_setting_icon nexter_block'>
                            <svg xmlns="http://www.w3.org/2000/svg" width="4" height="20" fill="none" viewBox="0 0 4 20"><path fill="#fff" d="m3.6 17.227-.01 2.222H.4v-3.088h2.308c.02 0 .035.008.052.01.099.011.196.036.288.072.03.01.06.023.088.036.007.005.015.008.022.012a.72.72 0 0 1 .349.331l.01.018a.603.603 0 0 1 .052.158c.02.075.03.151.031.229ZM3.59.82v12.998c0 .203-.092.378-.283.529-.197.152-.441.231-.69.223H.4V.4h2.662a.598.598 0 0 1 .37.12.36.36 0 0 1 .158.3Z" /></svg>
                        </div>
                        <span className='uich_plugin_icon_text'>{__('Nexter Blocks', 'uichemy')}</span>
                        {/* <span className='uich_alert_button uich_alert_button_green'>{__('Optional', 'uichemy')}</span> */}
                        <span className='uich_alert_button'>{__('Required', 'uichemy')}</span>

                    </div>
                    {active.findPlugin.includes('Nexter Blocks') ? (
                        active.nexterBlock === true ?
                            <div className='uich_activated_label'>
                                <span className='uich_check_green_icon'>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="12" fill="none" viewBox="0 0 14 12"><path stroke="#00A31B" strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.5" d="M1.545 7.638s1.228 0 2.864 2.864c0 0 4.548-7.5 8.591-9" /></svg>
                                </span>
                                <span>{__('Activated', 'uichemy')}</span>
                            </div> :
                            <button className='uich_secondary_button' onClick={(e) => nexterBlockInstall(e, setIsEnabled, 'nexterBlock')}>{__('Activate', 'uichemy')}</button>) : (
                        <button className='uich_secondary_button' onClick={(e) => nexterBlockInstall(e, setIsEnabled)}>{__('Install & Activate', 'uichemy')}</button>)
                    }
                </div>

                <div className={`uich_plugin_item ${active.spectra === true ? 'uich_activated' : ''}`}>
                    <div className='uich_plugin_info'>
                        <div className='uich_setting_icon spectra'>
                            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="none" viewBox="0 0 40 40"><path fill="#5733FF" fill-rule="evenodd" d="M20 40c11.046 0 20-8.954 20-20S31.046 0 20 0 0 8.954 0 20s8.954 20 20 20Zm-6.03-26.565c-3.129 1.845-2.379 6.78 1.175 7.728l5.934 1.583c.494.132.584.81.141 1.072l-5.736 3.37-.606 5.955 11.152-6.578c3.129-1.845 2.378-6.78-1.175-7.728l-5.934-1.583c-.494-.132-.584-.81-.141-1.072l5.736-3.37.606-5.955-11.152 6.578Z" clip-rule="evenodd" /></svg>
                        </div>
                        <span className='uich_plugin_icon_text'>{__('Spectra', 'uichemy')}</span>
                        <span className='uich_alert_button uich_alert_button_soon'>{__('Coming Soon', 'uichemy')}</span>

                    </div>

                    {active.Spectra === true ?
                        <div className='uich_activated_label'>
                            <span className='uich_check_green_icon'>
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="12" fill="none" viewBox="0 0 14 12"><path stroke="#00A31B" strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.5" d="M1.545 7.638s1.228 0 2.864 2.864c0 0 4.548-7.5 8.591-9" /></svg>
                            </span>
                            <span>{__('Activated', 'uichemy')}</span>
                        </div> :
                        <button className='uich_secondary_button uich_coming_soon'>{__('Install & Activate', 'uichemy')}</button>
                    }
                </div>

                <div className={`uich_plugin_item ${active.kadence === true ? 'uich_activated' : ''}`}>
                    <div className='uich_plugin_info'>
                        <div className='uich_setting_icon'>
                            <img src={plugin_url + 'assets/images/kadence.png'} alt="nexter" />
                        </div>
                        <span className='uich_plugin_icon_text'>{__('Kadence Blocks', 'uichemy')}</span>
                        <span className='uich_alert_button uich_alert_button_soon'>{__('Coming Soon', 'uichemy')}</span>
                    </div>

                    {active.kadence === true ?
                        <div className='uich_activated_label'>
                            <span className='uich_check_green_icon'>
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="12" fill="none" viewBox="0 0 14 12"><path stroke="#00A31B" strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.5" d="M1.545 7.638s1.228 0 2.864 2.864c0 0 4.548-7.5 8.591-9" /></svg>
                            </span>
                            <span>{__('Activated', 'uichemy')}</span>
                        </div> :
                        <button className='uich_secondary_button uich_coming_soon'>{__('Install & Activate', 'uichemy')}</button>
                    }
                </div>

            </>
        )
    }

    const required_plugins = () => {


        const elementorinstall = () => {
            return (
                <>
                    <div className={`uich_plugin_item ${active.elementor === true ? 'uich_activated' : ''}`}>
                        <div className='uich_plugin_info'>
                            <div className='uich_plugin_icon elementor'>
                                <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="none" viewBox="0 0 10 10"><path fill="#fff" d="M2 10H.003V.003H2v9.999Zm7.998 0H4V8.003h6V10Zm0-4H4V4.003h6V6Zm0-4H4V.003h6v1.999Z" /></svg>
                            </div>
                            <span className='uich_plugin_icon_text'>{__('Elementor', 'uichemy')}</span>
                            <span className='uich_alert_button'>{__('Required', 'uichemy')}</span>
                        </div>
                        {active.findPlugin.includes('Elementor') ? (
                            active.elementor === true ?
                                <div className='uich_activated_label'>
                                    <span className='uich_check_green_icon'>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="12" fill="none" viewBox="0 0 14 12"><path stroke="#00A31B" strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.5" d="M1.545 7.638s1.228 0 2.864 2.864c0 0 4.548-7.5 8.591-9" /></svg>
                                    </span>
                                    <span>{__('Activated', 'uichemy')}</span>
                                </div> :
                                <button className='uich_secondary_button' onClick={(e) => elementorInstall(e, setIsEnabled, 'elementor')}>{__('Activate', 'uichemy')}</button>) : (
                            <button className='uich_secondary_button' onClick={(e) => elementorInstall(e, setIsEnabled)}>{__('Install & Activate', 'uichemy')}</button>)
                        }
                    </div>

                    <div className={`uich_plugin_item ${(active.elementorPro === true) ? 'uich_activated' : ''}`}>
                        <div className='uich_setting_info'>
                            <div className='uich_plugin_icon elementor'>
                                <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="none" viewBox="0 0 10 10"><path fill="#fff" d="M2 10H.003V.003H2v9.999Zm7.998 0H4V8.003h6V10Zm0-4H4V4.003h6V6Zm0-4H4V.003h6v1.999Z" /></svg>
                            </div>
                            <span className='uich_plugin_icon_text'>{__('Elementor Pro', 'uichemy')}</span>
                            <span className='uich_alert_button uich_alert_button_green'>{__('Optional', 'uichemy')}</span>
                        </div>
                        {active.findPlugin.includes('Elementor Pro') ? (
                            (active.elementorPro === true && active.elementor === true) ?
                                <div className='uich_activated_label'>
                                    <span className='uich_check_green_icon'>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="12" fill="none" viewBox="0 0 14 12"><path stroke="#00A31B" strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.5" d="M1.545 7.638s1.228 0 2.864 2.864c0 0 4.548-7.5 8.591-9" /></svg>
                                    </span>
                                    <span>{__('Activated', 'uichemy')}</span>
                                </div> :
                                <button className='uich_secondary_button' onClick={(e) => elementorProInstall(e, setIsEnabled, 'elementorPro')}>{__('Activate', 'uichemy')}</button>) : (
                            <a className='uich_secondary_button' href='https://elementor.com/?utm_source=wpbackend&utm_medium=dashboard&utm_campaign=uichemy' target="_blank" rel="noopener noreferrer">
                                <span>{__('Installation Required', 'uichemy')}</span>
                                <span><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 16 16"><path stroke="#4B22CC" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2" d="M10 2h4m0 0v4m0-4L6.667 9.333M12 8.667v4A1.334 1.334 0 0 1 10.667 14H3.333A1.334 1.334 0 0 1 2 12.667V5.333A1.333 1.333 0 0 1 3.333 4h4" /></svg></span>
                            </a>)
                        }
                    </div>

                    <div className={`uich_setting_item ${(active.plusAddons === true) ? 'uich_activated' : ''}`}>
                        <div className='uich_setting_info'>
                            <div className='uich_setting_icon elementor_plus'>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path fill="#fff" d="M12.579 9.787H11.42v1.637H9.775v1.166h1.646v1.637h1.158V12.59h1.636v-1.166H12.58V9.787Z" /><path fill="#fff" d="M11.999.002H7.613v11.42H4.068v1.167h3.545v1.637H8.77V1.168H12a3.224 3.224 0 0 1 3.219 3.21v2.243h1.166V4.38A4.401 4.401 0 0 0 14.16.58a4.324 4.324 0 0 0-2.161-.579Z" /><path fill="#fff" d="M12.579 4.08H11.42v3.536H9.775v1.166h13.058v3.22a3.23 3.23 0 0 1-3.201 3.228H17.38v1.166h2.252a4.402 4.402 0 0 0 4.367-4.395V7.616H12.58V4.08Z" /><path fill="#fff" d="M16.385 9.787h-1.158v13.058H12a3.232 3.232 0 0 1-3.228-3.21v-2.243H7.613v2.242c0 1.574.86 3.03 2.225 3.798.66.38 1.401.57 2.16.57h4.387V12.59h3.544v-1.166h-3.544V9.787Z" /><path fill="#fff" d="M6.62 7.615H4.367A4.402 4.402 0 0 0 0 12.01v4.386h11.42v3.535h1.158v-3.535h1.637v-1.167H1.166v-3.22a3.23 3.23 0 0 1 3.202-3.227h2.251V7.615Z" /></svg>
                            </div>
                            <span className='uich_plugin_icon_text'>{__('The Plus Addons for Elementor', 'uichemy')}</span>
                            <span className='uich_alert_button uich_alert_button_green'>{__('Optional', 'uichemy')}</span>

                        </div>
                        {active.findPlugin.includes('The Plus Addons for Elementor') ? (
                            (active.plusAddons === true) ?
                                <div className='uich_activated_label'>
                                    <span className='uich_check_green_icon'>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="12" fill="none" viewBox="0 0 14 12"><path stroke="#00A31B" strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.5" d="M1.545 7.638s1.228 0 2.864 2.864c0 0 4.548-7.5 8.591-9" /></svg>
                                    </span>
                                    <span>{__('Activated', 'uichemy')}</span>
                                </div> :
                                <button className='uich_secondary_button' onClick={(e) => plusAddonsInstall(e, setIsEnabled, 'plusAddons')}>{__('Activate', 'uichemy')}</button>) : (
                            <button className='uich_secondary_button' onClick={(e) => plusAddonsInstall(e, setIsEnabled)}>{__('Install & Activate', 'uichemy')}</button>)
                        }
                    </div>
                </>
            )
        }

        const bricksinstall = () => {
            return (
                <>
                    <div className={`uich_setting_item ${active.activeTheme === "bricks" ? 'uich_activated' : ''}`}>
                        <div className='uich_setting_info'>
                            <div className='uich_plugin_icon uichmy'>
                                <svg xmlns="http://www.w3.org/2000/svg" width="11" height="13" fill="none" viewBox="0 0 11 13"><path fill="#212121" d="m3.33.145.215.133v3.724c.756-.5 1.592-.75 2.509-.75 1.321 0 2.416.461 3.285 1.384.857.922 1.286 2.06 1.286 3.41 0 1.357-.431 2.494-1.295 3.411-.869.923-1.96 1.384-3.276 1.384-1.15 0-2.131-.41-2.947-1.232v1.009H.714V.439L3.33.145ZM5.598 5.67c-.63 0-1.157.215-1.58.643-.423.44-.634 1.018-.634 1.732 0 .715.211 1.289.634 1.724.417.434.943.651 1.58.651.673 0 1.218-.226 1.634-.678.41-.447.616-1.012.616-1.697 0-.684-.208-1.253-.625-1.705-.416-.447-.958-.67-1.625-.67Z" /></svg>
                            </div>
                            <span className='uich_plugin_icon_text'>{__('Bricks Builder', 'uichemy')}</span>
                            <span className='uich_alert_button'>{__('Required', 'uichemy')}</span>
                        </div>

                        {active.findTheme.includes('Bricks') ?

                            (active.activeTheme === "bricks" ?
                                <div className='uich_activated_label'>
                                    <span className='uich_check_green_icon'>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="12" fill="none" viewBox="0 0 14 12"><path stroke="#00A31B" strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.5" d="M1.545 7.638s1.228 0 2.864 2.864c0 0 4.548-7.5 8.591-9" /></svg>
                                    </span>
                                    <span>{__('Activated', 'uichemy')}</span>
                                </div> :
                                <button className='uich_secondary_button' onClick={(e) => bricksActive(e, setIsEnabled)}>{__('Activate', 'uichemy')}</button>)
                            :
                            <a className='uich_secondary_button' href="https://bricksbuilder.io/pricing/?utm_source=wpbackend&utm_medium=dashboard&utm_campaign=uichemy" target="_blank" rel="noopener noreferrer">
                                <span>{__('Installation Required', 'uichemy')}</span>
                                <span><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 16 16"><path stroke="#4B22CC" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2" d="M10 2h4m0 0v4m0-4L6.667 9.333M12 8.667v4A1.334 1.334 0 0 1 10.667 14H3.333A1.334 1.334 0 0 1 2 12.667V5.333A1.333 1.333 0 0 1 3.333 4h4" /></svg></span>
                            </a>
                        }
                    </div>
                </>
            )
        }

        const installers = {
            Elementor: elementorinstall,
            Gutenberg: gutenberginstall,
            Bricks: bricksinstall,
        };

        return (
            <>
                <h3>Required Plugins for Figma Export</h3>
                <div className='uich_select_page_builder'>
                    <div className='uich_section uich_plugin_box_width uich_select_page_gap'>
                        {installers[nextstep]?.()}
                    </div>
                    <div className="uich-footer-actions-button">
                        <button className="uich-back-btn" onClick={() => { setOnBoardingStep(onBoardingStep - 1) }}>Back</button>
                        <button className="uich-board-purple-common-btn" onClick={() => { setOnBoardingStep(onBoardingStep + 1) }}>Next</button>
                    </div>
                </div>
            </>
        )
    }

    const complete = () => {
        const site_url = 'https://uichemy.com/';

        const twitterShareText = `Woah! 😲 Finally found a tool that converts my Figma designs into 100% editable and responsive Elementor, Gutenberg Blocks, and even Bricks websites! 🙌 .\n\n${site_url}\n\n#Figma #WordPress #UiChemy`;

        const twitterShareUrl = `https://twitter.com/intent/tweet?text=${encodeURIComponent(twitterShareText)}`;

        const handleClick = () => {
            setLoading(true);
            dataSave();

            setTimeout(() => {
                setLoading(false);
            }, 5000);
        };
        

        return (
            <>
                <div className="uich-board-conf-allcover">
                    <div className="uich-board-setup-h">
                        <span>
                            <svg width="42" height="42" viewBox="0 0 42 42" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M12.6064 4.87406C14.7141 3.81396 16.644 3.27365 19.1829 3.03428C19.744 2.97956 21.3248 2.99324 22.0229 3.05479C24.2675 3.25997 26.1562 3.80028 28.1819 4.79882C30.1049 5.74949 31.583 6.82326 33.0475 8.34159C34.382 9.7163 35.3332 11.0636 36.1818 12.7735C38.2964 17.0207 38.577 21.8766 36.9688 26.4042C36.6471 27.3139 35.8533 28.9553 35.3606 29.7282C34.0193 31.8347 32.1647 33.736 30.098 35.1175C29.1947 35.7194 27.4291 36.6085 26.4026 36.971C23.063 38.1542 19.6277 38.3183 16.206 37.4566C14.8989 37.1283 14.0709 36.8205 12.7843 36.1913C10.9434 35.2885 9.63636 34.3515 8.12397 32.8469C6.60474 31.3217 5.68089 30.0359 4.74335 28.1209C3.90846 26.4316 3.37467 24.6671 3.08725 22.6495C2.97092 21.8424 2.97092 19.1477 3.08725 18.3407C3.44995 15.8101 4.33959 13.2864 5.56455 11.3304C7.25486 8.622 9.82797 6.26244 12.6064 4.87406ZM22.0358 19.8617C24.7807 17.1026 27.1032 14.8198 27.1956 14.7801C27.4397 14.6809 27.9676 14.7404 28.1721 14.8926C28.5746 15.1904 28.733 15.6668 28.5878 16.1101C28.535 16.2755 27.0175 17.837 23.0256 21.8401C18.1495 26.7232 17.5029 27.3451 17.2786 27.3848C17.1609 27.4069 17.0683 27.4261 16.9763 27.4236C16.5989 27.4132 16.2317 27.0364 14.184 24.983C12.1313 22.9245 11.7587 22.5552 11.7502 22.1776C11.7481 22.0867 11.7671 21.9953 11.7889 21.8798C11.8483 21.5556 12.2442 21.1586 12.5675 21.099C13.1481 20.9865 13.1613 20.9932 15.1737 23.0046L17.0344 24.8705L22.0358 19.8617Z" fill="#00A31B" />
                            </svg>
                        </span>
                        <h2 className="uich-board-onbrd-crd-h">{__('Setup Up Done Successfully.', 'uichemy')}</h2>
                        <p>{__('Your Figma to WordPress Convertor is Ready to Import Design', 'uichemy')}</p>
                        <div className="uich-contribute-checkbox">
                            <input type="checkbox" id="uich-board-agree-ch" className="uich-board-chkbox-style" checked={isChecked}
                                onChange={() => setIsChecked(!isChecked)} />
                            <label className="uich-board-wid-chk-name" for="uich-board-agree-ch">{__('Contribute to improving our plugin by sharing non-sensitive details.', 'uichemy')}</label>
                            {/* <span className="uich-underline"> {__('Learn more.', 'uichemy')}</span> */}
                        </div >
                    </div>

                    <div className="uich-board-infcrd-cover">
                        <div className="uich-board-smxtx-incover">
                            <p className="uich-board-sm-intxt">
                                {__('I just installed UiChemy - Figma to WordPress Convertor Plugin. This connects my WordPress sites with Figma and easily import converted designs from Figma. Excited to use it.', 'uichemy')}
                            </p>
                        </div>

                        <div className="uich-board-btn-grp">
                            <div className="uich-footer-actions-button">
                                <a href={twitterShareUrl} target="_blank" rel="noopener noreferrer" className="uich-board-purple-common-btn">{__('Post on', 'uichemy')}
                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><g clip-path="url(#clip0_659_32813)"><path d="M9.49 6.775 15.317 0h-1.382l-5.06 5.883L4.834 0H.172l6.112 8.895L.172 16h1.381l5.344-6.212L11.166 16h4.662l-6.34-9.225h.001zM7.597 8.974l-.62-.886L2.051 1.04h2.122L8.15 6.728l.618.886 5.17 7.393h-2.122L7.598 8.974z" fill="white" /></g><defs><clipPath id="clip0_659_32813"><rect width="16" height="16" fill="white" /></clipPath></defs></svg>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div className="uich-footer-actions-button">
                        <button className="uich-back-btn" onClick={() => { setOnBoardingStep(onBoardingStep - 1) }}>{__('Back', 'uichemy')}</button>
                        <div className="uich-board-rit-btn-cover">
                            <button className={`uich-board-purple-common-btn ${loading ? 'loading' : ''}`} onClick={handleClick}>
                                <span className='button-text'>{__('Start Importing Design', 'uichemy')}</span>
                                <span className="uich-start-using-loader"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </>
        );

    };

    const handleOnboarding = () => {

        switch (onBoardingStep) {
            case 1: return select_page_builder();
            case 2: return Install_figma_plugin();
            case 3: return required_plugins();
            // case 4: return <>{('Bricks' === nextstep || active.findTheme.includes('Nexter') === true) ? complete() :
            //     select_your_theme()
            // }
            // </>
            case 4: return complete();
            default: return null;
        }
    }

    return (
        <div className="uich-board-onboarding-cover">


            {/* ----------Steps Design Start---------- */}

            <div className="uich-board-steps-cover-onbord">
                {nav_array()?.map((data, index) => (
                    <div key={index} className="uich-board-step-wrapper">
                        <div className={`uich-board-step-box ${(data.step_number === onBoardingStep) ? 'uich-board-step-active' : ''} ${(data.step_number < onBoardingStep) ? 'uich-board-step-confirmed' : ''}`}
                            onClick={() => {
                                if (data.step_number <= onBoardingStep) {
                                    setOnBoardingStep(data.step_number);
                                }
                            }}>
                            {/* Icon */}
                            {(data.step_number < onBoardingStep) ? (
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 16 16"><path fill="#14C38E" d="M6.95 9.35 5.338 7.738a.711.711 0 0 0-.526-.207.711.711 0 0 0-.525.207.711.711 0 0 0-.206.524c0 .213.069.388.206.525l2.138 2.138a.72.72 0 0 0 .525.225.72.72 0 0 0 .525-.225l4.238-4.238a.711.711 0 0 0 .206-.525.711.711 0 0 0-.207-.524.711.711 0 0 0-.524-.207.711.711 0 0 0-.526.207L6.95 9.35ZM8 15.5a7.3 7.3 0 0 1-2.925-.591 7.585 7.585 0 0 1-2.381-1.603 7.558 7.558 0 0 1-1.603-2.381A7.32 7.32 0 0 1 .5 8c0-1.037.197-2.012.591-2.925a7.591 7.591 0 0 1 1.603-2.381A7.563 7.563 0 0 1 5.075 1.09 7.306 7.306 0 0 1 8 .5c1.037 0 2.011.197 2.925.591a7.563 7.563 0 0 1 2.381 1.603 7.602 7.602 0 0 1 1.604 2.381A7.26 7.26 0 0 1 15.5 8a7.354 7.354 0 0 1-.591 2.925 7.524 7.524 0 0 1-1.603 2.381 7.619 7.619 0 0 1-2.381 1.604A7.266 7.266 0 0 1 8 15.5Z" /></svg>
                            ) : (data.step_number === onBoardingStep) ? (
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 16 16"><path fill="#1717CC" d="M6.95 9.35 5.338 7.738a.711.711 0 0 0-.526-.207.711.711 0 0 0-.525.207.711.711 0 0 0-.206.524c0 .213.069.388.206.525l2.138 2.138a.72.72 0 0 0 .525.225.72.72 0 0 0 .525-.225l4.238-4.238a.711.711 0 0 0 .206-.525.711.711 0 0 0-.207-.524.711.711 0 0 0-.524-.207.711.711 0 0 0-.526.207L6.95 9.35ZM8 15.5a7.3 7.3 0 0 1-2.925-.591 7.585 7.585 0 0 1-2.381-1.603 7.558 7.558 0 0 1-1.603-2.381A7.32 7.32 0 0 1 .5 8c0-1.037.197-2.012.591-2.925a7.591 7.591 0 0 1 1.603-2.381A7.563 7.563 0 0 1 5.075 1.09 7.306 7.306 0 0 1 8 .5c1.037 0 2.011.197 2.925.591a7.563 7.563 0 0 1 2.381 1.603 7.602 7.602 0 0 1 1.604 2.381A7.26 7.26 0 0 1 15.5 8a7.354 7.354 0 0 1-.591 2.925 7.524 7.524 0 0 1-1.603 2.381 7.619 7.619 0 0 1-2.381 1.604A7.266 7.266 0 0 1 8 15.5Z" /></svg>
                            ) : (
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 16 16"><path fill="#D9D9D9" d="M6.95 9.35 5.338 7.738a.711.711 0 0 0-.526-.207.711.711 0 0 0-.525.207.711.711 0 0 0-.206.524c0 .213.069.388.206.525l2.138 2.138a.72.72 0 0 0 .525.225.72.72 0 0 0 .525-.225l4.238-4.238a.711.711 0 0 0 .206-.525.711.711 0 0 0-.207-.524.711.711 0 0 0-.524-.207.711.711 0 0 0-.526.207L6.95 9.35ZM8 15.5a7.3 7.3 0 0 1-2.925-.591 7.585 7.585 0 0 1-2.381-1.603 7.558 7.558 0 0 1-1.603-2.381A7.32 7.32 0 0 1 .5 8c0-1.037.197-2.012.591-2.925a7.591 7.591 0 0 1 1.603-2.381A7.563 7.563 0 0 1 5.075 1.09 7.306 7.306 0 0 1 8 .5c1.037 0 2.011.197 2.925.591a7.563 7.563 0 0 1 2.381 1.603 7.602 7.602 0 0 1 1.604 2.381A7.26 7.26 0 0 1 15.5 8a7.354 7.354 0 0 1-.591 2.925 7.524 7.524 0 0 1-1.603 2.381 7.619 7.619 0 0 1-2.381 1.604A7.266 7.266 0 0 1 8 15.5Z" /></svg>
                            )}
                            <div className="uich-board-step-name">{data.step_name}</div>
                        </div>

                        {/* Chevron after each step except the last */}
                        {index !== nav_array().length - 1 && (
                            <svg xmlns="http://www.w3.org/2000/svg" width="8" height="14" fill="none" viewBox="0 0 8 14"><path fill="#D9D9D9" d="M7.707 6.293a1 1 0 0 1 0 1.414L2.05 13.364A1 1 0 1 1 .636 11.95L5.586 7 .636 2.05A1 1 0 0 1 2.05.636l5.657 5.657Z" /></svg>
                        )}
                    </div>
                ))}
            </div>

            {/* ----------Steps Design Completed---------- */}

            <div className="uich-board-steps-tab-cover uich-board-onbsec-cover">
                {handleOnboarding()}
            </div>

        </div>

    );
}
export default Onboarding;