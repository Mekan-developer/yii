jQuery(document).ready(function () {
    jQuery('#var-values-rows .addButton').click(varValueRowAdd);
    jQuery('#var-values-rows .removeButton').click(varValueRowRemove);
});

function varValueRowAdd() {
    var id = getInt(jQuery(this).attr('data-id'));
    var tid = 't' + id;
    var html = '<tr id="varRow' + tid + '">';

    html += '<td><input type="text" class="form-control" name="' + varRowForm + '[' + tid + '][value]" value="" /></td>';
    html += '<td><input type="text" class="form-control" name="' + varRowForm + '[' + tid + '][name]" value="" /></td>';
    html += '<td class="gridActions" style="vertical-align:middle;"><span class="btn btn-danger btn-sm removeButton" title="' +varRowDelete + '"><span class="fa fa-trash"></span></span></td>';
    html += '</tr>';

    id++;
    jQuery(this).attr('data-id', id);

    jQuery('#var-values-rows tbody').append(html);
    jQuery('#varRow' + tid + ' .removeButton').click(varValueRowRemove);
}

function varValueRowRemove() {
    if(!confirm(varRowConfirm)) return false;
    jQuery(this).closest('tr').remove();
}