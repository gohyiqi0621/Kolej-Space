"use strict";

const gulp = require("gulp"),
    newer = require("gulp-newer"),
    imagemin = require("gulp-imagemin"),
    sass = require("gulp-sass"),
    sourcemaps = require("gulp-sourcemaps"),
    autoprefixer = require("gulp-autoprefixer"),
    cleanCSS = require('gulp-clean-css'),
    rename = require("gulp-rename"),
    concat = require("gulp-concat"),
    uglify = require("gulp-uglify"),
    lodash = require("lodash"),
    browsersync = require("browser-sync"),
    fileinclude = require('gulp-file-include');

const folder = {
    src: "src/", // source files
    dist: "dist/", // build files
    dist_assets: "dist/assets/" //build assets files
};

/*
Copy assets/vendors from their node_module package to scss & js folder
Read More: https://florian.ec/articles/frontend-dependencies-npm/
*/

function copyAssets(done) {
    var assets = {
        js: [
            "./node_modules/jquery/dist/jquery.js",
            "./node_modules/bootstrap/dist/js/bootstrap.bundle.js",
            "./node_modules/metismenu/dist/metisMenu.js",
            "./node_modules/jquery-slimscroll/jquery.slimscroll.js",
            "./node_modules/node-waves/dist/waves.js",
            "./node_modules/waypoints/lib/jquery.waypoints.min.js",
            "./node_modules/jquery.counterup/jquery.counterup.min.js"
        ]
    };

    var third_party_assets = {
        css_js: [
            {"name": "animate", "assets": ["./node_modules/animate.css/animate.min.css"]},
            {"name": "custombox", "assets": ["./node_modules/custombox/dist/custombox.min.js", "./node_modules/custombox/dist/custombox.min.css"]},
            {"name": "toastr", "assets": ["./node_modules/toastr/build/toastr.min.js", "./node_modules/toastr/build/toastr.min.css"]},
            {"name": "ion-rangeslider", "assets": ["./node_modules/ion-rangeslider/js/ion.rangeSlider.min.js", "./node_modules/ion-rangeslider/css/ion.rangeSlider.css"] },
            {"name": "sweetalert2", "assets": ["./node_modules/sweetalert2/dist/sweetalert2.min.js", "./node_modules/sweetalert2/dist/sweetalert2.min.css"]},
            {"name": "ratings", "assets": ["./node_modules/admin-resources/ratings/jquery.raty-fa.js"]},
            {"name": "jstree", "assets": ["./node_modules/jstree/dist/jstree.min.js","./node_modules/jstree/dist/themes/default/style.css"]},
            {"name": "hopscotch", "assets": ["./node_modules/hopscotch/dist/js/hopscotch.min.js", "./node_modules/hopscotch/dist/css/hopscotch.min.css"]},
            {"name": "moment", "assets": ["./node_modules/moment/min/moment.min.js"] },
            {"name": "jquery-ui", "assets": ["./node_modules/jquery-ui/jquery-ui.min.js"]},
            {"name": "fullcalendar", "assets": ["./node_modules/fullcalendar/dist/fullcalendar.min.js", "./node_modules/fullcalendar/dist/fullcalendar.min.css"]},
            {"name": "raphael", "assets": ["./node_modules/raphael/raphael.min.js"]},
            {"name": "morris-js", "assets": ["./node_modules/morris.js/morris.min.js"]},
            {"name": "gmaps", "assets": ["./node_modules/gmaps/gmaps.min.js"]},
            { "name": "jquery-vectormap", "assets": ["./node_modules/admin-resources/jquery.vectormap/jquery-jvectormap-1.2.2.min.js",
            "./node_modules/admin-resources/jquery.vectormap/maps/jquery-jvectormap-world-mill-en.js",
            "./node_modules/admin-resources/jquery.vectormap/maps/jquery-jvectormap-us-merc-en.js",
            "./node_modules/admin-resources/jquery.vectormap/maps/jquery-jvectormap-au-mill-en.js",
            "./node_modules/admin-resources/jquery.vectormap/maps/jquery-jvectormap-us-il-chicago-mill-en.js",
            "./node_modules/admin-resources/jquery.vectormap/maps/jquery-jvectormap-in-mill-en.js",
            "./node_modules/admin-resources/jquery.vectormap/maps/jquery-jvectormap-uk-mill-en.js",
            "./node_modules/admin-resources/jquery.vectormap/maps/jquery-jvectormap-ca-lcc-en.js",
            "./node_modules/admin-resources/jquery.vectormap/jquery-jvectormap-1.2.2.css"]
             },
            { "name": "chartist", "assets": ["./node_modules/chartist/dist/chartist.min.js", "./node_modules/chartist-plugin-tooltips/dist/chartist-plugin-tooltip.min.js", "./node_modules/chartist/dist/chartist.min.css"] },
            {"name": "jquery-knob", "assets": ["./node_modules/jquery-knob/dist/jquery.knob.min.js"]},
            {"name": "bootstrap-tagsinput", "assets": ["./node_modules/@adactive/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js", "./node_modules/@adactive/bootstrap-tagsinput/dist/bootstrap-tagsinput.css"]},
            {"name": "bootstrap-filestyle2", "assets": ["./node_modules/bootstrap-filestyle2/src/bootstrap-filestyle.min.js"]},
            {"name": "bootstrap-touchspin", "assets": ["./node_modules/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.min.js", "./node_modules/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.css"] },
            {"name": "bootstrap-select", "assets": ["./node_modules/bootstrap-select/dist/js/bootstrap-select.min.js", "./node_modules/bootstrap-select/dist/css/bootstrap-select.min.css"]},
           
            {"name": "bxslider", "assets": ["./node_modules/bxslider/dist/jquery.bxslider.min.js", "./node_modules/bxslider/dist/jquery.bxslider.min.css"]},
            {"name": "switchery", "assets": ["./node_modules/mohithg-switchery/dist/switchery.min.js", "./node_modules/mohithg-switchery/dist/switchery.min.css"]},
            {"name": "multiselect", "assets": ["./node_modules/multiselect/js/jquery.multi-select.js", "./node_modules/multiselect/css/multi-select.css"]},
            {"name": "jquery-quicksearch", "assets": ["./node_modules/jquery.quicksearch/dist/jquery.quicksearch.min.js"]},
            {"name": "select2", "assets": ["./node_modules/select2/dist/js/select2.min.js", "./node_modules/select2/dist/css/select2.min.css"]},
            {"name": "jquery-mockjax", "assets": ["./node_modules/jquery-mockjax/dist/jquery.mockjax.min.js"]},
            {"name": "autocomplete", "assets": ["./node_modules/devbridge-autocomplete/dist/jquery.autocomplete.min.js"]},
            {"name": "bootstrap-maxlength", "assets": ["./node_modules/bootstrap-maxlength/bootstrap-maxlength.min.js"]},
            {"name": "parsleyjs", "assets": ["./node_modules/parsleyjs/dist/parsley.min.js"]},
            {"name": "bootstrap-timepicker", "assets": ["./node_modules/bootstrap-timepicker/js/bootstrap-timepicker.min.js", "./node_modules/bootstrap-timepicker/css/bootstrap-timepicker.min.css"] },
            {"name": "bootstrap-colorpicker", "assets": ["./node_modules/bootstrap-colorpicker/dist/js/bootstrap-colorpicker.min.js", "./node_modules/bootstrap-colorpicker/dist/css/bootstrap-colorpicker.min.css"]},
            {"name": "bootstrap-datepicker", "assets": ["./node_modules/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js","./node_modules/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css"]},
            {"name": "bootstrap-daterangepicker", "assets": ["./node_modules/bootstrap-daterangepicker/daterangepicker.js", "./node_modules/bootstrap-daterangepicker/daterangepicker.css"] },
            {"name": "clockpicker", "assets": ["./node_modules/clockpicker/dist/bootstrap-clockpicker.min.js", "./node_modules/clockpicker/dist/bootstrap-clockpicker.min.css"]},
            {"name": "jquery-steps", "assets": ["./node_modules/jquery-steps/build/jquery.steps.min.js"]},
            {"name": "jquery-validation", "assets": ["./node_modules/jquery-validation/dist/jquery.validate.min.js"]},
            {"name": "jquery-tabledit", "assets": ["./node_modules/jquery-tabledit/jquery.tabledit.min.js"]},
            {"name": "jquery-scrollto", "assets": ["./node_modules/jquery.scrollto/jquery.scrollTo.min.js"]},
            {"name": "jquery-mask-plugin", "assets": ["./node_modules/jquery-mask-plugin/dist/jquery.mask.min.js"]},
            {"name": "autonumeric", "assets": ["./node_modules/autonumeric/autoNumeric-min.js"]},
            {"name": "dropify", "assets": ["./node_modules/dropify/dist/js/dropify.min.js", "./node_modules/dropify/dist/css/dropify.min.css"]},
            {
                "name": "datatables", "assets": ["./node_modules/datatables.net/js/jquery.dataTables.min.js",
                    "./node_modules/datatables.net-bs4/js/dataTables.bootstrap4.min.js",
                    "./node_modules/datatables.net-responsive/js/dataTables.responsive.min.js",
                    "./node_modules/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js",
                    "./node_modules/datatables.net-buttons/js/dataTables.buttons.min.js",
                    "./node_modules/datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js",
                    "./node_modules/datatables.net-buttons/js/buttons.html5.min.js",
                    "./node_modules/datatables.net-buttons/js/buttons.print.min.js",
                    "./node_modules/drmonty-datatables-colvis/js/dataTables.colVis.js",
                    "./node_modules/datatables.net-keytable/js/dataTables.keyTable.min.js",
                    "./node_modules/datatables.net-fixedcolumns/js/dataTables.fixedcolumns.min.js",
                    "./node_modules/datatables.net-fixedheader/js/dataTables.fixedHeader.min.js",
                    "./node_modules/datatables.net-scroller/js/dataTables.scroller.min.js",
                    
                    "./node_modules/datatables.net-fixedcolumns-bs4/css/fixedcolumns.bootstrap4.min.css",
                    "./node_modules/datatables.net-bs4/css/dataTables.bootstrap4.min.css",
                    "./node_modules/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css",
                    "./node_modules/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css",
                    "./node_modules/datatables.net-select-bs4/css/select.bootstrap4.min.css",
                    "./node_modules/datatables.net-fixedheader-bs4/css/fixedHeader.bootstrap4.min.css",
                    "./node_modules/datatables.net-scroller-bs4/css/scroller.bootstrap4.min.css",
                    "./node_modules/drmonty-datatables-colvis/css/dataTables.colVis.css",]
            },
            {"name": "jszip", "assets": ["./node_modules/jszip/dist/jszip.min.js"]},
            {"name": "pdfmake", "assets": ["./node_modules/pdfmake/build/pdfmake.min.js", "./node_modules/pdfmake/build/vfs_fonts.js"]},
            {"name": "rwd-table", "assets": ["./node_modules/admin-resources/rwd-table/rwd-table.min.js", "./node_modules/admin-resources/rwd-table/rwd-table.min.css"]},
            {"name": "tablesaw", "assets": ["./node_modules/tablesaw/dist/tablesaw.js", "./node_modules/tablesaw/dist/tablesaw.css"]},
            {
                "name": "flot-charts", "assets": ["./node_modules/flot-charts/jquery.flot.js",
                    "./node_modules/flot-charts/jquery.flot.time.js",
                    "./node_modules/flot-charts/jquery.flot.resize.js",
                    "./node_modules/flot-charts/jquery.flot.pie.js",
                    "./node_modules/flot-charts/jquery.flot.selection.js",
                    "./node_modules/flot-charts/jquery.flot.stack.js",
                    "./node_modules/flot-charts/jquery.flot.crosshair.js",
                    "./node_modules/jquery.flot.tooltip/js/jquery.flot.tooltip.min.js",
                    "./node_modules/flot-axislabels/jquery.flot.axislabels.js",
                    "./node_modules/jquery.flot.tooltip/js/jquery.flot.tooltip.min.js",
                    "./node_modules/flot-orderbars/js/jquery.flot.orderBars.js"]
            },
            {"name": "katex", "assets": ["./node_modules/katex/dist/katex.min.js"]},
            {"name": "raphael", "assets": ["./node_modules/raphael/raphael.min.js"]},
            {"name": "multiselect", "assets": ["./node_modules/multiselect/js/jquery.multi-select.js", "./node_modules/multiselect/css/multi-select.css"]},
            {"name": "morris-js", "assets": ["./node_modules/morris.js/morris.min.js"]},
            {"name": "chart-js", "assets": ["./node_modules/chart.js/dist/Chart.bundle.min.js"]},
            {"name": "peity", "assets": ["./node_modules/peity/jquery.peity.min.js"]},
            {"name": "c3", "assets": ["./node_modules/c3/c3.min.js", "./node_modules/c3/c3.min.css"]},
            {"name": "d3", "assets": ["./node_modules/d3/dist/d3.min.js"]},
            {"name": "jquery-sparkline", "assets": ["./node_modules/jquery-sparkline/jquery.sparkline.min.js"]},
            {"name": "isotope", "assets": ["./node_modules/isotope-layout/dist/isotope.pkgd.min.js"]},
            {"name": "summernote", "assets": ["./node_modules/summernote/dist/summernote-bs4.min.js", "./node_modules/summernote/dist/summernote-bs4.css"]},
            {"name": "magnific-popup", "assets": ["./node_modules/magnific-popup/dist/jquery.magnific-popup.min.js", "./node_modules/magnific-popup/dist/magnific-popup.css"]},
            {"name": "nestable2", "assets": ["./node_modules/nestable2/dist/jquery.nestable.min.js", "./node_modules/nestable2/dist/jquery.nestable.min.css"]},
            {"name": "jquery-countdown", "assets": ["./node_modules/jquery-countdown/dist/jquery.countdown.min.js"]},
            {
                "name": "jquery-mapael", "assets": ["./node_modules/jquery-mapael/js/jquery.mapael.min.js",
                    "./node_modules/jquery-mapael/js/maps/world_countries.min.js",
                    "./node_modules/jquery-mapael/js/maps/usa_states.min.js"]
            },
            {
                "name": "quill", "assets": ["./node_modules/quill/dist/quill.min.js", "./node_modules/quill/dist/quill.core.css",
                    "./node_modules/quill/dist/quill.bubble.css",
                    "./node_modules/quill/dist/quill.snow.css"]
            },
            {"name": "tooltipster", "assets": ["./node_modules/tooltipster/dist/js/tooltipster.bundle.min.js", "./node_modules/tooltipster/dist/css/tooltipster.bundle.min.css"]},
        ],
        font: [
        ]
    };

    //copying third party assets
    lodash(third_party_assets).forEach(function (assets, type) {
        if (type == "css_js") {
            lodash(assets).forEach(function (plugin) {
                var name = plugin['name'];
                var assetlist = plugin['assets'];
                lodash(assetlist).forEach(function (asset) {
                    gulp.src(asset).pipe(gulp.dest(folder.dist_assets + "libs/" + name));
                });
            });
            //gulp.src(assets).pipe(gulp.dest(folder.dist_assets + "css/vendor"));
        }
    });

    //copying required assets
    lodash(assets).forEach(function (assets, type) {
        gulp.src(assets).pipe(gulp.dest(folder.src + "js/vendor"));
    });

    //copying data files
    gulp.src(folder.src + "data/**").pipe(gulp.dest(folder.dist_assets + "/data"));

    done();
}

