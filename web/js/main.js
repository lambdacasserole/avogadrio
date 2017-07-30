$(document).ready(function () {
    var foregroundColor = "ffffff";
    var backgroundColor = "000000";
        
    function buildUrl(width, height, foreground, background, name) {
        return '/api/name/' + width + '/' + height + '/' + foreground + '/' + background + '/' + name;
    }
    
    function buildMoleculeOnlyUrl(width, height, foreground, name) {
        return '/api/name/' + width + '/' + height + '/' + foreground + '/' + name;
    }
        
    function refreshPreview() {
        $('body')
            .css('background', 'url(' + buildMoleculeOnlyUrl(1920, 1280, foregroundColor, $('.comp-name').val()) + ')')
            .css('background-color', '#' + bgc)
            .css('background-position', '50% 50%')
            .css('background-repeat', 'no-repeat');
    }
        
    $('.picker-fg').on('change', function(e) {
        fgc = $(e.target).val().substring(1);
        refreshPreview();
    });
    
    $('.picker-bg').on('change', function(e) {
        bgc = $(e.target).val().substring(1);
        refreshPreview();
    });
    
    $('.update-btn').on('click', function(e) {
        refreshPreview();
    });
    
    $('.download-btn').on('click', function(e) {
        
    });

    $(".picker").spectrum({
        // color: tinycolor,
        // flat: true,
        // showInput: true,
        // showInitial: true,
        // allowEmpty: bool,
        showAlpha: false,
        // disabled: bool,
        // localStorageKey: string,
        // showPalette: true,
        // showPaletteOnly: true,
        // togglePaletteOnly: bool,
        // showSelectionPalette: bool,
        clickoutFiresChange: true,
        // cancelText: string,
        chooseText: "Update",
        // togglePaletteMoreText: string,
        // togglePaletteLessText: string,
        // containerClassName: string,
        // replacerClassName: string,
        preferredFormat: 'hex',
        // maxSelectionSize: int,
        // palette: [[string]],
        // selectionPalette: [string]
    });
    
    refreshPreview();
});

        