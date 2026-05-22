import React, { useState, useEffect } from 'react';

const { __ } = wp.i18n;

// ─── Helpers ────────────────────────────────────────────────────────────────

const getMcpData = () => {
    const dash        = uich_ajax_object?.dashData ?? {};
    const storedToken = dash.siteToken ?? '';
    const hasToken    = storedToken !== '';
    const isEnabled   = dash.mcpEnabled === true || dash.mcpEnabled === '1';
    const isActive    = hasToken && isEnabled;

    const siteName  = uich_ajax_object?.blogName ?? document.title ?? 'my-site';
    const siteSlug  = siteName.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '') || 'my-site';
    const configKey = `${siteSlug}-wordpress-uichemy-mcp`;

    const maskedToken = hasToken
        ? `${'X'.repeat(16)}...${storedToken.slice(-3)}`
        : '';

    const mcpUrl = dash.mcpUrl ?? (uich_ajax_object?.rest_url ?? '') + 'uichemy/v1/mcp';

    return { storedToken, hasToken, isEnabled, isActive, configKey, maskedToken, mcpUrl };
};

const TOOLS = [
    { name: 'check_config',          description: 'Checks MCP and Elementor readiness. Returns diagnostic details.' },
    { name: 'get_globals',           description: 'Returns all global colors, typography, and container widths from the active Elementor kit.' },
    { name: 'sync_globals',          description: 'Syncs color and typography changes directly to the active Elementor kit.' },
    { name: 'import_elementor_page', description: 'Imports a converted Elementor JSON from a URL into WordPress as a new page or template.' },
];

// ─── Icons ───────────────────────────────────────────────────────────────────

const McpLogoSvg = () => (
    <svg width="32" height="32" viewBox="0 0 180 180" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
        <g clipPath="url(#mcp-logo-clip)">
            <path d="M18 84.8528L85.8822 16.9706C95.2548 7.59798 110.451 7.59798 119.823 16.9706C129.196 26.3431 129.196 41.5391 119.823 50.9117L68.5581 102.177" stroke="#1F2124" strokeWidth="14" strokeLinecap="round"/>
            <path d="M69.2652 101.47L119.823 50.9117C129.196 41.5391 144.392 41.5391 153.765 50.9117L154.118 51.2652C163.491 60.6378 163.491 75.8338 154.118 85.2063L92.7248 146.6C89.6006 149.724 89.6006 154.789 92.7248 157.913L105.331 170.52" stroke="#1F2124" strokeWidth="14" strokeLinecap="round"/>
            <path d="M102.853 33.9411L52.6482 84.1457C43.2756 93.5183 43.2756 108.714 52.6482 118.087C62.0208 127.459 77.2167 127.459 86.5893 118.087L136.794 67.8822" stroke="#1F2124" strokeWidth="14" strokeLinecap="round"/>
        </g>
        <defs><clipPath id="mcp-logo-clip"><rect width="180" height="180" fill="white"/></clipPath></defs>
    </svg>
);

const CopyIcon = () => (
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
        <rect x="9" y="9" width="13" height="13" rx="2" ry="2"/>
        <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/>
    </svg>
);

const CheckIcon = () => (
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round">
        <polyline points="20 6 9 17 4 12"/>
    </svg>
);

const CodeIcon = () => (
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
        <polyline points="16 18 22 12 16 6"/>
        <polyline points="8 6 2 12 8 18"/>
    </svg>
);

const EyeIcon = ({ open }) => (
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8S1 12 1 12z" />
        <circle cx="12" cy="12" r="3" />
        {!open && <path d="M3 3l18 18" />}
    </svg>
);

const AlertIcon = ({ title }) => (
    <span className="uich-mcp-status-alert" title={title}>
        <svg width="14" height="14" viewBox="0 0 24 24" fill="#EF4444" aria-hidden="true">
            <circle cx="12" cy="12" r="12"/>
            <path d="M11 7h2v7h-2zm0 9h2v2h-2z" fill="white"/>
        </svg>
    </span>
);

// ─── Sub-components ───────────────────────────────────────────────────────────

