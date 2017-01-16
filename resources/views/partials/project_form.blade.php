@if(!$model->id)
{!! BootForm::open()->id('project-submit')
                    ->post()
                    ->action(route($model->getTable().".store"))
                    ->multipart()
                    ->role('form') !!}
@else
{!! BootForm::open()->id('project-submit')
                    ->put()
                    ->action(route($model->getTable().".update", $model->id))
                    ->multipart()
                    ->role('form') !!}
{!! BootForm::bind($model) !!}
@endif


<div class='clearfix'></div>
{!! BootForm::hidden('id') !!}
{!! BootForm::text('Name', 'name') !!}
{!! BootForm::text('Git Clone URL', 'repo') !!}
{!! BootForm::text('Default Branch', 'branch') !!}

{!! BootForm::checkbox('Send Slack Notification', 'send_slack_messages') !!}
{!! BootForm::text('Slack Webhook URL', 'slack_webhook_url') !!}

<div class='pin-right projects'>
    @include('includes.save_buttons')
</div>

{!! BootForm::close() !!}

<div class="modal fade" id="project-saving-modal" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content height-3x">
            <div class="modal-body">
                <div class="row">
                   <div class='inline header'>
                        Saving Repo...
                    </div>
                    <div class="inline">
                        <i class="fa fa-2x fa-spinner fa-spin" aria-hidden="true"></i>
                    </div>
                </div>
                <div class="row">
                    <div class="inline">
                        Cloning...
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@section('js')
<script type="text/javascript">
    $('form#project-submit').submit(function(){
        $('.projects .btn').prop('disabled', true);
    });

@if(!$model->id)
    $(".projects .btn.save").on('click', function(){
        $("#project-saving-modal").modal();
        return true;
    });
@endif
</script>
@append