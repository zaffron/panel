<?php
/**
 * Pterodactyl - Panel
 * Copyright (c) 2015 - 2017 Dane Everitt <dane@daneeveritt.com>.
 *
 * This software is licensed under the terms of the MIT license.
 * https://opensource.org/licenses/MIT
 */

namespace Pterodactyl\Repositories\Eloquent;


use function Aws\map;
use Pterodactyl\Models\Objects\PermissionSet;
use Pterodactyl\Models\Permission;

class PermissionRepository extends EloquentRepository
{
    /**
     * Return the model backing this repository.
     *
     * @return string|\Closure|object
     */
    public function model()
    {
        return Permission::class;
    }

    /**
     * Retrieves the permissions directly owned by the permission holder with the given id
     *
     * @param string $type
     * @param int $id
     * @return PermissionSet
     */
    public function getPermissionsFor(string $type, int $id): PermissionSet {
        return new PermissionSet($this->getBuilder()->where($type, $id)->get('permission')->toArray());
    }

    /**
     * Adds a set of permissions to the permission holder with the given id
     *
     * @param string $type
     * @param int $id
     * @param array $permissions
     * @return int
     */
    public function addPermissionsFor(string $type, int $id, array $permissions): int {
        return $this->getBuilder()->insert(array_map(function($p) use ($type, $id) {
            return array(
                'type' => $type,
                'user' => $id,
                'permission' => $p,
                'created_at' => date('Y-m-d H:i:s'),
            );
        }, $permissions));
    }

    /**
     * Deletes a set of permissions from the permission holder with the given id
     *
     * @param string $type
     * @param int $id
     * @param array $permissions
     * @return int
     */
    public function removePermissionsFor(string $type, int $id, array $permissions): int {
        return $this->getBuilder()->where($type, $id)->whereIn('permission', $permissions)->delete();
    }
}
