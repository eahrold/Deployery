<?php

namespace App\Services;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;


class EnvoyWrapper
{
    public function deploy($server, $callback = null){
        $envoy = base_path("/vendor/bin/envoy");

        $keypath = $server->project->keyPath("id_rsa");
        $local_repo = $server->project->repoPath();

        $builder = new ProcessBuilder([$envoy, "run", "deploy"]);
        $builder->add("--on={$server->slug}")
                ->add("--s_name={$server->name}")
                ->add("--s_protocol={$server->protocol}")
                ->add("--s_host={$server->hostname}")
                ->add("--s_port={$server->port}")
                ->add("--s_user={$server->username}")
                ->add("--s_pass={$server->password}")
                ->add("--s_sshkey={$keypath}")
                ->add("--s_deployment_path={$server->deployment_path}")
                ->add("--s_branch={$server->branch}")
                ->add("--s_clone_url={$server->project->repo}")
                ->add("--s_repo_path={$local_repo}")
                ->add("--s_sub_directory={$server->sub_directory}")
                ->add("--s_branch={$server->branch}")
                ->add("--s_backup_dir={$server->project->backupDir($server->name)}");

        $builder->getProcess()->run(function ($type, $buffer) use ($callback) {
            if (Process::ERR === $type) {
                $message = "ERROR: ".$buffer;
            } else {
                $message = $buffer;
            }
            $callback($message);
        });
    }
}