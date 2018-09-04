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
     * The symbol used at the beginning of a permission string to invert it.
     */
    const INVERT_SYMBOL = '!';
    /**
     * The symbol used to separate permission layers.
     */
    const SEPARATOR_SYMBOL = ':';
    /**
     * The symbol used as a wildcard at the end of a permission string.
     */
    const WILDCARD_SYMBOL = '*';

    /**
     * @var array is a map containing permissions and whether they are granted or denied.
     */
    protected $permissions;

    public function __construct(array $permissions) {
        $this->permissions = [];
        foreach ($permissions as $p) {
            $perm = $this->parsePermissionString($p);
            $this->permissions[$perm->string] = $perm->grants;
        }
    }

    /**
     * Checks whether the permission set grants a specific permission.
     * If the permission string is inverted (leading !) this will check if the set does NOT grant the permission.
     *
     * @param string $permission
     * @return bool
     */
    public function grants(string $permission): bool {
        $perm = $this->parsePermissionString($permission);

        if (key_exists($perm->string, $this->permissions)) return $this->permissions[$perm->string] === $perm->grants;

        $current = $perm->string;
        while (strlen($current = substr($current, 0, strrpos($current, PermissionSet::SEPARATOR_SYMBOL))) > 0) {
            $currentWildcard = $current . PermissionSet::SEPARATOR_SYMBOL . PermissionSet::WILDCARD_SYMBOL;
            if (key_exists($currentWildcard, $this->permissions))
                return $this->permissions[$currentWildcard] === $perm->grants;
        }

        return false;
    }

    /**
     * Checks whether the collection contains a given permission string.
     * This does NOT check if the collection actually GRANTS the permission.
     *
     * @param string $permission
     * @return bool
     */
    public function contains(string $permission): bool {
        $perm = $this->parsePermissionString($permission);
        return key_exists($perm->string, $this->permissions) && $this->permissions[$perm->string] === $perm->grants;
    }

    /**
     * Returns the permission set as an array of permission strings.
     *
     * @return array
     */
    public function asArray(): array {
        $permissions = [];
        foreach (array_keys($this->permissions) as $p) {
            array_push($permissions, $this->permissions[$p] ? $p : PermissionSet::INVERT_SYMBOL . $p);
        }
        return $permissions;
    }

    /**
     * Returns a mutable copy of this permission set.
     *
     * @return MutablePermissionSet
     */
    public function mutable(): MutablePermissionSet {
        return new MutablePermissionSet($this);
    }

    /**
     * This parses a permission string and returns an object containing the string and whether it grants or negates
     * the permission.
     * @param string $permission
     * @return object with keys 'string' and 'grants'
     */
    protected function parsePermissionString(string $permission): object {
        $granted = !starts_with($permission, PermissionSet::INVERT_SYMBOL);
        if (!$granted) {
            $permission = substr($permission, 1);
        }
        return (object) [
            'string' => $permission,
            'grants' => $granted
        ];
    }
}
