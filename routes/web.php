<?php 
use App\Controllers\AuthController;
use App\Controllers\HomeController;
use App\Controllers\AdminController;
use App\Controllers\CourseController;
use App\Models\Setting;

$request_method = $_SERVER['REQUEST_METHOD'];
$request = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$request = filter_var($request, FILTER_SANITIZE_URL);

// Get maintenance mode setting
$settingModel = new Setting($pdo);
$maintenanceMode = $settingModel->get('maintenance_mode') === 'true';

// Allowed routes during maintenance
$allowedRoutes = ['/login', '/logout', '/admin', '/admin/dashboard', '/admin/settings'];

if ($maintenanceMode && !in_array($request, $allowedRoutes) && 
    (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin')) {
    require ROOT_PATH . '/app/Views/maintenance.php';
    exit;
}

switch ($request) {

    case '/':
        (new HomeController($pdo))->index();
        break;
        
    case '/login':
        $controller = new AuthController($pdo);
        if ($request_method === 'GET') {
            $controller->showLogin();
        } else {
            $controller->login();
        }
        break;
        
    case '/register':
        $controller = new AuthController($pdo);
        if ($request_method === 'GET') {
            $controller->showRegister();
        } else {
            $controller->register();
        }
        break;
        
    case '/logout':
        $controller = new AuthController($pdo);
        $controller->logout();
        break;

    case '/admin':
    case '/admin/dashboard':
        $controller = new AdminController($pdo);
        $controller->dashboard();
        break;
        
    case '/dashboard':
        // Handle dashboard route
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Please login to access dashboard';
            header('Location: /login');
            exit;
        }
        require ROOT_PATH . '/app/Views/dashboard.php';
        break;

    case '/admin/settings':
        $controller = new AdminController($pdo);
        if ($request_method === 'POST') {
            $controller->updateSettings();
        } else {
            header('Location: /admin/dashboard');
        }
        break;

    case '/admin/courses':
        $controller = new AdminController($pdo);
        $controller->courses();
        break;

    case '/admin/courses/create':
        $controller = new AdminController($pdo);
        if ($request_method === 'GET') {
            $controller->createCourse();
        } else {
            $controller->createCourse();
        }
        break;

    case (preg_match('/^\/admin\/courses\/edit\/(\d+)$/', $request, $matches) ? true : false):
        $controller = new AdminController($pdo);
        $id = $matches[1];
        if ($request_method === 'GET') {
            $controller->editCourse($id);
        } else {
            $controller->editCourse($id);
        }
        break;

    case (preg_match('/^\/admin\/courses\/delete\/(\d+)$/', $request, $matches) ? true : false):
        $controller = new AdminController($pdo);
        $controller->deleteCourse($matches[1]);
        break;

    case (preg_match('/^\/admin\/courses\/(\d+)\/sections$/', $request, $matches) ? true : false):
        $controller = new AdminController($pdo);
        $controller->sections($matches[1]);
        break;

    case (preg_match('/^\/admin\/courses\/(\d+)\/sections\/create$/', $request, $matches) ? true : false):
        $controller = new AdminController($pdo);
        if ($request_method === 'GET') {
            $controller->createSection($matches[1]);
        } else {
            $controller->createSection($matches[1]);
        }
        break;

    case (preg_match('/^\/admin\/sections\/(\d+)\/edit$/', $request, $matches) ? true : false):
        $controller = new AdminController($pdo);
        if ($request_method === 'GET') {
            $controller->editSection($matches[1]);
        } else {
            $controller->editSection($matches[1]);
        }
        break;

    case (preg_match('/^\/admin\/sections\/(\d+)\/delete$/', $request, $matches) ? true : false):
        $controller = new AdminController($pdo);
        $controller->deleteSection($matches[1]);
        break;

    case (preg_match('/^\/admin\/sections\/(\d+)\/notes\/create$/', $request, $matches) ? true : false):
        $controller = new AdminController($pdo);
        if ($request_method === 'GET') {
            $controller->createNote($matches[1]);
        } else {
            $controller->createNote($matches[1]);
        }
        break;

    case '/pricing':
        require ROOT_PATH . '/app/Views/pricing.php';
        break;

    case (preg_match('/^\/syllabus\/(\d+)$/', $request, $matches) ? true : false):
        $controller = new HomeController($pdo);
        $controller->syllabus($matches[1]);
        break;

    case '/admin/settings/academic-years':
        $controller = new AdminController($pdo);
        $controller->academicYears();
        break;

    case '/admin/settings/academic-years/create':
        $controller = new AdminController($pdo);
        $controller->createAcademicYear();
        break;

    case '/admin/settings/academic-years/set-active':
        $controller = new AdminController($pdo);
        $controller->setActiveAcademicYear();
        break;

    case (preg_match('/^\/admin\/settings\/academic-years\/edit\/(\d+)$/', $request, $matches) ? true : false):
        $controller = new AdminController($pdo);
        $controller->editAcademicYear($matches[1]);
        break;

    // Student weekly plans route
    case (preg_match('/^\/courses\/(\d+)\/weekly-plans$/', $request, $matches) ? true : false):
        $controller = new CourseController($pdo);
        $controller->weeklyPlans($matches[1]);
        break;

    // Admin weekly plans routes
    case (preg_match('/^\/admin\/courses\/(\d+)\/weekly-plans$/', $request, $matches) ? true : false):
        $controller = new AdminController($pdo);
        $controller->weeklyPlans($matches[1]);
        break;

    case (preg_match('/^\/admin\/courses\/(\d+)\/weekly-plans\/(\d+)$/', $request, $matches) ? true : false):
        $controller = new AdminController($pdo);
        if ($request_method === 'POST') {
            $controller->updateWeeklyPlan($matches[1], $matches[2]);
        }
        break;

    case (preg_match('/^\/admin\/courses\/(\d+)\/weekly-plans\/(\d+)\/edit$/', $request, $matches) ? true : false):
        $controller = new AdminController($pdo);
        $controller->editWeeklyPlan($matches[1], $matches[2]);
        break;

    case (preg_match('/^\/admin\/courses\/(\d+)\/weekly-plans\/(\d+)\/create$/', $request, $matches) ? true : false):
        $controller = new AdminController($pdo);
        if ($request_method === 'GET') {
            $controller->createWeeklyPlan($matches[1], $matches[2]);
        } else if ($request_method === 'POST') {
            $controller->storeWeeklyPlan($matches[1], $matches[2]);
        }
        break;

    // Section Notes Route
    case (preg_match('/^\/courses\/(\d+)\/sections\/(\d+)\/notes$/', $request, $matches) ? true : false):
        $controller = new CourseController($pdo);
        $controller->sectionNotes($matches[1], $matches[2]);
        break;

    case '/courses':
        $controller = new CourseController($pdo);
        $controller->index();
        break;

    case (preg_match('/^\/courses\/(\d+)\/yearly-plans$/', $request, $matches) ? true : false):
        $controller = new CourseController($pdo);
        $controller->yearlyPlans($matches[1]);
        break;

    case (preg_match('/^\/admin\/notes\/(\d+)\/edit$/', $request, $matches) ? true : false):
        $controller = new AdminController($pdo);
        if ($request_method === 'GET') {
            $controller->editNote($matches[1]);
        } else {
            $controller->updateNote($matches[1]);
        }
        break;

    case (preg_match('/^\/admin\/notes\/(\d+)\/delete$/', $request, $matches) ? true : false):
        $controller = new AdminController($pdo);
        $controller->deleteNote($matches[1]);
        break;

    case (preg_match('/^\/courses\/(\d+)\/tags\/([^\/]+)$/', $request, $matches) ? true : false):
        $controller = new CourseController($pdo);
        $controller->notesByTag($matches[1], urldecode($matches[2]));
        break;

    case (preg_match('/^\/courses\/(\d+)\/sections\/(\d+)\/notes\/(\d+)$/', $request, $matches) ? true : false):
        $controller = new CourseController($pdo);
        $controller->singleNote($matches[1], $matches[2], $matches[3]);
        break;

    case (preg_match('/^\/courses\/(\d+)$/', $request, $matches) ? true : false):
        $controller = new CourseController($pdo);
        $controller->show($matches[1]);
        break;

    // Search routes
    case (preg_match('/^\/courses\/(\d+)\/search$/', $request, $matches) ? true : false):
        $controller = new CourseController($pdo);
        $controller->search($matches[1]);
        break;

    case (preg_match('/^\/courses\/(\d+)\/sections\/(\d+)\/search$/', $request, $matches) ? true : false):
        $controller = new CourseController($pdo);
        $controller->search($matches[1], $matches[2]);
        break;

    // Teacher Profile routes
    case '/admin/teacher-profiles/create':
        $controller = new AdminController($pdo);
        if ($request_method === 'GET') {
            $controller->createTeacherProfile();
        } else {
            $controller->createTeacherProfile();
        }
        break;

    case (preg_match('/^\/admin\/teacher-profiles\/(\d+)\/edit$/', $request, $matches) ? true : false):
        $controller = new AdminController($pdo);
        if ($request_method === 'GET') {
            $controller->editTeacherProfile($matches[1]);
        } else {
            $controller->editTeacherProfile($matches[1]);
        }
        break;

    case (preg_match('/^\/admin\/teacher-profiles\/(\d+)\/delete$/', $request, $matches) ? true : false):
        $controller = new AdminController($pdo);
        echo $controller->deleteTeacherProfile($matches[1]);
        break;

    case (preg_match('/^\/api\/teacher-profiles\/(\d+)$/', $request, $matches) ? true : false):
        $controller = new AdminController($pdo);
        $controller->getTeacherProfile($matches[1]);
        break;

    case (preg_match('/^\/teacher-profile\/(\d+)$/', $request, $matches) ? true : false):
        $controller = new HomeController($pdo);
        $controller->teacherProfile($matches[1]);
        break;

    // Learning Statement routes
    case '/admin/learning-statements/create':
        $controller = new AdminController($pdo);
        if ($request_method === 'POST') {
            $controller->createLearningStatement();
        }
        break;

    case (preg_match('/^\/admin\/learning-statements\/edit\/(\d+)$/', $request, $matches) ? true : false):
        $controller = new AdminController($pdo);
        if ($request_method === 'POST') {
            $controller->editLearningStatement($matches[1]);
        }
        break;

    case (preg_match('/^\/admin\/learning-statements\/delete\/(\d+)$/', $request, $matches) ? true : false):
        $controller = new AdminController($pdo);
        if ($request_method === 'POST') {
            echo $controller->deleteLearningStatement($matches[1]);
        }
        break;

    default:
        require ROOT_PATH . '/app/Views/404.php';
        break;
}