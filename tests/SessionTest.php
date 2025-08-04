<?php

declare(strict_types=1);

namespace Solo\Session\Tests;

use PHPUnit\Framework\TestCase;
use Solo\Session\Session;

class SessionTest extends TestCase
{
    protected function setUp(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
        $_SESSION = [];
        $_COOKIE = [];
        $_SERVER['HTTP_USER_AGENT'] = 'Test User Agent';
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
    }

    protected function tearDown(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
        $_SESSION = [];
        $_COOKIE = [];
    }

    public function testSessionCreation(): void
    {
        $session = new Session();
        $this->assertInstanceOf(Session::class, $session);
        $this->assertEquals(PHP_SESSION_ACTIVE, $session->getStatus());
    }

    public function testSetAndGet(): void
    {
        $session = new Session();
        $session->set('test_key', 'test_value');

        $this->assertEquals('test_value', $session->get('test_key'));
        $this->assertEquals('default', $session->get('nonexistent', 'default'));
    }

    public function testHas(): void
    {
        $session = new Session();
        $session->set('existing_key', 'value');

        $this->assertTrue($session->has('existing_key'));
        $this->assertFalse($session->has('nonexistent_key'));
    }

    public function testUnset(): void
    {
        $session = new Session();
        $session->set('key_to_remove', 'value');
        $this->assertTrue($session->has('key_to_remove'));

        $session->unset('key_to_remove');
        $this->assertFalse($session->has('key_to_remove'));
    }

    public function testAll(): void
    {
        $session = new Session();
        $session->set('key1', 'value1');
        $session->set('key2', 'value2');

        $all = $session->all();
        $this->assertArrayHasKey('key1', $all);
        $this->assertArrayHasKey('key2', $all);
        $this->assertEquals('value1', $all['key1']);
        $this->assertEquals('value2', $all['key2']);
    }

    public function testClear(): void
    {
        $session = new Session();
        $session->set('key1', 'value1');
        $session->set('key2', 'value2');

        $session->clear();
        $this->assertEmpty($session->all());
    }

    public function testGetCurrentId(): void
    {
        $session = new Session();
        $id = $session->getCurrentId();
        $this->assertIsString($id);
        $this->assertNotEmpty($id);
    }

    public function testGetCookieName(): void
    {
        $session = new Session();
        $name = $session->getCookieName();
        $this->assertIsString($name);
        $this->assertNotEmpty($name);
    }

    public function testGetTimeout(): void
    {
        $session = new Session(timeout: 3600);
        $this->assertEquals(3600, $session->getTimeout());
    }

    public function testIsExpired(): void
    {
        $session = new Session(timeout: 1);
        $this->assertFalse($session->isExpired());

        // Simulate old activity
        $_SESSION['last_activity'] = time() - 10;
        $this->assertTrue($session->isExpired());
    }

    public function testGetLastActivity(): void
    {
        $session = new Session();
        $activity = $session->getLastActivity();
        $this->assertIsInt($activity);
        $this->assertGreaterThan(0, $activity);
    }

    public function testRegenerateId(): void
    {
        $session = new Session();
        $oldId = $session->getCurrentId();

        $session->regenerateId();
        $newId = $session->getCurrentId();

        $this->assertNotEquals($oldId, $newId);
    }
}
