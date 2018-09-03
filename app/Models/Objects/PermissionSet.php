<?php
/**
 * Pterodactyl - Panel
 * Copyright (c) 2015 - 2017 Dane Everitt <dane@daneeveritt.com>.
 *
 * This software is licensed under the terms of the MIT license.
 * https://opensource.org/licenses/MIT
 */

namespace Pterodactyl\Models\Objects;

/**
 * A PermissionSet contains a set of permissions.
 * It allows to check if that set of permissions actually grants a specific permissions using all
 * permissions related rules including priority and negation.
 *
 * Class PermissionCollection
 * @package Pterodactyl\Models\Objects
 */
class PermissionSet
{
    /**
     * @var array is a list of permission strings
     */
    private $permissions;

    public function __construct(array $permissions) {
        $this->permissions = $permissions;
    }

    /**
     * Checks whether the permission set grants a specific permission.
     * @param string $permission
     * @return bool
     */
    public function grants(string $permission): bool {
        // TODO implement the required logic.
        return false;
    }

    /**
     * Checks whether the collection contains a certain permission string.
     * This does NOT check if the collection actually GRANTS the permission.
     * @param string $permissison
     * @return bool
     */
    public function contains(string $permissison): bool {
        return array_has($this->permissions, $permissison);
    }

    public function asArray(): array {
        return $this->permissions;
    }
}