const StatusCard = ({ isActive, isEnabled, hasToken, onToggle, toggling }) => {
    let indicator, subText;

    if (isActive) {
        indicator = <span className="uich-mcp-dot on" title="Active" />;
        subText   = __('Active: clients can connect', 'uichemy');
    } else if (!hasToken) {
        indicator = <AlertIcon title="No token" />;
        subText   = __('Inactive: no security token found', 'uichemy');
    } else {
        indicator = <AlertIcon title="Disabled" />;
        subText   = __('Disabled: connections blocked', 'uichemy');
    }

    return (
        <div className={`uich-mcp-status-row ${isActive ? 'is-active' : 'is-inactive'}`}>
            <div className="uich-mcp-status-info">
                <div className="uich-mcp-logo-box">
                    <McpLogoSvg />
                </div>
                <div className="uich-mcp-status-text">
                    <span className="uich-mcp-status-name">
                        {__('UiChemy MCP Server', 'uichemy')}
                        {indicator}
                    </span>
                    <span className="uich-mcp-status-sub">{subText}</span>
                </div>
            </div>

            {hasToken && (
                <button
                    className={`uich-mcp-toggle-btn ${isEnabled ? 'deactivate' : 'activate'}`}
                    onClick={onToggle}
                    disabled={toggling}
                >
                    {toggling
                        ? __('Saving…', 'uichemy')
                        : isEnabled
                            ? __('Deactivate', 'uichemy')
                            : __('Activate', 'uichemy')
                    }
                </button>
            )}
        </div>
    );
};

