<?php

namespace App\Services\SSH;

use phpseclib\Net\SFTP as SecLibSFTP;

// define('NET_SSH2_LOGGING', 2);
define('NET_SSH2_LOGGING_FILEPATH', storage_path('logs/ssh.log'));

/**
 * Pure-PHP implementations of SFTP.
 *
 * @package SFTP
 * @access  public
 */
class SFTP extends SecLibSFTP
{
    const LOG_REALTIME_FILENAME = NET_SSH2_LOGGING_FILEPATH;
}
