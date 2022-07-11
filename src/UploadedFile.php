<?php

declare(strict_types=1);

namespace PCore\HttpMessage;

use Psr\Http\Message\{StreamInterface, UploadedFileInterface};
use RuntimeException;
use SplFileInfo;

/**
 * Class UploadedFile
 * @package PCore\HttpMessage
 * @github https://github.com/pcore-framework/http-message
 */
class UploadedFile implements UploadedFileInterface
{

    protected const ERROR_MESSAGES = [
        UPLOAD_ERR_OK => 'Файл успешно загружен.',
        UPLOAD_ERR_INI_SIZE => 'Загруженный файл превысил ограничение для upload_max_filesize в php.ini.',
        UPLOAD_ERR_FORM_SIZE => 'Размер загруженного файла превышает значение, указанное параметром MAX_FILE_SIZE в HTML-форме.',
        UPLOAD_ERR_PARTIAL => 'Была загружена только часть файла.',
        UPLOAD_ERR_NO_FILE => 'Никакие файлы не были загружены.',
        UPLOAD_ERR_NO_TMP_DIR => 'Не удается найти временную папку.',
        UPLOAD_ERR_CANT_WRITE => 'Ошибка записи файла.'
    ];

    /**
     * @param null|StreamInterface $stream поток файлов
     * @param int $size размер файла
     * @param string $clientFilename имя файла клиента
     * @param string $clientMediaType тип клиентского носителя
     * @param int $error код ошибки
     */
    public function __construct(
        protected ?StreamInterface $stream = null,
        protected int $size = 0,
        protected string $clientFilename = '',
        protected string $clientMediaType = '',
        protected int $error = \UPLOAD_ERR_OK,
    )
    {
    }

    /**
     * {@inheritDoc}
     */
    public function getStream()
    {
        return $this->stream;
    }

    /**
     * {@inheritDoc}
     * @return SplFileInfo
     */
    public function moveTo($targetPath)
    {
        if (($code = $this->getError()) > 0) {
            throw new RuntimeException(static::ERROR_MESSAGES[$code], $code);
        }
        $path = pathinfo($targetPath, PATHINFO_DIRNAME);
        !is_dir($path) && mkdir($path, 0755, true);
        if (move_uploaded_file($this->stream->getMetadata('uri'), $targetPath)) {
            return new SplFileInfo($targetPath);
        }
        throw new RuntimeException('Не удалось загрузить файл. Проверьте разрешение каталога.');
    }

    /**
     * {@inheritDoc}
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * {@inheritDoc}
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * {@inheritDoc}
     */
    public function getClientFilename()
    {
        return $this->clientFilename;
    }

    /**
     * {@inheritDoc}
     */
    public function getClientMediaType()
    {
        return $this->clientMediaType;
    }

}