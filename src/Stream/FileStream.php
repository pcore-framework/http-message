<?php

declare(strict_types=1);

namespace PCore\HttpMessage\Stream;

use Exception;
use Psr\Http\Message\StreamInterface;
use function fclose;
use function feof;
use function fopen;
use function fread;
use function fstat;
use function ftell;
use function stream_get_contents;
use function stream_get_meta_data;

/**
 * Class FileStream
 * @package PCore\HttpMessage\Stream
 * @github https://github.com/pcore-framework/http-message
 */
class FileStream implements StreamInterface
{

    protected const READ_WRITE_HASH = [
        'read' => [
            'r' => true, 'w+' => true, 'r+' => true, 'x+' => true, 'c+' => true,
            'rb' => true, 'w+b' => true, 'r+b' => true, 'x+b' => true,
            'c+b' => true, 'rt' => true, 'w+t' => true, 'r+t' => true,
            'x+t' => true, 'c+t' => true, 'a+' => true
        ],
        'write' => [
            'w' => true, 'w+' => true, 'rw' => true, 'r+' => true, 'x+' => true,
            'c+' => true, 'wb' => true, 'w+b' => true, 'r+b' => true,
            'x+b' => true, 'c+b' => true, 'w+t' => true, 'r+t' => true,
            'x+t' => true, 'c+t' => true, 'a' => true, 'a+' => true
        ]
    ];

    /**
     * @var false|resource
     */
    protected $stream;

    /**
     * @param string $path адрес файла
     * @param int $offset смещение
     * @param int $length длина
     */
    public function __construct(string $path, int $offset = 0, protected int $length = -1)
    {
        $this->stream = fopen($path, 'rw+');
        $this->seek($offset);
    }

    /**
     * @return int
     */
    public function getLength(): int
    {
        return $this->length;
    }

    /**
     * @inheritDoc
     */
    public function __toString()
    {
        return stream_get_contents($this->stream, $this->length, $this->tell());
    }

    /**
     * @inheritDoc
     */
    public function close()
    {
        fclose($this->stream);
    }

    /**
     * @inheritDoc
     */
    public function detach()
    {
        throw new \BadMethodCallException('Не реализовано.');
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function getSize()
    {
        $stats = fstat($this->stream);
        return $stats['size'] ?? throw new Exception('Невозможно определить размер потока.');
    }

    /**
     * @inheritDoc
     */
    public function tell()
    {
        return (int)ftell($this->stream);
    }

    /**
     * @inheritDoc
     */
    public function eof()
    {
        return feof($this->stream);
    }

    /**
     * @inheritDoc
     */
    public function isSeekable()
    {
        return $this->getMetadata('seekable');
    }

    /**
     * @inheritDoc
     */
    public function seek($offset, $whence = SEEK_SET)
    {
        fseek($this->stream, $offset, $whence);
    }

    /**
     * @inheritDoc
     */
    public function rewind()
    {
        rewind($this->stream);
    }

    /**
     * @inheritDoc
     */
    public function isWritable()
    {
        return static::READ_WRITE_HASH['write'][$this->getMetadata('mode')];
    }

    /**
     * @inheritDoc
     */
    public function write($string)
    {
        fwrite($this->stream, $string);
    }

    /**
     * @inheritDoc
     */
    public function isReadable()
    {
        return static::READ_WRITE_HASH['read'][$this->getMetadata('mode')];
    }

    /**
     * @inheritDoc
     */
    public function read($length)
    {
        return fread($this->stream, $length);
    }

    /**
     * @inheritDoc
     */
    public function getContents()
    {
        return $this->__toString();
    }

    /**
     * @inheritDoc
     */
    public function getMetadata($key = null)
    {
        $meta = stream_get_meta_data($this->stream);
        return $key ? $meta[$key] ?? null : $meta;
    }

    public function __destruct()
    {
        if (is_resource($this->stream)) {
            $this->close();
        }
    }

}