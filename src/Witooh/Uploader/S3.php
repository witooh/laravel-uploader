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
        $cacheage = 3600 * 24 * 360 * 5;
        if (file_exists($src)) {
            $this->s3->putObject(array(
                'Bucket' => $this->bucket,
                'Key'    => $dest,
                'Body'   => EntityBody::factory(fopen($src, 'r')),
                'CacheControl'=>'max-age='.$cacheage,
                'ContentType' => $this->MimeContentType($dest),
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

        $re = $this->s3->listObjects([
            'Bucket'=>$this->bucket,
            'Prefix'=>$dir,
        ]);

        if (isset($re['Contents']) && count($re['Contents']) > 0){
            $keys = [];
            foreach ($re['Contents'] as $c){
                $keys[] = ['Key'=>$c['Key']];
            }

            $this->s3->deleteObjects([
                'Bucket'=>$this->bucket,
                'Objects'=>$keys,
                'Quiet'=>true,
            ]);
        }



        return true;
    }

    private function MimeContentType($filename) {

        $mime_types = array(

            'txt' => 'text/plain',
            'htm' => 'text/html',
            'html' => 'text/html',
            'php' => 'text/html',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'xml' => 'application/xml',
            'swf' => 'application/x-shockwave-flash',
            'flv' => 'video/x-flv',

            // images
            'png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'ico' => 'image/vnd.microsoft.icon',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'svg' => 'image/svg+xml',
            'svgz' => 'image/svg+xml',

            // archives
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
            'exe' => 'application/x-msdownload',
            'msi' => 'application/x-msdownload',
            'cab' => 'application/vnd.ms-cab-compressed',

            // audio/video
            'mp3' => 'audio/mpeg',
            'qt' => 'video/quicktime',
            'mov' => 'video/quicktime',

            // adobe
            'pdf' => 'application/pdf',
            'psd' => 'image/vnd.adobe.photoshop',
            'ai' => 'application/postscript',
            'eps' => 'application/postscript',
            'ps' => 'application/postscript',

            // ms office
            'doc' => 'application/msword',
            'docx' => 'application/msword',
            'rtf' => 'application/rtf',
            'xls' => 'application/vnd.ms-excel',
            'ppt' => 'application/vnd.ms-powerpoint',

            // open office
            'odt' => 'application/vnd.oasis.opendocument.text',
            'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        );


        $x = explode('.',$filename);
        $ext = strtolower(array_pop($x));
        $last = substr($ext, -1, 1);
        if ($last == '/'){
            return null;
        }
        if (array_key_exists($ext, $mime_types)) {
            return $mime_types[$ext];
        }
        elseif (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME);
            $mimetype = finfo_file($finfo, $filename);
            finfo_close($finfo);
            return $mimetype;
        }
        else {
            return 'application/octet-stream';
        }
    }

} 