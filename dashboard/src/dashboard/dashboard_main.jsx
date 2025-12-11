import React, { useState, useEffect } from 'react';
import { Route, HashRouter as Router, Routes, Link } from 'react-router-dom';
import { nexterBlockInstall, plusAddonsInstall, elementorInstall, flexboxContainer, elementorFileUploads, bricksSvgUploads, appendFormData, uichdash_fetch_api, bricksActive, elementorProInstall , addcusOption } from '../apicall';

import Onboarding from './onboarding/onboarding.jsx';


const { __ } = wp.i18n;

const Dashboard = () => {
    const [activeFaq, setActiveFaq] = useState(null);
    const [activeTab, setActiveTab] = useState(localStorage.getItem("onUichBoardingNextStep") || 'Elementor');
    const [currentStep, setCurrentStep] = useState(0);
    const [isEnabled, setIsEnabled] = useState(uich_ajax_object.dashData)
    const [isOnboardingVisible, setIsOnboardingVisible] = useState(null);
    const [isInitialized, setIsInitialized] = useState(false);
    const [showPopup, setShowPopup] = useState(false);
    const [onboardingIsChecked, setOnboardingIsChecked] = useState(false);
    const [selectedUser, setSelectedUser] = useState(uich_ajax_object.dashData.selectedAdminUsername);
    const [urlCopied, setUrlCopied] = useState(false);
    const [tokenCopied, setTokenCopied] = useState(false);
    const [originalToken, setOriginalToken] = useState(uich_ajax_object.dashData.siteToken);
    const [showWhatsNewIcon, setShowWhatsNewIcon] = useState(uich_ajax_object.dashData.pluginVersion);

    useEffect(() => {
        try {
            const onboardingDone = uich_ajax_object.dashData.uich_onbording;

            if (onboardingDone === '1') {
                setShowPopup(false);
                setIsOnboardingVisible(false);
                setIsInitialized(true);

                localStorage.setItem('uich_popup_shown', 'true');

                return;
            }

            const popupShown = localStorage.getItem('uich_popup_shown');
            const localOnboardingDone = localStorage.getItem('uich_onboarding_done');

            if (!popupShown) {
                setShowPopup(true);
                setIsOnboardingVisible(true);
            } else if (!localOnboardingDone) {
                setShowPopup(false);
                setIsOnboardingVisible(true);
            } else {
                setShowPopup(false);
                setIsOnboardingVisible(false);
            }

        } catch (error) {
            setIsOnboardingVisible(true);
            setShowPopup(true);
        } finally {
            setIsInitialized(true);
        }
    }, []);

    const data_save = async () => {

        const tpgbonData = {
            uich_onboarding: onboardingIsChecked,
        };

        let form = new FormData();
        const formData = {
            action: 'uich_boarding_store',
            boardingData: JSON.stringify(tpgbonData),
            nonce: uich_ajax_object.nonce,
        };
        appendFormData(form, formData);

        try {
            const response = await uichdash_fetch_api(form);
            const data = await response

            console.log('Response from server:', data);

            if (data && data.success) {
                sessionStorage.setItem('triggerOnComplete', 'yes');
                window.location.reload();
            }

        } catch (error) {
            console.error('Error scanning unused blocks:', error);
        }

    };

    const completeOnboarding = (onboardingData = {}) => {
        if (Object.keys(onboardingData).length > 0) {
            localStorage.setItem('uich_onboarding_data', JSON.stringify(onboardingData));
        }
        localStorage.setItem('uich_popup_shown', 'true');

        setIsOnboardingVisible(false);
        setShowPopup(false);
    };

    const toggleFaq = (index) => {
        setActiveFaq(activeFaq === index ? null : index);
    };

    var plugin_url = uich_ajax_object.uich_url;

    const UichPopup = ({ onClose, onGetStarted }) => {
        const handleSkip = () => {
            onClose();
            localStorage.setItem("onUichBoardingNextStep", "Elementor");
        };

        const handleGetStarted = () => {
            onGetStarted ? onGetStarted() : onClose();
        };

        return (

            <div className='uich-popup-overlay'>
                <div className="uich-popup">
                    <div className="uich-popup__inner">
                        <h2 className="uich-popup__heading">
                            {__('Get Started with UiChemy - Figma to', 'uichemy')}<br />{__('WordPress Convertor', 'uichemy')}
                        </h2>

                        <div className="uich-popup__media">
                            <iframe
                                width="800px"
                                height="350px"
                                style={{ borderRadius: "7px" }}
                                src="https://www.youtube.com/embed/YzPirLnkQTM"
                                title="YouTube video player"
                                frameBorder="0"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                allowFullScreen>
                            </iframe>
                        </div>

                        <div className="uich-popup__footer">
                            <button className="uich-popup__skip" onClick={handleSkip}>{__('Skip', 'uichemy')}</button>
                            <button className="uich-popup__start" onClick={handleGetStarted}>{__('Get Started', 'uichemy')}</button>
                        </div>
                    </div>
                </div>
            </div>
        );
    };

    const WelcomeHeaderBanner = () => {

        var user_info = uich_ajax_object.dashData.userData;

        const userName = user_info.userName.split(" ").map(word => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase()).join(" ");

        const pluginVersion = uich_ajax_object.dashData.pluginVersion;
        const currentVersion = uich_ajax_object.dashData.version;

        const seenVersion = localStorage.getItem('uich_seen_changelog_version');
        
        
        const handleWhatsNewClick = async (e) => {
            e.preventDefault();

            try {
                const formData = new FormData();
                formData.append('action', 'uich_update_notice_count');
                formData.append('security', uich_ajax_object.nonce);

                const response = await fetch(uich_ajax_object.ajax_url, {
                    method: 'POST',
                    body: formData,
                });

                const data = await response.json();

                if (data.success && data.data.updated) {
                    setShowWhatsNewIcon(false);
                    window.open('https://roadmap.uichemy.com/updates?utm_source=wpbackend&utm_medium=dashboard&utm_campaign=uichemy', '_blank');
                    window.location.reload();
                }else{
                    window.open('https://roadmap.uichemy.com/updates?utm_source=wpbackend&utm_medium=dashboard&utm_campaign=uichemy', '_blank');
                }
            } catch (error) {
                console.error('Failed to update notice count:', error)
                window.open('https://roadmap.uichemy.com/updates?utm_source=wpbackend&utm_medium=dashboard&utm_campaign=uichemy', '_blank');
                window.location.reload();
            }
        };
        
        return (
            <div className='uich_main_welcome_header_ban'>
                <div className='uich_header'>
                    <div className='uich_logo_container'>
                        <div className='uich_logo'>
                            <img src={plugin_url + 'assets/images/uichemy.svg'} alt="Logo" />
                        </div>
                    </div>
                    <div className='uich_header_right'>
                        <div className='uich_whats_new'>
                            <a href="https://roadmap.uichemy.com/updates?utm_source=wpbackend&utm_medium=dashboard&utm_campaign=uichemy" target="_blank" rel="noopener noreferrer" onClick={handleWhatsNewClick}>
                                {__("What's New?", 'uichemy')}
                            </a>
                            {showWhatsNewIcon && 
                                <span className='uich_whats_new_icon'>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 14 14">
                                        <rect width="13" height="13" x=".5" y=".5" fill="#FF1E1E" rx="6.5" />
                                        <rect width="13" height="13" x=".5" y=".5" stroke="#4B22CC" rx="6.5" />
                                        <path fill="#fff" d="M6.897 10V4.72H5.769v-.68h1.92V10h-.792Z" />
                                    </svg>
                                </span>
                            }
                        </div>
                        <div className='uich_version'>
                            <span>{__('Version ', 'uichemy') + currentVersion}</span>
                        </div>
                    </div>
                </div>
                <div className='uich_welcome_banner'>
                    <div className='uich_welcome_icon'>
                        <img src={user_info.profileLink} alt="user_img" />
                    </div>
                    <div className='uich_welcome_content'>
                        <h2>{__('Welcome,', 'uichemy')} {userName} !</h2>
                        <p>{__('Convert your Figma designs into 100% editable templates for Elementor, Gutenberg, and Bricks in seconds.', 'uichemy')}</p>
                    </div>
                </div>
            </div>
        )
    }

    const OurProducts = () => {
        const products = [
            {
                name: "SproutUI",
                icon: <img src={plugin_url + 'assets/images/sproutui.svg'} alt="SproutUI" />,
                link: "https://sproutui.com/"

            },
            {
                name: "The Plus Addons for Elementor",
                icon: <img src={plugin_url + 'assets/images/elementer.svg'} alt="elementer" />,
                link: "https://theplusaddons.com/"
            },
            {
                name: "Nexter",
                icon: <img src={plugin_url + 'assets/images/nexter.svg'} alt="nexter" />,
                link: "https://nexterwp.com/"

            },
            {
                name: "WDesignKit",
                icon: <img src={plugin_url + 'assets/images/wdesignkit.svg'} alt="WDesignKit" />,
                link: "https://wdesignkit.com/"

            },
        ];

        var link_icon = <svg xmlns="http://www.w3.org/2000/svg" width="20" height="21" fill="none" viewBox="0 0 20 21"><path fill="#020202" d="m4.878 5.351.353.354-.353-.354ZM10 16.475v.5-.5Zm-5.122-.88-.354.354.354-.353ZM9.4 4.974a.5.5 0 0 0-.002-1l.002 1Zm7.1 6.102a.5.5 0 1 0-1-.002l1 .002Zm-.805-6.269.365-.341-.365.341Zm-4.048 3.312a.5.5 0 0 0 .704.71l-.704-.71Zm3.705-3.494-.087.492.087-.492Zm-2.686-.651a.5.5 0 0 0 0 1v-1Zm2.834 3.834a.5.5 0 1 0 1 0h-1Zm.349-2.686.492-.088-.492.088ZM3.999 10.473h.5c0-1.428.001-2.45.105-3.225.103-.762.296-1.212.627-1.543l-.353-.354-.354-.353c-.547.547-.793 1.244-.91 2.116-.116.859-.115 1.96-.115 3.36h.5ZM10 16.476v-.5c-1.428 0-2.45-.002-3.226-.106-.761-.102-1.212-.296-1.543-.627l-.353.354-.354.353c.548.548 1.245.794 2.117.911.858.116 1.959.115 3.36.115v-.5ZM4 10.473h-.5c0 1.4-.001 2.501.114 3.36.118.872.364 1.569.911 2.116l.354-.353.353-.354c-.33-.33-.524-.781-.627-1.543-.104-.776-.105-1.797-.105-3.226h-.5ZM10 16.476v.5c1.4 0 2.501 0 3.36-.115.872-.117 1.568-.363 2.116-.91l-.354-.354-.353-.354c-.331.331-.781.525-1.543.627-.776.104-1.797.106-3.226.106v.5ZM9.4 4.473v-.5c-1.21.002-2.177.017-2.948.142-.786.127-1.42.375-1.928.883l.354.353.353.354c.308-.308.717-.496 1.38-.603.679-.11 1.565-.127 2.79-.13v-.5Zm6.601 6.6h-.5c-.002 1.225-.02 2.11-.13 2.789-.107.664-.295 1.073-.602 1.38l.354.354.353.353c.507-.507.756-1.142.883-1.927.125-.771.14-1.739.142-2.947l-.5-.001Zm-.305-6.267-.352-.355-3.696 3.667.352.355.352.355 3.696-3.667-.352-.355Zm-.343-.182.087-.492c-.467-.083-1.172-.121-1.734-.14a35.823 35.823 0 0 0-1.017-.02h-.021v1h.004a5.405 5.405 0 0 1 .073 0 30.327 30.327 0 0 1 .928.02c.566.018 1.204.056 1.593.124l.087-.492Zm.648 3.183h.5V7.723a31.577 31.577 0 0 0-.02-.956c-.018-.563-.056-1.267-.14-1.735l-.491.088-.493.087c.069.388.106 1.027.125 1.593a34.896 34.896 0 0 1 .019.928V7.806h.5Zm-.648-3.183-.087.492a.11.11 0 0 1 .064.031l.366-.341.365-.341a1.108 1.108 0 0 0-.62-.333l-.088.492Zm.343.182-.366.341c.01.01.021.029.027.06l.493-.086.492-.088a1.113 1.113 0 0 0-.28-.568l-.366.341Z" /></svg>;

        return (
            <div className='uich_our_products'>
                <div className='uich_section uich_third_box_height'>
                    <h3>{__('Our Products', 'uichemy')}</h3>
                    <div className='uich_product_list'>
                        {products.map((product, index) => (
                            <div key={index} className='uich_product_list_item'>
                                <div className='uich_product_icon'>{product.icon}</div>
                                <span className='uich_product_text'>{product.name}</span>
                                <a className='uich_external_link' href={product.link+ '?utm_source=wpbackend&utm_medium=dashboard&utm_campaign=uichemy'} target="_blank" rel="noopener noreferrer">{link_icon}</a>
                            </div>
                        ))}
                    </div>
                </div>
            </div>
        )
    }

    const AllResources = () => {
        const resources = [
            {
                label: 'Documentation',
                buttonText: 'Read Documentation',
                link: 'https://uichemy.com/docs/',
            },
            {
                label: 'Educational Design Guidelines',
                buttonText: 'Learn How to Optimize',
                link: 'https://www.figma.com/community/file/1329383275066935195',
            },
            {
                label: 'Community',
                buttonText: 'Join Our Community',
                link: 'https://www.facebook.com/uichemy/',
            },
            {
                label: 'Join Discord Channel',
                buttonText: 'Join Now',
                link: ' https://go.posimyth.com/uichemy-discord',
            },
            {
                label: 'YouTube',
                buttonText: 'Watch YouTube Tutorials',
                link: 'https://youtu.be/8_6DymM-5KQ?si=YdyU6U-ASiWMzHkb',
            },
            {
                label: 'Template Library',
                buttonText: 'Visit Now',
                link: 'https://uichemy.com/templates-library/'
            },
        ];

        return (
            <div className='uich_all_resources'>
                <div className='uich_section uich_second_box_height'>
                    {resources.map((item, index) => (
                        <div className='uich_resource_item' key={index}>
                            <span className='uich_resources_text'>{item.label}</span>
                            <a href={item.link+ '?utm_source=wpbackend&utm_medium=dashboard&utm_campaign=uichemy'} className='uich_secondary_button' target="_blank" rel="noopener noreferrer">
                                {item.buttonText}
                            </a>
                        </div>
                    ))}
                </div>
            </div>

        )
    }


    const FrequentlySection = () => {
        const faqData = [
            {
                question: 'How does UiChemy work?',
                answer: 'UiChemy works by integrating directly with Figma and WordPress. Once you have designed your website layout in Figma, you can export your design files using UiChemy\'s conversion feature.',
            },
            {
                question: 'Is UiChemy a standalone software or a plugin?',
                answer: 'UiChemy provides both a Figma plugin and a WordPress plugin that work together.',
            },
            {
                question: 'Does UiChemy require any coding knowledge?',
                answer: 'No, UiChemy is designed to be used without any coding knowledge. It automatically converts your Figma designs to Elementor templates.',
            },
            {
                question: 'How can I get support or assistance with UiChemy?',
                answer: 'You can reach our support team through the Helpdesk, join our community forums, or connect via Live Chat on our website for immediate assistance.',
            },
        ];

        return (
            <div className='uich_faq_section uich_section  uich_third_box_height'>
                <h3>{__('Frequently Asked Questions', 'uichemy')}</h3>
                <p>{__('For any further help, reach us at Helpdesk or connect via Live Chat on website.', 'uichemy')}</p>
                <div className='uich_faq_items'>
                    {faqData.map((item, index) => (
                        <div key={index} className={`uich_faq_item ${activeFaq === index ? 'uich_active' : ''}`}>
                            <div className='uich_faq_question' onClick={() => toggleFaq(index)}>
                                <h4>{item.question}</h4>
                                <span className='uich_faq_toggle'>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="17" fill="none" viewBox="0 0 16 17"><path stroke="#09090B" strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.333" d="m4 6.473 4 4 4-4" />
                                    </svg>
                                </span>
                            </div>
                            {activeFaq === index && (
                                <div className={`uich_faq_answer ${activeFaq === index ? "opan-accordion" : ""}`} >
                                    <p>{item.answer}</p>
                                </div>
                            )}
                        </div>
                    ))}
                </div>
            </div>
        )

    }

    const PageBuilderSelector = () => {

        const handleTabClick = (tab) => {
            setActiveTab(tab);
            localStorage.setItem("onUichBoardingNextStep", tab);
        };

        var active = uich_ajax_object.dashData;

        return (
            <div className='uich_select_page_builder'>
                <div className='uich_section uich_first_box_height uich_select_page_gap' style={ activeTab === 'Elementor' ? { height: '820px' } : { height: '730px' }}>
                    <h3>{__('Select Page Builder', 'uichemy')}</h3>
                    <div className='uich_tabs'>
                        <div className={`uich_tab ${activeTab === 'Elementor' ? 'uich_active' : ''}`} onClick={() => handleTabClick('Elementor')}>{__('Elementor', 'uichemy')}
                        </div>
                        <div className={`uich_tab ${activeTab === 'Gutenberg' ? 'uich_active' : ''}`} onClick={() => handleTabClick('Gutenberg')} >{__('Gutenberg', 'uichemy')}
                        </div>
                        <div className={`uich_tab ${activeTab === 'Bricks' ? 'uich_active' : ''}`} onClick={() => handleTabClick('Bricks')}>{__('Bricks', 'uichemy')}
                        </div>
                    </div>

                    {/* Elementor Content */}
                    {activeTab === 'Elementor' && (
                        <>
                            <h3>{__('Install Required Plugins', 'uichemy')}</h3>
                            <div className='uich_plugin_item'>
                                <div className='uich_plugin_info'>
                                    <div className='uich_plugin_icon figma'>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 14 14"><path fill="#fff" d="m8.225 7.461-.01 2.85s-2.408.14-2.408-1.715V3.868A3.432 3.432 0 0 0 2.375.438H.503V9.86a3.815 3.815 0 0 0 3.818 3.816 4.2 4.2 0 0 0 .778 0h4.56a3.868 3.868 0 0 0 3.867-3.868V7.463l-5.3-.002Zm4.842 2.345a3.411 3.411 0 0 1-3.404 3.405H7.498a2.96 2.96 0 0 0 .598-.646c.513-.757.578-1.603.578-2.255l.01-2.39h4.383v1.886Z" /><path fill="#fff" d="M11.311.438a3.087 3.087 0 0 0-3.087 3.086v1.851h5.304V.437h-2.217Z" /></svg>
                                    </div>
                                    <span className='uich_plugin_icon_text'>{__('Figma to Elementor | UiChemy', 'uichemy')}</span>
                                </div>
                                <a href="https://www.figma.com/community/plugin/1265873702834050352/figma-to-elementor-uichemy" target="_blank" rel="noopener noreferrer" className='uich_link_button'>
                                    <span>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 14 14"><path fill="#4B22CC" d="m1.877 1.879.353.353-.353-.354Zm5.122 11.123v.5-.5ZM1.877 12.123l-.354.354.354-.354ZM6.4 1.5a.5.5 0 0 0-.002-1l.002 1Zm7.1 6.102a.5.5 0 0 0-1-.002l1 .002Zm-.805-6.269.365-.34-.365.34ZM8.647 4.646a.5.5 0 1 0 .705.71l-.705-.71Zm3.705-3.495-.087.493.087-.493ZM9.666.5a.5.5 0 1 0 0 1v-1ZM12.5 4.334a.5.5 0 0 0 1 0h-1Zm.349-2.686.492-.087-.492.087ZM.998 7h.5c0-1.429.001-2.45.105-3.226.103-.762.296-1.212.627-1.543l-.353-.354-.354-.353C.976 2.073.73 2.769.613 3.642.496 4.5.497 5.6.497 7h.5Zm6.001 6.001v-.5c-1.428 0-2.45-.001-3.226-.105-.761-.103-1.212-.296-1.543-.627l-.353.353-.354.354c.548.547 1.245.793 2.117.91.858.116 1.959.115 3.36.115v-.5ZM.998 7.001h-.5c0 1.4-.001 2.5.114 3.359.118.872.364 1.569.911 2.117l.354-.354.353-.353c-.33-.332-.524-.782-.627-1.543C1.5 9.45 1.498 8.429 1.498 7h-.5Zm6.001 6.001v.5c1.4 0 2.501.001 3.36-.114.872-.118 1.568-.364 2.116-.911l-.354-.354-.353-.353c-.331.33-.781.524-1.543.627-.776.104-1.797.105-3.226.105v.5ZM6.4 1V.5C5.188.502 4.221.517 3.45.642c-.786.127-1.42.376-1.928.883l.354.354.353.353c.308-.307.717-.496 1.38-.603.679-.11 1.565-.127 2.79-.129V1ZM13 7.6h-.5c-.002 1.225-.02 2.111-.13 2.79-.106.663-.295 1.072-.602 1.38l.353.353.354.354c.507-.508.756-1.143.883-1.928.125-.77.14-1.739.142-2.947L13 7.6Zm-.305-6.267-.353-.355-3.695 3.668L9 5l.353.355 3.695-3.668-.352-.355Zm-.343-.182L12.44.66c-.467-.083-1.172-.121-1.734-.14A35.872 35.872 0 0 0 9.668.5h-.001v1h.004a10.554 10.554 0 0 0 .073 0 30.94 30.94 0 0 1 .928.019c.566.019 1.205.056 1.593.125l.087-.493ZM13 4.334h.5V4.25a31.585 31.585 0 0 0-.02-.956c-.018-.562-.056-1.267-.139-1.734l-.492.087-.493.087c.069.389.106 1.027.125 1.594a35.536 35.536 0 0 1 .019.927V4.333h.5Zm-.648-3.183-.087.493a.11.11 0 0 1 .064.03l.366-.34.365-.342a1.108 1.108 0 0 0-.62-.333l-.088.492Zm.343.182-.366.342c.01.01.022.028.027.06l.493-.087.492-.087a1.113 1.113 0 0 0-.28-.569l-.366.341Z" /></svg>
                                    </span>
                                    <span>{__('Install in Figma', 'uichemy')}</span>
                                </a>
                            </div>

                            <div className={`uich_plugin_item ${active.elementor === true ? 'uich_activated' : ''}`}>
                                <div className='uich_plugin_info'>
                                    <div className='uich_plugin_icon elementor'>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="none" viewBox="0 0 10 10"><path fill="#fff" d="M2 10H.003V.003H2v9.999Zm7.998 0H4V8.003h6V10Zm0-4H4V4.003h6V6Zm0-4H4V.003h6v1.999Z" /></svg>
                                    </div>
                                    <span className='uich_plugin_icon_text'>{__('Elementor Page Builder', 'uichemy')}</span>
                                    {active.elementor === true ? '' :
                                        <span className='uich_alert_full_stop'>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" viewBox="0 0 12 12"><path fill="#FF1E1E" d="M5.625.002A5.627 5.627 0 0 0 0 5.627a5.627 5.627 0 0 0 5.625 5.625 5.627 5.627 0 0 0 5.625-5.625A5.627 5.627 0 0 0 5.625.002Zm0 9a.752.752 0 0 1-.75-.75c0-.413.337-.75.75-.75.412 0 .75.337.75.75 0 .412-.338.75-.75.75Zm.859-5.816-.252 3.007a.609.609 0 0 1-1.215 0l-.25-3.007a.861.861 0 1 1 1.72-.072c0 .023 0 .05-.003.072Z" /></svg>
                                        </span>}
                                </div>
                                {active.findPlugin.includes('Elementor') ? (
                                    active.elementor ?
                                        <div className='uich_activated_label'>
                                            <span className='uich_check_green_icon'>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="12" fill="none" viewBox="0 0 14 12"><path stroke="#00A31B" strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.5" d="M1.545 7.638s1.228 0 2.864 2.864c0 0 4.548-7.5 8.591-9" /></svg>
                                            </span>
                                            <span>{__('Activated', 'uichemy')}</span>
                                        </div> :
                                        <button className='uich_secondary_button' onClick={(e) => elementorInstall(e, setIsEnabled, 'elementor')}>{__('Activate', 'uichemy')}</button>) : (
                                    <button className='uich_secondary_button' onClick={(e) => elementorInstall(e, setIsEnabled)}>{__('Install & Activate', 'uichemy')}</button>
                                )
                                }
                            </div>

                            <h3>{__('Recommended Settings', 'uichemy')}</h3>
                            <div className={`uich_plugin_item ${(active.flexboxCon === "active" && active.elementor === true) ? 'uich_activated' : ''}`}>
                                <div className='uich_setting_info'>
                                    <div className='uich_setting_icon wp'>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 14 14"><path fill="#fff" d="M7 .002c3.857 0 7 3.143 7 7 0 3.864-3.143 7-7 7-3.864 0-7-3.136-7-7 0-3.857 3.136-7 7-7ZM5.397 12.994a5.51 5.51 0 0 0 1.603.23c.665 0 1.295-.11 1.897-.314L7.091 8.024c-.56 1.652-1.127 3.304-1.694 4.97ZM1.281 4.488C.938 5.265.777 6.091.777 7.001c0 2.471 1.408 4.655 3.494 5.663C3.27 9.941 2.275 7.211 1.28 4.488Zm11.207-.462c.217 1.666-.133 2.528-.3 2.976-.687 1.806-1.367 3.632-2.045 5.445 1.868-1.078 3.08-3.142 3.08-5.445 0-1.078-.245-2.073-.735-2.976ZM7 .78a6.21 6.21 0 0 0-5.229 2.815c.147.007.28.007.406.007.64 0 1.648-.077 1.659-.078.35-.014.392.736.042.778-.009 0-.438.042-.82.055l2.388 6.406L6.713 6.68 5.83 4.294l-.644-.057c-.35-.014-.308-.874.042-.853a26.1 26.1 0 0 0 1.631.084c.658 0 1.666-.084 1.666-.084.35-.02.392.818.042.853 0 0-.196.105-.574.12l2.121 5.859s.98-2.457.98-3.5c0-.77-.14-1.296-.385-1.723-.315-.518-.63-.952-.63-1.47 0-.832.68-1.182 1.155-1.105A6.222 6.222 0 0 0 7 .779Z" /></svg>
                                    </div>
                                    <span className='uich_plugin_icon_text'>{__('Flexbox Container', 'uichemy')}</span>
                                    {(active.flexboxCon === "active" && active.elementor === true) ? '' :
                                        <span className='uich_alert_full_stop'>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" viewBox="0 0 12 12"><path fill="#FF1E1E" d="M5.625.002A5.627 5.627 0 0 0 0 5.627a5.627 5.627 0 0 0 5.625 5.625 5.627 5.627 0 0 0 5.625-5.625A5.627 5.627 0 0 0 5.625.002Zm0 9a.752.752 0 0 1-.75-.75c0-.413.337-.75.75-.75.412 0 .75.337.75.75 0 .412-.338.75-.75.75Zm.859-5.816-.252 3.007a.609.609 0 0 1-1.215 0l-.25-3.007a.861.861 0 1 1 1.72-.072c0 .023 0 .05-.003.072Z" /></svg>
                                        </span>}
                                </div>
                                {(active.flexboxCon === "active" && active.elementor === true) ?
                                    <div className='uich_activated_label'>
                                        <span className='uich_check_green_icon'>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="12" fill="none" viewBox="0 0 14 12"><path stroke="#00A31B" strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.5" d="M1.545 7.638s1.228 0 2.864 2.864c0 0 4.548-7.5 8.591-9" /></svg>
                                        </span>
                                        <span>{__('Activated', 'uichemy')}</span>
                                    </div> :
                                    <button className='uich_secondary_button' onClick={(e) => flexboxContainer(e, setIsEnabled)}>{__('Activate', 'uichemy')}</button>
                                }
                            </div>

                            <div className={`uich_setting_item ${(active.eleFileLoad === "1" && active.elementor === true) ? 'uich_activated' : ''}`}>
                                <div className='uich_setting_info'>
                                    <div className='uich_setting_icon wp'>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 14 14"><path fill="#fff" d="M7 .002c3.857 0 7 3.143 7 7 0 3.864-3.143 7-7 7-3.864 0-7-3.136-7-7 0-3.857 3.136-7 7-7ZM5.397 12.994a5.51 5.51 0 0 0 1.603.23c.665 0 1.295-.11 1.897-.314L7.091 8.024c-.56 1.652-1.127 3.304-1.694 4.97ZM1.281 4.488C.938 5.265.777 6.091.777 7.001c0 2.471 1.408 4.655 3.494 5.663C3.27 9.941 2.275 7.211 1.28 4.488Zm11.207-.462c.217 1.666-.133 2.528-.3 2.976-.687 1.806-1.367 3.632-2.045 5.445 1.868-1.078 3.08-3.142 3.08-5.445 0-1.078-.245-2.073-.735-2.976ZM7 .78a6.21 6.21 0 0 0-5.229 2.815c.147.007.28.007.406.007.64 0 1.648-.077 1.659-.078.35-.014.392.736.042.778-.009 0-.438.042-.82.055l2.388 6.406L6.713 6.68 5.83 4.294l-.644-.057c-.35-.014-.308-.874.042-.853a26.1 26.1 0 0 0 1.631.084c.658 0 1.666-.084 1.666-.084.35-.02.392.818.042.853 0 0-.196.105-.574.12l2.121 5.859s.98-2.457.98-3.5c0-.77-.14-1.296-.385-1.723-.315-.518-.63-.952-.63-1.47 0-.832.68-1.182 1.155-1.105A6.222 6.222 0 0 0 7 .779Z" /></svg>
                                    </div>
                                    <span className='uich_plugin_icon_text'>{__('Enable Unfiltered File Uploads', 'uichemy')}</span>
                                    {(active.eleFileLoad === "1" && active.elementor === true) ? '' :
                                        <span className='uich_alert_full_stop'>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" viewBox="0 0 12 12"><path fill="#FF1E1E" d="M5.625.002A5.627 5.627 0 0 0 0 5.627a5.627 5.627 0 0 0 5.625 5.625 5.627 5.627 0 0 0 5.625-5.625A5.627 5.627 0 0 0 5.625.002Zm0 9a.752.752 0 0 1-.75-.75c0-.413.337-.75.75-.75.412 0 .75.337.75.75 0 .412-.338.75-.75.75Zm.859-5.816-.252 3.007a.609.609 0 0 1-1.215 0l-.25-3.007a.861.861 0 1 1 1.72-.072c0 .023 0 .05-.003.072Z" /></svg>
                                        </span>}
                                </div>
                                {(active.eleFileLoad === "1" && active.elementor === true) ?
                                    <div className='uich_activated_label'>
                                        <span className='uich_check_green_icon'>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="12" fill="none" viewBox="0 0 14 12"><path stroke="#00A31B" strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.5" d="M1.545 7.638s1.228 0 2.864 2.864c0 0 4.548-7.5 8.591-9" /></svg>
                                        </span>
                                        <span>{__('Activated', 'uichemy')}</span>
                                    </div> :
                                    <button className='uich_secondary_button' onClick={(e) => elementorFileUploads(e, setIsEnabled)}>{__('Activate', 'uichemy')}</button>
                                }
                            </div>
                            <div className={`uich_setting_item ${(active.elementorCustomCss !== "1" ) ? 'uich_activated' : ''}`}>
                                <div className='uich_setting_info'>
                                    <div className='uich_setting_icon wp'>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 14 14"><path fill="#fff" d="M7 .002c3.857 0 7 3.143 7 7 0 3.864-3.143 7-7 7-3.864 0-7-3.136-7-7 0-3.857 3.136-7 7-7ZM5.397 12.994a5.51 5.51 0 0 0 1.603.23c.665 0 1.295-.11 1.897-.314L7.091 8.024c-.56 1.652-1.127 3.304-1.694 4.97ZM1.281 4.488C.938 5.265.777 6.091.777 7.001c0 2.471 1.408 4.655 3.494 5.663C3.27 9.941 2.275 7.211 1.28 4.488Zm11.207-.462c.217 1.666-.133 2.528-.3 2.976-.687 1.806-1.367 3.632-2.045 5.445 1.868-1.078 3.08-3.142 3.08-5.445 0-1.078-.245-2.073-.735-2.976ZM7 .78a6.21 6.21 0 0 0-5.229 2.815c.147.007.28.007.406.007.64 0 1.648-.077 1.659-.078.35-.014.392.736.042.778-.009 0-.438.042-.82.055l2.388 6.406L6.713 6.68 5.83 4.294l-.644-.057c-.35-.014-.308-.874.042-.853a26.1 26.1 0 0 0 1.631.084c.658 0 1.666-.084 1.666-.084.35-.02.392.818.042.853 0 0-.196.105-.574.12l2.121 5.859s.98-2.457.98-3.5c0-.77-.14-1.296-.385-1.723-.315-.518-.63-.952-.63-1.47 0-.832.68-1.182 1.155-1.105A6.222 6.222 0 0 0 7 .779Z" /></svg>
                                    </div>
                                    <span className='uich_plugin_icon_text'>{__('Custom CSS Field', 'uichemy')}</span>
                                    { active.elementorCustomCss !== "1" ? '' :
                                        <span className='uich_alert_full_stop'>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" viewBox="0 0 12 12"><path fill="#FF1E1E" d="M5.625.002A5.627 5.627 0 0 0 0 5.627a5.627 5.627 0 0 0 5.625 5.625 5.627 5.627 0 0 0 5.625-5.625A5.627 5.627 0 0 0 5.625.002Zm0 9a.752.752 0 0 1-.75-.75c0-.413.337-.75.75-.75.412 0 .75.337.75.75 0 .412-.338.75-.75.75Zm.859-5.816-.252 3.007a.609.609 0 0 1-1.215 0l-.25-3.007a.861.861 0 1 1 1.72-.072c0 .023 0 .05-.003.072Z" /></svg>
                                        </span>
                                    }
                                </div>
                                
                                <button className='uich_secondary_button' onClick={(e) => addcusOption(e, setIsEnabled , 'uich_elementor_custom_css')}>{active.elementorCustomCss === "1" ? __('Activate', 'uichemy') : __('Deactivate', 'uichemy')}</button>
                                
                            </div>

                            <h3>{__('Optional Plugins', 'uichemy')}</h3>
                            <div className={`uich_setting_item ${(active.plusAddons === true) ? 'uich_activated' : ''}`}>
                                <div className='uich_setting_info'>
                                    <div className='uich_setting_icon'>
                                        <img src={plugin_url + 'assets/images/elementer.svg'} alt="elementer" />
                                    </div>
                                    <span className='uich_plugin_icon_text'>{__('The Plus Addons for Elementor (Free)', 'uichemy')}</span>
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
                                    <button className='uich_secondary_button' onClick={(e) => plusAddonsInstall(e, setIsEnabled)}>{__('Install & Activate', 'uichemy')}</button>
                                )
                                }
                            </div>

                            <div className={`uich_plugin_item ${(active.elementorPro === true) ? 'uich_activated' : ''}`}>
                                <div className='uich_setting_info'>
                                    <div className='uich_plugin_icon elementor'>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="none" viewBox="0 0 10 10"><path fill="#fff" d="M2 10H.003V.003H2v9.999Zm7.998 0H4V8.003h6V10Zm0-4H4V4.003h6V6Zm0-4H4V.003h6v1.999Z" /></svg>
                                    </div>
                                    <span className='uich_plugin_icon_text'>{__('Elementor Pro', 'uichemy')}</span>
                                </div>
                                {active.findPlugin.includes('Elementor Pro') ? (
                                    (active.elementorPro === true) ?
                                        <div className='uich_activated_label'>
                                            <span className='uich_check_green_icon'>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="12" fill="none" viewBox="0 0 14 12"><path stroke="#00A31B" strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.5" d="M1.545 7.638s1.228 0 2.864 2.864c0 0 4.548-7.5 8.591-9" /></svg>
                                            </span>
                                            <span>{__('Activated', 'uichemy')}</span>
                                        </div> :
                                        <button className='uich_secondary_button' onClick={(e) => elementorProInstall(e, setIsEnabled, 'elementorPro')}>{__('Activate', 'uichemy')}</button>) : (
                                    <a className='uich_secondary_button' href='https://elementor.com/' target="_blank" rel="noopener noreferrer">
                                        <span>{__('Install', 'uichemy')}</span>
                                        <span><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 16 16"><path stroke="#4B22CC" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2" d="M10 2h4m0 0v4m0-4L6.667 9.333M12 8.667v4A1.334 1.334 0 0 1 10.667 14H3.333A1.334 1.334 0 0 1 2 12.667V5.333A1.333 1.333 0 0 1 3.333 4h4" /></svg></span>
                                    </a>)
                                }
                            </div>
                        </>
                    )}

                    {/* Gutenberg Content */}
                    {activeTab === 'Gutenberg' && (
                        <>
                            <h3>{__('Install Required Plugins', 'uichemy')}</h3>
                            <div className='uich_plugin_item'>
                                <div className='uich_plugin_info'>
                                    <div className='uich_setting_icon wp'>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 14 14"><path fill="#fff" d="m8.225 7.461-.01 2.85s-2.408.14-2.408-1.715V3.868A3.432 3.432 0 0 0 2.375.438H.503V9.86a3.815 3.815 0 0 0 3.818 3.816 4.2 4.2 0 0 0 .778 0h4.56a3.868 3.868 0 0 0 3.867-3.868V7.463l-5.3-.002Zm4.842 2.345a3.411 3.411 0 0 1-3.404 3.405H7.498a2.96 2.96 0 0 0 .598-.646c.513-.757.578-1.603.578-2.255l.01-2.39h4.383v1.886Z" /><path fill="#fff" d="M11.311.438a3.087 3.087 0 0 0-3.087 3.086v1.851h5.304V.437h-2.217Z" /></svg>
                                    </div>
                                    <span className='uich_plugin_icon_text'>{__('Figma to Gutenberg | UiChemy', 'uichemy')}</span>
                                </div>
                                <a href="https://www.figma.com/community/plugin/1379733208974981538/uichemy-convert-figma-to-gutenberg-block-editor-wordpress" target="_blank" rel="noopener noreferrer" className='uich_link_button'>
                                    <span>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 14 14"><path fill="#4B22CC" d="m1.877 1.879.353.353-.353-.354Zm5.122 11.123v.5-.5ZM1.877 12.123l-.354.354.354-.354ZM6.4 1.5a.5.5 0 0 0-.002-1l.002 1Zm7.1 6.102a.5.5 0 0 0-1-.002l1 .002Zm-.805-6.269.365-.34-.365.34ZM8.647 4.646a.5.5 0 1 0 .705.71l-.705-.71Zm3.705-3.495-.087.493.087-.493ZM9.666.5a.5.5 0 1 0 0 1v-1ZM12.5 4.334a.5.5 0 0 0 1 0h-1Zm.349-2.686.492-.087-.492.087ZM.998 7h.5c0-1.429.001-2.45.105-3.226.103-.762.296-1.212.627-1.543l-.353-.354-.354-.353C.976 2.073.73 2.769.613 3.642.496 4.5.497 5.6.497 7h.5Zm6.001 6.001v-.5c-1.428 0-2.45-.001-3.226-.105-.761-.103-1.212-.296-1.543-.627l-.353.353-.354.354c.548.547 1.245.793 2.117.91.858.116 1.959.115 3.36.115v-.5ZM.998 7.001h-.5c0 1.4-.001 2.5.114 3.359.118.872.364 1.569.911 2.117l.354-.354.353-.353c-.33-.332-.524-.782-.627-1.543C1.5 9.45 1.498 8.429 1.498 7h-.5Zm6.001 6.001v.5c1.4 0 2.501.001 3.36-.114.872-.118 1.568-.364 2.116-.911l-.354-.354-.353-.353c-.331.33-.781.524-1.543.627-.776.104-1.797.105-3.226.105v.5ZM6.4 1V.5C5.188.502 4.221.517 3.45.642c-.786.127-1.42.376-1.928.883l.354.354.353.353c.308-.307.717-.496 1.38-.603.679-.11 1.565-.127 2.79-.129V1ZM13 7.6h-.5c-.002 1.225-.02 2.111-.13 2.79-.106.663-.295 1.072-.602 1.38l.353.353.354.354c.507-.508.756-1.143.883-1.928.125-.77.14-1.739.142-2.947L13 7.6Zm-.305-6.267-.353-.355-3.695 3.668L9 5l.353.355 3.695-3.668-.352-.355Zm-.343-.182L12.44.66c-.467-.083-1.172-.121-1.734-.14A35.872 35.872 0 0 0 9.668.5h-.001v1h.004a10.554 10.554 0 0 0 .073 0 30.94 30.94 0 0 1 .928.019c.566.019 1.205.056 1.593.125l.087-.493ZM13 4.334h.5V4.25a31.585 31.585 0 0 0-.02-.956c-.018-.562-.056-1.267-.139-1.734l-.492.087-.493.087c.069.389.106 1.027.125 1.594a35.536 35.536 0 0 1 .019.927V4.333h.5Zm-.648-3.183-.087.493a.11.11 0 0 1 .064.03l.366-.34.365-.342a1.108 1.108 0 0 0-.62-.333l-.088.492Zm.343.182-.366.342c.01.01.022.028.027.06l.493-.087.492-.087a1.113 1.113 0 0 0-.28-.569l-.366.341Z" /></svg>
                                    </span>
                                    <span>{__('Install in Figma', 'uichemy')}</span>
                                </a>
                            </div>

                            <div className={`uich_plugin_item ${active.nexterBlock === true ? 'uich_activated' : ''}`}>
                                <div className='uich_plugin_info'>
                                    <div className='uich_setting_icon wp'>
                                        <img src={plugin_url + 'assets/images/nexter.png'} alt="nexter" />
                                    </div>
                                    <span className='uich_plugin_icon_text'>{__('Nexter Blocks', 'uichemy')}</span>
                                </div>
                                {active.findPlugin.includes('Nexter Blocks') ? (
                                    active.nexterBlock ?
                                        <div className='uich_activated_label'>
                                            <span className='uich_check_green_icon'>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="12" fill="none" viewBox="0 0 14 12"><path stroke="#00A31B" strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.5" d="M1.545 7.638s1.228 0 2.864 2.864c0 0 4.548-7.5 8.591-9" /></svg>
                                            </span>
                                            <span>{__('Activated', 'uichemy')}</span>
                                        </div> :
                                        <button className='uich_secondary_button' onClick={(e) => nexterBlockInstall(e, setIsEnabled, 'the-plus-addons-for-block-editor')}>{__('Activate', 'uichemy')}</button>) : (
                                    <button className='uich_secondary_button' onClick={(e) => nexterBlockInstall(e, setIsEnabled , 'the-plus-addons-for-block-editor')}>{__('Install & Activate', 'uichemy')}</button>
                                )
                                }
                            </div>
                           
                            <h3>{__('Recommended Settings', 'uichemy')}</h3>
                            <div className={`uich_setting_item ${(active.gutenbergCustomCss !== "1" ) ? 'uich_activated' : ''}`}>
                                <div className='uich_setting_info'>
                                    <div className='uich_setting_icon wp'>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 14 14"><path fill="#fff" d="M7 .002c3.857 0 7 3.143 7 7 0 3.864-3.143 7-7 7-3.864 0-7-3.136-7-7 0-3.857 3.136-7 7-7ZM5.397 12.994a5.51 5.51 0 0 0 1.603.23c.665 0 1.295-.11 1.897-.314L7.091 8.024c-.56 1.652-1.127 3.304-1.694 4.97ZM1.281 4.488C.938 5.265.777 6.091.777 7.001c0 2.471 1.408 4.655 3.494 5.663C3.27 9.941 2.275 7.211 1.28 4.488Zm11.207-.462c.217 1.666-.133 2.528-.3 2.976-.687 1.806-1.367 3.632-2.045 5.445 1.868-1.078 3.08-3.142 3.08-5.445 0-1.078-.245-2.073-.735-2.976ZM7 .78a6.21 6.21 0 0 0-5.229 2.815c.147.007.28.007.406.007.64 0 1.648-.077 1.659-.078.35-.014.392.736.042.778-.009 0-.438.042-.82.055l2.388 6.406L6.713 6.68 5.83 4.294l-.644-.057c-.35-.014-.308-.874.042-.853a26.1 26.1 0 0 0 1.631.084c.658 0 1.666-.084 1.666-.084.35-.02.392.818.042.853 0 0-.196.105-.574.12l2.121 5.859s.98-2.457.98-3.5c0-.77-.14-1.296-.385-1.723-.315-.518-.63-.952-.63-1.47 0-.832.68-1.182 1.155-1.105A6.222 6.222 0 0 0 7 .779Z" /></svg>
                                    </div>
                                    <span className='uich_plugin_icon_text'>{__('Custom CSS Field', 'uichemy')}</span>
                                    { active.gutenbergCustomCss !== "1" ? '' :
                                        <span className='uich_alert_full_stop'>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" viewBox="0 0 12 12"><path fill="#FF1E1E" d="M5.625.002A5.627 5.627 0 0 0 0 5.627a5.627 5.627 0 0 0 5.625 5.625 5.627 5.627 0 0 0 5.625-5.625A5.627 5.627 0 0 0 5.625.002Zm0 9a.752.752 0 0 1-.75-.75c0-.413.337-.75.75-.75.412 0 .75.337.75.75 0 .412-.338.75-.75.75Zm.859-5.816-.252 3.007a.609.609 0 0 1-1.215 0l-.25-3.007a.861.861 0 1 1 1.72-.072c0 .023 0 .05-.003.072Z" /></svg>
                                        </span>
                                    }
                                </div>
                                
                                <button className='uich_secondary_button' onClick={(e) => addcusOption(e, setIsEnabled, 'uictmcss_enabled')}>{active.gutenbergCustomCss === "1" ? __('Activate', 'uichemy') : __('Deactivate', 'uichemy')}</button>
                                
                            </div>

                            <h3>{__('Optional Plugins', 'uichemy')}</h3>
                            <div className={`uich_plugin_item ${active.spectra === true ? 'uich_activated' : ''}`}>
                                <div className='uich_plugin_info'>
                                    <div className='uich_setting_icon'>
                                        <img src={plugin_url + 'assets/images/spectra.png'} alt="nexter" />
                                    </div>
                                    <span className='uich_plugin_icon_text'>{__('Spectra', 'uichemy')}</span>
                                    <span className='uich_alert_red_icon'>
                                        <p>{__('Beta', 'uichemy')}</p>
                                    </span>
                                </div>
                                {active.findPlugin.includes('Spectra') ? 
                                    active.spectra === true ?
                                        <div className='uich_activated_label'>
                                            <span className='uich_check_green_icon'>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="12" fill="none" viewBox="0 0 14 12"><path stroke="#00A31B" strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.5" d="M1.545 7.638s1.228 0 2.864 2.864c0 0 4.548-7.5 8.591-9" /></svg>
                                            </span>
                                            <span>{__('Activated', 'uichemy')}</span>
                                        </div> : ( <button className='uich_secondary_button' onClick={(e) => nexterBlockInstall(e, setIsEnabled, 'ultimate-addons-for-gutenberg')}>{__('Activate', 'uichemy')}</button> ) : (
                                        <button className='uich_secondary_button' onClick={(e) => nexterBlockInstall(e, setIsEnabled , 'ultimate-addons-for-gutenberg')}>{__('Install & Activate', 'uichemy')}</button> )
                                    
                                }
                            </div>
                            
                            
                            <div className={`uich_plugin_item ${active.kadence === true ? 'uich_activated' : ''}`}>
                                <div className='uich_plugin_info'>
                                    <div className='uich_setting_icon'>
                                        <img src={plugin_url + 'assets/images/kadence.png'} alt="nexter" />
                                    </div>
                                    <span className='uich_plugin_icon_text'>{__('Kadence', 'uichemy')}</span>
                                    <span className='uich_alert_red_icon'>
                                        <p>{__('Beta', 'uichemy')}</p>
                                    </span>
                                </div>
                                {active.findPlugin.includes('Kadence Blocks – Gutenberg Blocks for Page Builder Features') ? 
                                    active.kadence === true ?
                                        <div className='uich_activated_label'>
                                            <span className='uich_check_green_icon'>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="12" fill="none" viewBox="0 0 14 12"><path stroke="#00A31B" strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.5" d="M1.545 7.638s1.228 0 2.864 2.864c0 0 4.548-7.5 8.591-9" /></svg>
                                            </span>
                                            <span>{__('Activated', 'uichemy')}</span>
                                        </div> :
                                        ( <button className='uich_secondary_button' onClick={(e) => nexterBlockInstall(e, setIsEnabled, 'kadence-blocks')}>{__('Activate', 'uichemy')}</button> ) : (
                                            <button className='uich_secondary_button' onClick={(e) => nexterBlockInstall(e, setIsEnabled , 'kadence-blocks')}>{__('Install & Activate', 'uichemy')}</button> )
                                        
                                    }
                            </div>
                            
                            <div className={`uich_plugin_item ${active.generateblocks === true ? 'uich_activated' : ''}`}>
                                <div className='uich_plugin_info'>
                                    <div className='uich_setting_icon'>
                                        <img src={plugin_url + 'assets/images/generateblocks.png'} alt="nexter" />
                                    </div>
                                    <span className='uich_plugin_icon_text'>{__('GenerateBlocks', 'uichemy')}</span>
                                    <span className='uich_alert_red_icon'>
                                        <p>{__('Beta', 'uichemy')}</p>
                                    </span>
                                </div>
                                {active.findPlugin.includes('GenerateBlocks') ? 
                                active.generateblocks === true ?
                                    <div className='uich_activated_label'>
                                        <span className='uich_check_green_icon'>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="12" fill="none" viewBox="0 0 14 12"><path stroke="#00A31B" strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.5" d="M1.545 7.638s1.228 0 2.864 2.864c0 0 4.548-7.5 8.591-9" /></svg>
                                        </span>
                                        <span>{__('Activated', 'uichemy')}</span>
                                    </div> :
                                    ( <button className='uich_secondary_button' onClick={(e) => nexterBlockInstall(e, setIsEnabled, 'ultimate-addons-for-gutenberg')}>{__('Activate', 'uichemy')}</button> ) : (
                                        <button className='uich_secondary_button' onClick={(e) => nexterBlockInstall(e, setIsEnabled , 'ultimate-addons-for-gutenberg')}>{__('Install & Activate', 'uichemy')}</button> )
                                }
                            </div>
                                
                        </>
                    )}

                    {/* Bricks Content */}
                    {activeTab === 'Bricks' && (
                        <>
                            <h3>{__('Install Required Plugins', 'uichemy')}</h3>
                            <div className='uich_plugin_item'>
                                <div className='uich_plugin_info'>
                                    <div className='uich_plugin_icon uichmy'>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 14 14"><path fill="#000" d="m8.225 7.461-.01 2.85s-2.408.14-2.408-1.715V3.868A3.432 3.432 0 0 0 2.375.438H.503V9.86a3.815 3.815 0 0 0 3.818 3.816 4.2 4.2 0 0 0 .778 0h4.56a3.868 3.868 0 0 0 3.867-3.868V7.463l-5.3-.002Zm4.842 2.345a3.411 3.411 0 0 1-3.404 3.405H7.498a2.96 2.96 0 0 0 .598-.646c.513-.757.578-1.603.578-2.255l.01-2.39h4.383v1.886Z" /><path fill="#000" d="M11.311.438a3.088 3.088 0 0 0-3.087 3.086v1.851h5.305V.437H11.31Z" /></svg>
                                    </div>
                                    <span className='uich_plugin_icon_text'>{__('Figma to Bricks | UiChemy', 'uichemy')}</span>
                                </div>
                                <a href="https://www.figma.com/community/plugin/1344313361212431142/uichemy-convert-figma-to-bricks-page-builder-wordpress" target="_blank" rel="noopener noreferrer" className='uich_link_button'>
                                    <span>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 14 14"><path fill="#4B22CC" d="m1.877 1.879.353.353-.353-.354Zm5.122 11.123v.5-.5ZM1.877 12.123l-.354.354.354-.354ZM6.4 1.5a.5.5 0 0 0-.002-1l.002 1Zm7.1 6.102a.5.5 0 0 0-1-.002l1 .002Zm-.805-6.269.365-.34-.365.34ZM8.647 4.646a.5.5 0 1 0 .705.71l-.705-.71Zm3.705-3.495-.087.493.087-.493ZM9.666.5a.5.5 0 1 0 0 1v-1ZM12.5 4.334a.5.5 0 0 0 1 0h-1Zm.349-2.686.492-.087-.492.087ZM.998 7h.5c0-1.429.001-2.45.105-3.226.103-.762.296-1.212.627-1.543l-.353-.354-.354-.353C.976 2.073.73 2.769.613 3.642.496 4.5.497 5.6.497 7h.5Zm6.001 6.001v-.5c-1.428 0-2.45-.001-3.226-.105-.761-.103-1.212-.296-1.543-.627l-.353.353-.354.354c.548.547 1.245.793 2.117.91.858.116 1.959.115 3.36.115v-.5ZM.998 7.001h-.5c0 1.4-.001 2.5.114 3.359.118.872.364 1.569.911 2.117l.354-.354.353-.353c-.33-.332-.524-.782-.627-1.543C1.5 9.45 1.498 8.429 1.498 7h-.5Zm6.001 6.001v.5c1.4 0 2.501.001 3.36-.114.872-.118 1.568-.364 2.116-.911l-.354-.354-.353-.353c-.331.33-.781.524-1.543.627-.776.104-1.797.105-3.226.105v.5ZM6.4 1V.5C5.188.502 4.221.517 3.45.642c-.786.127-1.42.376-1.928.883l.354.354.353.353c.308-.307.717-.496 1.38-.603.679-.11 1.565-.127 2.79-.129V1ZM13 7.6h-.5c-.002 1.225-.02 2.111-.13 2.79-.106.663-.295 1.072-.602 1.38l.353.353.354.354c.507-.508.756-1.143.883-1.928.125-.77.14-1.739.142-2.947L13 7.6Zm-.305-6.267-.353-.355-3.695 3.668L9 5l.353.355 3.695-3.668-.352-.355Zm-.343-.182L12.44.66c-.467-.083-1.172-.121-1.734-.14A35.872 35.872 0 0 0 9.668.5h-.001v1h.004a10.554 10.554 0 0 0 .073 0 30.94 30.94 0 0 1 .928.019c.566.019 1.205.056 1.593.125l.087-.493ZM13 4.334h.5V4.25a31.585 31.585 0 0 0-.02-.956c-.018-.562-.056-1.267-.139-1.734l-.492.087-.493.087c.069.389.106 1.027.125 1.594a35.536 35.536 0 0 1 .019.927V4.333h.5Zm-.648-3.183-.087.493a.11.11 0 0 1 .064.03l.366-.34.365-.342a1.108 1.108 0 0 0-.62-.333l-.088.492Zm.343.182-.366.342c.01.01.022.028.027.06l.493-.087.492-.087a1.113 1.113 0 0 0-.28-.569l-.366.341Z" /></svg>
                                    </span>
                                    <span>{__('Install in Figma', 'uichemy')}</span>
                                </a>
                            </div>

                            <h3>{__('Recommended Settings', 'uichemy')}</h3>
                            <div className={`uich_setting_item ${active.bricksFileLoad === "Bricks" ? 'uich_activated' : ''}`}>
                                <div className='uich_setting_info'>
                                    <div className='uich_plugin_icon uichmy'>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="11" height="13" fill="none" viewBox="0 0 11 13"><path fill="#212121" d="m3.33.145.215.133v3.724c.756-.5 1.592-.75 2.509-.75 1.321 0 2.416.461 3.285 1.384.857.922 1.286 2.06 1.286 3.41 0 1.357-.431 2.494-1.295 3.411-.869.923-1.96 1.384-3.276 1.384-1.15 0-2.131-.41-2.947-1.232v1.009H.714V.439L3.33.145ZM5.598 5.67c-.63 0-1.157.215-1.58.643-.423.44-.634 1.018-.634 1.732 0 .715.211 1.289.634 1.724.417.434.943.651 1.58.651.673 0 1.218-.226 1.634-.678.41-.447.616-1.012.616-1.697 0-.684-.208-1.253-.625-1.705-.416-.447-.958-.67-1.625-.67Z" /></svg>
                                    </div>
                                    <span className='uich_plugin_icon_text'>{__('Bricks Page Builder', 'uichemy')}</span>
                                    {active.bricksFileLoad === "Bricks" ? '' :
                                        <span className='uich_alert_full_stop'>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" viewBox="0 0 12 12"><path fill="#FF1E1E" d="M5.625.002A5.627 5.627 0 0 0 0 5.627a5.627 5.627 0 0 0 5.625 5.625 5.627 5.627 0 0 0 5.625-5.625A5.627 5.627 0 0 0 5.625.002Zm0 9a.752.752 0 0 1-.75-.75c0-.413.337-.75.75-.75.412 0 .75.337.75.75 0 .412-.338.75-.75.75Zm.859-5.816-.252 3.007a.609.609 0 0 1-1.215 0l-.25-3.007a.861.861 0 1 1 1.72-.072c0 .023 0 .05-.003.072Z" /></svg>
                                        </span>}
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
                                    <a className='uich_secondary_button' href="https://bricksbuilder.io/pricing/" target="_blank" rel="noopener noreferrer">
                                        <span>{__('Install', 'uichemy')}</span>
                                        <span><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 16 16"><path stroke="#4B22CC" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2" d="M10 2h4m0 0v4m0-4L6.667 9.333M12 8.667v4A1.334 1.334 0 0 1 10.667 14H3.333A1.334 1.334 0 0 1 2 12.667V5.333A1.333 1.333 0 0 1 3.333 4h4" /></svg></span>
                                    </a>
                                }
                            </div>

                            <div className={`uich_setting_item ${(active.bricksSvgLoad === true && active.bricksFileLoad === "Bricks") ? 'uich_activated' : ''}`}>
                                <div className='uich_setting_info'>
                                    <div className='uich_setting_icon wp'>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 14 14"><path fill="#fff" d="M7 .002c3.857 0 7 3.143 7 7 0 3.864-3.143 7-7 7-3.864 0-7-3.136-7-7 0-3.857 3.136-7 7-7ZM5.397 12.994a5.51 5.51 0 0 0 1.603.23c.665 0 1.295-.11 1.897-.314L7.091 8.024c-.56 1.652-1.127 3.304-1.694 4.97ZM1.281 4.488C.938 5.265.777 6.091.777 7.001c0 2.471 1.408 4.655 3.494 5.663C3.27 9.941 2.275 7.211 1.28 4.488Zm11.207-.462c.217 1.666-.133 2.528-.3 2.976-.687 1.806-1.367 3.632-2.045 5.445 1.868-1.078 3.08-3.142 3.08-5.445 0-1.078-.245-2.073-.735-2.976ZM7 .78a6.21 6.21 0 0 0-5.229 2.815c.147.007.28.007.406.007.64 0 1.648-.077 1.659-.078.35-.014.392.736.042.778-.009 0-.438.042-.82.055l2.388 6.406L6.713 6.68 5.83 4.294l-.644-.057c-.35-.014-.308-.874.042-.853a26.1 26.1 0 0 0 1.631.084c.658 0 1.666-.084 1.666-.084.35-.02.392.818.042.853 0 0-.196.105-.574.12l2.121 5.859s.98-2.457.98-3.5c0-.77-.14-1.296-.385-1.723-.315-.518-.63-.952-.63-1.47 0-.832.68-1.182 1.155-1.105A6.222 6.222 0 0 0 7 .779Z" /></svg>
                                    </div>
                                    <span className='uich_plugin_icon_text'>{__('SVG Uploads', 'uichemy')}</span>
                                    {(active.bricksSvgLoad === true && active.bricksFileLoad === "Bricks") ? '' :
                                        <span className='uich_alert_full_stop'>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" viewBox="0 0 12 12"><path fill="#FF1E1E" d="M5.625.002A5.627 5.627 0 0 0 0 5.627a5.627 5.627 0 0 0 5.625 5.625 5.627 5.627 0 0 0 5.625-5.625A5.627 5.627 0 0 0 5.625.002Zm0 9a.752.752 0 0 1-.75-.75c0-.413.337-.75.75-.75.412 0 .75.337.75.75 0 .412-.338.75-.75.75Zm.859-5.816-.252 3.007a.609.609 0 0 1-1.215 0l-.25-3.007a.861.861 0 1 1 1.72-.072c0 .023 0 .05-.003.072Z" /></svg>
                                        </span>}
                                </div>
                                {(active.bricksSvgLoad === true && active.bricksFileLoad === "Bricks") ?
                                    <div className='uich_activated_label'>
                                        <span className='uich_check_green_icon'>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="12" fill="none" viewBox="0 0 14 12"><path stroke="#00A31B" strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.5" d="M1.545 7.638s1.228 0 2.864 2.864c0 0 4.548-7.5 8.591-9" /></svg>
                                        </span>
                                        <span>{__('Activated', 'uichemy')}</span>
                                    </div> :
                                    <button className='uich_secondary_button' onClick={(e) => bricksSvgUploads(e, setIsEnabled)}>{__('Activate', 'uichemy')}</button>
                                }
                            </div>
                        </>
                    )}
                </div>
            </div>
        )
    }

    const HowItWorksComponent = () => {

        const steps = [
            {
                title: "Install UiChemy Figma Plugin",
                description: "Convert Figma Designs to Elementor Websites and edit in WordPress. Our tool makes it easy to transfer all your design content from Figma to Elementor Website easily.",
                buttonText: "Next Step"
            },
            {
                title: "Connect to Use Import using Security Token",
                description: "Connect Figma Plugin to UiChemy Website and edit in WordPress. Our tool makes it easy to transfer all your design content from Figma to Elementor Website easily.",
                buttonText: "Next Step"
            },
            {
                title: "Import in Elementor",
                description: "Convert Figma Designs to Elementor Websites and edit in WordPress. Our tool makes it easy to transfer all your design content from Figma to Elementor Website easily.",
                buttonText: "Go to First Step"
            }
        ];

        const handleNextStep = () => {
            if (currentStep === steps.length - 1) {
                setCurrentStep(0);
            } else {
                setCurrentStep(currentStep + 1);
            }
        };

        return (
            <div className="uich_how_it_works">
                <div className="uich_section uich_second_box_height">
                    <div className="uich_section_header">
                        <h3>{__('How Does It Work?', 'uichemy')}</h3>
                        <a href="https://youtu.be/8_6DymM-5KQ?si=YdyU6U-ASiWMzHkb" target="_blank" rel="noopener noreferrer" className="uich_video_link">
                            <span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="13" fill="none" viewBox="0 0 18 13">
                                    <path fill="red" d="M8.993.168c.027 0 5.627 0 7.02.38a2.264 2.264 0 0 1 1.593 1.595c.595 2.393.458 6.174.012 8.66a2.264 2.264 0 0 1-1.593 1.592c-1.392.38-6.97.382-7.02.382 0 0-5.623 0-7.02-.381A2.265 2.265 0 0 1 .39 10.803C-.207 8.42-.044 4.637.379 2.154A2.265 2.265 0 0 1 1.973.561C3.37.18 8.993.168 8.993.168Zm-1.79 9.007 4.665-2.702L7.203 3.77v5.404Z" />
                                </svg>
                            </span>
                            <span>{__('Watch Video', 'uichemy')}</span>
                        </a>
                    </div>
                    <div className="uich_img_page_count">
                        <span>{currentStep + 1}/3</span>
                    </div>

                    {currentStep === 0 && (
                        <div className="uich_video_preview">
                            <div className="uich_video_icon">
                                <img src={plugin_url + 'assets/images/howtowork-1.png'} alt="Video Preview" />
                            </div>
                        </div>
                    )}

                    {currentStep === 1 && (
                        <div className="uich_video_preview">
                            <div className="uich_video_icon">
                                <img src={plugin_url + 'assets/images/howtowork-2.png'} alt="Video Preview" />
                            </div>
                        </div>
                    )}

                    {currentStep === 2 && (
                        <div className="uich_video_preview">
                            <div className="uich_video_icon">
                                <img src={plugin_url + 'assets/images/howtowork-3.png'} alt="Video Preview" />
                            </div>
                        </div>
                    )}
                    <div className="uich_img_active_page">
                        {[0, 1, 2].map((step) => (
                            <div key={step} className={`uich_img_page_dot ${currentStep === step ? "active_img" : ""}`}
                            />
                        ))}
                    </div>

                    <div className="uich_install_info">
                        <div className="uich_install_info_content">
                            <h4>{steps[currentStep].title}</h4>
                            <p>{steps[currentStep].description}</p>
                        </div>
                        <div className="uich_install_info_button">
                            <button
                                className="uich_secondary_button uich_sm"
                                onClick={handleNextStep}
                            >
                                {steps[currentStep].buttonText}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        );
    };

    const copyToClipboard = (content) => {
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(content);
        } else {
            const tempInput = document.createElement("input");
            tempInput.value = content;
            document.body.appendChild(tempInput);
            tempInput.select();
            document.execCommand("copy");
            document.body.removeChild(tempInput);
        }
    };

    const useCopyToClipboard = (setCopiedState) => {
        const copy = (content) => {
            copyToClipboard(content);
            setCopiedState(true);

            setTimeout(() => {
                setCopiedState(false);
            }, 1500);
        };

        return copy;
    };

    const UichTokenPanel = () => {

        const copyUrl = useCopyToClipboard(setUrlCopied);
        const copyToken = useCopyToClipboard(setTokenCopied);

        var user_info = uich_ajax_object.dashData.userData;

        const maskToken = (token) => {
            if (!token) return '';
        
            const parts = token.split('-');
            const maskedParts = parts.map((part, index) => {
                if (index === parts.length - 1) {
                    return part;
                }
                return 'X'.repeat(part.length);
            });
        
            return maskedParts.join('-');
        };

        const handleChange = (e) => {
            setSelectedUser(e.target.value);
        };

        const handleUrlCopy = (e) => {
            e.preventDefault();
            copyUrl(user_info.siteUrl);
        };

        const handleTokenCopy = (e) => {
            e.preventDefault();
            const tokenInput = document.getElementById("uichemy-token-input");
            const originalTokenValue = tokenInput.getAttribute('data-original-token');

            if (originalTokenValue) {
                copyToken(originalTokenValue);
                setOriginalToken(originalTokenValue);
            }
        };

        return (
            <div className='uich_section uich_first_box_height uich_field_group_gap' style={ activeTab === 'Elementor' ? { height: '820px' } : { height: '730px' }}>
                <div className='uich_field_group uich_field_group_image_wrapper'>
                    <img className='uich_field_group_image' src={plugin_url + 'assets/images/security.png'} alt="Logo" />
                </div>
                <div className='uich_field_group'>
                    <label>{__('Site URL', 'uichemy')}</label>
                    <div className='uich_input_with_copy'>
                        <input readOnly id="uichemy-site-url-input" name="SiteURL" type="url" value={user_info.siteUrl} />
                        <button className='uich_copy_btn' id="uichemy-url-copy-btn" onClick={handleUrlCopy}>
                            <span className={`copy-icon ${urlCopied ? 'hidden' : ''}`}>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="25" fill="none" viewBox="0 0 24 25">
                                    <rect width="24" height="24" y=".736" fill="#F9F9FB" rx="6.782" /><g stroke="#737373" strokeLinecap="round" strokeLinejoin="round" clipPath="url(#url-copy-a)">
                                        <path d="M16.521 11.041h-5.086a1.13 1.13 0 0 0-1.13 1.13v5.087c0 .624.506 1.13 1.13 1.13h5.086a1.13 1.13 0 0 0 1.13-1.13V12.17a1.13 1.13 0 0 0-1.13-1.13Z" />
                                        <path d="M8.044 14.433h-.565a1.13 1.13 0 0 1-1.13-1.13V8.215a1.13 1.13 0 0 1 1.13-1.13h5.086a1.13 1.13 0 0 1 1.13 1.13v.565" />
                                    </g><defs><clipPath id="url-copy-a"><path fill="#fff" d="M5.218 5.955h13.563v13.563H5.218z" />
                                    </clipPath></defs>
                                </svg>
                            </span>
                            <span className={`done-icon ${urlCopied ? '' : 'hidden'}`}>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                    <path stroke="#737373" strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.5" d="M5 14.5s1.5 0 3.5 3.5c0 0 5.559-9.167 10.5-11" />
                                </svg>
                            </span>
                        </button>
                    </div>
                </div>

                <div className='uich_field_group'>
                    <label>{__('Security Token', 'uichemy')}</label>
                    <div className='uich_input_with_copy'>
                        <input readOnly id="uichemy-token-input" name="Security" type="text" value={maskToken(originalToken)} data-original-token={originalToken} />
                        <button className='uich_copy_btn' id="uichemy-token-copy-btn" onClick={handleTokenCopy}>
                            <span className={`copy-icon ${tokenCopied ? 'hidden' : ''}`}>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="25" fill="none" viewBox="0 0 24 25">
                                    <rect width="24" height="24" y=".736" fill="#F9F9FB" rx="6.782" />
                                    <g stroke="#737373" strokeLinecap="round" strokeLinejoin="round" clipPath="url(#token-copy-b)">
                                        <path d="M16.521 11.041h-5.086a1.13 1.13 0 0 0-1.13 1.13v5.087c0 .624.506 1.13 1.13 1.13h5.086a1.13 1.13 0 0 0 1.13-1.13V12.17a1.13 1.13 0 0 0-1.13-1.13Z" />
                                        <path d="M8.044 14.433h-.565a1.13 1.13 0 0 1-1.13-1.13V8.215a1.13 1.13 0 0 1 1.13-1.13h5.086a1.13 1.13 0 0 1 1.13 1.13v.565" />
                                    </g>
                                    <defs>
                                        <clipPath id="token-copy-b">
                                            <path fill="#fff" d="M5.218 5.955h13.563v13.563H5.218z" />
                                        </clipPath>
                                    </defs>
                                </svg>
                            </span>
                            <span className={`done-icon ${tokenCopied ? '' : 'hidden'}`}>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                    <path stroke="#737373" strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.5" d="M5 14.5s1.5 0 3.5 3.5c0 0 5.559-9.167 10.5-11" />
                                </svg>
                            </span>
                        </button>
                    </div>
                </div>

                <div className='uich_field_group_button'>
                    <button type="button" id="uichemy-regenerate-btn" className='uich_primary_button uich_full_width'>
                        <span>{__('Regenerate Token', 'uichemy')}</span>
                        <span style={{ display: "none" }}>
                            <div className="uich-loader"></div>
                        </span>
                    </button>
                </div>

                <div className='uich_import_as_user'>
                    <span>{__('Import as User', 'uichemy')}</span>
                    <select value={selectedUser} onChange={handleChange} id="uichemy-user-select" className='uich_dropdown'>
                        {uich_ajax_object.dashData.adminUsername.map((user, index) => (
                            <option key={index} value={user}>{user}</option>
                        ))}
                    </select>
                </div>
            </div>
        );
    };

    const handlePopupClose = () => {
        data_save();
    };

    const handleGetStarted = () => {
        setShowPopup(false);
        setIsOnboardingVisible(true);
        localStorage.setItem('uich_popup_shown', 'true');
    };

    return (
        <>
            <Router>
                {!isInitialized ? '' : (showPopup || isOnboardingVisible) ? (<Onboarding onComplete={completeOnboarding}
                    dataSave={data_save}
                    isChecked={onboardingIsChecked}
                    setIsChecked={setOnboardingIsChecked}
                />) : (
                    <div className='uich_dashboard_main_cover'>
                        <div className='uich_main_cover'>
                            <div className='uich_main_header'>
                                {WelcomeHeaderBanner()}
                            </div>
                            <div className='uich_main_content'>
                                <div className='uich_left_content'>
                                    {UichTokenPanel()}
                                </div>
                                <div className='uich_right_content'>
                                    {PageBuilderSelector()}
                                </div>
                            </div>
                            <div className='uich_main_content'>
                                <div className='uich_left_content'>
                                    {HowItWorksComponent()}
                                </div>
                                <div className='uich_right_content'>
                                    {AllResources()}
                                </div>
                            </div>
                            <div className='uich_main_content'>
                                <div className='uich_left_content'>
                                    {FrequentlySection()}
                                </div>
                                <div className='uich_right_content'>
                                    {OurProducts()}
                                </div>
                            </div>
                        </div>
                    </div>
                )}
            </Router>

            {showPopup && (
                <UichPopup onClose={handlePopupClose} onGetStarted={handleGetStarted} />
            )}
        </>
    );
};

export default Dashboard;