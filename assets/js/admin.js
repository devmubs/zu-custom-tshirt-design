/**
 * Admin JavaScript for ZU Custom T-Shirt Design
 */
(function($) {
    'use strict';

    $(document).ready(function() {
        initAdmin();
    });

    function initAdmin() {
        // Image upload handlers
        initImageUploads();

        // Template slug auto-generation
        initSlugGeneration();

        // Pricing form validation
        initPricingValidation();

        // Order approval handlers
        initOrderApproval();

        // Confirmation dialogs
        initConfirmationDialogs();
    }

    function initImageUploads() {
        $('.upload-image-button').on('click', function(e) {
            e.preventDefault();
            var button = $(this);
            var wrapper = button.closest('.zu-ctsd-image-upload');
            var preview = wrapper.find('.image-preview-wrapper');
            var urlInput = wrapper.find('.image-url');
            var removeButton = wrapper.find('.remove-image-button');

            var mediaUploader = wp.media({
                title: zuCtsdAdmin.strings.uploadImage,
                button: {
                    text: zuCtsdAdmin.strings.selectImage,
                },
                multiple: false,
            });

            mediaUploader.on('select', function() {
                var attachment = mediaUploader.state().get('selection').first().toJSON();
                
                urlInput.val(attachment.url);
                preview.html('<img src="' + attachment.url + '" alt="" class="image-preview">');
                removeButton.show();
            });

            mediaUploader.open();
        });

        $('.remove-image-button').on('click', function(e) {
            e.preventDefault();
            var button = $(this);
            var wrapper = button.closest('.zu-ctsd-image-upload');
            var preview = wrapper.find('.image-preview-wrapper');
            var urlInput = wrapper.find('.image-url');

            urlInput.val('');
            preview.html('<div class="image-placeholder"><span class="dashicons dashicons-format-image"></span></div>');
            button.hide();
        });
    }

    function initSlugGeneration() {
        var nameInput = $('#template_name');
        var slugInput = $('#template_slug');

        if (nameInput.length && slugInput.length && !slugInput.val()) {
            nameInput.on('blur', function() {
                if (!slugInput.val()) {
                    var slug = nameInput.val()
                        .toLowerCase()
                        .replace(/[^a-z0-9]+/g, '-')
                        .replace(/^-+|-+$/g, '');
                    slugInput.val(slug);
                }
            });
        }
    }

    function initPricingValidation() {
        $('.zu-ctsd-pricing-form').on('submit', function(e) {
            var valid = true;
            
            $(this).find('input[type="number"]').each(function() {
                var value = parseFloat($(this).val());
                if (isNaN(value) || value < 0) {
                    valid = false;
                    $(this).addClass('error');
                } else {
                    $(this).removeClass('error');
                }
            });

            if (!valid) {
                e.preventDefault();
                alert('Please enter valid prices (non-negative numbers).');
            }
        });
    }

    function initOrderApproval() {
        // Approve design
        $('.zu-ctsd-approve-design').on('click', function(e) {
            e.preventDefault();
            var designId = $(this).data('design-id');
            updateDesignStatus(designId, 'approved');
        });

        // Reject design
        $('.zu-ctsd-reject-design').on('click', function(e) {
            e.preventDefault();
            var designId = $(this).data('design-id');
            updateDesignStatus(designId, 'rejected');
        });
    }

    function updateDesignStatus(designId, status) {
        $.ajax({
            url: zuCtsdAdmin.ajaxUrl,
            method: 'POST',
            data: {
                action: 'zu_ctsd_update_design_status',
                nonce: zuCtsdAdmin.nonce,
                design_id: designId,
                status: status,
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert(response.data.message || 'Error updating status.');
                }
            },
            error: function() {
                alert('Error updating status. Please try again.');
            },
        });
    }

    function initConfirmationDialogs() {
        $('a[href*="action=delete"]').on('click', function(e) {
            if (!confirm(zuCtsdAdmin.strings.confirmDelete)) {
                e.preventDefault();
            }
        });
    }

})(jQuery);
