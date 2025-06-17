
/**
* Theme:  Zircos - Responsive Bootstrap 4 Admin & Dashboard
* Author: Coderthemes
* File:   Form Validation
*/

$(document).ready(function() {
    $('form').parsley();
});
$(function () {
    $('#demo-form').parsley().on('field:validated', function () {
        var ok = $('.parsley-error').length === 0;
        $('.alert-info').toggleClass('d-none', !ok);
        $('.alert-warning').toggleClass('d-none', ok);
    })
    .on('form:submit', function () {
        return false; // Don't submit form for this demo
    });
});