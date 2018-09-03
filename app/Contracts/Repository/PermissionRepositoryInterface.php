<?php
/**
 * Created by PhpStorm.
 * User: schrej
 * Date: 19.08.18
 * Time: 14:52
 */

namespace Pterodactyl\Contracts\Repository;


use Pterodactyl\Models\Objects\PermissionSet;
use Pterodactyl\Models\User;

interface PermissionRepositoryInterface extends RepositoryInterface
{
    /**
     * Return the permissions directly owned by a given user.
     *
     * @param string $type
     * @param int $id
     * @return PermissionSet
     */
    public function getPermissionsFor(string $type, int $id): PermissionSet;

    /**
     * Adds a set of permissions to a given user.
     *
     * @param string $type
     * @param int $id
     * @param array $permissions
     * @return int
     */
    public function addPermissionsFor(string $type, int $id, array $permissions): int;

    /**
     * Deletes a set of permissions from a given user.
     *
     * @param string $type
     * @param int $id
     * @param array $permissions
     * @return int
     */
    public function removePermissionsFor(string $type, int $id, array $permissions): int;
}
