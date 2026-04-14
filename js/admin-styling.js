jQuery(document).ready(function($) {
    
    // Auto-fill logic for Redux Options panel
    // When primary or secondary color changes, we intelligently cascade the updates to related fields 
    // to save time, exactly as the user requested.
    
    var autoFills = {
        'primary_color': [
            'primary_hover_color',
            'btn_bg_color',
            'header_menu_text_hover',
            'header_dropdown_text_hover',
            'header_login_text_hover'
        ],
        'secondary_color': [
            'secondary_hover_color',
            'btn_bg_color_hover',
            'header_host_btn_bg',
            'search_bar_btn_bg',
            'featured_bg_color'
        ]
    };

    $.each(autoFills, function(master, slaves) {
        var masterSelector = 'input[name="thessnest[' + master + ']"]';
        
        $(document).on('change', masterSelector, function() {
            var newColor = $(this).val();
            
            slaves.forEach(function(slave) {
                var slaveInput = $('input[name="thessnest[' + slave + ']"]');
                
                if (slaveInput.length > 0) {
                    slaveInput.val(newColor).trigger('change');
                    
                    // Attempt to visually update the Redux color pickers (Spectrum or WP Color Picker)
                    var container = slaveInput.closest('.redux-color-picker');
                    if (container.length) {
                        container.find('.sp-preview-inner').css('background-color', newColor);
                        container.find('.redux-color-init').val(newColor);
                    }
                    
                    if (typeof slaveInput.wpColorPicker === 'function') {
                        try {
                            slaveInput.wpColorPicker('color', newColor);
                        } catch(e) {}
                    }
                }
            });
        });
        
        // Redux might use Iris or Spectrum. This handles manual typing as well.
        $('body').on('keyup', masterSelector, function() {
            $(this).trigger('change');
        });
    });
});
