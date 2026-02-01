/**
 * My Account JavaScript for ZU Custom T-Shirt Design
 */
(function($) {
    'use strict';

    $(document).ready(function() {
        initMyAccount();
    });

    function initMyAccount() {
        // Delete design
        $('.zu-ctsd-delete-design').on('click', function(e) {
            e.preventDefault();
            var designId = $(this).data('design-id');
            deleteDesign(designId);
        });

        // Reorder design
        $('.zu-ctsd-reorder-design').on('click', function(e) {
            e.preventDefault();
            var designId = $(this).data('design-id');
            reorderDesign(designId);
        });
    }

    function deleteDesign(designId) {
        if (!confirm(zuCtsdMyAccount.strings.confirmDelete)) {
            return;
        }

        $.ajax({
            url: zuCtsdMyAccount.restUrl + 'designs/' + designId,
            method: 'DELETE',
            data: {
                nonce: zuCtsdMyAccount.nonce,
            },
            success: function(response) {
                if (response.success) {
                    // Remove the design card from the DOM
                    $('.zu-ctsd-design-card[data-design-id="' + designId + '"]').fadeOut(300, function() {
                        $(this).remove();
                        
                        // Check if no designs left
                        if ($('.zu-ctsd-design-card').length === 0) {
                            location.reload();
                        }
                    });
                } else {
                    alert(response.message || 'Error deleting design.');
                }
            },
            error: function() {
                alert('Error deleting design. Please try again.');
            },
        });
    }

    function reorderDesign(designId) {
        if (!confirm(zuCtsdMyAccount.strings.confirmReorder)) {
            return;
        }

        $.ajax({
            url: zuCtsdMyAccount.restUrl + 'designs/' + designId + '/reorder',
            method: 'POST',
            data: {
                nonce: zuCtsdMyAccount.nonce,
            },
            success: function(response) {
                if (response.success) {
                    // Redirect to product page
                    window.location.href = response.redirect_url;
                } else {
                    alert(response.message || 'Error creating reorder.');
                }
            },
            error: function() {
                alert('Error creating reorder. Please try again.');
            },
        });
    }

})(jQuery);
