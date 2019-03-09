<?php

namespace App\Services\SSH;

use AdamBrett\ShellWrapper\Command\Builder as Command;
use AdamBrett\ShellWrapper\Command\Param;
use AdamBrett\ShellWrapper\Command\Flag;
use AdamBrett\ShellWrapper\Runners\Exec;

class SSHKeyer
{
    /**
     * Exit Code
     * @var integer
     */
    private $rc = 0;

    /**
     * Shell Exex
     * @var \AdamBrett\ShellWrapper\Runners\Exec
     */
    private $shell;

    public function __construct() {
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
    public function generate(string $path, $force = false) {
        if (!starts_with($path, storage_path())) {
            abort(500, 'Generating SSH Keys at invalid path');
        }

        if ($this->mkdir($path)) {
            if ($force && file_exists("{$path}id_rsa") && $this->destroy($path) == false) {
                return $this->success();
            }
            $this->create($path);
        }
        return $this->success();
    }

    /**
     * Run a command
     * @param  Command $command the command to run
     * @return boolean   success/failure of the last command
     */
    private function run(Command $command) {
        $this->shell->run($command);
        return $this->success();
    }

    /**
     * Make a directory
     *
     * @param  string $path
     * @return boolean   success/failure of the last command
     */
    private function mkdir($path) {
        $command = new Command('mkdir');
        $command->addFlag('p') // recursive create path
                ->addFlag('m', '700') // mode: Restrict to PHP Process user
                ->addParam($path);
        return $this->run($command);
    }

    /**
     * Create the SSH Key
     *
     * @param  string $path
     * @return boolean   success/failure of the last command
     */
    private function create($path) {
        // ssh-keygen -b 4096 -t rsa -N "" -f /path/to/keys/ -q
        $command = new Command('ssh-keygen');
        $command->addFlag('b', '4096')
                ->addFlag('t', 'rsa')
                ->addFlag('N', "")
                ->addFlag('f', $path.'id_rsa')
                ->addFlag('q');
        return $this->run($command);
    }

    /**
     * Remove the SSH Key
     *
     * @param  string $path
     * @return boolean   success/failure of the last command
     */
    private function destroy($path) {
        $command = new Command('rm');
        $command->addParam("{$path}id_rsa");
        $command->addParam("{$path}id_rsa.pub");
        return $this->run($command);
    }

    /**
     * Get the exit status of the last command
     *
     * @return boolean   success/failure of the command
     */
    private function success() {
        return $this->status() == 0;
    }

    /**
     * Get the exit code of the last command
     *
     * @return integer
     */
    public function status() {
        return $this->shell->getReturnValue();
    }

    /**
     * String output of the last command
     *
     * @return string
     */
    public function output() {
        return $this->shell->getOutput();
    }
}