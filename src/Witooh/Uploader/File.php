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
        try{
            return unlink ($this->getFullPath($dest));
        }catch (\Exception $e){
            return false;
        }
    }

    /**
     * @param string $dest
     * @return bool
     */
    public function deleteFolder($dest)
    {
        try{
            return $this->deleteAll($this->getFullPath($dest));
        }catch (\Exception $e){
            return false;
        }
    }

    protected function deleteAll($directory, $empty = false) {
        if(substr($directory,-1) == "/") {
            $directory = substr($directory,0,-1);
        }

        if(!file_exists($directory) || !is_dir($directory)) {
            return false;
        } elseif(!is_readable($directory)) {
            return false;
        } else {
            $directoryHandle = opendir($directory);

            while ($contents = readdir($directoryHandle)) {
                if($contents != '.' && $contents != '..') {
                    $path = $directory . "/" . $contents;

                    if(is_dir($path)) {
                        $this->deleteAll($path);
                    } else {
                        unlink($path);
                    }
                }
            }

            closedir($directoryHandle);

            if($empty == false) {
                if(!rmdir($directory)) {
                    return false;
                }
            }

            return true;
        }
    }
} 