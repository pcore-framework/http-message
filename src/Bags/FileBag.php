<?php

namespace PCore\HttpMessage\Bags;

use PCore\HttpMessage\Stream\FileStream;
use PCore\HttpMessage\UploadedFile;

/**
 * Class FileBag
 * @package PCore\HttpMessage\Bags
 * @github https://github.com/pcore-framework/http-message
 */
class FileBag
{

    /**
     * @param array $uploadedFiles
     */
    public function __construct(protected array $uploadedFiles = [])
    {
    }

    /**
     * @return static
     */
    public static function createFromGlobal(): static
    {
        $bag = new static();
        foreach ($_FILES as $key => $file) {
            $bag->convertToUploadedFiles($bag->uploadedFiles, $key, $file['name'], $file['tmp_name'], $file['type'], $file['size'], $file['error']);
        }
        return $bag;
    }

    /**
     * @param $uploadedFiles
     * @param $k
     * @param $name
     * @param $tmpName
     * @param $type
     * @param $size
     * @param $error
     * @return void
     */
    protected function convertToUploadedFiles(&$uploadedFiles, $k, $name, $tmpName, $type, $size, $error): void
    {
        if (is_string($name)) {
            $uploadedFiles[$k] = new UploadedFile($error > 0 ? null : new FileStream($tmpName), $size, $name, $type, $error);
        } else {
            foreach ($name as $key => $value) {
                $this->convertToUploadedFiles($uploadedFiles[$k], $key, $value, $tmpName[$key], $type[$key], $size[$key], $error[$key]);
            }
        }
    }

    /**
     * @return array
     */
    public function all(): array
    {
        return $this->uploadedFiles;
    }

}