{{ Form::open(['route' => ['test.send.mail'], 'data-validate']) }}
<div class="modal-body">
    <div class="form-group">
        {{ Form::label('email', __('Email'), ['class' => 'form-label']) }}
        {!! Form::text('email', null, [
            'class' => 'form-control',
            'required',
            'placeholder' => __('Enter email'),
        ]) !!}
        @error('email')
            <span class="invalid-email" role="alert">
                <strong class="text-danger">{{ $message }}</strong>
            </span>
        @enderror
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
    {{ Form::button(__('Save'), ['type' => 'submit', 'class' => 'btn btn-primary']) }}
</div>
{{ Form::close() }}
