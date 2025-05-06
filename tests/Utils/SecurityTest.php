<?php

use PHPUnit\Framework\TestCase;
use App\Utils\Security;
use Tests\Helpers\TestHelper;

class SecurityTest extends TestCase {
    private $security;
    private $pdo;

    protected function setUp(): void {
        $this->pdo = TestHelper::getMockPDO();
        $this->security = new Security($this->pdo);
    }

    public function testPasswordHashing() {
        $password = 'Test123!';
        $hash = $this->security->hashPassword($password);
        
        $this->assertIsString($hash);
        $this->assertNotEquals($password, $hash);
        $this->assertTrue($this->security->verifyPassword($password, $hash));
        $this->assertFalse($this->security->verifyPassword('wrongpassword', $hash));
    }

    public function testCsrfTokenGeneration() {
        $token1 = $this->security->generateCsrfToken();
        $token2 = $this->security->generateCsrfToken();
        
        $this->assertIsString($token1);
        $this->assertNotEquals($token1, $token2);
        $this->assertEquals(64, strlen($token1));
    }

    public function testInputSanitization() {
        $input = '<script>alert("xss")</script>';
        $sanitized = $this->security->sanitizeInput($input);
        
        $this->assertIsString($sanitized);
        $this->assertNotEquals($input, $sanitized);
        $this->assertStringNotContainsString('<script>', $sanitized);
    }

    public function testRateLimiting() {
        $ip = '127.0.0.1';
        $email = 'test@example.com';
        
        // First attempt
        $this->assertTrue($this->security->checkRateLimit($ip, $email));
        
        // Multiple attempts
        for ($i = 0; $i < 4; $i++) {
            $this->assertTrue($this->security->checkRateLimit($ip, $email));
        }
        
        // Should be blocked after 5 attempts
        $this->assertFalse($this->security->checkRateLimit($ip, $email));
    }

    protected function tearDown(): void {
        $this->pdo->exec("DROP TABLE IF EXISTS login_attempts");
        $this->pdo->exec("DROP TABLE IF EXISTS security_settings");
    }
} 