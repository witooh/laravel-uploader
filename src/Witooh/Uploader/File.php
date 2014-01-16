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
        return $this->basePath . $path;
    }

    /**
     * @param string $src
     * @param string $dest
     * @return bool
     */
    public function save($src, $dest)
    {
        $dest = $this->getFullPath($dest);
        $savePath = $dest;
        $parts = explode('/', $dest);
        array_pop($parts);
        $dest = '';
        foreach($parts as $part)
            if(!is_dir($dest .= "/$part")) mkdir($dest);

        return \File::move($src, $savePath);

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