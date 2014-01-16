<?php
namespace Witooh\Uploader;

class File implements IUploader {

    /**
     * @var string
     */
    protected $basePath;

    public function __construct($basePath)
    {
        $this->basePath = $basePath;
    }

    protected function getFullPath($path)
    {
        return $this->basePath . '/' . $path;
    }

    /**
     * @param string $src
     * @param string $dest
     * @return bool
     */
    public function save($src, $dest)
    {
        $dest = $this->getFullPath($dest);
        $parts = explode('/', $dest);
        $file = array_pop($parts);
        $dest = '';
        foreach($parts as $part)
            if(!is_dir($dest .= "/$part")) mkdir($dest);
        $f = file_put_contents("$dest/$file", $src);

        return $f == false ? false : true;
    }

    /**
     * @param $dest
     * @return bool
     */
    public function delete($dest)
    {
        return unlink ($this->getFullPath($dest));
    }


} 