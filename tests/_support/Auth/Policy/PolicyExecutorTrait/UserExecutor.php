<?php

namespace TimurFlush\Auth\Tests\Support\Auth\Policy\PolicyExecutorTrait;

use Phalcon\Di;
use Phalcon\Di\DiInterface;
use TimurFlush\Auth\Policy\PolicyExecutorTrait;
use TimurFlush\Auth\User\UserInterface;

class UserExecutor implements UserInterface
{
    use PolicyExecutorTrait;

    public function getDI(): DiInterface
    {
        return Di::getDefault();
    }

    /**
     * @inheritDoc
     */
    public function setPermissions(array $permissions)
    {
        // TODO: Implement setPermissions() method.
    }

    /**
     * @inheritDoc
     */
    public function getPermissions(): array
    {
        // TODO: Implement getPermissions() method.
    }

    /**
     * @inheritDoc
     */
    public function removePermission(string $permission)
    {
        // TODO: Implement removePermission() method.
    }

    /**
     * @inheritDoc
     */
    public function isPermitted(string $permission, callable $callback = null, ...$callbackArguments): bool
    {
        // TODO: Implement isPermitted() method.
    }

    /**
     * @inheritDoc
     */
    public function flushPermissions()
    {
        // TODO: Implement flushPermissions() method.
    }

    /**
     * @inheritDoc
     */
    public function setId(int $id)
    {
        // TODO: Implement setId() method.
    }

    /**
     * @inheritDoc
     */
    public function getId(): ?int
    {
        // TODO: Implement getId() method.
    }

    /**
     * @inheritDoc
     */
    public function getPassword(): ?string
    {
        // TODO: Implement getPassword() method.
    }

    /**
     * @inheritDoc
     */
    public function checkCredentials(array $credentials, bool $allowWithoutPassword = false): bool
    {
        // TODO: Implement checkCredentials() method.
    }

    public function setBanStatus(?bool $status)
    {
        // TODO: Implement setBanStatus() method.
    }

    public function getBanStatus(): bool
    {
        // TODO: Implement getBanStatus() method.
    }

    public function setActivationStatus(?bool $status)
    {
        // TODO: Implement setActivationStatus() method.
    }

    public function getActivationStatus(): bool
    {
        // TODO: Implement getActivationStatus() method.
    }

    /**
     * @inheritDoc
     */
    public function addRoles(array $roles)
    {
        // TODO: Implement addRoles() method.
    }

    /**
     * @inheritDoc
     */
    public function getRoles(): array
    {
        // TODO: Implement getRoles() method.
    }

    /**
     * @inheritDoc
     */
    public function addPermission(string $permission, bool $value)
    {
        // TODO: Implement addPermission() method.
    }

    /**
     * @inheritDoc
     */
    public function hasRole($role)
    {
        // TODO: Implement hasRole() method.
    }

    /**
     * @inheritDoc
     */
    public function rewritePermission(string $permission, bool $newValue, bool $createIfNotExists)
    {
        // TODO: Implement rewritePermission() method.
    }

    public function setApiToken(string $token)
    {
        // TODO: Implement setApiToken() method.
    }

    public function getApiToken(): ?string
    {
        // TODO: Implement getApiToken() method.
    }
}
