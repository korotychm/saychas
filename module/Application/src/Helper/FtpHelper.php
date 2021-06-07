<?php

// src/Helper/FtpHelper.php

namespace Application\Helper;

use Interop\Container\ContainerInterface;
use Laminas\Stdlib\Exception\InvalidArgumentException;

//use Laminas\I18n\Exception\InvalidArgumentException;
//use Laminas\Log\Logger;

/**
 * Description of FtpHelper
 *
 * @author alex
 */
class FtpHelper
{

    /**
     * Fetch file from ftp
     *
     * @param Config $config
     * @param string $table
     * @param string $file_name
     */
    public static function fetchOne($config, $table, $file_name)
    {
        $server_catalog = $config['parameters']['server_catalog'][$table]['path'];
        $ftp_server = $config['parameters']['ftp_server']['domain']; // "nas01.saychas.office";
        $username = $config['parameters']['ftp_server']['username']; //"1C";
        $password = $config['parameters']['ftp_server']['password']; //"ree7EC2A";
        $filename = "ftp://$username:$password@{$ftp_server}{$server_catalog}{$file_name}";
        echo file_get_contents($filename, true);
        exit;
    }

    /**
     * Fetches files from specified ftp catalog
     * $table name should be either product or brand or provider at the moment
     *
     * @param ContainerInterface $container
     * @param string $table
     * @param array $files
     * @return void
     * @throws \Exception
     */
    public static function fetch($container, string $table, array $files, $callback, $update = null): void
    {
        if (null == $callback) {
            throw new InvalidArgumentException('Callback cannot be null');
        }
        $config = $container->get('Config');
        $local_catalog = $config['parameters']['local_catalog'][$table]['path'];
        $server_catalog = $config['parameters']['server_catalog'][$table]['path'];

        $ftp_server = $config['parameters']['ftp_server']['domain']; // "nas01.saychas.office";
        $username = $config['parameters']['ftp_server']['username']; //"1C";
        $password = $config['parameters']['ftp_server']['password']; //"ree7EC2A";
        // perform connection
        $conn_id = ftp_connect($ftp_server);
        $login_result = ftp_login($conn_id, $username, $password);

        if ((!$conn_id) || (!$login_result)) {
            throw new \Exception('FTP connection has failed! Attempted to connect to nas01.saychas.office for user ' . $username . '.');
        }

        $list = ftp_nlist($conn_id, $server_catalog);
        foreach ($files as $file) {
            $local_file = realpath($local_catalog) . "/" . $file;
            $server_file = $server_catalog . $file;
            if (!in_array($server_file, $list) && null != $callback) {
                continue;
            }
            // trying to download $server_file and save it to $local_file
            if (!ftp_get($conn_id, $local_file, $server_file, FTP_BINARY)) {
                self::warn($server_file);
            }
            if (null != $update) {
                $update($file);
            }
        }
        // close connection
        ftp_close($conn_id);
    }

    /**
     * Logs warnings
     *
     * @param type $message
     * @param type $extra
     */
    private static function warn(/* $logger, */ $message, $extra = [])
    {
        echo 'Failed to download file ' . $message;
    }

}
