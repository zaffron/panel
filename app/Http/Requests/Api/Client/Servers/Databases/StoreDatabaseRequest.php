<?php

namespace Pterodactyl\Http\Requests\Api\Client\Servers\Databases;

use Webmozart\Assert\Assert;
use Pterodactyl\Models\Server;
use Illuminate\Validation\Rule;
use Pterodactyl\Models\Permission;
use Illuminate\Database\Query\Builder;
use Pterodactyl\Contracts\Http\ClientPermissionsRequest;
use Pterodactyl\Http\Requests\Api\Client\ClientApiRequest;

class StoreDatabaseRequest extends ClientApiRequest implements ClientPermissionsRequest
{
    /**
     * @return string
     */
    public function permission(): string
    {
        return Permission::ACTION_DATABASE_CREATE;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        $server = $this->route()->parameter('server');

        Assert::isInstanceOf($server, Server::class);

        return [
            'database' => [
                'required',
                'alpha_dash',
                'min:3',
                'max:48',
                // Yes, I am aware that you could have the same database name across two unique hosts. However,
                // I don't really care about that for this validation. We just want to make sure it is unique to
                // the server itself. No need for complexity.
                Rule::unique('databases', 'database')->where(function (Builder $query) use ($server) {
                    $query->where('server_id', $server->id);
                }),
            ],
            'remote' => 'required|string|regex:/^[0-9%.]{1,15}$/',
        ];
    }

    public function messages()
    {
        return [
            'database.unique' => 'The database name you have selected is already in use by this server.',
        ];
    }
}
