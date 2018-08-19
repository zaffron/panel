<?php
/**
 * Pterodactyl - Panel
 * Copyright (c) 2015 - 2017 Dane Everitt <dane@daneeveritt.com>.
 *
 * This software is licensed under the terms of the MIT license.
 * https://opensource.org/licenses/MIT
 */

namespace Pterodactyl\Services\Subusers;

use Webmozart\Assert\Assert;
use Pterodactyl\Models\SubuserPermission;
use Pterodactyl\Contracts\Repository\SubuserPermissionRepositoryInterface;

class PermissionCreationService
{
    /**
     * @var \Pterodactyl\Contracts\Repository\SubuserPermissionRepositoryInterface
     */
    protected $repository;

    /**
     * PermissionCreationService constructor.
     *
     * @param \Pterodactyl\Contracts\Repository\SubuserPermissionRepositoryInterface $repository
     */
    public function __construct(SubuserPermissionRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Assign permissions to a given subuser.
     *
     * @param int   $subuser
     * @param array $permissions
     *
     * @throws \Pterodactyl\Exceptions\Model\DataValidationException
     */
    public function handle($subuser, array $permissions)
    {
        Assert::integerish($subuser, 'First argument passed to handle must be an integer, received %s.');

        $permissionMappings = SubuserPermission::getPermissions(true);
        $insertPermissions = [];

        foreach ($permissions as $permission) {
            if (array_key_exists($permission, $permissionMappings)) {
                Assert::stringNotEmpty($permission, 'SubuserPermission argument provided must be a non-empty string, received %s.');

                array_push($insertPermissions, [
                    'subuser_id' => $subuser,
                    'permission' => $permission,
                ]);
            }
        }

        if (! empty($insertPermissions)) {
            $this->repository->withoutFreshModel()->insert($insertPermissions);
        }
    }
}
