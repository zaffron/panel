<?php
/**
 * Pterodactyl - Panel
 * Copyright (c) 2015 - 2017 Dane Everitt <dane@daneeveritt.com>.
 *
 * This software is licensed under the terms of the MIT license.
 * https://opensource.org/licenses/MIT
 */

namespace Pterodactyl\Services\Permissions;



use Pterodactyl\Contracts\Repository\PermissionRepositoryInterface;
use Pterodactyl\Contracts\Repository\TaskRepositoryInterface;
use Pterodactyl\Models\Objects\PermissionSet;

class PermissionsUpdateService
{
    /**
     * @var \Pterodactyl\Contracts\Repository\PermissionRepositoryInterface;
     */
    protected $repository;

    /**
     * PermissionUpdateService constructor
     *
     * @param PermissionRepositoryInterface $repository
     */
    public function __construct(PermissionRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Updates the permissions of the permission holder with the given id.
     *
     * @param string $type
     * @param int $id
     * @param PermissionSet $newPermissions
     */
    public function handle(string $type, int $id, PermissionSet $newPermissions) {
        $currentPermissions = $this->repository->getPermissionsFor($type, $id)->asArray();

        $addedPermissions = array_diff($newPermissions->asArray(), $currentPermissions);
        $removedPermissions = array_diff($currentPermissions, $newPermissions->asArray());

        if (sizeof($addedPermissions) > 0) {
            $this->repository->addPermissionsFor($type, $id, $addedPermissions);
        }
        if (sizeof($removedPermissions) > 0) {
            $this->repository->removePermissionsFor($type, $id, $removedPermissions);
        }
    }
}
