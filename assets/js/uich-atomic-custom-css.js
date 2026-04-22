/**
 * UiChemy: Custom CSS for Elementor v4 Atomic Widgets
 *
 * Adds "UiChemy : Custom CSS" section in the atomic widget Style tab
 * using Elementor's native custom_css field via useCustomCss() hook.
 *
 * @since 4.7.4
 */
( function () {
	'use strict';

	var React          = window.React;
	var editingPanel   = window.elementorV2 && window.elementorV2.editorEditingPanel;
	var editorControls = window.elementorV2 && window.elementorV2.editorControls;
	var i18n           = window.wp && window.wp.i18n;

	if ( ! editingPanel || ! React || ! i18n ) {
		return;
	}

	var createElement      = React.createElement;
	var useState           = React.useState;
	var useEffect          = React.useEffect;
	var useMemo            = React.useMemo;
	var useRef             = React.useRef;
	var useCallback        = React.useCallback;
	var __                 = i18n.__;
	var injectIntoStyleTab = editingPanel.injectIntoStyleTab;
	var useCustomCss       = editingPanel.useCustomCss;
	var useStyle           = editingPanel.useStyle;
	var StyleTabSection    = editingPanel.StyleTabSection;
	var SectionContent     = editingPanel.SectionContent;

	if ( ! injectIntoStyleTab || ! useCustomCss || ! useStyle || ! StyleTabSection ) {
		return;
	}

	// ── Ace CSS Editor ──────────────────────────────────────────

	function AceCssEditor( props ) {
		var value        = props.value || '';
		var onChange     = props.onChange;
		var containerRef = useRef( null );
		var editorRef    = useRef( null );
		var isInternal   = useRef( false );
		var onChangeRef  = useRef( onChange );
		var debounceRef  = useRef( null );

		onChangeRef.current = onChange;

		useEffect( function () {
			if ( ! containerRef.current || editorRef.current ) return;
			if ( typeof window.ace === 'undefined' ) return;

			var editorEl = document.createElement( 'div' );
			editorEl.style.width = '100%';
			editorEl.style.minHeight = '150px';
			containerRef.current.appendChild( editorEl );

			var aceEditor = window.ace.edit( editorEl );

			var uiTheme = '';
			try { uiTheme = window.elementor.settings.editorPreferences.model.get( 'ui_theme' ); } catch ( e ) {}
			var dark = window.matchMedia( '(prefers-color-scheme: dark)' ).matches;
			if ( uiTheme === 'dark' || ( uiTheme === 'auto' && dark ) ) {
				aceEditor.setTheme( 'ace/theme/merbivore_soft' );
			}

			aceEditor.setOptions( {
				mode: 'ace/mode/css',
				minLines: 5,
				maxLines: Infinity,
				showGutter: true,
				useWorker: false,
				enableBasicAutocompletion: true,
				enableLiveAutocompletion: true,
				fontSize: 13,
				showPrintMargin: false,
				tabSize: 4,
				useSoftTabs: false,
			} );

			aceEditor.getSession().setUseWrapMode( true );
			aceEditor.setValue( value, -1 );

			aceEditor.on( 'change', function () {
				isInternal.current = true;
				if ( debounceRef.current ) clearTimeout( debounceRef.current );
				debounceRef.current = setTimeout( function () {
					if ( onChangeRef.current ) {
						onChangeRef.current( aceEditor.getValue() );
					}
				}, 500 );
			} );

			editorRef.current = aceEditor;

			return function () {
				if ( debounceRef.current ) clearTimeout( debounceRef.current );
				if ( editorRef.current ) {
					editorRef.current.destroy();
					editorRef.current = null;
				}
				if ( containerRef.current ) containerRef.current.innerHTML = '';
			};
		}, [] );

		useEffect( function () {
			if ( editorRef.current && ! isInternal.current ) {
				if ( editorRef.current.getValue() !== value ) {
					editorRef.current.setValue( value, -1 );
				}
			}
			isInternal.current = false;
		}, [ value ] );

		return createElement( 'div', { ref: containerRef, className: 'uich-atomic-css-editor' } );
	}

	// ── Custom CSS Content ──────────────────────────────────────

	function CustomCssContent() {
		var styleCtx     = useStyle();
		var id           = styleCtx.id;
		var meta         = styleCtx.meta;
		var cssCtx       = useCustomCss();
		var customCss    = cssCtx.customCss;
		var setCustomCss = cssCtx.setCustomCss;

		var metaKey = ( meta.breakpoint || 'desktop' ) + '-' + ( meta.state || 'default' ) + '-' + id;

		var localStatesRef = useRef( {} );
		var _forceState    = useState( 0 );
		var forceUpdate    = _forceState[1];

		useEffect( function () {
			if ( ! localStatesRef.current[ metaKey ] ) {
				localStatesRef.current[ metaKey ] = {
					value: customCss && customCss.raw ? customCss.raw : '',
				};
				forceUpdate( function ( n ) { return n + 1; } );
			}
		}, [ metaKey ] );

		var currentLocal = useMemo( function () {
			return localStatesRef.current[ metaKey ] || {
				value: customCss && customCss.raw ? customCss.raw : '',
			};
		}, [ metaKey, customCss, _forceState[0] ] );

		var handleChange = useCallback( function ( value ) {
			localStatesRef.current[ metaKey ] = { value: value };
			forceUpdate( function ( n ) { return n + 1; } );
			setCustomCss( value, {
				history: { propDisplayName: 'Custom CSS' },
			} );
		}, [ metaKey, setCustomCss ] );

		var labelEl = null;
		if ( editorControls && editorControls.ControlFormLabel ) {
			labelEl = createElement(
				'div',
				{ style: { display: 'flex', alignItems: 'center', gap: '4px', marginBottom: '6px' } },
				createElement( editorControls.ControlFormLabel, null, __( 'CSS Code', 'uichemy' ) ),
				editorControls.ControlAdornments ? createElement( editorControls.ControlAdornments, null ) : null
			);
		} else {
			labelEl = createElement(
				'label',
				{ style: { fontSize: '12px', color: '#9da5ae', marginBottom: '6px', display: 'block' } },
				__( 'CSS Code', 'uichemy' )
			);
		}

		return createElement( SectionContent || 'div', { gap: 1 },
			labelEl,
			createElement( AceCssEditor, {
				key: metaKey,
				value: currentLocal.value,
				onChange: handleChange,
			} )
		);
	}

	// ── Section wrapper ─────────────────────────────────────────

	function UichCustomCssSection() {
		return createElement( StyleTabSection, {
			section: {
				component: CustomCssContent,
				name: 'UiChemy : Custom CSS',
				title: __( 'UiChemy : Custom CSS', 'uichemy' ),
			},
			fields: [ 'custom_css' ],
			unmountOnExit: false,
		} );
	}

	// ── Inject ──────────────────────────────────────────────────

	injectIntoStyleTab( {
		id: 'uichemy-custom-css',
		component: UichCustomCssSection,
	} );

} )();
