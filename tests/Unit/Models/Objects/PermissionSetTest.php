<?php
/**
 * Pterodactyl - Panel
 * Copyright (c) 2015 - 2017 Dane Everitt <dane@daneeveritt.com>.
 *
 * This software is licensed under the terms of the MIT license.
 * https://opensource.org/licenses/MIT
 */

namespace Tests\Unit\Models\Objects;


use Pterodactyl\Models\Objects\PermissionSet;
use Tests\TestCase;

class PermissionSetTest extends TestCase
{
    public function testGrantsPermission() {
        $set = new PermissionSet(['test:one']);
        $this->assertTrue($set->grants('test:one'));
    }

    public function testGrantsPermissionWildcard() {
        $set = new PermissionSet(['test:*']);
        $this->assertTrue($set->grants('test:one'));
        $this->assertTrue($set->grants('test:one:more'));
    }

    public function testDeniedPermission() {
        $set = new PermissionSet(['!test:one']);
        $this->assertFalse($set->grants('test:one'));
    }

    public function testDeniedPermissionWildcard() {
        $set = new PermissionSet(['test:*', '!test:one']);
        $this->assertFalse($set->grants('test:one'));
        $this->assertTrue($set->grants('test:two'));
    }

    public function testConfirmsDeniedPermission() {
        $set = new PermissionSet(['test:*', '!test:one']);
        $this->assertTrue($set->grants('!test:one'));
    }

    public function testAsArray() {
        $perms = ['test:one', '!test:two'];
        $set = new PermissionSet($perms);
        $this->assertEquals($perms, $set->asArray());
    }

    public function testMutable() {
        $perms = ['test:one', '!test:two'];
        $set = new PermissionSet($perms);
        $this->assertEquals($perms, $set->mutable()->asArray());
    }

    public function testMutableAdd() {
        $set = (new PermissionSet(['test:one', '!test:two']))->mutable();

        $this->assertTrue($set->add('test:three'));
        $this->assertFalse($set->add('test:three'));
        $this->assertEquals(['test:one', '!test:two', 'test:three'], $set->asArray());

        $this->assertTrue($set->add('!test:three'));
        $this->assertFalse($set->add('!test:three'));
        $this->assertEquals(['test:one', '!test:two', '!test:three'], $set->asArray());
    }

    public function testMutableRemove() {
        $initialPerms = ['test:one', '!test:two'];
        $set = (new PermissionSet($initialPerms))->mutable();

        $this->assertFalse($set->remove('test:three'));
        $this->assertFalse($set->remove('!test:three'));
        $this->assertEquals($initialPerms, $set->asArray());

        $this->assertTrue($set->remove('test:one'));
        $this->assertEquals(['!test:two'], $set->asArray());
        $this->assertTrue($set->remove('!test:two'));
        $this->assertEmpty($set->asArray());
    }
}
