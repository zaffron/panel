<?php
/**
 * Created by PhpStorm.
 * User: schrej
 * Date: 04.09.18
 * Time: 21:37
 */

namespace Pterodactyl\Models\Objects;


class MutablePermissionSet extends PermissionSet
{

    protected function __construct(PermissionSet $permissionSet)
    {
        $this->permissions = $permissionSet->permissions;
    }

    /**
     * Adds a permission if the set doesn't contain it yet.
     * If this inverts a permission that is in the set, that permission will be updated.
     *
     * @param string $permission the permission to add
     * @return bool whether it was actually added
     */
    public function add(string $permission): bool {
        $perm = $this->parsePermissionString($permission);

        if (!key_exists($perm->string, $this->permissions) || $this->permissions[$perm->string] !== $perm->grants) {
            $this->permissions[$perm->string] = $perm->grants;
            return true;
        }
        return false;
    }

    /**
     * Removes a permission if the set contains it.
     *
     * @param string $permission the permission to remove
     * @return bool whether it was actually removed
     */
    public function remove(string $permission): bool {
        $perm = $this->parsePermissionString($permission);

        if (key_exists($perm->string, $this->permissions) && $this->permissions[$perm->string] === $perm->grants) {
            unset($this->permissions[$perm->string]);
            return true;
        }
        return false;
    }
}
