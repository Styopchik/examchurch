function articleImage(e) {
    "use strict";
    var a = new FormData;
    a.append("image", e), swal({
        text: "Image uploading. Please Wait! ...",
        button: !1
    }), fetch("/article-image", {
        method: "POST",
        body: a,
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
        }
    }).then(e => e.json()).then(e => {
        $("#summernote").summernote("insertImage", e)
    }).then(() => {
        swal({
            icon: "success",
            text: "Uploaded successfully"
        })
    }).catch(e => {
        swal({
            icon: "error",
            text: e
        })
    })
}

$(document).ready(function () {
    $(document).on('click', '.show_confirm', function (event) {
        var form = $(this).closest("form");
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success',
                cancelButton: 'btn btn-danger'
            },
            buttonsStyling: false
        })
        swalWithBootstrapButtons.fire({
            title: 'Are you sure?',
            text: "This action can not be undone. Do you want to continue?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes',
            cancelButtonText: 'No',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        })
    });

    if ($(".pc-dt-simple").length > 0) {
        $($(".pc-dt-simple")).each(function (index, element) {
            var id = $(element).attr('id');
            const dataTable = new simpleDatatables.DataTable("#" + id);
        });
    }

    $(document).on("click", ".event-tag label", function () {
        $(".event-tag label").removeClass("active");
        $(this).addClass("active");
    });
    if ($(".pc-dt-simple").length > 0) {
        $($(".pc-dt-simple")).each(function (index, element) {
            var id = $(element).attr("id");
            const dataTable = new simpleDatatables.DataTable("#" + id);
        });
    }

    // Adding spinner for successful validation
    document.addEventListener('bouncerFormValid', function (event) {
        // Show spinner or perform any desired actions
        showSpinner();
    }, false);

    function showSpinner() {
        var submitBtn = document.querySelector('button[type="submit"]');
        var spinner = document.createElement('span');
        spinner.innerHTML = '<span class="spinner-border spinner-border-sm"></span> ';

        // Add the spinner before the button text
        submitBtn.insertAdjacentElement('afterbegin', spinner);

        // Disable the submit button to prevent multiple submissions
        submitBtn.disabled = true;

        // Simulate some delay to demonstrate the spinner
        setTimeout(function () {
            // Hide the spinner after a delay or perform further actions
            hideSpinner(submitBtn, spinner);
        }, 7000); // Adjust the delay time as needed
    }

    function hideSpinner(submitBtn, spinner) {
        // Remove the spinner element
        spinner.remove();

        // Enable the submit button
        submitBtn.disabled = false;
    }
});

function showToStr(title, message, type) {
    var o, i;
    var icon = '';
    var cls = '';
    var toster_pos = 'right';

    if (type == "success") {
        icon = "fas fa-check-circle";
        cls = "success";
    } else if (type == "warning") {
        icon = "fas fa-check-circle";
        cls = "warning";
    } else if (type == "info") {
        icon = "fas fa-check-circle";
        cls = "info";
    } else {
        icon = "fas fa-times-circle";
        cls = "danger";
    }

    $.notify({
        icon: icon,
        title: " " + title,
        message: message,
        url: ""
    }, {
        element: "body",
        type: cls,
        allow_dismiss: !0,
        placement: {
            from: 'top',
            align: toster_pos
        },
        offset: {
            x: 15,
            y: 15
        },
        spacing: 10,
        z_index: 1080,
        delay: 2500,
        timer: 2000,
        url_target: "_blank",
        mouse_over: !1,
        animate: {
            enter: o,
            exit: i
        },
        template: '<div class="toast text-white bg-' + cls + ' fade show" role="alert" aria-live="assertive" aria-atomic="true">' +
            '<div class="d-flex">' +
            '<div class="toast-body"> ' + message + ' </div>' +
            '<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>' +
            '</div>' +
            '</div>'
    });
}

$(document).on("change", ".chnageStatus", function (e) {
    var csrf = $("meta[name=csrf-token]").attr("content");
    var value = $(this).is(":checked");
    var id = $(this).data("id");
    var action = $(this).data("url");
    $.ajax({
        type: "POST",
        url: action,
        data: {
            _token: csrf,
            id: id,
            value: value,
        },
        success: function (response) {
            if (response.warning) {
                showToStr("Warning!", response.warning, "warning");
            }
            if (response.is_success) {
                showToStr("Success!", response.message, "success");
            }
        },
    });
});

$(document).ready(function () {
    $(document).on('click', '#downloadButton', function () {
        const videoUrl = '{{ $row->value }}';
        window.location.href = videoUrl;
    });
});

