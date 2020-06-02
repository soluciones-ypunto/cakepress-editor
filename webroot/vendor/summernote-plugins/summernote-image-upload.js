/**
 * Image uploads addon for cakephp editor plugin (based on summernote wysiwyg editor)
 */
(function (factory) {
    if (typeof define === 'function' && define.amd) {
        define(['jquery'], factory);
    } else if (typeof module === 'object' && module.exports) {
        module.exports = factory(require('jquery'));
    } else {
        factory(window.jQuery);
    }
}(function ($) {
    $('body').on('summernote.image.upload', function (event, files) {
        var $element = $(event.target);
        var summernote = $element.data('summernote');
        var uploadHandlerUrl = summernote.options.uploadHandlerUrl;

        for (var i = 0; i < files.length; i++) {
            var data = new FormData();
            data.append("images", files[i]);

            $.ajax({
                data: data,
                type: 'POST',
                url: uploadHandlerUrl,
                cache: false,
                contentType: false,
                processData: false,

                /**
                 *
                 * @param data
                 */
                success: function (data) {
                    if (data.url) {
                        $element.summernote('insertImage', data.url, data.name);
                    }
                    if (data.error) {
                        alert('No se pudo subir el archivo. Error: ' + data.error);
                    }
                }
            });
        }
    })
}));