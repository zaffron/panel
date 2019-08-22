<?php

namespace Tests\Unit\Http\Controllers\Server\Files;

use Mockery as m;
use Carbon\Carbon;
use App\Models\Node;
use App\Models\Server;
use Tests\Traits\MocksUuids;
use Illuminate\Cache\Repository;
use Tests\Unit\Http\Controllers\ControllerTestCase;
use App\Http\Controllers\Server\Files\DownloadController;

class DownloadControllerTest extends ControllerTestCase
{
    use MocksUuids;

    /**
     * @var \Illuminate\Cache\Repository|\Mockery\Mock
     */
    protected $cache;

    /**
     * Setup tests.
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->cache = m::mock(Repository::class);
    }

    /**
     * Test the download controller redirects correctly.
     */
    public function testIndexController()
    {
        $controller = $this->getController();
        $server = factory(Server::class)->make();
        $server->setRelation('node', factory(Node::class)->make());

        $this->setRequestAttribute('server', $server);

        $controller->shouldReceive('authorize')->with('download-files', $server)->once()->andReturnNull();

        Carbon::setTestNow(now());

        $this->cache->shouldReceive('put')
            ->once()
            ->with('Server:Downloads:' . $this->getKnownUuid(), ['server' => $server->uuid, 'path' => '/my/file.txt'], \Hamcrest\Matchers::equalTo(now()->addMinutes(5)))
            ->andReturnNull();

        $response = $controller->index($this->request, $server->uuidShort, '/my/file.txt');
        $this->assertIsRedirectResponse($response);
        $this->assertRedirectUrlEquals(sprintf(
            '%s://%s:%s/v1/server/file/download/%s',
            $server->node->scheme,
            $server->node->fqdn,
            $server->node->daemonListen,
            $this->getKnownUuid()
        ), $response);
    }

    /**
     * Return a mocked instance of the controller to allow access to authorization functionality.
     *
     * @return \App\Http\Controllers\Server\Files\DownloadController|\Mockery\Mock
     */
    private function getController()
    {
        return $this->buildMockedController(DownloadController::class, [$this->cache]);
    }
}
