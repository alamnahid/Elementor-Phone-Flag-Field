/**
 * Phone Flag Field for Elementor — Frontend JS
 * Fixed: dial code submission + flag display + search UI
 */
( function( $ ) {
    'use strict';

    var itiInstances = [];

    // ── Set flag image paths via CSS variables ──────────────────────────
    if ( typeof epffSettings !== 'undefined' && epffSettings.flagsUrl ) {
        document.documentElement.style.setProperty(
            '--iti-path-flags-1x', 'url("' + epffSettings.flagsUrl + '")'
        );
        document.documentElement.style.setProperty(
            '--iti-path-flags-2x', 'url("' + epffSettings.flags2xUrl + '")'
        );
    }

    // ── Fix search field UI after dropdown opens ─────────────────────────
    function fixSearchUI( dropdown ) {
        if ( ! dropdown ) return;

        // Find the search input
        var searchInput = dropdown.querySelector( 'input[type="search"], .iti__search-input' );
        if ( searchInput ) {
            // Change type from "search" to "text" — this removes ALL browser search icons
            searchInput.setAttribute( 'type', 'text' );
            searchInput.style.cssText = [
                'width: 100%',
                'box-sizing: border-box',
                'padding: 9px 40px 9px 10px',
                'border: none',
                'border-bottom: 1px solid #e5e5e5',
                'outline: none',
                'font-size: 13px',
                'background: #ffffff',
                'background-image: none',
            ].join( ' !important;' ) + ' !important;';
        }

        // Find the clear/X button and style it
        var clearBtn = dropdown.querySelector( '.iti__search-input-clear, button[class*="clear"], button[class*="close"]' );
        if ( clearBtn ) {
            clearBtn.style.cssText = [
                'background: transparent',
                'border: none',
                'cursor: pointer',
                'padding: 4px',
                'display: flex',
                'align-items: center',
                'justify-content: center',
                'color: #333333',
                'position: absolute',
                'right: 6px',
                'top: 50%',
                'transform: translateY(-50%)',
            ].join( ' !important;' ) + ' !important;';

            // Replace inner content with a clean black X
            clearBtn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none"><line x1="1" y1="1" x2="13" y2="13" stroke="#333333" stroke-width="2" stroke-linecap="round"/><line x1="13" y1="1" x2="1" y2="13" stroke="#333333" stroke-width="2" stroke-linecap="round"/></svg>';
        }

        // Make the search wrapper use relative positioning for the absolute button
        var searchWrapper = dropdown.querySelector( '.iti__search-input-container, .iti__country-search' );
        if ( searchWrapper ) {
            searchWrapper.style.cssText = [
                'position: relative',
                'display: flex',
                'align-items: center',
                'width: 100%',
                'box-sizing: border-box',
            ].join( ' !important;' ) + ' !important;';
        }
    }

    function initPhoneFlagFields() {

        var telFields = document.querySelectorAll(
            '.elementor-field-type-tel input[type="tel"], ' +
            '.elementor-field-group input[type="tel"]'
        );

        if ( ! telFields.length ) return;

        telFields.forEach( function( input ) {

            if ( input.dataset.epffInit ) return;
            input.dataset.epffInit = 'true';

            var options = {
                utilsScript:       epffSettings.utilsScript,
                separateDialCode:  true,
                preferredCountries: [ epffSettings.defaultCountry || 'us' ],
                initialCountry:    epffSettings.autoDetect ? 'auto' : ( epffSettings.defaultCountry || 'us' ),
            };

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

            if ( epffSettings.allowedCountries && epffSettings.allowedCountries.length ) {
                options.onlyCountries = epffSettings.allowedCountries;
            }
            if ( epffSettings.excludedCountries && epffSettings.excludedCountries.length ) {
                options.excludeCountries = epffSettings.excludedCountries;
            }

            var iti = window.intlTelInput( input, options );
            itiInstances.push( { input: input, iti: iti } );

            // ── Fix search UI every time dropdown opens ──────────────────
            var wrapper = input.closest( '.iti' );
            if ( wrapper ) {
                // Watch for dropdown to appear using MutationObserver
                var observer = new MutationObserver( function( mutations ) {
                    mutations.forEach( function( mutation ) {
                        mutation.addedNodes.forEach( function( node ) {
                            if ( node.nodeType === 1 ) {
                                // Check if it's the dropdown or contains it
                                var dropdown = node.classList && node.classList.contains( 'iti__dropdown-content' )
                                    ? node
                                    : node.querySelector( '.iti__dropdown-content' );
                                if ( dropdown ) {
                                    setTimeout( function() { fixSearchUI( dropdown ); }, 50 );
                                }
                                // Also check for country list directly
                                if ( node.classList && node.classList.contains( 'iti__country-list' ) ) {
                                    setTimeout( function() { fixSearchUI( node.parentElement ); }, 50 );
                                }
                            }
                        } );
                    } );
                } );

                observer.observe( document.body, { childList: true, subtree: true } );

                // Also fix on flag click directly
                var flagBtn = wrapper.querySelector( '.iti__selected-country' );
                if ( flagBtn ) {
                    flagBtn.addEventListener( 'click', function() {
                        setTimeout( function() {
                            var dropdown = document.querySelector( '.iti__dropdown-content' )
                                || document.querySelector( '.iti.iti--container' );
                            fixSearchUI( dropdown || wrapper );
                        }, 100 );
                    } );
                }
            }

            // ── Inject dial code on submit ────────────────────────────────
            var form = input.closest( 'form' );
            if ( ! form ) return;

            form.addEventListener( 'submit', function() {
                injectDialCode( input, iti );
            }, true );

            $( form ).on( 'submit', function() {
                injectDialCode( input, iti );
            } );
        } );
    }

    function injectDialCode( input, iti ) {
        var countryData = iti.getSelectedCountryData();
        if ( ! countryData || ! countryData.dialCode ) return;

        var dialCode  = '+' + countryData.dialCode;
        var rawNumber = input.value.trim();

        if ( rawNumber.indexOf( '+' ) === 0 ) return;

        input.value = dialCode + rawNumber;
    }

    $( document ).ready( function() {
        initPhoneFlagFields();
    } );

    $( document ).on( 'elementor/popup/show', function() {
        setTimeout( initPhoneFlagFields, 300 );
    } );

    $( window ).on( 'elementor/frontend/init', function() {
        initPhoneFlagFields();
    } );

} )( jQuery );