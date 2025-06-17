
/**
* Theme:  Zircos - Responsive Bootstrap 4 Admin & Dashboard
* Author: Coderthemes
* File:   Datatables
*/

var handleDataTableButtons = function() {
    "use strict";
    0 !== $("#datatable-buttons").length && $("#datatable-buttons").DataTable({
        dom: "Bfrtip",
        buttons: [{
            extend: "copy",
            className: "btn-sm"
        }, {
            extend: "csv",
            className: "btn-sm"
        }, {
            extend: "excel",
            className: "btn-sm"
        }, {
            extend: "pdf",
            className: "btn-sm"
        }, {
            extend: "print",
            className: "btn-sm"
        }],
        responsive: !0
    })
},
TableManageButtons = function() {
    "use strict";
    return {
        init: function() {
            handleDataTableButtons()
        }
    }
}();


$(document).ready(function () {
$('#datatable').dataTable();
$('#datatable-keytable').DataTable({keys: true});
$('#datatable-responsive').DataTable();
$('#datatable-colvid').DataTable({
    "dom": 'C<"clear">lfrtip',
    "colVis": {
        "buttonText": "Change columns"
    }
});
$('#datatable-scroller').DataTable({
    ajax: "../assets/data/scroller-demo.json",
    deferRender: true,
    scrollY: 380,
    scrollCollapse: true,
    scroller: true
});
var table = $('#datatable-fixed-header').DataTable({fixedHeader: true});
var table = $('#datatable-fixed-col').DataTable({
    scrollY: "300px",
    scrollX: true,
    scrollCollapse: true,
    paging: false,
    fixedColumns: {
        leftColumns: 1,
        rightColumns: 1
    }
});
});
TableManageButtons.init();