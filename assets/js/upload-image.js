/***************************************************************************************
*    Title: Upload Image w Preview & Filename
*    Author: suketran
*    Code version: 1.0
*    Availability: https://bootsnipp.com/snippets/eNbOa
***************************************************************************************/

$(document).ready(function () {
    $(document).on('change', '.image-picker', function () {

        //defaultImageSelect

        var input = $(this),
            label = input.val().replace(/\\/g, '/').replace(/.*\//, '');

        input.trigger('fileselect', [label]);
    });


    $(document).on('change', '.btn-file :file', function () {
        var input = $(this),
            label = input.val().replace(/\\/g, '/').replace(/.*\//, '');

        input.trigger('fileselect', [label]);
    });

    $('.btn-file :file').on('fileselect', function (event, label) {

        var input = $(this).parents('.input-group').find(':text'),
            log = label;

        //3/3/19: Included this logic to ensure only valid files
        //are capable of being submitted.
        if (!log.includes(".jpeg") && !log.includes(".jpg") &&
            !log.includes(".png") && !log.includes(".bmp") &&
            !log.includes(".JPG") && !log.includes(".JPEG") &&
            !log.includes(".PNG") && !log.includes(".BMP") &&
            !log.includes(".gif") && !log.includes(".GIF")) {
            return;
        }

        if (input.length) {
            input.val(log);
        } else {
            if (log) alert(log);
        }

    });

    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                uploadedImage = 1;
                $('#img-upload').attr('src', e.target.result);
            }

            reader.readAsDataURL(input.files[0]);
        }
    }

    $("#imgInp").change(function () {
        readURL(this);
    });
});


/***************************************************************************************
*    End of code from: Upload Image w Preview & Filename
*    Author: suketran
*    Code version: 1.0
*    Availability: https://bootsnipp.com/snippets/eNbOa
*
***************************************************************************************/