<?php

namespace App\Services;

use AdamBrett\ShellWrapper\Command\Builder as Command;
use AdamBrett\ShellWrapper\Command\Param;
use AdamBrett\ShellWrapper\Command\Flag;
use AdamBrett\ShellWrapper\Runners\Exec;

class SSHKeyer
{
    private $rc = 0;
    private $shell;

    public function __construct(){
        $this->shell = new Exec();
    }

    public function generate($path, $force=false){
        // ssh-keygen -b 2048 -t rsa -N "" -f /path/to/keys/ -q
        if(!starts_with($path, storage_path())){
            abort(500, 'Generating SSH Keys at invalid path');
        }

        $command = new Command('mkdir');
        $command->addFlag('p')
                ->addFlag('m', '700') // Restrict to PHP Process user
                ->addParam($path);

        if($this->run($command)){
            if($force && file_exists($path.'id_rsa') && !$this->destroy($path)){
                return $this->success();
            }
            $this->create($path);
        }
        return $this->success();
    }

    private function run(Command $command){
        $this->shell->run($command);
        return $this->success();
    }

    private function create($path){
        $command = new Command('ssh-keygen');
        $command->addFlag('b','4096')
                ->addFlag('t', 'rsa')
                ->addFlag('N', "")
                ->addFlag('f', $path.'id_rsa')
                ->addFlag('q');
        return $this->run($command);
    }

    private function destroy($path){
        $command = new Command('rm');
        $command->addParam($path.'id_rsa');
        $command->addParam($path.'id_rsa.pub');
        $this->run($command);
    }

    private function success(){
        return $this->status() == 0;
    }

    public function status(){
        return $this->shell->getReturnValue();
    }

    public function output(){
        return $this->shell->getOutput();
    }
}