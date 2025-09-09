<?php

use PHPUnit\Framework\TestCase;
use App\Models\User;
use App\Utils\Security;
use Tests\Helpers\TestHelper;

class UserTest extends TestCase {
    private $pdo;
    private $user;

    protected function setUp(): void {
        $this->pdo = TestHelper::getMockPDO();
        $this->user = new User($this->pdo);
    }

    public function testCreateUser() {
        $userData = TestHelper::createTestUser();
        $userId = $this->user->create($userData);

        $this->assertIsInt($userId);
        $this->assertGreaterThan(0, $userId);

        $created = $this->user->findByEmail($userData['email']);
        $this->assertEquals($userData['name'], $created['name']);
        $this->assertEquals($userData['email'], $created['email']);
    }

    public function testFindUserByEmail() {
        $userData = TestHelper::createTestUser();
        $this->user->create($userData);
        
        $foundUser = $this->user->findByEmail($userData['email']);
        
        $this->assertIsArray($foundUser);
        $this->assertEquals($userData['email'], $foundUser['email']);
        $this->assertEquals($userData['name'], $foundUser['name']);
    }

    public function testValidatePassword() {
        $security = new Security($this->pdo);

        $valid = $security->validatePassword('Valid123!');
        $this->assertTrue($valid);

        $invalid = $security->validatePassword('short');
        $this->assertNotTrue($invalid);
    }

    protected function tearDown(): void {
        $this->pdo->exec("DROP TABLE users");
        $this->pdo->exec("DROP TABLE IF EXISTS login_attempts");
        $this->pdo->exec("DROP TABLE IF EXISTS security_settings");
    }
}
