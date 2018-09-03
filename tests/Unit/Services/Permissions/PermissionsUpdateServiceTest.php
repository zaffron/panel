<?php
/**
 * Pterodactyl - Panel
 * Copyright (c) 2015 - 2017 Dane Everitt <dane@daneeveritt.com>.
 *
 * This software is licensed under the terms of the MIT license.
 * https://opensource.org/licenses/MIT
 */

namespace Tests\Unit\Services\Permissions;

use Mockery as m;
use Illuminate\Database\ConnectionInterface;
use Pterodactyl\Contracts\Repository\PermissionRepositoryInterface;
use Pterodactyl\Models\Objects\PermissionSet;
use Pterodactyl\Services\Subusers\PermissionCreationService;
use Tests\TestCase;

class PermissionsUpdateServiceTest extends TestCase
{
    /**
     * @var \Illuminate\Database\ConnectionInterface|\Mockery\Mock
     */
    private $connection;

    /**
     * @var \Pterodactyl\Contracts\Repository\PermissionRepositoryInterface|\Mockery\Mock
     */
    private $repository;

    /**
     * Setup tests.
     */
    public function setUp()
    {
        parent::setUp();

        $this->connection = m::mock(ConnectionInterface::class);
        $this->repository = m::mock(PermissionRepositoryInterface::class);
    }

    /**
     * Tests that newly added permissions get added to the database.
     */
    public function testNewPermissionsGetAdded() {
        $this->repository->shouldReceive('getPermissionsFor')
            ->with('user', 1)
            ->once()
            ->andReturn(new PermissionSet(['test:permission:one']));
        $this->repository->shouldReceive('addPermissionsFor')
            ->with('user',1 , [1 => 'test:permission:two'])
            ->once()
            ->andReturn(1);

        $this->getService()->handle('user', 1, new PermissionSet(['test:permission:one', 'test:permission:two']));
    }

    /**
     * Tests that permissions that are no longer present get removed from the database.
     */
    public function testMissingPermissionsGetRemoved() {
        $this->repository->shouldReceive('getPermissionsFor')
            ->with('user', 1)
            ->once()
            ->andReturn(new PermissionSet(['test:permission:one', 'test:permission:two']));
        $this->repository->shouldReceive('removePermissionsFor')
            ->with('user',1 ,[1 => 'test:permission:two'])
            ->once()
            ->andReturn(1);

        $this->getService()->handle('user', 1, new PermissionSet(['test:permission:one']));
    }

    /**
     * Tests that the database doesn't get changed when the permissions sets are identical
     */
    public function testNothingChangesOnIdenticalPermissions() {
        $this->repository->shouldReceive('getPermissionsFor')
            ->with('user', 1)
            ->once()
            ->andReturn(new PermissionSet(['test:permission:one', 'test:permission:two']));

        $this->getService()->handle('user', 1, new PermissionSet(['test:permission:two', 'test:permission:one']));
    }

    /**
     * Tests that additions and removals get applied to the database at the same time.
     */
    public function testChangedPermissionsGetAddedAndRemoved() {
        $this->repository->shouldReceive('getPermissionsFor')
            ->with('user', 1)
            ->once()
            ->andReturn(new PermissionSet(['test:permission:two']));
        $this->repository->shouldReceive('removePermissionsFor')
            ->with('user',1 ,[0 => 'test:permission:two'])
            ->once()
            ->andReturn(1);
        $this->repository->shouldReceive('addPermissionsFor')
            ->with('user',1 ,[0 => 'test:permission:one'])
            ->once()
            ->andReturn(1);

        $this->getService()->handle('user', 1, new PermissionSet(['test:permission:one']));
    }

    private function getService() {
        return new \Pterodactyl\Services\Permissions\PermissionsUpdateService($this->repository);
    }
}
