<?php

declare(strict_types=1);

namespace PCore\HttpMessage;

use PCore\HttpMessage\Bags\HeaderBag;
use PCore\HttpMessage\Stream\StringStream;
use Psr\Http\Message\{MessageInterface, StreamInterface};

/**
 * Class Message
 * @package PCore\HttpMessage
 * @github https://github.com/pcore-framework/http-message
 */
class Message implements MessageInterface
{

    protected string $protocolVersion = '1.1';
    protected HeaderBag $headers;
    protected ?StreamInterface $body = null;

    /**
     * @inheritDoc
     */
    public function getProtocolVersion()
    {
        return $this->protocolVersion;
    }

    /**
     * @inheritDoc
     */
    public function withProtocolVersion($version)
    {
        if ($this->protocolVersion !== $version) {
            $this->protocolVersion = $version;
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getHeaders()
    {
        return $this->headers->all();
    }

    /**
     * @inheritDoc
     */
    public function hasHeader($name)
    {
        return $this->headers?->has($name);
    }

    /**
     * @inheritDoc
     */
    public function getHeader($name)
    {
        return $this->headers?->get($name);
    }

    /**
     * @inheritDoc
     */
    public function getHeaderLine($name)
    {
        if ($this->hasHeader($name)) {
            return implode(', ', $this->getHeader($name));
        }
        return '';
    }

    /**
     * @inheritDoc
     */
    public function withHeader($name, $value)
    {
        $new = clone $this;
        $new->headers = clone $this->headers;
        $new->headers->set($name, $value);
        return $new;
    }

    /**
     * @inheritDoc
     */
    public function withAddedHeader($name, $value)
    {
        $new = clone $this;
        $new->headers = clone $this->headers;
        $new->headers->add($name, $value);
        return $new;
    }

    /**
     * @inheritDoc
     */
    public function withoutHeader($name)
    {
        $new = clone $this;
        $new->headers = clone $this->headers;
        $new->headers->remove($name);
        return $new;
    }

    /**
     * @inheritDoc
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @inheritDoc
     */
    public function withBody(StreamInterface $body)
    {
        $this->body = $body;
        return $this;
    }

    protected function formatBody(string|StreamInterface|null $body)
    {
        $this->body = $body instanceof StreamInterface ? $body : new StringStream((string)$body);
    }

}