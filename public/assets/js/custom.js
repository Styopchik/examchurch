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

document.addEventListener('bouncerFormValid', function (event) {
    showSpinner(event.target);
}, false);

function showSpinner(form) {
    var submitBtn = form.querySelector('button[type="submit"]');
    var originalText = submitBtn.innerText;

    submitBtn.innerText = 'Loading...';
    var spinner = document.createElement('span');
    spinner.setAttribute('class', 'spinner-border spinner-border-sm');
    spinner.setAttribute('role', 'status');

    submitBtn.insertAdjacentElement('afterbegin', spinner);

    submitBtn.disabled = true;

    setTimeout(function () {

        hideSpinner(submitBtn, originalText);
    }, 7000);
}

function hideSpinner(submitBtn, originalText) {
    submitBtn.innerText = originalText;
    submitBtn.disabled = false;
}

function showToStr(title, message, type) {
    var o, i;
    var icon = '';
    var cls = '';
    var toster_pos = "right";
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
        template: '<div class="toast btn-success text-white bg-' + cls + ' fade show" role="alert" aria-live="assertive" aria-atomic="true">' +
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

$(document).on("click", ".event-tag label", function () {
    $(".event-tag label").removeClass("active");
    $(this).addClass("active");
});
