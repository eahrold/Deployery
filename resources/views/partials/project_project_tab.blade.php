@if(!$model->id)
{!! BootForm::open()->post()
                    ->action(route($model->getTable().".store"))
                    ->multipart()
                    ->role('form') !!}
@else
{!! BootForm::open()->put()
                    ->action(route($model->getTable().".update", $model->id))
                    ->multipart()
                    ->role('form') !!}
{!! BootForm::bind($model) !!}
@endif

<div class='pin-right'>
    @include('includes.save_buttons')
</div>
<div class='clearfix'></div>
{!! BootForm::hidden('id') !!}
{!! BootForm::text('Name', 'name') !!}
{!! BootForm::text('Git Clone URL', 'repo') !!}
{!! BootForm::text('Default Branch', 'branch') !!}
{!! BootForm::close() !!}