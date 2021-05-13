<?php

// src/Helper/FtpHelper.php

use Interop\Container\ContainerInterface;

namespace Application\Helper;

/**
 * Description of FtpHelper
 *
 * @author alex
 */
class FtpHelper
{

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
    public static function fetch(ContainerInterface $container, string $table, array $files = []): void
    {
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

        foreach ($files as $file) {
            $local_file = realpath($local_catalog) . "/" . $file;
            $server_file = $server_catalog . $file;

            // trying to download $server_file and save it to $local_file
            if (!ftp_get($conn_id, $local_file, $server_file, FTP_BINARY)) {
                //throw new \Exception('Could not complete the operation');
            }
        }
        // close connection
        ftp_close($conn_id);
    }

}
