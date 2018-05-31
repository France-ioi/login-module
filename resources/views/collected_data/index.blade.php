@extends('layouts.popup')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('collected_data.header')
        </div>
        <div class="panel-body">
            <p>@lang('collected_data.p1')</p>
            <p>@lang('collected_data.p2')</p>
            <p>@lang('collected_data.p3')</p>

            <ul>
                <li>
                    @lang('collected_data.item_self'):
                    <a class="btn btn-default btn-xs" href="/collected_data/summary">
                        @lang('collected_data.summary')
                    </a>
                    <a class="btn btn-default btn-xs" href="/collected_data/export">
                        @lang('collected_data.export')
                    </a>
                    <a class="btn btn-danger btn-xs" href="#" id="btn-delete">
                        @lang('collected_data.delete')
                    </a>
                </li>
                @foreach($clients as $client)
                    <li>
                        @lang('collected_data.item_client') <strong>{{$client->name }}</strong>:
                        @if($client->api_url)
                            <a class="btn btn-default btn-xs"
                                href="{!!$client->api_url!!}?module=collected_data&user_id={{Auth::user()->id}}">
                                @lang('collected_data.manage')
                            </a>
                        @else
                            @lang('collected_data.api_not_available')
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>
    </div>


    <div class="modal fade" tabindex="-1" role="dialog" id="delete-confirmation">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">
                        @lang('collected_data.confirmation.title')
                    </h4>
                </div>
                <div class="modal-body">
                    {!! BootForm::open(['url' => '/collected_data/delete', 'id' => 'delete']) !!}
                        {!! BootForm::checkbox('confirm1', trans('collected_data.confirmation.cb1')) !!}
                        {!! BootForm::checkbox('confirm2', trans('collected_data.confirmation.cb2')) !!}
                    {!! BootForm::close() !!}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal" id="btn-confirm">
                        @lang('collected_data.confirmation.submit')
                    </button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">
                        @lang('ui.cancel')
                    </button>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" tabindex="-1" role="dialog" id="delete-alert">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">
                        @lang('collected_data.alert.title')
                    </h4>
                </div>
                <div class="modal-body">
                    <p>@lang('collected_data.alert.p1')</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">
                        @lang('ui.ok')
                    </button>
                </div>
            </div>
        </div>
    </div>


    <script>
        $(document).ready(function() {

            function toggleBtnConfirm() {
                var all_checked = true;
                $('form#delete').find('input[type=checkbox]').each(function() {
                    all_checked = all_checked && $(this).prop('checked');
                });
                $('#btn-confirm').toggle(all_checked);
            }

            function showConfirmation() {
                $('form#delete').find('input[type=checkbox]').each(function() {
                    $(this).prop('checked', false);
                    $(this).change(toggleBtnConfirm);
                });
                $('#btn-confirm').hide();
                $('#delete-confirmation').modal('show');
            }


            $('#btn-confirm').click(function() {
                $('#btn-confirm').prop('diabled', true);
                $('form#delete').submit();
            })


            var clients_linked = {!! count($clients) > 0 !!};
            clients_linked = 0;
            $('#btn-delete').click(function(e) {
                if(clients_linked) {
                    $('#delete-alert').modal('show');
                } else {
                    showConfirmation();
                }
            })


        })
    </script>
@endsection