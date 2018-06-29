<?php

namespace Pterodactyl\Repositories\Daemon;

use Webmozart\Assert\Assert;
use Psr\Http\Message\ResponseInterface;
use Pterodactyl\Contracts\Repository\Daemon\ServerRepositoryInterface;

class ServerRepository extends BaseRepository implements ServerRepositoryInterface
{
    /**
     * Create a new server on the daemon for the panel.
     *
     * @param array $structure
     * @param array $overrides
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function create(array $structure, array $overrides = []): ResponseInterface
    {
        foreach ($overrides as $key => $value) {
            $structure[$key] = value($value);
        }

        return $this->getHttpClient()->request('POST', 'servers', [
            'json' => $structure,
        ]);
    }

    /**
     * Update server details on the daemon.
     *
     * @param array $data
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function update(array $data): ResponseInterface
    {
        return $this->getHttpClient()->request('PATCH', $this->getServerUri(), [
            'json' => $data,
        ]);
    }

    /**
     * Mark a server to be reinstalled on the system.
     *
     * @param array|null $data
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function reinstall(array $data = null): ResponseInterface
    {
        return $this->getHttpClient()->request('POST', $this->getServerUri() . '/reinstall', [
            'json' => $data ?? [],
        ]);
    }

    /**
     * Mark a server as needing a container rebuild the next time the server is booted.
     *
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function rebuild(): ResponseInterface
    {
        return $this->getHttpClient()->request('POST', $this->getServerUri() . '/rebuild');
    }

    /**
     * Suspend a server on the daemon.
     *
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function suspend(): ResponseInterface
    {
        return $this->getHttpClient()->request('POST', $this->getServerUri() . '/suspend');
    }

    /**
     * Un-suspend a server on the daemon.
     *
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function unsuspend(): ResponseInterface
    {
        return $this->getHttpClient()->request('POST', $this->getServerUri() . '/unsuspend');
    }

    /**
     * Delete a server on the daemon.
     *
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function delete(): ResponseInterface
    {
        return $this->getHttpClient()->request('DELETE', $this->getServerUri());
    }

    /**
     * Return details on a specific server.
     *
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function details(): ResponseInterface
    {
        return $this->getHttpClient()->request('GET', $this->getServerUri());
    }

    /**
     * Revoke an access key on the daemon before the time is expired.
     *
     * @param string|array $key
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function revokeAccessKey($key): ResponseInterface
    {
        if (is_array($key)) {
            return $this->getHttpClient()->request('POST', 'keys/batch-delete', [
                'json' => ['keys' => $key],
            ]);
        }

        Assert::stringNotEmpty($key, 'First argument passed to revokeAccessKey must be a non-empty string or array, received %s.');

        return $this->getHttpClient()->request('DELETE', 'keys/' . $key);
    }
}
