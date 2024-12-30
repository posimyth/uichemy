( function( $ ) {
    "use strict";
    elementor.hooks.addFilter( 'editor/style/styleText', function( css, context ) {
        if ( ! context ) {
            return;
        }

        var model = context.model,
            customCSS = model.get('settings').get('uich_custom_css_field'),
            selector = '.elementor-element.elementor-element-' + model.get('id');
        
        if ( 'document' === model.get('elType') ) {
            selector = elementor.config.document.settings.cssWrapperSelector;
        }
       
        if ( customCSS ) {
            css += customCSS.replace(/selector/g, selector);
        }

        return css;
    });

}( jQuery ) );