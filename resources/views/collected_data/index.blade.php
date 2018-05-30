@extends('layouts.popup')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('collected_data.header')
        </div>
        <div class="panel-body">
            <p>@lang('collected_data.description')</p>

            <h4>@lang('collected_data.s1.title')</h4>
            <p>@lang('collected_data.s1.description')</p>

            <ul>
                <li>
                    @lang('collected_data.s1.self'):
                    <a class="btn btn-default btn-xs" href="/collected_data/summary">
                        @lang('collected_data.s1.summary')
                    </a>
                    <a class="btn btn-default btn-xs" href="/collected_data/export">
                        @lang('collected_data.s1.export')
                    </a>
                </li>
                @foreach($clients as $client)
                    <li>
                        @lang('collected_data.s1.client') <strong>{{$client->name }}</strong>:
                        @if($client->api_url)
                            <a class="btn btn-default btn-xs"
                                href="{!!$client->api_url!!}?module=collected_data&action=summary">
                                @lang('collected_data.s1.summary')
                            </a>
                            <a class="btn btn-default btn-xs"
                                href="{!!$client->api_url!!}?module=collected_data&action=export">
                                @lang('collected_data.s1.export')
                            </a>
                        @else
                            @lang('collected_data.s1.not_available')
                        @endif
                    </li>
                @endforeach
            </ul>

            <h4>@lang('collected_data.s2.title')</h4>
            <p>@lang('collected_data.s2.description')</p>

            {!! BootForm::open(['url' => '/collected_data/delete', 'id' => 'delete']) !!}
                {!! BootForm::checkbox(
                    'del[]',
                    trans('collected_data.s2.self'),
                    'all',
                    false
                ) !!}
                @foreach($clients as $client)
                    {!! BootForm::checkbox(
                        'del['.$client->id.']',
                        trans('collected_data.s2.client').' '.$client->name,
                        $client->id,
                        false
                    ) !!}
                @endforeach
                {!! BootForm::submit(trans('collected_data.submit'), ['id' => 'btn-delete', 'class' => 'btn btn-danger']) !!}
            {!! BootForm::close() !!}

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
                    {!! BootForm::checkbox('confirm1', trans('collected_data.confirmation.cb1')) !!}
                    <ul>
                        <li del-target="all">
                            @lang('collected_data.confirmation.self')
                        </li>
                        @foreach($clients as $client)
                            <li del-target="{{$client->id}}">
                                @lang('collected_data.confirmation.client') <strong>{{$client->name }}</strong>
                            </li>
                        @endforeach
                    </ul>
                    {!! BootForm::checkbox('confirm2', trans('collected_data.confirmation.cb2')) !!}
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

    <script>
        $(document).ready(function() {

            var form = $('form#delete');
            var popup = $('#delete-confirmation');


            form.find('input[type=checkbox]').change(function() {
                var ckecked = $(this).prop('checked');
                var target = $(this).val();
                if(target == 'all' && ckecked) {
                    form.find('input[type=checkbox]').prop('checked', true);
                } else {
                    form.find('input[value=all]').prop('checked', false);
                }
            });


            popup.find('input[type=checkbox]').change(function() {
                var checked = [];
                popup.find('input[type=checkbox]').each(function() {
                    checked.push($(this).prop('checked'));
                });
                var all_checked = typeof checked.find(function(v) {
                    return v === false;
                }) === 'undefined';
                $('#btn-confirm').toggle(all_checked);
            });



            function submit() {
                $('btn-delete').prop('disabled', true);
                form.submit();
            }


            function showConfirmation() {
                var selection = {}
                form.find('input[type=checkbox]').each(function() {
                    var el = $(this);
                    selection[el.val()] = el.prop('checked');
                });
                popup.find('li').each(function() {
                    var el = $(this);
                    el.toggle(selection[el.attr('del-target')]);
                });
                $('#btn-confirm').hide();
                popup.find('input[type=checkbox]').prop('checked', false);
                popup.modal('show');
            }


            $('#btn-delete').click(function(e) {
                var need_confirmation = form.find('input[value=all]').prop('checked');
                if(need_confirmation) {
                    e.preventDefault();
                    showConfirmation();
                    return false;
                }
                submit();
            })

            $('#btn-confirm').click(function() {
                submit();
            })
        })
    </script>
@endsection