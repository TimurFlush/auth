<?php

declare(strict_types=1);

namespace TimurFlush\Auth\Accessor\PhpSession;

use TimurFlush\Auth\Exception;

final class CookieOptions
{
    /**
     * The prefixed name of the cookie.
     */
    protected string $prefixedName = 'TFAccessor';

    /**
     * The domain that the cookie is available.
     */
    protected string $domain = '';

    /**
     * The path on the server in which the cookie will be available on.
     */
    protected string $path = '/';

    /**
     * Indicates that the cookie should only be transmitted over a
     * secure HTTPS connection from the client.
     */
    protected bool $secure = false;

    /**
     * When true the cookie will be made accessible only through the HTTP
     * protocol.
     */
    protected bool $httpOnly = false;

    /**
     * Custom options.
     */
    protected array $customOptions = [];

    /**
     * CookieOptions constructor.
     *
     * @param string|null $prefixedName  A cookie's prefix name.
     * @param string|null $domain        A cookie's domain name.
     * @param string|null $path          A cookie's path.
     * @param bool|null   $secure        A cookie's secure status.
     * @param bool|null   $httpOnly      A cookie's http-only status.
     * @param array|null  $customOptions A cookie's custom options.
     *
     * @throws \TimurFlush\Auth\Exception If a specified prefixed name is empty.
     */
    public function __construct(
        string $prefixedName = null,
        string $domain = null,
        string $path = null,
        bool $secure = null,
        bool $httpOnly = null,
        array $customOptions = null
    ) {
        if (is_string($prefixedName) && empty($prefixedName)) {
            throw new Exception('A specified prefixed name cannot be empty.');
        }
        $this->prefixedName ??= $prefixedName;
        $this->domain ??= $domain;
        $this->path ??= $path;
        $this->secure ??= $secure;
        $this->httpOnly ??= $httpOnly;
        $this->customOptions ??= $customOptions;
    }

    /**
     * Sets cookie's custom options.
     *
     * @param array $options Associative array.
     */
    public function setCustomOptions(array $options): void
    {
        $this->customOptions = $options;
    }

    /**
     * Returns the prefixed name.
     */
    public function getPrefixedName(): string
    {
        return $this->prefixedName;
    }

    /**
     * Returns a cookie's domain name.
     */
    public function getDomain(): string
    {
        return $this->domain;
    }

    /**
     * Returns a cookie's path.
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Returns a cookie's secure status.
     */
    public function isSecure(): bool
    {
        return $this->secure;
    }

    /**
     * Returns a cookie's http-only status.
     */
    public function isHttpOnly(): bool
    {
        return $this->httpOnly;
    }

    /**
     * Returns cookie's custom options.
     */
    public function getCustomOptions(): array
    {
        return $this->customOptions;
    }
}
