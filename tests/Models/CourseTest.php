<?php

use PHPUnit\Framework\TestCase;
use App\Models\Course;
use Tests\Helpers\TestHelper;

class CourseTest extends TestCase {
    private $pdo;
    private $course;

    protected function setUp(): void {
        $this->pdo = TestHelper::getMockPDO();
        $this->course = new Course($this->pdo);
        
        // Create courses table
        $this->pdo->exec("CREATE TABLE courses (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            description TEXT,
            code TEXT NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )");
    }

    public function testCreateCourse() {
        $courseData = TestHelper::createTestCourse();
        $courseId = $this->course->create($courseData);
        
        $this->assertIsInt($courseId);
        $this->assertGreaterThan(0, $courseId);
    }

    public function testFindCourseByCode() {
        $courseData = TestHelper::createTestCourse();
        $this->course->create($courseData);
        
        $foundCourse = $this->course->findByCode($courseData['code']);
        
        $this->assertIsArray($foundCourse);
        $this->assertEquals($courseData['code'], $foundCourse['code']);
        $this->assertEquals($courseData['name'], $foundCourse['name']);
    }

    public function testUpdateCourse() {
        $courseData = TestHelper::createTestCourse();
        $courseId = $this->course->create($courseData);
        
        $updatedData = [
            'name' => 'Updated Course Name',
            'description' => 'Updated Description'
        ];
        
        $result = $this->course->update($courseId, $updatedData);
        $this->assertTrue($result);
        
        $updatedCourse = $this->course->findByCode($courseData['code']);
        $this->assertEquals($updatedData['name'], $updatedCourse['name']);
        $this->assertEquals($updatedData['description'], $updatedCourse['description']);
    }

    public function testDeleteCourse() {
        $courseData = TestHelper::createTestCourse();
        $courseId = $this->course->create($courseData);
        
        $result = $this->course->delete($courseId);
        $this->assertTrue($result);
        
        $deletedCourse = $this->course->findByCode($courseData['code']);
        $this->assertFalse($deletedCourse);
    }

    protected function tearDown(): void {
        $this->pdo->exec("DROP TABLE courses");
    }
} 