{!! Form::open([
    'route' => 'coupon.upload.store',
    'method' => 'Post',
    'data-validate',
    'enctype' => 'multipart/form-data',
]) !!}
<div class="modal-body">
    <div class="mb-4 col-md-12">
          <a href="{{ Storage::url('coupon/coupon.csv') }}" class="btn btn-primary btn-sm"><i class="ti ti-download"></i> {{ __('Sample File') }}</a>
    </div>
    <div class="form-group">
        {{ Form::label('file', __('CSV Upload'), ['class' => 'col-form-label']) }}
        <div class="input-group">
            {!! Form::file('file', [
                'class' => 'form-control font-style',
                'placeholder' => __('CSV Upload'),
                'required' => 'required',
            ]) !!}
        </div>
    </div>
</div>
<div class="modal-footer">
    <div class="float-end">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
        {{ Form::button(__('Save'), ['type' => 'submit', 'class' => 'btn btn-primary']) }}
    </div>
</div>
{!! Form::close() !!}

