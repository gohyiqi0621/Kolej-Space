/**
* Theme:  Zircos - Responsive Bootstrap 4 Admin & Dashboard
* Author: Coderthemes
* File:   Form Summernote
*/

jQuery(document).ready(function(){
    $('.summernote').summernote({
        height: 250,                 // set editor height
        minHeight: null,             // set minimum height of editor
        maxHeight: null,             // set maximum height of editor
        focus: false                 // set focus to editable area after initializing summernote
    });
    $('.inline-editor').summernote({
        airMode: true
    });
});