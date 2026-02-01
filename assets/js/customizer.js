/**
 * Customizer JavaScript for ZU Custom T-Shirt Design
 */
(function($) {
    'use strict';

    // Customizer State
    var customizer = {
        canvas: null,
        currentSide: 'front',
        designData: {
            elements: [],
            print_side: 'front',
            print_method: 'dtf',
            material: 'cotton',
            urgency: 'normal',
            product_id: zuCtsdData.productId,
            template_id: zuCtsdData.template ? zuCtsdData.template.id : null,
        },
        designId: null,
        isModified: false,
        zoom: 1,
    };

    // Initialize
    $(document).ready(function() {
        initCustomizer();
    });

    function initCustomizer() {
        // Start designing button
        $('#zu-ctsd-start-designing').on('click', openCustomizer);

        // Modal close
        $('.zu-ctsd-modal-close, .zu-ctsd-modal-overlay').on('click', closeCustomizer);

        // Print side selector
        $('.zu-ctsd-side-btn').on('click', function() {
            switchSide($(this).data('side'));
        });

        // Add text
        $('#zu-ctsd-add-text').on('click', showTextModal);
        $('#zu-ctsd-text-add').on('click', addText);
        $('#zu-ctsd-text-cancel').on('click', hideTextModal);

        // Add image
        $('#zu-ctsd-add-image').on('click', uploadImage);

        // Element properties
        $('#zu-ctsd-font-family').on('change', updateTextProperties);
        $('#zu-ctsd-font-size').on('input', updateTextProperties);
        $('#zu-ctsd-font-color').on('input', updateTextProperties);
        $('.zu-ctsd-text-align button').on('click', function() {
            $('.zu-ctsd-text-align button').removeClass('active');
            $(this).addClass('active');
            updateTextProperties();
        });
        $('#zu-ctsd-opacity').on('input', updateElementOpacity);

        // Layer controls
        $('#zu-ctsd-bring-front').on('click', bringToFront);
        $('#zu-ctsd-send-back').on('click', sendToBack);

        // Delete element
        $('#zu-ctsd-delete-element').on('click', deleteSelectedElement);

        // Canvas controls
        $('#zu-ctsd-zoom-in').on('click', zoomIn);
        $('#zu-ctsd-zoom-out').on('click', zoomOut);
        $('#zu-ctsd-reset-zoom').on('click', resetZoom);

        // Print options
        $('#zu-ctsd-print-method, #zu-ctsd-material, #zu-ctsd-urgency').on('change', updatePrice);

        // Footer actions
        $('#zu-ctsd-save-design').on('click', saveDesign);
        $('#zu-ctsd-export-design').on('click', exportDesign);
        $('#zu-ctsd-share-design').on('click', shareDesign);
        $('#zu-ctsd-preview-design').on('click', showPreview);
        $('#zu-ctsd-add-to-cart').on('click', addToCart);

        // Share modal
        $('#zu-ctsd-share-copy').on('click', copyShareLink);
        $('#zu-ctsd-share-close').on('click', hideShareModal);

        // Keyboard shortcuts
        $(document).on('keydown', handleKeydown);
    }

    function openCustomizer() {
        $('#zu-ctsd-customizer-modal').fadeIn(200);
        initCanvas();
        updatePrice();
    }

    function closeCustomizer() {
        if (customizer.isModified) {
            if (!confirm('You have unsaved changes. Are you sure you want to close?')) {
                return;
            }
        }
        $('#zu-ctsd-customizer-modal').fadeOut(200);
        if (customizer.canvas) {
            customizer.canvas.dispose();
            customizer.canvas = null;
        }
    }

    function initCanvas() {
        var canvas = new fabric.Canvas('zu-ctsd-canvas', {
            width: zuCtsdData.settings.canvasWidth,
            height: zuCtsdData.settings.canvasHeight,
            backgroundColor: '#ffffff',
            preserveObjectStacking: true,
        });

        customizer.canvas = canvas;

        // Load template background
        loadTemplateBackground();

        // Canvas events
        canvas.on('selection:created', onObjectSelected);
        canvas.on('selection:updated', onObjectSelected);
        canvas.on('selection:cleared', onSelectionCleared);
        canvas.on('object:modified', onObjectModified);
        canvas.on('object:added', onObjectModified);

        // Make canvas responsive
        $(window).on('resize', resizeCanvas);
    }

    function loadTemplateBackground() {
        if (!zuCtsdData.template) return;

        var imageUrl = zuCtsdData.template.frontImage;
        if (!imageUrl) return;

        fabric.Image.fromURL(imageUrl, function(img) {
            img.set({
                selectable: false,
                evented: false,
                originX: 'center',
                originY: 'center',
                left: customizer.canvas.width / 2,
                top: customizer.canvas.height / 2,
            });

            // Scale to fit canvas
            var scale = Math.min(
                customizer.canvas.width / img.width,
                customizer.canvas.height / img.height
            );
            img.scale(scale * 0.9);

            customizer.canvas.setBackgroundImage(img, customizer.canvas.renderAll.bind(customizer.canvas));
        });
    }

    function switchSide(side) {
        customizer.currentSide = side;
        customizer.designData.print_side = side;

        $('.zu-ctsd-side-btn').removeClass('active');
        $('.zu-ctsd-side-btn[data-side="' + side + '"]').addClass('active');

        // Update background image
        if (zuCtsdData.template) {
            var imageUrl = null;
            switch (side) {
                case 'front':
                    imageUrl = zuCtsdData.template.frontImage;
                    break;
                case 'back':
                    imageUrl = zuCtsdData.template.backImage;
                    break;
                case 'left_sleeve':
                    imageUrl = zuCtsdData.template.leftSleeveImage;
                    break;
                case 'right_sleeve':
                    imageUrl = zuCtsdData.template.rightSleeveImage;
                    break;
            }

            if (imageUrl) {
                fabric.Image.fromURL(imageUrl, function(img) {
                    img.set({
                        selectable: false,
                        evented: false,
                        originX: 'center',
                        originY: 'center',
                        left: customizer.canvas.width / 2,
                        top: customizer.canvas.height / 2,
                    });

                    var scale = Math.min(
                        customizer.canvas.width / img.width,
                        customizer.canvas.height / img.height
                    );
                    img.scale(scale * 0.9);

                    customizer.canvas.setBackgroundImage(img, customizer.canvas.renderAll.bind(customizer.canvas));
                });
            }
        }
    }

    function showTextModal() {
        $('#zu-ctsd-text-modal').fadeIn(200);
        $('#zu-ctsd-text-input').focus();
    }

    function hideTextModal() {
        $('#zu-ctsd-text-modal').fadeOut(200);
        $('#zu-ctsd-text-input').val('');
    }

    function addText() {
        var text = $('#zu-ctsd-text-input').val().trim();
        if (!text) return;

        var textObject = new fabric.Text(text, {
            left: customizer.canvas.width / 2,
            top: customizer.canvas.height / 2,
            fontFamily: $('#zu-ctsd-font-family').val(),
            fontSize: parseInt($('#zu-ctsd-font-size').val()),
            fill: $('#zu-ctsd-font-color').val(),
            textAlign: $('.zu-ctsd-text-align button.active').data('align') || 'left',
            originX: 'center',
            originY: 'center',
        });

        customizer.canvas.add(textObject);
        customizer.canvas.setActiveObject(textObject);
        customizer.canvas.renderAll();

        hideTextModal();
        markAsModified();
    }

    function uploadImage() {
        var input = $('<input type="file" accept="image/*">');
        input.on('change', function(e) {
            var file = e.target.files[0];
            if (!file) return;

            // Check file size
            var maxSize = zuCtsdData.settings.maxFileSize * 1024 * 1024;
            if (file.size > maxSize) {
                alert(zuCtsdData.strings.fileTooLarge);
                return;
            }

            var reader = new FileReader();
            reader.onload = function(event) {
                fabric.Image.fromURL(event.target.result, function(img) {
                    // Scale image to fit canvas
                    var scale = Math.min(
                        (customizer.canvas.width * 0.5) / img.width,
                        (customizer.canvas.height * 0.5) / img.height
                    );

                    img.set({
                        left: customizer.canvas.width / 2,
                        top: customizer.canvas.height / 2,
                        scaleX: scale,
                        scaleY: scale,
                        originX: 'center',
                        originY: 'center',
                    });

                    customizer.canvas.add(img);
                    customizer.canvas.setActiveObject(img);
                    customizer.canvas.renderAll();
                    markAsModified();
                });
            };
            reader.readAsDataURL(file);
        });
        input.trigger('click');
    }

    function onObjectSelected(e) {
        var object = e.selected[0];
        if (!object) return;

        $('#zu-ctsd-element-properties').show();

        if (object.type === 'text' || object.type === 'i-text') {
            $('#zu-ctsd-text-properties').show();
            $('#zu-ctsd-font-family').val(object.fontFamily);
            $('#zu-ctsd-font-size').val(object.fontSize);
            $('#zu-ctsd-font-color').val(object.fill);
        } else {
            $('#zu-ctsd-text-properties').hide();
        }

        $('#zu-ctsd-opacity').val((object.opacity || 1) * 100);
    }

    function onSelectionCleared() {
        $('#zu-ctsd-element-properties').hide();
        $('#zu-ctsd-text-properties').hide();
    }

    function onObjectModified() {
        markAsModified();
        updatePrice();
    }

    function updateTextProperties() {
        var object = customizer.canvas.getActiveObject();
        if (!object || (object.type !== 'text' && object.type !== 'i-text')) return;

        object.set({
            fontFamily: $('#zu-ctsd-font-family').val(),
            fontSize: parseInt($('#zu-ctsd-font-size').val()),
            fill: $('#zu-ctsd-font-color').val(),
            textAlign: $('.zu-ctsd-text-align button.active').data('align') || 'left',
        });

        customizer.canvas.renderAll();
        markAsModified();
    }

    function updateElementOpacity() {
        var object = customizer.canvas.getActiveObject();
        if (!object) return;

        object.set('opacity', parseInt($('#zu-ctsd-opacity').val()) / 100);
        customizer.canvas.renderAll();
        markAsModified();
    }

    function bringToFront() {
        var object = customizer.canvas.getActiveObject();
        if (!object) return;

        customizer.canvas.bringForward(object);
        customizer.canvas.renderAll();
        markAsModified();
    }

    function sendToBack() {
        var object = customizer.canvas.getActiveObject();
        if (!object) return;

        customizer.canvas.sendBackwards(object);
        customizer.canvas.renderAll();
        markAsModified();
    }

    function deleteSelectedElement() {
        var object = customizer.canvas.getActiveObject();
        if (!object) return;

        customizer.canvas.remove(object);
        customizer.canvas.renderAll();
        $('#zu-ctsd-element-properties').hide();
        markAsModified();
        updatePrice();
    }

    function zoomIn() {
        customizer.zoom = Math.min(customizer.zoom + 0.1, 2);
        applyZoom();
    }

    function zoomOut() {
        customizer.zoom = Math.max(customizer.zoom - 0.1, 0.5);
        applyZoom();
    }

    function resetZoom() {
        customizer.zoom = 1;
        applyZoom();
    }

    function applyZoom() {
        customizer.canvas.setZoom(customizer.zoom);
    }

    function resizeCanvas() {
        // Handle responsive canvas
    }

    function updatePrice() {
        if (!zuCtsdData.settings.enableLivePrice) return;

        var designData = getDesignData();
        designData.print_method = $('#zu-ctsd-print-method').val();
        designData.material = $('#zu-ctsd-material').val();
        designData.urgency = $('#zu-ctsd-urgency').val();

        $.ajax({
            url: zuCtsdData.restUrl + 'calculate-price',
            method: 'POST',
            data: {
                product_id: zuCtsdData.productId,
                design_data: designData,
            },
            success: function(response) {
                if (response.success) {
                    displayPriceBreakdown(response.price_data);
                }
            },
        });
    }

    function displayPriceBreakdown(priceData) {
        $('#zu-ctsd-base-price').text(formatPrice(priceData.base_price));
        
        var extraCostsHtml = '';
        $.each(priceData.breakdown, function(index, item) {
            extraCostsHtml += '<div class="zu-ctsd-price-row">' +
                '<span>' + item.label + '</span>' +
                '<span>+' + formatPrice(item.cost) + '</span>' +
                '</div>';
        });
        $('#zu-ctsd-extra-costs').html(extraCostsHtml);
        
        $('#zu-ctsd-total-price').text(formatPrice(priceData.total_price));
    }

    function formatPrice(price) {
        return new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'USD',
        }).format(price);
    }

    function saveDesign() {
        var designData = getDesignData();
        var previewImage = customizer.canvas.toDataURL('image/png');

        $.ajax({
            url: zuCtsdData.restUrl + 'designs',
            method: 'POST',
            data: {
                nonce: zuCtsdData.nonce,
                design_name: zuCtsdData.productName + ' - ' + customizer.currentSide,
                design_data: designData,
                preview_image: previewImage,
                product_id: zuCtsdData.productId,
                print_side: customizer.currentSide,
                total_price: parseFloat($('#zu-ctsd-total-price').text().replace(/[^0-9.-]+/g, '')) || 0,
            },
            success: function(response) {
                if (response.success) {
                    customizer.designId = response.design_id;
                    customizer.isModified = false;
                    alert(zuCtsdData.strings.designSaved);
                } else {
                    alert(response.message || zuCtsdData.strings.designError);
                }
            },
            error: function() {
                alert(zuCtsdData.strings.designError);
            },
        });
    }

    function exportDesign() {
        var dataURL = customizer.canvas.toDataURL('image/png');
        var link = document.createElement('a');
        link.download = 'design-' + Date.now() + '.png';
        link.href = dataURL;
        link.click();
    }

    function shareDesign() {
        if (!customizer.designId) {
            alert('Please save your design first.');
            return;
        }

        $.ajax({
            url: zuCtsdData.restUrl + 'designs/' + customizer.designId + '/share',
            method: 'POST',
            data: {
                nonce: zuCtsdData.nonce,
            },
            success: function(response) {
                if (response.success) {
                    $('#zu-ctsd-share-url').val(response.share_url);
                    $('#zu-ctsd-share-modal').fadeIn(200);
                }
            },
        });
    }

    function copyShareLink() {
        var input = document.getElementById('zu-ctsd-share-url');
        input.select();
        document.execCommand('copy');
        alert('Link copied to clipboard!');
    }

    function hideShareModal() {
        $('#zu-ctsd-share-modal').fadeOut(200);
    }

    function showPreview() {
        var dataURL = customizer.canvas.toDataURL('image/png');
        $('#zu-ctsd-preview-image').attr('src', dataURL);
        $('#zu-ctsd-preview-modal').fadeIn(200);
    }

    function addToCart() {
        if (!customizer.designId) {
            // Save first, then add to cart
            saveDesignAndAddToCart();
            return;
        }

        submitAddToCart();
    }

    function saveDesignAndAddToCart() {
        var designData = getDesignData();
        var previewImage = customizer.canvas.toDataURL('image/png');

        $.ajax({
            url: zuCtsdData.restUrl + 'designs',
            method: 'POST',
            data: {
                nonce: zuCtsdData.nonce,
                design_name: zuCtsdData.productName + ' - ' + customizer.currentSide,
                design_data: designData,
                preview_image: previewImage,
                product_id: zuCtsdData.productId,
                print_side: customizer.currentSide,
                total_price: parseFloat($('#zu-ctsd-total-price').text().replace(/[^0-9.-]+/g, '')) || 0,
            },
            success: function(response) {
                if (response.success) {
                    customizer.designId = response.design_id;
                    customizer.isModified = false;
                    submitAddToCart();
                } else {
                    alert(response.message || zuCtsdData.strings.designError);
                }
            },
        });
    }

    function submitAddToCart() {
        $('#zu-ctsd-design-id').val(customizer.designId);
        $('#zu-ctsd-form-print-method').val($('#zu-ctsd-print-method').val());
        $('#zu-ctsd-form-urgency').val($('#zu-ctsd-urgency').val());
        $('#zu-ctsd-cart-form').submit();
    }

    function getDesignData() {
        var elements = [];
        customizer.canvas.getObjects().forEach(function(obj) {
            if (obj.selectable) {
                elements.push({
                    type: obj.type === 'text' || obj.type === 'i-text' ? 'text' : 'image',
                    content: obj.type === 'text' || obj.type === 'i-text' ? obj.text : obj.getSrc(),
                    position_x: obj.left,
                    position_y: obj.top,
                    width: obj.width * obj.scaleX,
                    height: obj.height * obj.scaleY,
                    rotation: obj.angle,
                    scale_x: obj.scaleX,
                    scale_y: obj.scaleY,
                    opacity: obj.opacity,
                    font_family: obj.fontFamily,
                    font_size: obj.fontSize,
                    font_color: obj.fill,
                    text_align: obj.textAlign,
                });
            }
        });

        return {
            elements: elements,
            print_side: customizer.currentSide,
            print_method: $('#zu-ctsd-print-method').val(),
            material: $('#zu-ctsd-material').val(),
            urgency: $('#zu-ctsd-urgency').val(),
            product_id: zuCtsdData.productId,
            template_id: zuCtsdData.template ? zuCtsdData.template.id : null,
        };
    }

    function markAsModified() {
        customizer.isModified = true;
    }

    function handleKeydown(e) {
        if (!$('#zu-ctsd-customizer-modal').is(':visible')) return;

        // Delete key
        if (e.keyCode === 46 || e.keyCode === 8) {
            deleteSelectedElement();
        }

        // Ctrl+S to save
        if (e.ctrlKey && e.keyCode === 83) {
            e.preventDefault();
            saveDesign();
        }
    }

})(jQuery);
