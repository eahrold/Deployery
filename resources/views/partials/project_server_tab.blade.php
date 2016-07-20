<table class='table'>
    <thead>
        <th>Name</th>
        <th>Hostname</th>
        <th class='crunch'></th>
        <th class='crunch'>
            <a href='{{ route("projects.{projects}.servers.create", $model ) }}'>
                <i class="fa fa-plus-circle" aria-hidden="true"></i>
            </a>
        </th>
    </thead>
    <tbody>
        @foreach($servers as $server)
        <tr>
            <td>{{ $server->name }}</td>
            <td>{{ $server->hostname }}</td>
            <td>
                <a href='{{ route("projects.{projects}.servers.edit", [$model->id, $server->id]) }}' alt='edit'>
                    <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                </a>
            </td>
            <td>
                @include('includes.trash_button', ['route'=>route("projects.{projects}.servers.destroy", [$model->id, $server->id])])
            </td>
            <td>
                <a href="#" onclick="deploy({{$server->id}},'{{$server->name}}')">Deploy</a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<div id='deployment'>
    <div class='table-responsive'>
        <table class="table">
            <tr v-for='message in messages'>
                <td>@{{ message }}</td>
            </tr>
        </table>
    </div>
</div>

@section('deploy-js')

<script src="https://js.pusher.com/3.1/pusher.min.js"></script>
<script type="text/javascript">

    var deployment = new Vue({
        el: '#deployment',
        data: {
            messages: [],
            errors: [],
            deploying: false
        },
        ready: function(){
            Pusher.logToConsole = true;
            var pusher = new Pusher('6b86fa670dba9514e144', {
                encrypted: true
            });

            var socket_id = 'deployment_{{ $model->id }}';
            var channel = pusher.subscribe(socket_id);
            var self = this;
            channel.bind('App\\Events\\ServerDeploymentEvent', this.handleMessage);
        },
        computed: {
            complete: function(){
                return !this.deploying;
            },
            hasErrors: function(){
                return this.errors.length;
            }
        },
        methods: {
            beginDeployment: function(name){
                this.messages = ['Queued server deployment for'+name];
                this.errors = [];
            },

            handleMessage: function(data){
                this.messages.unshift(data.message);
                if(data.type === 'complete'){
                    this.deploying = false;
                }
                if(data.type === 'starting'){
                    this.deploying = true;
                }
            }
        }
    });

    function deploy(serverId, serverName){
        var endpoint = '/api/projects/{{$model->id}}/servers/'+serverId+'/deploy';
        $.ajax({
            url: endpoint,
            headers: {
                'X-CSRF-Token': '{!! csrf_token() !!}'
            },
            method: 'POST',
            success: function(data){
                deployment.beginDeployment(serverName);
            },
            error: function(resp){
                console.log('error:', resp);
            }
        });
    }
</script>
@endsection