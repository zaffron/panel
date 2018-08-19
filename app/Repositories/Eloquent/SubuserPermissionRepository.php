<?php

namespace Pterodactyl\Repositories\Eloquent;

use Pterodactyl\Models\SubuserPermission;
use Pterodactyl\Contracts\Repository\SubuserPermissionRepositoryInterface;

class SubuserPermissionRepository extends EloquentRepository implements SubuserPermissionRepositoryInterface
{
    /**
     * Return the model backing this repository.
     *
     * @return string
     */
    public function model()
    {
        return SubuserPermission::class;
    }
}
