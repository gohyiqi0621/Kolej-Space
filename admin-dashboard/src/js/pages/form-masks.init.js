/**
* Theme:  Zircos - Responsive Bootstrap 4 Admin & Dashboard
* Author: Coderthemes
* File:   Form Masks
*/

$( document ).ready(function() {
    $('[data-toggle="input-mask"]').each(function (idx, obj) {
        var maskFormat = $(obj).data("maskFormat");
        var reverse = $(obj).data("reverse");
        if (reverse != null)
            $(obj).mask(maskFormat, {'reverse': reverse});
        else
            $(obj).mask(maskFormat);
    });
});

jQuery(function($) {
    $('.autonumber').autoNumeric('init');
});