const ConfigBlock = ({ configKey, mcpUrl, storedToken, maskedToken }) => {
    const [copied, setCopied] = useState(false);
    const [licenseKey, setLicenseKey] = useState('');
    const [figmaToken, setFigmaToken] = useState('');
    const [isUnlocked, setIsUnlocked] = useState(false);
    const [showLicenseKey, setShowLicenseKey] = useState(false);
    const [showFigmaToken, setShowFigmaToken] = useState(false);

    const isFormValid = licenseKey.trim() !== '' && figmaToken.trim() !== '';

    const maskSecret = (value) => {
        if (!value) return '';
        if (value.length <= 3) return 'X'.repeat(value.length);
        return `${'X'.repeat(Math.min(16, value.length - 3))}...${value.slice(-3)}`;
    };

    const handleCopy = () => {
        if (!isUnlocked) return;

        const servers = {
            [configKey]: {
                command: 'npx',
                args: ['-y', 'mcp-remote', mcpUrl, '--header', `UiChemy-Security-Token: ${storedToken}`],
            },
            'uichemy-api-mcp': {
                command: 'npx',
                args: [
                    '-y',
                    'mcp-remote',
                    'https://core.uichemy.com/mcp',
                    '--allow-http',
                    '--header',
                    `UiChemy-License-Key: ${licenseKey.trim()}`,
                    '--header',
                    `X-Figma-Token: ${figmaToken.trim()}`,
                ],
            },
        };
        const text = '"mcpServers": ' + JSON.stringify(servers, null, 2);

        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(text);
        } else {
            const tmp = document.createElement('textarea');
            tmp.value = text;
            document.body.appendChild(tmp);
            tmp.select();
            document.execCommand('copy');
            document.body.removeChild(tmp);
        }

        setCopied(true);
        setTimeout(() => setCopied(false), 2000);
    };

    const handleUnlock = () => {
        if (!isFormValid) return;
        setIsUnlocked(true);
    };

    const maskedLicenseKey = maskSecret(licenseKey.trim());
    const maskedFigmaToken = maskSecret(figmaToken.trim());

    const raw = [
        '"mcpServers": {',
        `  "${configKey}": {`,
        '    "command": "npx",',
        '    "args": [',
        '      "-y",',
        '      "mcp-remote",',
        `      "${mcpUrl}",`,
        '      "--header",',
        `      "UiChemy-Security-Token: ${maskedToken}"`,
        '    ]',
        '  },',
        '  "uichemy-api-mcp": {',
        '    "command": "npx",',
        '    "args": [',
        '      "-y",',
        '      "mcp-remote",',
        '      "https://core.uichemy.com/mcp",',
        '      "--allow-http",',
        '      "--header",',
        `      "UiChemy-License-Key: ${maskedLicenseKey}"`,
        '      "--header",',
        `      "X-Figma-Token: ${maskedFigmaToken}"`,
        '    ]',
        '  }',
        '}',
    ].join('\n');

    const highlighted = raw
        .replace(/"([^"]+)":/g, '<span style="color:#9cdcfe;">"$1"</span>:')
        .replace(/: "([^"]+)"/g, ': <span style="color:#ce9178;">"$1"</span>');

    return (
        <div style={{ display: 'flex', flexDirection: 'column', gap: 8 }}>
            <div className="uich-mcp-code-wrap">
                <div className="uich-mcp-code-header">
                    <span className="uich-mcp-code-label">
                        <CodeIcon />
                        claude_desktop_config.json
                    </span>
                    <button
                        type="button"
                        className={`uich-mcp-copy-btn${copied ? ' copied' : ''}${!isUnlocked ? ' is-disabled' : ''}`}
                        onClick={handleCopy}
                        disabled={!isUnlocked}
                        aria-label={__('Copy config to clipboard', 'uichemy')}
                    >
                        {copied ? <CheckIcon /> : <CopyIcon />}
                        <span>{copied ? __('Copied!', 'uichemy') : __('Copy config', 'uichemy')}</span>
                    </button>
                </div>
                <div className="uich-mcp-pre-wrap">
                    <pre
                        className={`uich-mcp-pre${!isUnlocked ? ' is-blurred' : ''}`}
                        dangerouslySetInnerHTML={{ __html: highlighted }}
                    />

                    {!isUnlocked && (
                        <div className="uich-mcp-pre-overlay">
                            <div className="uich-mcp-overlay-card">
                                <h4>{__('Details for MCP config', 'uichemy')}</h4>
                                <p>{__('Enter your UiChemy license key and Figma token to reveal and copy the complete WordPress + API MCP setup.', 'uichemy')}</p>
                                <div className="uich-mcp-input-wrap">
                                    <input
                                        type={showLicenseKey ? 'text' : 'password'}
                                        value={licenseKey}
                                        onChange={(e) => setLicenseKey(e.target.value)}
                                        placeholder={__('Enter UiChemy license key', 'uichemy')}
                                    />
                                    <button
                                        type="button"
                                        className={`uich-mcp-eye-btn${showLicenseKey ? ' is-open' : ''}`}
                                        onClick={() => setShowLicenseKey(prev => !prev)}
                                        aria-label={showLicenseKey ? __('Hide license key', 'uichemy') : __('Show license key', 'uichemy')}
                                    >
                                        <EyeIcon open={showLicenseKey} />
                                    </button>
                                </div>
                                <div className="uich-mcp-input-wrap">
                                    <input
                                        type={showFigmaToken ? 'text' : 'password'}
                                        value={figmaToken}
                                        onChange={(e) => setFigmaToken(e.target.value)}
                                        placeholder={__('Enter Figma token', 'uichemy')}
                                    />
                                    <button
                                        type="button"
                                        className={`uich-mcp-eye-btn${showFigmaToken ? ' is-open' : ''}`}
                                        onClick={() => setShowFigmaToken(prev => !prev)}
                                        aria-label={showFigmaToken ? __('Hide Figma token', 'uichemy') : __('Show Figma token', 'uichemy')}
                                    >
                                        <EyeIcon open={showFigmaToken} />
                                    </button>
                                </div>
                                <p className="uich-mcp-note" style={{ margin: '2px 0 6px' }}>
                                    {__('Need a UiChemy license key ? ', 'uichemy')}
                                    <a  style={{ marginLeft: '2px' }}
                                        href="https://uichemy.com/pricing/?utm_source=wpbackend&utm_medium=dashboard&utm_campaign=uichemy"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                    >
                                        {__('View pricing plans', 'uichemy')}
                                    </a>
                                </p>
                                <button
                                    type="button"
                                    className="uich-mcp-unlock-btn"
                                    disabled={!isFormValid}
                                    onClick={handleUnlock}
                                >
                                    {__('Show Config', 'uichemy')}
                                </button>
                            </div>
                        </div>
                    )}
                </div>
            </div>

            <p className="uich-mcp-note">
                <strong>{__('Note:', 'uichemy')}</strong>{' '}
                {__('The token above is masked. The real token is copied when you click ', 'uichemy')}
                <strong>{__('Copy Config', 'uichemy')}</strong>.
            </p>
        </div>
    );
};

