<?php

declare(strict_types=1);

namespace TimurFlush\Auth\Cookies;

interface CookiesInterface
{
    /**
     * Sets cookies.
     *
     * @param mixed              $value
     * @param \DateTimeInterface $dateTime
     * @param bool|null          $secure
     * @param string|null        $path
     * @param string|null        $domain
     *
     * @return void
     */
    public function set($value, \DateTimeInterface $dateTime, bool $secure = null, string $path = null, string $domain = null): void;

    /**
     * Returns cookies.
     *
     * @return string|null
     */
    public function get(): ?string;

    /**
     * Remove cookies.
     *
     * @return void
     */
    public function remove(): void;

    /**
     * Sets a module name.
     *
     * @param string $module
     *
     * @return mixed
     */
    public function setModuleName(string $module);
}
