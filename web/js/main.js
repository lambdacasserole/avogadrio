$(document).ready(function () {
    var foregroundColor = "ffffff";
    var backgroundColor = "000000";
        
    function buildUrl(width, height, foreground, background, name) {
        return '/api/name/' + width + '/' + height + '/' + background + '/' + foreground + '/' + name;
    }
    
    function buildMoleculeOnlyUrl(width, height, foreground, name) {
        return '/api/name/' + width + '/' + height + '/' + foreground + '/' + name;
    }
        
    function refreshPreview() {
        $('body')
            .css('background', 'url(' + buildMoleculeOnlyUrl(1920, 1280, foregroundColor, $('.comp-name').val()) + ')')
            .css('background-color', '#' + backgroundColor)
            .css('background-position', '50% 50%')
            .css('background-repeat', 'no-repeat');
    }
        
    $('.picker-fg').on('change', function(e) {
        foregroundColor = $(e.target).val().substring(1);
        refreshPreview();
    });
    
    $('.picker-bg').on('change', function(e) {
        backgroundColor = $(e.target).val().substring(1);
        refreshPreview();
    });
    
    $('.update-btn').on('click', function(e) {
        refreshPreview();
    });
    
    $('.download-btn').on('click', function(e) {
        window.open(buildUrl(1920, 1280, foregroundColor, backgroundColor, $('.comp-name').val()),'Image','width=largeImage.stylewidth,height=largeImage.style.height,resizable=1');
    });

    $(".picker-fg").spectrum({
        color: '#' + foregroundColor,
        showAlpha: false,
        clickoutFiresChange: true,
        chooseText: "Update",
        preferredFormat: 'hex',
    });
    
    $(".picker-bg").spectrum({
        color: '#' + backgroundColor,
        showAlpha: false,
        clickoutFiresChange: true,
        chooseText: "Update",
        preferredFormat: 'hex',
    });
    
    refreshPreview();
});

        