const SetupSteps = ({ configKey, mcpUrl, storedToken, maskedToken }) => (
    <div style={{ display: 'flex', marginTop: 24, flexDirection: 'column', gap: 16 }}>
        <div>
            <h3>{__('Setup Claude Desktop', 'uichemy')}</h3>
            <ol className="uich-mcp-steps">
                <li>{__('Open ', 'uichemy')}<strong>{__('Claude Desktop', 'uichemy')}</strong>.</li>
                <li>
                    {__('Go to ', 'uichemy')}<strong>{__('Settings', 'uichemy')}</strong>
                    {' '}(<kbd>Ctrl</kbd>+<kbd>,</kbd> {__('Windows', 'uichemy')} / <kbd>Cmd</kbd>+<kbd>,</kbd> {__('macOS', 'uichemy')}).
                </li>
                <li>{__('Click ', 'uichemy')}<strong>{__('Developer', 'uichemy')}</strong>{__(' in the sidebar.', 'uichemy')}</li>
                <li>
                    {__('Click ', 'uichemy')}<strong>{__('Edit Config', 'uichemy')}</strong>
                    {__(' and open ', 'uichemy')}
                    <code style={{ background: '#F3F4F6', padding: '2px 5px', borderRadius: 4, fontFamily: 'monospace', fontSize: 12 }}>
                        claude_desktop_config.json
                    </code>.
                </li>
                <li>
                    {__('Paste the config below inside the root ', 'uichemy')}
                    <code style={{ background: '#F3F4F6', padding: '2px 5px', borderRadius: 4, fontFamily: 'monospace', fontSize: 12 }}>
                        {'{ }'}
                    </code>
                    {__(' of the file.', 'uichemy')}
                </li>
                <li>{__('Save and ', 'uichemy')}<strong>{__('restart Claude Desktop', 'uichemy')}</strong>.</li>
            </ol>
        </div>

        <ConfigBlock
            configKey={configKey}
            mcpUrl={mcpUrl}
            storedToken={storedToken}
            maskedToken={maskedToken}
        />

        <div className="uich-mcp-help-box uich-mcp-note">
            <strong>{__('Need help? ', 'uichemy')}</strong>
            <div>
                <a href="https://modelcontextprotocol.io/quickstart/user" target="_blank" rel="noopener noreferrer">
                    {__('MCP User Quickstart', 'uichemy')}
                </a>
                {' · '}
                <a href="https://support.anthropic.com/en/articles/10065433-installing-custom-integrations-on-claude-desktop" target="_blank" rel="noopener noreferrer">
                    {__('Installing MCP in Claude Desktop', 'uichemy')}
                </a>
                {' · '}
                <a href="https://modelcontextprotocol.io/docs/develop/connect-local-servers" target="_blank" rel="noopener noreferrer">
                    {__('Connect Local Servers', 'uichemy')}
                </a>
            </div>
        </div>
    </div>
);

const ToolsTable = () => (
    <div style={{ display: 'flex', flexDirection: 'column', gap: 16 }}>
        <h3>{__('Available Tools', 'uichemy')}</h3>
        <div className="uich-mcp-table-wrap">
            <table className="uich-mcp-table">
                <thead>
                    <tr>
                        <th>{__('Tool Name', 'uichemy')}</th>
                        <th>{__('Description', 'uichemy')}</th>
                    </tr>
                </thead>
                <tbody>
                    {TOOLS.map(t => (
                        <tr key={t.name}>
                            <td><code>{t.name}</code></td>
                            <td>{__(t.description, 'uichemy')}</td>
                        </tr>
                    ))}
                </tbody>
            </table>
        </div>
    </div>
);

