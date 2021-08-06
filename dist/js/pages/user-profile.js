$(function () {
    // Register plugins
    FilePond.registerPlugin(
        FilePondPluginFileValidateSize,
        FilePondPluginImageExifOrientation,
        FilePondPluginImageCrop,
        FilePondPluginImageResize,
        FilePondPluginImagePreview,
        FilePondPluginImageTransform
    );

    // Turn a file input into a file pond
    let pondProfile = FilePond.create(document.querySelector('#profile-image-upload'), {
        labelIdle: 'Edit profile image.<br> Drag here or <span class="filepond--label-action"> Browse </span>',
        allowRevert: false,
        instantUpload: false,
        maxFiles: 1,
        credits: false,
        allowFileEncode: false,
        acceptedFileTypes: ['image/*'],
        // maximum allowed file size
        maxFileSize: '3MB',
        // crop the image to a 1:1 ratio
        imageCropAspectRatio: '1:1',
        // resize the image
        imageResizeTargetWidth: 200,
        // upload to this server end point
        server: $('#profile-image-form').attr('action'),

        onprocessfile(error, file) {
            const response = JSON.parse(file.serverId);
            if (response.success) {
                $(".profile-avatar").attr("src", "assets/profile/"+response.imgUrl);
            }
            else
                toastr.error(response.errors);

            let pond_ids = [];
            if (pondProfile.getFiles().length !== 0) {  // "pond" is an object, created by FilePond.create
                pondProfile.getFiles().forEach(function(file) {
                    pond_ids.push(file.id);
                });
            }
            pondProfile.removeFiles(pond_ids);
        },

        imageTransformImageFilter: (file) => new Promise(resolve => {

            // no gif mimetype, do transform
            if (!/image\/gif/.test(file.type)) return resolve(true);

            const reader = new FileReader();
            reader.onload = () => {

                let arr = new Uint8Array(reader.result),
                    i, len, length = arr.length, frames = 0;

                // make sure it's a gif (GIF8)
                if (arr[0] !== 0x47 || arr[1] !== 0x49 ||
                    arr[2] !== 0x46 || arr[3] !== 0x38) {
                    // it's not a gif, we can safely transform it
                    resolve(true);
                    return;
                }

                for (i=0, len = length - 9; i < len, frames < 2; ++i) {
                    if (arr[i] === 0x00 && arr[i+1] === 0x21 &&
                        arr[i+2] === 0xF9 && arr[i+3] === 0x04 &&
                        arr[i+8] === 0x00 &&
                        (arr[i+9] === 0x2C || arr[i+9] === 0x21)) {
                        frames++;
                    }
                }

                // if frame count > 1, it's animated, don't transform
                if (frames > 1) {
                    return resolve(false);
                }

                // do transform
                resolve(true);
            }
            reader.readAsArrayBuffer(file);

        })
    });

    // Turn a file input into a file pond
    let pondThumbnail = FilePond.create(document.querySelector('#post-thumbnail'), {
        labelIdle: 'Upload thumbnail.<br> Drag here or <span class="filepond--label-action"> Browse </span>',
        allowProcess: false,
        allowRevert: false,
        instantUpload: false,
        credits: false,
        maxFiles: 1,
        // maximum allowed file size
        maxFileSize: '5MB',
        // crop the image to a 16:9 ratio
        imageCropAspectRatio: '16:9',
        // resize the image
        imageResizeTargetHeight: 100,
        // upload to this server end point
        server: "/url",
    });

    //Initialize Select2 Elements
    $(".select2").select2();

    // Summernote
    $('#post-content').summernote();

    $('#create-post-form').submit (function (e) {
        e.preventDefault();
        let form_data = new FormData();
        form_data.append('title', $('#post-title').val());
        form_data.append('categories', JSON.stringify($('#post-categories').val()));
        if (pondThumbnail.getFiles().length !== 0)
            form_data.append('thumbnail', pondThumbnail.getFiles()[0].file);
        form_data.append('content', $('#post-content').summernote('code'));
        $.ajax({
            url: $('#create-post-form').attr('action'),
            type: $('#create-post-form').attr('method'),
            dataType: 'json',  // <-- what to expect back from the PHP script, if anything
            cache: false,
            contentType: false,
            processData: false,
            data: form_data,
        }).done(function(response) {
            if (response.success) {
                toastr.success("Post added successfully");
                location.replace(response.url);
            }
            else {
                for (let key in response.errors)
                    toastr.error(key+" error: "+ response.errors[key]);
            }
        }).fail(function () {
            toastr.error("Error connecting to server. Please try later !!");
        });
    });

    $('.post-count').text($('.get-post-count').text());
    $('.like-count').text($('.get-like-count').text());


    $(".delete-post-btn").click(function () {
        $deleteBtn = $(this);
        let postId = $deleteBtn.data("id");
        $postItem = $deleteBtn.parent().parent().parent();
        $.ajax({
            url: "user/delete-post.php",
            type: 'post',
            data: {
                'postId': postId
            },
            dataType: 'json',
        }).done(function (response) {
            if (response.success) {
                toastr.success("Post deleted");
                $postItem.hide('slow', function(){ $postItem.remove(); });
            }
            else {
                toastr.error(response.error);
            }
        }).fail(function () {
            toastr.error("Error connecting to server. Please try later !!");
        });
    });

    $('#settings-form').submit (function (e) {
        e.preventDefault();
        $form = $(this);
        $.ajax({
            url: $form.attr('action'),
            type: $form.attr('method'),
            data: $form.serialize(),
            dataType: "json",
            encode: true,
        }).done(function(response) {
            if(response.success) {
                toastr.success("Details updated successfully");
                $('.short-description').text(response.values.shortDescription);
                $('.education').text(response.values.education);
                $('.skills').text(response.values.skills);
                $('.bio').text(response.values.bio);
            }
            else {
                for (let key in response.errors)
                    toastr.error(key+" Error: "+ response.errors[key]);
            }
        }).fail(function() {
            toastr.error("Error connecting to server. Please try later !!");
        });
    });

    $('#password-form').validate({
        submitHandler: function (form) {
            $.ajax({
                url: form.action,
                type: form.method,
                data: $(form).serialize(),
                dataType: "json",
                encode: true,
            }).done(function(response) {
                if(response.success) {
                    toastr.success("Password changed successfully");
                    $('#password-form').trigger("reset");
                }
                else
                    toastr.error("Invalid form data");
            }).fail(function() {
                toastr.error("Error connecting to server. Please try later !!");
            });
        },
        rules: {
            password: {
                required: true,
                minlength: 5
            },
            confirmPassword: {
                required: true,
                equalTo: "#password"
            }
        },
        messages: {
            password: {
                required: "Please provide a password",
                minlength: "Your password must be at least 5 characters long"
            },
            confirmPassword: {
                required: "This field cannot be empty",
                equalTo: "Passwords do not match"
            }
        },
        errorElement: 'span',
        errorPlacement: function (error, element) {
            error.addClass('invalid-feedback offset-sm-4 col-sm-8');
            element.closest('.form-group').append(error);
        },
        highlight: function (element, errorClass, validClass) {
            $(element).removeClass('is-valid');
            $(element).addClass('is-invalid');
        },
        unhighlight: function (element, errorClass, validClass) {
            $(element).removeClass('is-invalid');
            $(element).addClass('is-valid');
        }
    });
});