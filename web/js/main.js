$(document).ready(function () {
    var fgc = "000000";
    var bgc = "ffffff";
        
    function ref() {
        $('body').attr('style', 'background:url(/api/name/1920/1080/' + fgc + '/caffeine);background-position:50% 50%;background-color:#' + bgc + ";background-repeat:no-repeat;");
    }
        
    $('.picker-fg').on('change', function(e) {
        fgc = $(e.target).val().substring(1);
        ref();
    });
    $('.picker-bg').on('change', function(e) {
        bgc = $(e.target).val().substring(1);
        ref();
    });

    $(".picker").spectrum({
        // color: tinycolor,
        flat: true,
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
});

        