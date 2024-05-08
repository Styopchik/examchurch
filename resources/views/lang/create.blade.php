{!! Form::open([
    'route' => 'store.language',
    'method' => 'POST',
    'data-validate',
    'novalidate',
]) !!}
<div class="modal-body">
    <div class="form-group">
        {{ Form::label('code', __('Language Code'), ['class' => 'form-label']) }}
        {{ Form::text('code', null, ['placeholder' => __('Enter language code'), 'class' => 'form-control', 'required']) }}
    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{ __('Close') }}" class="btn btn-secondary" data-bs-dismiss="modal">
    {!! Form::button(__('Save'), ['type' => 'submit', 'class' => 'btn btn-primary']) !!}
</div>
{!! Form::close() !!}
