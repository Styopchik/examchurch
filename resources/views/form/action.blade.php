@can('edit-form')
    @if ($form->json)
        @php
            $hashids = new Hashids('', 20);
            $id = $hashids->encodeHex($form->id);
        @endphp
        @can('theme-setting-form')
            <a class="text-white btn btn-secondary btn-sm" href="{{ route('form.theme', $form->id) }}" data-bs-toggle="tooltip"
                data-bs-placement="bottom" data-bs-original-title="{{ __('Theme Setting') }}"><i class="ti ti-layout-2"></i></a>
        @endcan
        @can('payment-form')
            <a class="text-white btn btn-warning btn-sm" href="{{ route('form.payment.integration', $form->id) }}"
                data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-original-title="{{ __('Payment Integration') }}"><i
                    class="ti ti-report-money"></i></a>
        @endcan
        @can('integration-form')
            <a class="text-white btn btn-info btn-sm" href="{{ route('form.integration', $form->id) }}" data-bs-toggle="tooltip"
                data-bs-placement="bottom" data-bs-original-title="{{ __('Integration') }}"><i class="ti ti-send"></i></a>
        @endcan
        @can('manage-form-rule')
            <a class="text-white btn btn-secondary btn-sm" href="{{ route('form.rules', $form->id) }}" data-bs-toggle="tooltip"
                data-bs-placement="bottom" data-bs-original-title="{{ __('Conditional Rules') }}"><i
                    class="ti ti-notebook"></i></a>
        @endcan
        <a class="btn btn-primary embed_form btn-sm" href="javascript:void(0);"
            onclick="copyToClipboard('#embed-form-{{ $form->id }}')" id="embed-form-{{ $form->id }}"
            data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-original-title="{{ __('Embed') }}"
            data-url='<iframe src="{{ route('forms.survey', $id) }}"
            scrolling="auto" align="bottom" height="100vh" width="100%"></iframe>'>
            <i class="ti ti-code"></i></a>
        @if ($form->limit_status == 1)
            @if ($form->limit > $formValue)
                <a class="btn btn-success btn-sm copy_form" onclick="copyToClipboard('#copy-form-{{ $form->id }}')"
                    href="javascript:void(0)" id="copy-form-{{ $form->id }}" data-bs-toggle="tooltip"
                    data-bs-placement="bottom" data-bs-original-title="{{ __('Copy Form URL') }}"
                    data-url="{{ route('forms.survey', $id) }}"><i class="ti ti-copy"></i></a>
            @endif
        @else
            <a class="btn btn-success btn-sm copy_form" onclick="copyToClipboard('#copy-form-{{ $form->id }}')"
                href="javascript:void(0)" id="copy-form-{{ $form->id }}" data-bs-toggle="tooltip"
                data-bs-placement="bottom" data-bs-original-title="{{ __('Copy Form URL') }}"
                data-url="{{ route('forms.survey', $id) }}"><i class="ti ti-copy"></i></a>
        @endif
        <a class="text-white btn btn-info btn-sm cust_btn" data-bs-toggle="tooltip"
            data-share="{{ route('forms.survey.qr', $id) }}" data-bs-toggle="tooltip" data-bs-placement="bottom"
            data-bs-original-title="{{ __('Show QR Code') }}" id="share-qr-code"><i class="ti ti-qrcode"></i></a>
    @endif
@endcan
@can('fill-form')
    @if ($form->json)
        @if ($form->limit_status == 1)
            @if ($form->limit > $formValue)
                <a class="btn btn-primary btn-sm" data-bs-toggle="tooltip" data-bs-placement="bottom"
                    data-bs-original-title="{{ __('Fill Form') }}" href="{{ route('forms.fill', $form->id) }}"><i
                        class="ti ti-list"></i></a>
            @endif
        @else
            <a class="btn btn-primary btn-sm" data-bs-toggle="tooltip" data-bs-placement="bottom"
                data-bs-original-title="{{ __('Fill Form') }}" href="{{ route('forms.fill', $form->id) }}"><i
                    class="ti ti-list"></i></a>
        @endif
    @endif
@endcan
@can('duplicate-form')
    <a href="#" class="btn btn-warning btn-sm" data-toggle="tooltip" data-bs-toggle="tooltip"
        data-bs-placement="bottom" data-bs-original-title="{{ __('Duplicate') }}"
        onclick="document.getElementById('duplicate-form-{{ $form->id }}').submit();" title="Duplicate"><i
            class="ti ti-box-multiple"></i></a>
@endcan
@can('design-form')
    <a class="btn btn-info btn-sm" href="{{ route('forms.design', $form->id) }}" id="design-form" data-bs-toggle="tooltip"
        data-bs-placement="bottom" data-bs-original-title="{{ __('Design') }}"><i class="ti ti-brush"></i></a>
@endcan
@can('edit-form')
    <a class="btn btn-primary edit_form btn-sm" href="{{ route('forms.edit', $form->id) }}" id="edit-form"
        data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-original-title="{{ __('Edit') }}"><i
            class="ti ti-edit"></i></a>
@endcan
@can('delete-form')
    {!! Form::open([
        'method' => 'DELETE',
        'route' => ['forms.destroy', $form->id],
        'id' => 'delete-form-' . $form->id,
        'class' => 'd-inline',
    ]) !!}
    <a href="#" class="btn btn-danger btn-sm show_confirm" id="delete-form-{{ $form->id }}"
        data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-original-title="{{ __('Delete') }}"><i
            class="mr-0 ti ti-trash"></i></a>
    {!! Form::close() !!}
@endcan
@can('duplicate-form')
    {!! Form::open(['method' => 'POST', 'route' => ['forms.duplicate'], 'id' => 'duplicate-form-' . $form->id]) !!}
    <input type="hidden" value="{{ $form->id }}" name="form_id">
    {!! Form::close() !!}
@endcan
