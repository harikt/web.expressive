var elixir = require('laravel-elixir');

elixir(function (mix) {
    mix.sass([
        './bower_components/bootstrap/dist/css/bootstrap.css',
        './bower_components/font-awesome/css/font-awesome.css',
        './bower_components/bootstrap-multiselect/dist/css/bootstrap-multiselect.css',
        './bower_components/iCheck/skins/square/blue.css',
        './bower_components/bootstrap-sweetalert/lib/sweet-alert.css',
        './bower_components/dropzone/dist/min/dropzone.min.css',
        './bower_components/bootstrap-daterangepicker/daterangepicker.css',
        './bower_components/jquery-minicolors/jquery.minicolors.css',
        './bower_components/typeahead.js-bootstrap3.less/typeahead.css',
        './bower_components/admin-lte/dist/css/AdminLTE.css',
        './bower_components/admin-lte/dist/css/skins/skin-blue.css',
        './bower_components/ladda-bootstrap/dist/ladda-themeless.css',
        './bower_components/darkroom/build/darkroom.css',
        './bower_components/morris.js/morris.css',
        //
        './resources/sass/main.scss',
        './resources/views/**/*.scss'
    ], './dist/css/all.css');

    mix.scripts([
        './bower_components/jquery/dist/jquery.js',
        './bower_components/bootstrap/dist/js/bootstrap.js',
        './bower_components/parsleyjs/dist/parsley.js',
        './bower_components/bootstrap-multiselect/dist/js/bootstrap-multiselect.js',
        './bower_components/fastclick/lib/fastclick.js',
        './bower_components/jquery-slimscroll/jquery.slimscroll.js',
        './bower_components/jquery-slimscroll/jquery.slimscroll.min.js',
        './bower_components/moment/moment.js',
        './bower_components/moment-timezone/builds/moment-timezone-with-data-2010-2020.js',
        './bower_components/eve-raphael/eve.js',
        './bower_components/mocha/mocha.js',
        './bower_components/jquery-ui/jquery-ui.js',
        './bower_components/html5shiv/dist/html5shiv.js',
        './bower_components/respond/dest/respond.src.js',
        './bower_components/iCheck/icheck.min.js',
        './bower_components/bootstrap-sweetalert/lib/sweet-alert.js',
        './bower_components/js-cookie/src/js.cookie.js',
        './bower_components/spin.js/spin.js',
        './bower_components/Sortable/Sortable.js',
        './bower_components/dropzone/dist/min/dropzone.min.js',
        './bower_components/JavaScript-Canvas-to-Blob/js/canvas-to-blob.min.js',
        './bower_components/fabric/dist/fabric.min.js',
        './bower_components/bootstrap-daterangepicker/daterangepicker.js',
        './bower_components/jquery-minicolors/jquery.minicolors.js',
        './bower_components/typeahead.js/dist/typeahead.bundle.min.js',
        './bower_components/typeahead-addresspicker/dist/typeahead-addresspicker.min.js',
        './bower_components/Download-File-JS/dist/download.min.js',
        './bower_components/admin-lte/dist/js/app.js',
        './bower_components/raphael/raphael.min.js',
        './bower_components/ladda-bootstrap/dist/ladda.js',
        './bower_components/darkroom/build/darkroom.js',
        './bower_components/morris.js/morris.js',
        //
        './resources/js/main.js',
        './resources/js/services/**/*.js',
        './resources/views/**/*.js'
    ], './dist/js/all.js');

    mix.copy('./resources/images/', './dist/img/');

    mix.copy([
        './bower_components/font-awesome/fonts/fontawesome-webfont.woff',
        './bower_components/font-awesome/fonts/fontawesome-webfont.woff2'
    ], './dist/fonts/');

    mix.copy('./bower_components/admin-lte/dist/img/', './dist/img/');
    mix.copy([
        './bower_components/iCheck/skins/square/blue.png',
        './bower_components/iCheck/skins/square/blue@2x.png'
    ], './dist/css/');

    // Wysiwyg
    var wysiwygScripts = [
        './bower_components/tinymce/tinymce.min.js',
        './bower_components/tinymce/themes/modern/theme.min.js'
    ];
    var plugins = [
        "advlist", "autolink", "lists", "link", "image", "charmap", "textcolor",
        "print", "preview", "anchor", "searchreplace", "visualblocks",
        "code", "insertdatetime", "media", "table", "contextmenu", "paste", "imagetools"
    ];
    for (var pluginIndex in plugins) {
        wysiwygScripts.push('./bower_components/tinymce/plugins/' + plugins[pluginIndex] + '/plugin.min.js');
    }
    mix.scripts(wysiwygScripts, './dist/wysiwyg/wysiwyg.js');
    mix.styles('./bower_components/tinymce/skins/lightgray/skin.min.css', './dist/wysiwyg/wysiwyg.css');
    mix.copy('./bower_components/tinymce/skins/lightgray/', './dist/wysiwyg/skins/lightgray/');
    mix.copy('./bower_components/tinymce/skins/lightgray/fonts', './dist/wysiwyg/fonts/');
    mix.copy('./bower_components/tinymce/skins/lightgray/img', './dist/wysiwyg/img/');
});