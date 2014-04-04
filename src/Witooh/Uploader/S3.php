<?php
namespace Witooh\Uploader;

use Aws\S3\Enum\CannedAcl;
use Aws\S3\S3Client;
use Guzzle\Http\EntityBody;

class S3 implements IUploader {

    protected $s3;
    protected $bucket;

    public function __construct($config){
        $this->s3 = S3Client::factory([
            'key'=>$config['key'],
            'secret'=>$config['secret'],
            'region'=>$config['region'],
        ]);

        $this->bucket = $config['bucket'];
    }
    /**
     * @param string $src
     * @param string $dest
     * @return bool
     */
    public function save($src, $dest)
    {
        if (file_exists($src)) {
            $this->s3->putObject(array(
                'Bucket' => $this->bucket,
                'Key'    => $dest,
                'Body'   => EntityBody::factory(fopen($src, 'r')),
                'ContentType' => 'image/jpeg',
                'ACL'    => CannedAcl::PUBLIC_READ,
            ));
        }
    }

    /**
     * @param $dest
     * @return bool
     */
    public function delete($dest)
    {
        $this->s3->deleteObject(array(
            'Bucket'=>$this->bucket,
            'Key'=>$dest
        ));

        return true;
    }

    /**
     * @param string $directory
     * @return bool
     */
    public function deleteFolder($directory)
    {
        if(substr($directory,-1) == "/") {
            $directory = substr($directory,0,-1);
        }
        if(!file_exists($directory) || !is_dir($directory)) {
            return false;
        } elseif(!is_readable($directory)) {
            return false;
        } else {
            return $this->delete($directory);
        }
    }

} 