// ─── Main export ──────────────────────────────────────────────────────────────

const McpSection = () => {
    const [mcpData, setMcpData] = useState(getMcpData);
    const [toggling, setToggling] = useState(false);

    // Smooth-scroll when hash is #mcp-setup
    useEffect(() => {
        const scroll = () => {
            if (window.location.hash === '#mcp-setup') {
                setTimeout(() => {
                    const el = document.getElementById('mcp-setup');
                    if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }, 300);
            }
        };
        scroll();
        window.addEventListener('hashchange', scroll);
        return () => window.removeEventListener('hashchange', scroll);
    }, []);

    const handleToggle = async () => {
        setToggling(true);
        try {
            const form = new FormData();
            form.append('action', 'uich_mcp_toggle');
            form.append('security', uich_ajax_object.nonce);

            const res  = await fetch(uich_ajax_object.ajax_url, { method: 'POST', body: form });
            const data = await res.json();

            if (data?.success !== false) {
                setMcpData(prev => {
                    const next = { ...prev, isEnabled: !prev.isEnabled };
                    next.isActive = next.hasToken && next.isEnabled;
                    return next;
                });
            }
        } catch (e) {
            console.error('MCP toggle failed', e);
        } finally {
            setToggling(false);
        }
    };

    const { storedToken, hasToken, isEnabled, isActive, configKey, maskedToken, mcpUrl } = mcpData;

    return (
        <>

            {/* Left card: status + setup steps + config */}
            <div className="uich_left_content uich_mcp_left_content">
                <div className='uich_section uich_fourth_box_height'>
                    <div>
                        <h3>{__('UiChemy MCP Server', 'uichemy')}</h3>
                        <p>{__('Manage Elementor globals and import converted designs into WordPress directly from Claude Desktop.', 'uichemy')}</p>
                    </div>

                    <div>
                        <StatusCard
                            isActive={isActive}
                            isEnabled={isEnabled}
                            hasToken={hasToken}
                            onToggle={handleToggle}
                            toggling={toggling}
                        />

                        <SetupSteps
                            configKey={configKey}
                            mcpUrl={mcpUrl}
                            storedToken={storedToken}
                            maskedToken={maskedToken}
                        />
                    </div>
                </div>
            </div>

            {/* Right card: available tools */}
            <div className="uich_right_content uich_mcp_right_content">
                <div className='uich_section uich_fourth_box_height'>
                    <div>
                        <h3>{__('How to Set Up MCP', 'uichemy')}</h3>
                        <p>
                            {__('Follow this quick walkthrough to connect Claude Desktop with your WordPress MCP server correctly.', 'uichemy')}
                        </p>
                    </div>
                    <div>
                        <div className="uich_video_preview uich-mcp-video-wrap">
                            <iframe
                                width="100%"
                                height="317"
                                src="https://www.youtube.com/embed/nHFJJCH_Awk"
                                title="MCP setup flow video"
                                frameBorder="0"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                referrerPolicy="strict-origin-when-cross-origin"
                                allowFullScreen
                                style={{ border: 0 }}
                            />
                        </div>

                        <p className="uich-mcp-note uich-mcp-video-outro">
                            {__('After watching, copy the config from the left panel and restart Claude Desktop to apply it.', 'uichemy')}
                        </p>

                        <p className="uich-mcp-note uich-mcp-video-requirements">
                            <strong>{__('Requirements: ', 'uichemy')}</strong>
                            {__('Elementor (free or Pro) must be installed and active.', 'uichemy')}
                        </p>
                    </div>
                    <div>
                            <h3>{__('What Can the MCP Server Do?', 'uichemy')}</h3>
                            <p>{__('Once connected, Claude can call these tools directly on your WordPress site without leaving the conversation.', 'uichemy')}</p>
                    </div>
                    <ToolsTable />
                </div>
            </div>

        </>
    );
};

export default McpSection;