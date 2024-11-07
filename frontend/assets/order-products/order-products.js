jQuery(document).ready(function () {
    orderProductAssignActions('#order-products');
});

function orderProductAssignActions(objStr) {
    jQuery(objStr + ' .product-q').change(orderProductRecalc);
    jQuery(objStr + ' .numeric0').numeric({ decimal: false, negative: true, decimalPlaces : 0 });
}


function orderProductRecalc() {
    var row = $(this).closest('tr');
    var price = getInt(row.find('.product-price').html());
    var q = getInt(row.find('.product-q').val());
    var max = getInt(row.find('.product-q').attr('max'));
    var sum = price * q;

    row.find('.product-sum').html(sum);
    if (q > max) {
        row.find('.product-max-td').addClass('has_error_max');
    } else {
        row.find('.product-max-td').removeClass('has_error_max');
    }
    orderProductTotalRecalc();
}
function orderProductTotalRecalc() {
    var total = 0;
    jQuery('#order-products tbody tr .product-sum').each(function (i, o) {
        var sum = getInt($(o).html());
        total += sum;
    })
    $('#sum').val(total);
}
