@setup

    if(isset($on)){
        if(!isset(
            $s_name,
            $s_host,
            $s_user,
            $s_pass,
            $s_branch,
            $s_clone_url,
            $s_repo_path,
            $s_backup_dir,
            $s_port,
            $s_sshkey,
            $s_deployment_path,
            $s_sub_directory))
            {
                throw new Exception('Required Deployment Parameters are not present)');
            }
    } else {
            $s_name=null;
            $s_name=null;
            $s_host=null;
            $s_user=null;
            $s_pass=null;
            $s_branch=null;
            $s_clone_url=null;
            $s_repo_path=null;
            $s_backup_dir=null;
            $s_port=null;
            $s_sshkey=null;
            $s_deployment_path=null;
            $s_sub_directory=null;

            $on = 'default';
    }

    $cwd = realpath(dirname(__FILE__));

    //----------------------------------------------------------
    // Vars...
    //-------------------------------------------------------
    $pubkey = null;

    //----------------------------------------------------------
    // Servers
    //-------------------------------------------------------
    $servers = [
        'localhost' => '127.0.0.1',
        'default'=>''
    ];

    if(isset($s_name, $s_host, $s_user)){
        $servers = array_merge($servers,["{$on}" => "{$s_user}@{$s_host}"]);
    }

    //----------------------------------------------------------
    // Directories
    //-------------------------------------------------------
    $s_backup_dir  = 'storage/backups/';
    $uploads_dir = 'public/uploads/';

    //----------------------------------------------------------
    // Slack Deplyment
    //-------------------------------------------------------
    $slack_webhook = '';
    $slack_channel = '#deployments';

    //----------------------------------------------------------
    // Init Variables
    //-------------------------------------------------------
    $backup = (bool)isset($backup);

    //----------------------------------------------------------
    // Timestamps
    //-------------------------------------------------------
    date_default_timezone_set("UTC");
    $now = new DateTime('NOW');
    $timestamp = $now->format('c');
@endsetup

@servers($servers)

@macro('deploy',['on' => 'localhost'])
    hello
@endmacro

@task('hello')
    echo "Hello world" {{ $on }}
@endtask

@macro('backup',['on' => $on])
    backup_db
@endmacro

{{-- Access --}}
@task('upload_key', ['on' => $on])
    @if($pubkey)
    echo {{ trim($pubkey) }} >> ~/.ssh/authorized_keys
    echo Added key.
    @else
    echo No Key Specified
    @endif
@endtask

{{-- DB --}}
@task('backup_db',['on'=>$on])
    cd {{ $s_deployment_path }}
    while read line; do
        if [ -n "$line" ]; then
            export "$line"  ;
        fi ; done < .env
    if [ -n ${DB_USERNAME} ] && [ -n ${DB_PASSWORD} ] && [ -n ${DB_DATABASE} ]; then
        mkdir -p {{ $s_backup_dir }}/db/
        /usr/bin/mysqldump -u ${DB_USERNAME} --password=${DB_PASSWORD} ${DB_DATABASE} > {{ $s_backup_dir }}/db/{{$timestamp}}.sql
        echo Done backing up the SQL Database ${DB_DATABASE}
    else
        echo The .env file did not contain the requisit information to backup the DB.
    fi
@endtask

@task('migrate', ['on'=>$on])
    cd {{ $s_deployment_path }}
    artisan migrate
@endtask


@task('git_branches', ['on'=>'localhost'])
    cd {{ $s_repo_path }}
    git branch -a
@endtask

@task('switch_branch', ['on'=>'localhost'])
    cd {{ $s_repo_path }}
    git checkout {{ $s_repo_path }}
@endtask

{{-- Files --}}
@task('get_uploads',['on'=>'localhost'])
    rsync -ave "ssh -i {{ $s_sshkey }}" {{ $servers[$on] }}:{{ $s_deployment_path }}/{{ $uploads_dir }}/ $(pwd)/{{ $uploads_dir }}
@endtask

@task('put_uploads',['on'=>'localhost'])
    rsync -ave "ssh -i {{ $s_sshkey }}" $(pwd)/{{ $uploads_dir }}/ {{ $servers[$on] }}:{{ $s_deployment_path }}/{{ $uploads_dir }}
@endtask

@task('get_backups',['on'=>'localhost'])
    @if(isset($servers[$on]))
    echo Getting backups
    mkdir -p $(pwd)/{{ $s_backup_dir }}
    echo make directory $(pwd)/{{ $s_backup_dir }}
    rsync -ave "ssh -i {{ $s_sshkey }}" {{ $servers[$on] }}:{{ $s_deployment_path }}/{{ $s_backup_dir }}/ $(pwd)/{{ $s_backup_dir }}
    @else
    echo No Servers specified.
    @endif
@endtask

{{-- Distro --}}
@task('composer_update', ['on'=>$on])
    cd {{ $s_deployment_path }}
    composer update
@endtask

@task('composer_install', ['on'=>$on])
    cd {{ $s_deployment_path }}
    composer install
@endtask

@task('npm_install', ['on'=>$on])
    cd {{ $s_deployment_path }}
    npm install
@endtask

@task('gulp', ['on'=>$on])
    cd {{ $s_deployment_path }}
    gulp --production
@endtask

@after
    @slack($slack_webhook, $slack_channel, "$task ran on $on.")
@endafter