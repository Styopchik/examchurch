<script>
    @if (session('failed'))
    showToStr('Sorry!', '{{ session('failed') }}', 'danger',
            '{{ asset('assets/images/notification/high_priority-48.png') }}', 3000);
    @endif
    @if ($errors = session('errors'))
        @if (is_object($errors))
            @foreach ($errors->all() as $error)
            showToStr('Error!', '{{ $error }}', 'danger',
                    '{{ asset('assets/images/notification/high_priority-48.png') }}', 4000);
            @endforeach
        @else
        showToStr('Error!', '{{ session('errors') }}', 'danger',
                '{{ asset('assets/images/notification/high_priority-48.png') }}', 4000);
        @endif
    @endif
    @if (session('success'))
    showToStr('Success', '{{ session('success') }}', 'success',
            '{{ asset('assets/images/notification/ok-48.png') }}', 3000);
    @endif
    @if (session('successful'))
    showToStr('Success', '{{ session('success') }}', 'success',
            '{{ asset('assets/images/notification/ok-48.png') }}', 3000);
    @endif
    @if (session('warning'))
    showToStr('Warning!', '{{ session('warning') }}', 'warning',
            '{{ asset('assets/images/notification/medium_priority-48.png') }}', 3000);
    @endif
    @if (session('status'))
    showToStr('Success', '{{ session('status') }}', 'info',
            '{{ asset('assets/images/notification/ok-48.png') }}', 3000);
    @endif
    $(document).ready(function() {
        $(document).on('click', '.show_confirm', function(event) {
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
            $($(".pc-dt-simple")).each(function(index, element) {
                var id = $(element).attr('id');
                const dataTable = new simpleDatatables.DataTable("#" + id);
            });
        }
    });
</script>
<script>
    const sweetAlert = Swal.mixin({
        customClass: {
            confirmButton: 'btn btn-success m-1',
            cancelButton: 'btn btn-danger m-1'
        },
        buttonsStyling: false,
        title: 'Are you sure?',
         text: "This action can not be undone. Do you want to continue?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Next Page',
        cancelButtonText: 'No',
        reverseButtons: true
    })
</script>
