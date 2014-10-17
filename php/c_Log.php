<?php
/**
 * Created by PhpStorm.
 * User: Jonathan
 * Date: 10/16/2014
 * Time: 8:00 PM
 */
/*
 *  This class is used for writing a log file of database changes. It is intended to be directly related to
 *  the inventory system's layout.
 */

class LogFile
{
    private $log_file;
    private $directory;

    public function __construct()
    {
        $this->setDirectory();
        $this->setCompletePath('database-logs.txt');
    }

    public function setDirectory()
    {
        $this->directory = $_SERVER['DOCUMENT_ROOT'] . '/logs/';
    }

    public function setFile($file)
    {
        $this->setCompletePath($file);
    }


    public function writeLog($log)
    {

        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        date_default_timezone_set('America/New_York');

        $time =  ' ' . date('D') . ' [' . date('d/M/Y H:i:s', time()) . '] ';

        $log_line = $ip . $time . $_SERVER['SERVER_PROTOCOL'] . ' "' . $_SERVER['REQUEST_METHOD'] . ' ' . $_SERVER['REQUEST_URI'] . '" ' . $log . "\r\n";

        $this->file_force_contents($log_line);
    }


    private function setCompletePath($file_name)
    {
        $this->log_file = $this->directory . $file_name;
    }

    private function file_force_contents($contents)
    {
        return;
        $parts = explode('/', $this->log_file);
        $file = array_pop($parts);
        $dir = array_shift($parts);

        foreach ($parts as $part) {
            if (!is_dir($dir .= "/$part")) {
                mkdir($dir);
            }
        }
        file_put_contents("$dir/$file", $contents, FILE_APPEND | LOCK_EX);
    }

}