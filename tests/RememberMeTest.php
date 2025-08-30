<?php
/**
 * Remember Me Functionality Test
 * 
 * This test file verifies that the remember me system works correctly.
 * Run this test to ensure the remember me functionality is working.
 */

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Define paths
define('ROOT_PATH', dirname(__DIR__));

// Load required files
require_once ROOT_PATH . '/app/Utils/Config.php';
require_once ROOT_PATH . '/app/Utils/RememberMe.php';

class RememberMeTest {
    private $db;
    private $rememberMe;
    
    public function __construct() {
        try {
            // Get database connection
            $config = \App\Utils\Config::getInstance();
            $this->db = $config->getDatabase();
            
            if (!$this->db) {
                throw new Exception("Could not establish database connection");
            }
            
            // Initialize RememberMe utility
            $this->rememberMe = new \App\Utils\RememberMe($this->db);
            
        } catch (Exception $e) {
            echo "Error initializing test: " . $e->getMessage() . "\n";
            exit(1);
        }
    }
    
    /**
     * Test token creation
     */
    public function testTokenCreation() {
        echo "Testing token creation...\n";
        
        try {
            // Create a test user first
            $userId = $this->createTestUser();
            
            // Test token creation
            $result = $this->rememberMe->createToken($userId);
            
            if ($result) {
                echo "✓ Token creation successful\n";
                
                // Check if cookie was set
                if (isset($_COOKIE['remember_token'])) {
                    echo "✓ Remember me cookie set successfully\n";
                } else {
                    echo "✗ Remember me cookie not set\n";
                }
                
                // Clean up test user
                $this->cleanupTestUser($userId);
                
                return true;
            } else {
                echo "✗ Token creation failed\n";
                return false;
            }
            
        } catch (Exception $e) {
            echo "✗ Token creation test failed: " . $e->getMessage() . "\n";
            return false;
        }
    }
    
    /**
     * Test token validation
     */
    public function testTokenValidation() {
        echo "Testing token validation...\n";
        
        try {
            // Create a test user
            $userId = $this->createTestUser();
            
            // Create a token
            $this->rememberMe->createToken($userId);
            
            // Test token validation
            $user = $this->rememberMe->validateToken();
            
            if ($user && $user['id'] == $userId) {
                echo "✓ Token validation successful\n";
                
                // Clean up
                $this->rememberMe->removeAllTokens($userId);
                $this->cleanupTestUser($userId);
                
                return true;
            } else {
                echo "✗ Token validation failed\n";
                return false;
            }
            
        } catch (Exception $e) {
            echo "✗ Token validation test failed: " . $e->getMessage() . "\n";
            return false;
        }
    }
    
    /**
     * Test token cleanup
     */
    public function testTokenCleanup() {
        echo "Testing token cleanup...\n";
        
        try {
            // Create a test user
            $userId = $this->createTestUser();
            
            // Create a token
            $this->rememberMe->createToken($userId);
            
            // Test token removal
            $result = $this->rememberMe->removeAllTokens($userId);
            
            if ($result) {
                echo "✓ Token cleanup successful\n";
                
                // Clean up test user
                $this->cleanupTestUser($userId);
                
                return true;
            } else {
                echo "✗ Token cleanup failed\n";
                return false;
            }
            
        } catch (Exception $e) {
            echo "✗ Token cleanup test failed: " . $e->getMessage() . "\n";
            return false;
        }
    }
    
    /**
     * Create a test user for testing
     */
    private function createTestUser() {
        $sql = "INSERT INTO users (email, password, role) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'test@example.com',
            password_hash('testpassword123', PASSWORD_DEFAULT),
            'user'
        ]);
        
        return $this->db->lastInsertId();
    }
    
    /**
     * Clean up test user
     */
    private function cleanupTestUser($userId) {
        $sql = "DELETE FROM users WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
    }
    
    /**
     * Run all tests
     */
    public function runAllTests() {
        echo "Running Remember Me functionality tests...\n\n";
        
        $tests = [
            'testTokenCreation' => 'Token Creation',
            'testTokenValidation' => 'Token Validation',
            'testTokenCleanup' => 'Token Cleanup'
        ];
        
        $passed = 0;
        $total = count($tests);
        
        foreach ($tests as $method => $description) {
            echo "=== {$description} ===\n";
            if ($this->$method()) {
                $passed++;
            }
            echo "\n";
        }
        
        echo "Test Results: {$passed}/{$total} tests passed\n";
        
        if ($passed === $total) {
            echo "🎉 All tests passed! Remember Me functionality is working correctly.\n";
        } else {
            echo "❌ Some tests failed. Please check the implementation.\n";
        }
    }
}

// Run tests if this file is executed directly
if (php_sapi_name() === 'cli') {
    $test = new RememberMeTest();
    $test->runAllTests();
}
