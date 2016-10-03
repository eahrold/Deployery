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

    /**
     * Generate an SSK key for the project.
     * The key this generates is added to the ~/.ssh/authorized_keys file on the server
     * @param  string  $path  path where the keys should be generated
     * @param  boolean $force should old keys be removed and regenerated
     *
     * @return boolean  true if file was successfully created, false otherwise
     */
    public function generate(string $path, $force=false){
        if(!starts_with($path, storage_path())){
            abort(500, 'Generating SSH Keys at invalid path');
        }

        if($this->mkdir($path)){
            if($force && file_exists("{$path}id_rsa") && !$this->destroy($path)){
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

    private function mkdir($path){
        $command = new Command('mkdir');
        $command->addFlag('p') // recursive create path
                ->addFlag('m', '700') // mode: Restrict to PHP Process user
                ->addParam($path);
        return $this->run($command);
    }

    private function create($path){
        // ssh-keygen -b 2048 -t rsa -N "" -f /path/to/keys/ -q
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
        $command->addParam("{$path}id_rsa");
        $command->addParam("{$path}id_rsa.pub");
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