<?php
namespace Witooh\Uploader;

class UploaderFactory {

    /**
     * @param array $config
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function make($config)
    {
        $driver = $config['driver'];
        switch($driver){
            case 'file': return $this->createFile($config);
            case 's3': return $this->createS3($config);
        }

        throw new \InvalidArgumentException("Unsupported driver [$driver]");
    }

    /**
     * @param array $config
     * @return File
     */
    public function createFile($config)
    {
        return new File($config['base_path']);
    }

    public function createS3($config)
    {
        return new S3($config);
    }
} 