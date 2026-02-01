/**
 * Public JavaScript for ZU Custom T-Shirt Design
 */
(function($) {
    'use strict';

    $(document).ready(function() {
        initPublic();
    });

    function initPublic() {
        // Initialize any public-facing functionality
        // This file is loaded on all pages where the plugin is active

        // Handle shared design view
        handleSharedDesign();

        // Initialize tooltips or other UI elements
        initUIElements();
    }

    function handleSharedDesign() {
        var urlParams = new URLSearchParams(window.location.search);
        var shareToken = urlParams.get('zu_ctsd_share');

        if (shareToken) {
            // Load shared design
            loadSharedDesign(shareToken);
        }
    }

    function loadSharedDesign(token) {
        $.ajax({
            url: zuCtsdData.restUrl + 'designs/shared/' + token,
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    // Display shared design
                    displaySharedDesign(response.design);
                } else {
                    console.log('Shared design not found or not accessible.');
                }
            },
            error: function() {
                console.log('Error loading shared design.');
            },
        });
    }

    function displaySharedDesign(design) {
        // This function can be extended to show a modal or redirect to the product page
        // with the design pre-loaded
        console.log('Shared design loaded:', design);
    }

    function initUIElements() {
        // Initialize any tooltips, popovers, or other UI elements
        // This is a placeholder for future enhancements
    }

})(jQuery);
