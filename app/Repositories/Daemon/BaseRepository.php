<?php

namespace Pterodactyl\Repositories\Daemon;

use RuntimeException;
use GuzzleHttp\Client;
use Pterodactyl\Models\Node;
use Pterodactyl\Models\Server;
use Illuminate\Foundation\Application;
use Pterodactyl\Contracts\Repository\NodeRepositoryInterface;
use Pterodactyl\Contracts\Repository\Daemon\BaseRepositoryInterface;

abstract class BaseRepository implements BaseRepositoryInterface
{
    /**
     * @var \Illuminate\Foundation\Application
     */
    private $app;

    /**
     * @var \Pterodactyl\Models\Server
     */
    private $server;

    /**
     * @var string|null
     */
    private $token;

    /**
     * @var \Pterodactyl\Models\Node|null
     */
    private $node;

    /**
     * @var \Pterodactyl\Contracts\Repository\NodeRepositoryInterface
     */
    private $nodeRepository;

    /**
     * BaseRepository constructor.
     *
     * @param \Illuminate\Foundation\Application                        $app
     * @param \Pterodactyl\Contracts\Repository\NodeRepositoryInterface $nodeRepository
     */
    public function __construct(Application $app, NodeRepositoryInterface $nodeRepository)
    {
        $this->app = $app;
        $this->nodeRepository = $nodeRepository;
    }

    /**
     * Set the node model to be used for this daemon connection.
     *
     * @param \Pterodactyl\Models\Node $node
     * @return $this
     */
    public function setNode(Node $node)
    {
        $this->node = $node;

        return $this;
    }

    /**
     * Return the node model being used.
     *
     * @return \Pterodactyl\Models\Node|null
     */
    public function getNode()
    {
        return $this->node;
    }

    /**
     * Set the Server model to use when requesting information from the Daemon.
     *
     * @param \Pterodactyl\Models\Server $server
     * @return $this
     */
    public function setServer(Server $server)
    {
        $this->server = $server;

        return $this;
    }

    /**
     * Return the Server model.
     *
     * @return \Pterodactyl\Models\Server|null
     */
    public function getServer()
    {
        return $this->server;
    }

    /**
     * Returns the relative API uri of the server.
     *
     * @return string
     */
    protected function getServerUri() {
        if ($this->getNodeDaemonGeneration() == 2) {
            return 'servers/' . $this->getServer()->uuid;
        }
        return 'server';
    }

    /**
     * Resolve the node using the server if it is not set.
     * Throws a RuntimeException if the server isn't set either.
     *
     * @throws RuntimeException
     */
    private function resolveServerNodeIfNotSet() {
        if (! $this->getNode() instanceof Node) {
            if (! $this->getServer() instanceof  Server) {
                throw new RuntimeException('An instance of ' . Node::class . ' or ' . Server::class . ' must be set on this repository in order to return a client.');
            }

            $this->getServer()->loadMissing('node');
            $this->setNode($this->getServer()->getRelation('node'));
        }
    }

    /**
     * Return the daemon_generation of the node being used.
     * If node is not set we try to resolve it using the server.
     * If the server is not set it will throw an exception.
     *
     * @return int
     */
    public function getNodeDaemonGeneration()
    {
        $this->resolveServerNodeIfNotSet();
        return $this->getNode()->daemon_generation;
    }

    /**
     * Set the token to be used in the X-Access-Token header for requests to the daemon.
     *
     * @param string $token
     * @return $this
     */
    public function setToken(string $token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Return the access token being used for requests.
     *
     * @return string|null
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Return an instance of the Guzzle HTTP Client to be used for requests.
     *
     * @param array $headers
     * @return \GuzzleHttp\Client
     */
    public function getHttpClient(array $headers = []): Client
    {
        $this->resolveServerNodeIfNotSet();

        if ($this->getNode()->daemon_generation == 2) {
            $headers['Authorization'] = 'Baerer ' . $this->getToken();

            $baseUri = sprintf('%s://%s:%s/api/v1/', $this->getNode()->scheme, $this->getNode()->fqdn, $this->getNode()->daemonListen);
        } else {
            if ($this->getServer() instanceof Server) {
                $headers['X-Access-Server'] = $this->getServer()->uuid;
            }

            $headers['X-Access-Token'] = $this->getToken() ?? $this->getNode()->daemonSecret;

            $baseUri = sprintf('%s://%s:%s/v1/', $this->getNode()->scheme, $this->getNode()->fqdn, $this->getNode()->daemonListen);
        }


        return new Client([
            'base_uri' => $baseUri,
            'timeout' => config('pterodactyl.guzzle.timeout'),
            'connect_timeout' => config('pterodactyl.guzzle.connect_timeout'),
            'headers' => $headers,
        ]);
    }
}