// image processing
function imageMin() {
    var out = folder.dist_assets + "images";
    return gulp
        .src(folder.src + "images/**/*")
        .pipe(newer(out))
        .pipe(imagemin())
        .pipe(gulp.dest(out));
}

// copy fonts from src folder to dist folder
function fonts() {
    var out = folder.dist_assets + "fonts/";

    return gulp.src([folder.src + "fonts/**/*"]).pipe(gulp.dest(out));
}

// copy html files from src folder to dist folder, also copy favicons
function html() {
    var out = folder.dist;

    return gulp
        .src([
            folder.src + "html/*.html",
            folder.src + "html/*.ico", // favicons
            folder.src + "html/*.png"
        ])
        .pipe(fileinclude({
            prefix: '@@',
            basepath: '@file',
            indent: true
        }))
        .pipe(gulp.dest(out));
}

// compile & minify sass
function css() {
    gulp
        .src([folder.src + "/scss/bootstrap.scss"])
        .pipe(sourcemaps.init())
        .pipe(sass()) // scss to css
        .pipe(
            autoprefixer({
                overrideBrowserslist: ["last 2 versions"]
            })
        )
        .pipe(gulp.dest(folder.dist_assets + "css/"))
        .pipe(cleanCSS())
        .pipe(
            rename({
                // rename app.css to icons.min.css
                suffix: ".min"
            })
        )
        .pipe(sourcemaps.write("./")) // source maps for icons.min.css
        .pipe(gulp.dest(folder.dist_assets + "css/"));

    gulp
        .src([folder.src + "/scss/bootstrap-dark.scss"])
        .pipe(sourcemaps.init())
        .pipe(sass()) // scss to css
        .pipe(
            autoprefixer({
                overrideBrowserslist: ["last 2 versions"]
            })
        )
        .pipe(gulp.dest(folder.dist_assets + "css/"))
        .pipe(cleanCSS())
        .pipe(
            rename({
                // rename app.css to icons.min.css
                suffix: ".min"
            })
        )
        .pipe(sourcemaps.write("./")) // source maps for icons.min.css
        .pipe(gulp.dest(folder.dist_assets + "css/"));
    gulp
        .src([folder.src + "/scss/icons.scss"])
        .pipe(sourcemaps.init())
        .pipe(sass()) // scss to css
        .pipe(
            autoprefixer({
                overrideBrowserslist: ["last 2 versions"]
            })
        )
        .pipe(gulp.dest(folder.dist_assets + "css/"))
        .pipe(cleanCSS())
        .pipe(
            rename({
                // rename app.css to icons.min.css
                suffix: ".min"
            })
        )
        .pipe(sourcemaps.write("./")) // source maps for icons.min.css
        .pipe(gulp.dest(folder.dist_assets + "css/"));
    gulp
        .src([folder.src + "/scss/app-rtl.scss"])
        .pipe(sourcemaps.init())
        .pipe(sass()) // scss to css
        .pipe(
            autoprefixer({
                overrideBrowserslist: ["last 2 versions"]
            })
        )
        .pipe(gulp.dest(folder.dist_assets + "css/"))
        .pipe(cleanCSS())
        .pipe(
            rename({
                // rename app.css to app.min.css
                suffix: ".min"
            })
        )
        .pipe(sourcemaps.write("./")) // source maps for app.min.css
        .pipe(gulp.dest(folder.dist_assets + "css/"));
    gulp
        .src([folder.src + "/scss/app-dark.scss"])
        .pipe(sourcemaps.init())
        .pipe(sass()) // scss to css
        .pipe(
            autoprefixer({
                overrideBrowserslist: ["last 2 versions"]
            })
        )
        .pipe(gulp.dest(folder.dist_assets + "css/"))
        .pipe(cleanCSS())
        .pipe(
            rename({
                // rename app.css to app.min.css
                suffix: ".min"
            })
        )
        .pipe(sourcemaps.write("./")) // source maps for app.min.css
        .pipe(gulp.dest(folder.dist_assets + "css/"));

    gulp
        .src([folder.src + "/scss/app-dark-rtl.scss"])
        .pipe(sourcemaps.init())
        .pipe(sass()) // scss to css
        .pipe(
            autoprefixer({
                browsers: ["last 2 versions"]
            })
        )
        .pipe(gulp.dest(folder.dist_assets + "css/"))
        .pipe(cleanCSS())
        .pipe(
            rename({
                // rename app.css to app.min.css
                suffix: ".min"
            })
        )
        .pipe(sourcemaps.write("./")) // source maps for app.min.css
        .pipe(gulp.dest(folder.dist_assets + "css/"));
        
    return gulp
        .src([folder.src + "/scss/app.scss"])
        .pipe(sourcemaps.init())
        .pipe(sass()) // scss to css
        .pipe(
            autoprefixer({
                overrideBrowserslist: ["last 2 versions"]
            })
        )
        .pipe(gulp.dest(folder.dist_assets + "css/"))
        .pipe(cleanCSS())
        .pipe(
            rename({
                // rename app.css to app.min.css
                suffix: ".min"
            })
        )
        .pipe(sourcemaps.write("./")) // source maps for app.min.css
        .pipe(gulp.dest(folder.dist_assets + "css/"));

}

