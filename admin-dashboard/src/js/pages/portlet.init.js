
/**
* Theme:  Zircos - Responsive Bootstrap 4 Admin & Dashboard
* Author: Coderthemes
* File:   portlet
*/

$(function () {
    // sortable
    $(".sortable").sortable({
        connectWith: '.sortable',
        items: '.card-draggable',
        revert: true,
        placeholder: 'card-draggable-placeholder',
        forcePlaceholderSize: true,
        opacity: 0.77,
        cursor: 'move'
    });
});