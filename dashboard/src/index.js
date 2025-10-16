import App from "./App";
import './style/main.scss';

import domReady from '@wordpress/dom-ready';
import { createRoot } from '@wordpress/element';


domReady( () => {
    const root = createRoot( document.getElementById( 'uich-dash' ) );
    root.render( <App /> );
} );