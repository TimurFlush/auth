<?php

declare(strict_types=1);

namespace TimurFlush\Auth\Cookies;

use Carbon\Carbon;
use Phalcon\Http\Response\CookiesInterface as PhalconCookies;
use DateTimeInterface;

class Phalcon implements CookiesInterface
{
    /**
     * @var \Phalcon\Http\Response\CookiesInterface
     */
    protected PhalconCookies $cookies;

    protected array $options = [
        'name'      => 'TF_UserSession_Id',
        'domain'    => '',
        'path'      => '/',
        'secure'    => false,
    ];

    public function __construct(
        PhalconCookies $cookies,
        array $options = null
    ) {
        $this->cookies = $cookies;

        if (is_array($options)) {
            $this->options = array_merge(
                $this->options,
                $options
            );
        }
    }

    public function set($value, DateTimeInterface $dateTime, bool $secure = null, string $path = null, string $domain = null): void
    {
        $diff = Carbon::now()->diffInSeconds($dateTime, true);

        $this->cookies->set(
            $this->options['name'],
            $value,
            time() + $diff,
            $path ?? $this->options['path'],
            $secure ?? $this->options['secure'],
            $domain ?? $this->options['domain'],
            true
        );
    }

    public function get(): ?string
    {
        return $this
            ->cookies
            ->get($this->options['name'])
            ->getValue();
    }

    public function remove(): void
    {
        $this
            ->cookies
            ->delete($this->options['name']);
    }
}
