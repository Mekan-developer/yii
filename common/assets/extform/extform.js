jQuery(document).ready(function() {
    /*jQuery('.select2').select2();
    jQuery('.iCheck').iCheck({
        tap: true,
        checkboxClass: 'icheckbox_square-blue',
        radioClass: 'iradio_square-blue'
    });*/
    jQuery('.numeric').numeric({ decimal: ".", negative: true });
    jQuery('.numeric0').numeric({ decimal: false, negative: true, decimalPlaces : 0 });
    jQuery('.numeric1').numeric({ decimal: ".", negative: true, decimalPlaces : 1 });
    jQuery('.numeric2').numeric({ decimal: ".", negative: true, decimalPlaces : 2 });
    jQuery('.numeric3').numeric({ decimal: ".", negative: true, decimalPlaces : 3 });
    jQuery('.numeric4').numeric({ decimal: ".", negative: true, decimalPlaces : 4 });
    jQuery('.numeric5').numeric({ decimal: ".", negative: true, decimalPlaces : 5 });
    jQuery('.numeric6').numeric({ decimal: ".", negative: true, decimalPlaces : 6 });

    jQuery('.clear-zero').focusin(clearZeroFocus).focusout(clearZeroLeave);

    jQuery('.ClearSearchButton').click(ClearSearch);
});

function ClearSearch() {
    var form = jQuery(this.form);
    form.find('input[type=text]').val('');
    form.find('select').val(0);
    return true;
}

function getInt(str) {
    var i = parseInt(str);
    if(isNaN(i)) i = 0;
    return i;
}

function getFloat(str) {
    var f = parseFloat(str);
    if(isNaN(f)) f = 0.0;
    return f;
}

function clearZeroFocus(e) {
    const obj = jQuery(this);
    const data = obj.val();
    const val = getFloat(data);
    if (val === 0) {
        obj.val('');
        obj.data('zero', data === '' ? '0' : data);
    }
}

function clearZeroLeave(e) {
    const obj = jQuery(this);
    const data = obj.val();
    const val = getFloat(data);
    if (val === 0) {
        obj.val(obj.data('zero')).data('zero', '');
    }
}
