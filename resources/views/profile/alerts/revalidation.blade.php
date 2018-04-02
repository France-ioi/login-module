@if(count($revalidation_fields) > 0)
    <div class="alert alert-danger">
        <ul>
            @foreach($revalidation_fields as $field)
                <li>{{ trans('merging_accounts.profile_messages')[$field] }}</li>
            @endforeach
        </ul>
    </div>
@endif