// js
function javascript() {
    var out = folder.dist_assets + "js/";

    //copying demo pages related assets
    var app_pages_assets = {
        js: [
            folder.src + "js/pages/dashboard.init.js",
            folder.src + "js/pages/dashboard_2.init.js",
            folder.src + "js/pages/toastr.init.js",
            folder.src + "js/pages/nestable.init.js",
            folder.src + "js/pages/tooltipster.init.js",
            folder.src + "js/pages/range-sliders.init.js",
            folder.src + "js/pages/sweetalerts.init.js",
            folder.src + "js/pages/rating.init.js",
            folder.src + "js/pages/treeview.init.js",
            folder.src + "js/pages/tour.init.js",
            folder.src + "js/pages/calendar.init.js",
            folder.src + "js/pages/widgets.init.js",
            folder.src + "js/pages/jquery.chat.js",
            folder.src + "js/pages/jquery.todo.js",
            folder.src + "js/pages/email-compose.init.js",
            folder.src + "js/pages/form-advanced.init.js",
            folder.src + "js/pages/form-validation.init.js",
            folder.src + "js/pages/form-pickers.init.js",
            folder.src + "js/pages/form-wizard.init.js",
            folder.src + "js/pages/form-masks.init.js",
            folder.src + "js/pages/form-quilljs.init.js",
            folder.src + "js/pages/form-fileuploads.init.js",
            folder.src + "js/pages/form-summernote.init.js",
            folder.src + "js/pages/datatables.init.js",
            folder.src + "js/pages/responsive-table.init.js",
            folder.src + "js/pages/tablesaw.init.js",
            folder.src + "js/pages/tabledit.init.js" ,
            folder.src + "js/pages/flot.init.js",
            folder.src + "js/pages/morris.init.js",
            folder.src + "js/pages/chartjs.init.js",
            folder.src + "js/pages/peity.init.js",
            folder.src + "js/pages/chartist.init.js",
            folder.src + "js/pages/c3.init.js",
            folder.src + "js/pages/sparkline.init.js",
            folder.src + "js/pages/gallery.init.js",
            folder.src + "js/pages/coming-soon.init.js",
            folder.src + "js/pages/animate.init.js",
            folder.src + "js/pages/icon.init.js",
            folder.src + "js/pages/google-charts.init.js",
            folder.src + "js/pages/gmaps.init.js",
            folder.src + "js/pages/jvectormap.init.js",
            folder.src + "js/pages/jvectormap.init.js",
            folder.src + "js/pages/mapael-map.init.js",
            folder.src + "js/pages/blog-dashboard.init.js",
            folder.src + "js/pages/blog-add.init.js",
            folder.src + "js/pages/real-estate-dashboard.init.js",
            folder.src + "js/pages/bxslider.init.js",
            folder.src + "js/pages/property-add.init.js",
            folder.src + "js/pages/portlet.init.js",

            
        ]
    };

    lodash(app_pages_assets).forEach(function (assets, type) {
        gulp.src(assets)
            .pipe(uglify())
            .on("error", function (err) {
                console.log(err.toString());
            })
            .pipe(gulp.dest(out + "pages"));
    });

    // It's important to keep files at this order
    // so that `app.min.js` can be executed properly
    gulp
        .src([
            folder.src + "js/vendor/jquery.js",
            folder.src + "js/vendor/bootstrap.bundle.js",
            folder.src + "js/vendor/jquery.slimscroll.js",
            folder.src + "js/vendor/metisMenu.js",
            folder.src + "js/vendor/waves.js",
            folder.src + "js/vendor/jquery.waypoints.min.js",
            folder.src + "js/vendor/jquery.counterup.min.js"
        ])
        .pipe(sourcemaps.init())
        .pipe(concat("vendor.js"))
        .pipe(gulp.dest(out))
        .pipe(
            rename({
                // rename app.js to app.min.js
                suffix: ".min"
            })
        )
        .pipe(uglify())
        .on("error", function (err) {
            console.log(err.toString());
        })
        .pipe(sourcemaps.write("./"))
        .pipe(gulp.dest(out));

    return gulp
        .src([
            folder.src + "js/app.js"
        ])
        .pipe(sourcemaps.init())
        .pipe(concat("app.js"))
        .pipe(gulp.dest(out))
        .pipe(
            rename({
                // rename app.js to app.min.js
                suffix: ".min"
            })
        )
        .pipe(uglify())
        .on("error", function (err) {
            console.log(err.toString());
        })
        .pipe(sourcemaps.write("./"))
        .pipe(gulp.dest(out));
}

// live browser loading
function browserSync(done) {
    browsersync.init({
        server: {
            baseDir: folder.dist
        }
    });
    done();
}

function reloadBrowserSync(done) {
    browsersync.reload();
    done();
}

// watch all changes
function watchFiles() {
    gulp.watch(folder.src + "html/**", gulp.series(html, reloadBrowserSync));
    gulp.watch(folder.src + "assets/images/**/*", gulp.series(imageMin, reloadBrowserSync));
    gulp.watch(folder.src + "assets/fonts/**/*", gulp.series(fonts, reloadBrowserSync));
    gulp.watch(folder.src + "scss/**/*", gulp.series(css, reloadBrowserSync));
    gulp.watch(folder.src + "js/**/*", gulp.series(javascript, reloadBrowserSync));
}

// watch all changes
gulp.task("watch", gulp.parallel(watchFiles, browserSync));

// default task
gulp.task(
    "default",
    gulp.series(
        copyAssets,
        html,
        imageMin,
        fonts,
        css,
        javascript,
        'watch'
    ),
    function(done) {done();}
);

// build
gulp.task(
    "build",
    gulp.series(copyAssets,
        html,
        imageMin,
        fonts,
        css,
        javascript)
);