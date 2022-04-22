require(['jquery'], function ($) {
    $(document).on('change', '.rep-select-all', function () {
        var $this = $(this),
            checked = $this.prop('checked'),
            repId = $this.attr('data-rep-id');
        $('input[data-rep-id-child="' + repId + '"]').each(function () {
            $(this).prop('checked', checked ? 'checked' : false);
        })
    }).on('change', '.rep-status-select', function () {
        var repId = $(this).attr('data-rep-id-select'),
            form = $(this).parent();
        $('input[data-rep-id-child="' + repId + '"]').each(function () {
            if ($(this).prop('checked')) {
                form.append('<input type="hidden"' +
                    ' name="orders_to_change[]" value="' +
                    $(this).attr('value')
                    + '"/>');
            }
        });
        form.submit();
    });
});