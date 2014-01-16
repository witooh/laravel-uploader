<?php
namespace Witooh\Uploader;

class Uploader {

    /**
     * @var IUploader
     */
    protected $driver;

    public function __construct($driver)
    {
        $this->driver = $driver;
    }

    /**
     * @param string $src
     * @param string $dest
     * @return bool
     */
    public function save($src, $dest)
    {
        return $this->driver->save($src, $dest);
    }

    /**
     * @param string $dest
     * @return bool
     */
    public function delete($dest)
    {
        return $this->driver->delete($dest);
    }
} 