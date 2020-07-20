define(
    [
        'Magento_Ui/js/form/element/file-uploader'
    ],
    function (FileUploader) {
        'use strict';

        return FileUploader.extend(
            {
                processFile: function (file) {
                    file.previewType = this.getFilePreviewType(file);

                    this.observe.call(file, true, [
                        'previewWidth',
                        'previewHeight'
                    ]);

                    return file;
                },
            }
        );
    }
);