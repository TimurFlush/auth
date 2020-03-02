<?php

declare(strict_types=1);

namespace TimurFlush\Auth\Accessor;

interface AccessorInterface
{
    public function isAuth();

    public function isGuest();

    public function setUser();

    public function getUser();

    public function getUserId();
}
