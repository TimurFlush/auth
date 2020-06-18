<?php

declare(strict_types=1);

namespace TimurFlush\Auth\Policy;

use Closure;
use ReflectionFunction;
use ReflectionNamedType;
use ReflectionClass;
use TimurFlush\Auth\Exception\InvalidArgumentException;
use TimurFlush\Auth\Role\RoleInterface;
use TimurFlush\Auth\User\UserInterface;

final class PolicyInspector
{
    protected bool $toUser = false;

    protected bool $toRole = false;

    protected bool $isAllowNull = false;

    /**
     * PolicyInspector constructor.
     *
     * @param string $policyAction A policy name.
     * @param callable $policy     A policy callback.
     *
     * @throws InvalidArgumentException If a specified policy do not have at least one argument.
     *
     * @throws InvalidArgumentException If the first argument of a specified policy is not the typed property.
     *
     * @throws InvalidArgumentException If the first argument of a specified policy does not refer to
     *                                  the \TimurFlush\Auth\UserModel\UserInterface or the \TimurFlush\Auth\RoleModel\RoleInterface
     *
     * @throws \ReflectionException     Inspection errors.
     */
    public function __construct(string $policyAction, callable $policy)
    {
        if ($policy instanceof Closure) {
            $reflectedFunction = new ReflectionFunction($policy);
        } else {
            $reflectedFunction = (new ReflectionClass($policy[0]))->getMethod($policy[1]);
        }

        if ($reflectedFunction->getNumberOfParameters() === 0) {
            throw new InvalidArgumentException(
                "The policy '" . $policyAction . "' must have at least one argument"
            );
        }

        $argumentType = $reflectedFunction
            ->getParameters()[0]
            ->getType();

        if ($argumentType instanceof ReflectionNamedType === false) {
            throw new InvalidArgumentException(
                "A first argument of the policy '" . $policyAction . "' must be the typed property"
            );
        }

        if (is_a(
            $argumentType->getName(),
            UserInterface::class,
            true
        )) {
            $this->toUser = true;
            $this->isAllowNull = $argumentType->allowsNull();
        } elseif (is_a(
            $argumentType->getName(),
            RoleInterface::class,
            true
        )) {
            $this->toRole = true;
        } else {
            throw new InvalidArgumentException(
                sprintf(
                    "The first argument of the policy '%s' must refer to the %s or the %s",
                    $policyAction,
                    UserInterface::class,
                    RoleInterface::class
                )
            );
        }
    }

    /**
     * Determines whether this policy relates to the user.
     *
     * @return bool
     */
    public function isUserPolicy(): bool
    {
        return $this->toUser;
    }

    /**
     * Determines whether this policy relates to the role.
     *
     * @return bool
     */
    public function isRolePolicy(): bool
    {
        return $this->toRole;
    }

    /**
     * Determines whether the inspected policy can accept null in the first argument.
     *
     * @return bool
     */
    public function isAllowNull(): bool
    {
        return $this->isAllowNull;
    }
}
