/**
 * Phone Flag Field for Elementor — Frontend JS
 * Fixed: dial code submission + flag display + absolute flag paths
 */
( function( $ ) {
    'use strict';

    var itiInstances = [];

    // -------------------------------------------------------
    // Set flag image paths dynamically using absolute URLs
    // passed from PHP via wp_localize_script
    // -------------------------------------------------------
    if ( typeof epffSettings !== 'undefined' && epffSettings.flagsUrl ) {
        document.documentElement.style.setProperty(
            '--iti-path-flags-1x', 'url("' + epffSettings.flagsUrl + '")'
        );
        document.documentElement.style.setProperty(
            '--iti-path-flags-2x', 'url("' + epffSettings.flags2xUrl + '")'
        );
    }

    function initPhoneFlagFields() {

        // Target all tel inputs inside Elementor forms
        var telFields = document.querySelectorAll(
            '.elementor-field-type-tel input[type="tel"], ' +
            '.elementor-field-group input[type="tel"]'
        );

        if ( ! telFields.length ) return;

        telFields.forEach( function( input ) {

            // Skip if already initialized
            if ( input.dataset.epffInit ) return;
            input.dataset.epffInit = 'true';

            var options = {
                utilsScript:       epffSettings.utilsScript,
                separateDialCode:  true,
                preferredCountries: [ epffSettings.defaultCountry || 'us' ],
                initialCountry:    epffSettings.autoDetect ? 'auto' : ( epffSettings.defaultCountry || 'us' ),
            };

            // Auto-detect country via IP
            if ( epffSettings.autoDetect ) {
                options.geoIpLookup = function( success ) {
                    fetch( 'https://ipapi.co/json/' )
                        .then( function( res ) { return res.json(); } )
                        .then( function( data ) {
                            success( data && data.country_code
                                ? data.country_code.toLowerCase()
                                : ( epffSettings.defaultCountry || 'us' )
                            );
                        } )
                        .catch( function() {
                            success( epffSettings.defaultCountry || 'us' );
                        } );
                };
            }

            // Country restrictions
            if ( epffSettings.allowedCountries && epffSettings.allowedCountries.length ) {
                options.onlyCountries = epffSettings.allowedCountries;
            }
            if ( epffSettings.excludedCountries && epffSettings.excludedCountries.length ) {
                options.excludeCountries = epffSettings.excludedCountries;
            }

            // Initialize intl-tel-input
            var iti = window.intlTelInput( input, options );
            itiInstances.push( { input: input, iti: iti } );

            // -------------------------------------------------------
            // FIX: Before submit, prepend dial code directly to value
            // This is the most reliable method for Elementor forms
            // -------------------------------------------------------
            var form = input.closest( 'form' );
            if ( ! form ) return;

            // Handle both standard and Elementor AJAX submit
            // useCapture=true so it fires BEFORE Elementor reads the value
            form.addEventListener( 'submit', function() {
                injectDialCode( input, iti );
            }, true );

            // Also hook into Elementor's jQuery submit event
            $( form ).on( 'submit', function() {
                injectDialCode( input, iti );
            } );

        } );
    }

    /**
     * Prepend the dial code to the input value before form submits.
     * e.g. "1796281914" becomes "+8801796281914"
     */
    function injectDialCode( input, iti ) {
        var countryData = iti.getSelectedCountryData();
        if ( ! countryData || ! countryData.dialCode ) return;

        var dialCode  = '+' + countryData.dialCode;
        var rawNumber = input.value.trim();

        // Avoid double-prepending if already has a dial code
        if ( rawNumber.indexOf( '+' ) === 0 ) return;

        // Set the value directly — Elementor reads this on submit
        input.value = dialCode + rawNumber;
    }

    // -------------------------------------------------------
    // Run on DOM ready
    // -------------------------------------------------------
    $( document ).ready( function() {
        initPhoneFlagFields();
    } );

    // Re-run when Elementor popups open
    $( document ).on( 'elementor/popup/show', function() {
        setTimeout( initPhoneFlagFields, 300 );
    } );

    // Re-run when Elementor frontend re-renders
    $( window ).on( 'elementor/frontend/init', function() {
        initPhoneFlagFields();
    } );

} )( jQuery );