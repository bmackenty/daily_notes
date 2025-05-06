<?php

use PHPUnit\Framework\TestCase;
use App\Models\User;
use Tests\Helpers\TestHelper;

class UserTest extends TestCase {
    private $pdo;
    private $user;

    protected function setUp(): void {
        $this->pdo = TestHelper::getMockPDO();
        $this->user = new User($this->pdo);
        
        // Create users table
        $this->pdo->exec("CREATE TABLE users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT NOT NULL,
            email TEXT NOT NULL,
            password TEXT NOT NULL,
            role TEXT NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )");
    }

    public function testCreateUser() {
        $userData = TestHelper::createTestUser();
        $userId = $this->user->create($userData);
        
        $this->assertIsInt($userId);
        $this->assertGreaterThan(0, $userId);
    }

    public function testFindUserByEmail() {
        $userData = TestHelper::createTestUser();
        $this->user->create($userData);
        
        $foundUser = $this->user->findByEmail($userData['email']);
        
        $this->assertIsArray($foundUser);
        $this->assertEquals($userData['email'], $foundUser['email']);
        $this->assertEquals($userData['username'], $foundUser['username']);
    }

    public function testValidatePassword() {
        $userData = TestHelper::createTestUser();
        $this->user->create($userData);
        
        $this->assertTrue($this->user->validatePassword($userData['email'], $userData['password']));
        $this->assertFalse($this->user->validatePassword($userData['email'], 'wrongpassword'));
    }

    protected function tearDown(): void {
        $this->pdo->exec("DROP TABLE users");
    }
} 