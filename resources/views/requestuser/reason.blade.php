{!! Form::model($requestUser, [
    'route' => ['requestuser.disapprove', $requestUser->id],
    'method' => 'POST',
    'enctype' => 'multipart/form-data',
]) !!}
<div class="modal-body">
    <div class="form-group">
        {{ Form::label('disapprove_reason', __('Disapprove Reason'), ['class' => 'form-label']) }}
        <div class="input-group">
            {!! Form::textarea('disapprove_reason', null, [
                'class' => 'form-control ',
                ' required',
                'placeholder' => __('Enter disapprove reason'),
            ]) !!}
        </div>
    </div>
</div>
<div class="modal-footer">
    <div class="btn-flt float-end">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
        {{ Form::button(__('Save'), ['type' => 'submit', 'class' => 'btn btn-primary']) }}
    </div>
</div>
{!! Form::close() !!}
