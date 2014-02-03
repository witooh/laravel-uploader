<?php
namespace Witooh\Uploader;

interface IUploader {

    /**
     * @param string $src
     * @param string $dest
     * @return bool
     */
    public function save($src, $dest);

    /**
     * @param $dest
     * @return bool
     */
    public function delete($dest);

    /**
     * @param string $dest
     * @return bool
     */
    public function deleteFolder($dest);
} 