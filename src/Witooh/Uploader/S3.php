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
     * @param string $dir
     * @return bool
     */
    public function deleteFolder($dir)
    {
        if(substr($dir,-1) == "/") {
            $dir = substr($dir,0,-1);
        }

        if(substr($dir,0, 1) == "/"){
            $dir = substr($dir, 1);
        }
        $keys = [];
        $re = $this->s3->listObjects([
            'Bucket'=>$this->bucket,
            'Prefix'=>$dir,
        ]);

        foreach ($re['Contents'] as $c){
            $keys[] = ['Key'=>$c['Key']];
        }

        $this->s3->deleteObjects([
            'Bucket'=>$this->bucket,
            'Objects'=>$keys,
            'Quiet'=>true,
        ]);

        return true;
    }

} 