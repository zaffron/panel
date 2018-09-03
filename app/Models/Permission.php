<?php

namespace Pterodactyl\Models;

use Illuminate\Database\Eloquent\Model;
use Sofa\Eloquence\Contracts\Validable as ValidableContract;
use Sofa\Eloquence\Validable;

class Permission extends Model implements ValidableContract
{
    use Validable;
    /**
     * The resource name for this model when it is transformed into an
     * API representation using fractal.
     */
    const RESOURCE_NAME = 'permission';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'permissions';

    /**
     * Rules verifying that the data being stored matches the expectations of the database.
     *
     * @var array
     */
    protected static $dataIntegrityRules = [
        'type' => 'required',
        'permission' => 'required',
        'user' => 'required_if:type,user',
    ];
}
