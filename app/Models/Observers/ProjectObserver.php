<?php

namespace App\Models\Observers;

use AdamBrett\ShellWrapper\Command;
use App\Models\Project;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;


class ProjectObserver {

    public function saving(Project $model)
    {
        if(file_exists($model->repoPath())){
            return;
        }

        $builder = new ProcessBuilder(['git','clone', $model->repo, $model->local_repo_name]);
        $builder->setWorkingDirectory($model->projectStore());
        $builder->getProcess()->run();
    }

    public function deleted(Project $model)
    {
        if(!file_exists($model->projectStore())){
            return;
        }

        $command = new Command('rm');
        $command->addFlag('-R');
        $command->addParam($model->projectStore());
        $this->run($command);
    }

}