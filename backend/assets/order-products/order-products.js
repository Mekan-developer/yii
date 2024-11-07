jQuery(document).ready(function () {
    jQuery('#order-products .addButton').click(orderProductAdd);

    orderProductAssignActions('#order-products');
});
$('#order-product').change(function(e) {
    const val = getInt($(this).val());
    if (val != 0) {
        $('#btn-product-add').prop('disabled', null);
    } else {
        $('#btn-product-add').prop('disabled', true);
    }
});

$('#btn-product-add').click(function (e) {
    e.cancelBubble = true;
    e.stopPropagation();
    e.preventDefault();

    if (!OrderProductAddRow()) {
        return;
    }

    if (modalProducts) {
        modalProducts.hide();
    }
});

var modalProducts = null;

function orderProductAdd() {
    if (!modalProducts) {
        modalProducts = new bootstrap.Modal(document.getElementById('products_modal'), {
            keyboard: false
        });
    }
    $('#order-product').val(0).change();
    modalProducts.show();
}

function OrderProductAddRow(serviceId) {
    var rid = getInt(jQuery('#order-products .addButton').attr('data-id'));

    var item = $('#order-product option:selected');
    if (item.length <= 0) return;

    var pid = getInt(item.val());
    var name = item.html();
    var price = item.data('price');
    var q = item.data('q');
    if (orderProductsRows[pid] > 0) {
        q += orderProductsRows[pid];
    }

    if ($('tr.product-id' + pid).length > 0) {
        alert('Товар уже добавлен!');
        return false;
    }

    var tid = 't' + rid;
    var html = '<tr id="order-product-row' + tid + '" class="product-id' + pid + '">';
    html += '<td>';
    html += '<input type="hidden" class="product-id" name="' + orderProductForm + '[' + tid + '][product_id]" value="' + pid + '" />';
    html += '<input type="text" class="form-control product-name" name="' + orderProductForm + '[' + tid + '][name]" value="' + name + ' (' + q + 'шт.)" readonly disabled /></td>';
    html += '<td><input type="text" class="form-control product-price" name="' + orderProductForm + '[' + tid + '][price]" value="' + price + '" readonly disabled /></td>';
    html += '<td class="product-max-td"><input type="number" step="1" min="0" max="' + q + '" class="form-control numeric0 product-q" name="' + orderProductForm + '[' + tid + '][q]" value="0" /><small class="product-info">Макс: ' + q + '</small></td>';
    html += '<td><input type="text" class="form-control product-sum" name="' + orderProductForm + '[' + tid + '][sum]" value="0" readonly disabled /></td>';
    html += '<td class="gridActions" style="vertical-align:middle;"><span class="btn btn-danger btn-sm removeButton" title="' + orderProductDelete + '"><span class="fa fa-trash"></span></span></td>';
    html += '</tr>';

    rid++;
    jQuery('#order-products .addButton').attr('data-id', rid);

    jQuery('#order-products tbody').append(html);
    orderProductAssignActions('#order-product-row' + tid);
    orderProductTotalRecalc();
    return true;
}

function orderProductAssignActions(objStr) {
    jQuery(objStr + ' .removeButton').click(orderProductRemove);
    jQuery(objStr + ' .product-q').change(orderProductRecalc);
    jQuery(objStr + ' .numeric0').numeric({ decimal: false, negative: true, decimalPlaces : 0 });
}

function orderProductRemove() {
    if(!confirm(orderProductConfirm)) return false;
    jQuery(this).parent().parent().remove();
    orderProductTotalRecalc();
}
function orderProductRecalc() {
    var row = $(this).closest('tr');
    var price = getInt(row.find('.product-price').val());
    var q = getInt(row.find('.product-q').val());
    var max = getInt(row.find('.product-q').attr('max'));
    var sum = price * q;

    row.find('.product-sum').val(sum);
    if (q == 0) {
        row.find('.product-max-td').addClass('has_error');
    } else {
        row.find('.product-max-td').removeClass('has_error');
    }
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
        var sum = getInt($(o).val());
        total += sum;
    })
    $('#sum').val(total);
}

function orderProductsCheck() {
    jQuery('#order-products tbody tr .product-q').each(function (i, o) {
        $(o).change();
    })

    if (jQuery('#order-products .has_error').length > 0) return false;
    if (jQuery('#order-products .has_error_max').length > 0) return false;
    return true;
}