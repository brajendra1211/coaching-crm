<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\FrontendController;
use App\Http\Controllers\CourseController as FrontCourseController;
use App\Http\Controllers\AdmissionController as FrontAdmissionController;
use App\Http\Controllers\ContactController as FrontContactController;
use App\Http\Controllers\ResultController as FrontResultController;
use App\Http\Controllers\LeadController as FrontLeadController;
use App\Http\Controllers\DynamicPageController;
use App\Http\Controllers\BlogController as FrontBlogController;
use App\Http\Controllers\GalleryController as FrontGalleryController;
use App\Http\Controllers\TestimonialController as FrontTestimonialController;
use App\Http\Controllers\SeoToolController;
use App\Http\Controllers\Portal\AuthController as PortalAuthController;
use App\Http\Controllers\Portal\StaffDashboardController;
use App\Http\Controllers\Portal\StudentDashboardController;
use App\Http\Controllers\Portal\StudentExamController;
use App\Http\Controllers\Portal\TeacherDashboardController;
use App\Http\Controllers\SuperAdmin\AuthController as SuperAdminAuthController;
use App\Http\Controllers\SuperAdmin\DashboardController as SuperAdminDashboardController;
use App\Http\Controllers\SuperAdmin\PlanController as SuperAdminPlanController;
use App\Http\Controllers\SuperAdmin\TenantController as SuperAdminTenantController;
use App\Services\Tenancy\TenantManager;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CourseController as AdminCourseController;
use App\Http\Controllers\Admin\LeadController as AdminLeadController;
use App\Http\Controllers\Admin\SeoLocationController;
use App\Http\Controllers\Admin\CoachingSettingController;
use App\Http\Controllers\Admin\WebsitePageController as AdminWebsitePageController;
use App\Http\Controllers\Admin\BlogController as AdminBlogController;
use App\Http\Controllers\Admin\GalleryController as AdminGalleryController;
use App\Http\Controllers\Admin\EditorUploadController;
use App\Http\Controllers\Admin\TestimonialController as AdminTestimonialController;
use App\Http\Controllers\Admin\SeoMetaController as AdminSeoMetaController;
use App\Http\Controllers\Admin\AdmissionController as AdminAdmissionController;
use App\Http\Controllers\Admin\StudentController as AdminStudentController;
use App\Http\Controllers\Admin\ParentController as AdminParentController;
use App\Http\Controllers\Admin\TeacherController as AdminTeacherController;
use App\Http\Controllers\Admin\SubjectController as AdminSubjectController;
use App\Http\Controllers\Admin\BatchController as AdminBatchController;
use App\Http\Controllers\Admin\AttendanceController as AdminAttendanceController;
use App\Http\Controllers\Admin\OnlineClassController as AdminOnlineClassController;
use App\Http\Controllers\Admin\StudyMaterialController as AdminStudyMaterialController;
use App\Http\Controllers\Admin\BatchFeePlanController as AdminBatchFeePlanController;
use App\Http\Controllers\Admin\FeeCollectionController as AdminFeeCollectionController;
use App\Http\Controllers\Admin\FeeDashboardController as AdminFeeDashboardController;
use App\Http\Controllers\Admin\InvoiceSettingController as AdminInvoiceSettingController;
use App\Http\Controllers\Admin\IdCardController as AdminIdCardController;
use App\Http\Controllers\Admin\CertificateController as AdminCertificateController;
use App\Http\Controllers\Admin\QuestionController as AdminQuestionController;
use App\Http\Controllers\Admin\ExamController as AdminExamController;
use App\Http\Controllers\Admin\ExamSeriesController as AdminExamSeriesController;
use App\Http\Controllers\Admin\ExamResultController as AdminExamResultController;
use App\Http\Controllers\Admin\MarksheetController as AdminMarksheetController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\TenantDomainController as AdminTenantDomainController;
/*
|--------------------------------------------------------------------------
| Frontend Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [FrontendController::class, 'home'])->name('home');

Route::get('/courses', [FrontCourseController::class, 'index'])->name('courses.index');
Route::get('/courses/{slug}', [FrontCourseController::class, 'show'])->name('courses.show');

Route::get('/admission', [FrontAdmissionController::class, 'index'])->name('admission.index');
Route::post('/admission', [FrontAdmissionController::class, 'store'])->name('admission.store');

Route::get('/contact', [FrontContactController::class, 'index'])->name('contact.index');
Route::post('/contact', [FrontContactController::class, 'store'])->name('contact.store');

Route::get('/results', [FrontResultController::class, 'index'])->name('results.index');

Route::get('/blogs', [FrontBlogController::class, 'index'])->name('blogs.index');
Route::get('/blogs/{slug}', [FrontBlogController::class, 'show'])->name('blogs.show');

Route::get('/gallery', [FrontGalleryController::class, 'index'])->name('gallery.index');
Route::redirect('/gallary', '/gallery', 301);

Route::get('/testimonials', [FrontTestimonialController::class, 'index'])->name('testimonials.index');

Route::post('/lead-submit', [FrontLeadController::class, 'store'])->name('lead.submit');

Route::get('/student/login', [PortalAuthController::class, 'showLogin'])
    ->defaults('role', 'student')
    ->name('student.login');
Route::post('/student/login', [PortalAuthController::class, 'login'])
    ->defaults('role', 'student')
    ->name('student.login.submit');

Route::middleware(['portal:student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');
    Route::get('/exams', [StudentDashboardController::class, 'exams'])->name('exams.index');
    Route::get('/exams/{exam}', [StudentExamController::class, 'show'])->name('exams.show');
    Route::post('/exams/{exam}/submit', [StudentExamController::class, 'submit'])->name('exams.submit');
    Route::get('/exam-attempts/{attempt}', [StudentExamController::class, 'result'])->name('exams.result');
    Route::get('/results', [StudentDashboardController::class, 'results'])->name('results.index');
    Route::get('/fees', [StudentDashboardController::class, 'fees'])->name('fees.index');
    Route::get('/attendance', [StudentDashboardController::class, 'attendance'])->name('attendance.index');
    Route::get('/materials', [StudentDashboardController::class, 'materials'])->name('materials.index');
    Route::get('/classes', [StudentDashboardController::class, 'classes'])->name('classes.index');
    Route::get('/certificates', [StudentDashboardController::class, 'certificates'])->name('certificates.index');
    Route::get('/certificates/{certificate}/pdf', [StudentDashboardController::class, 'certificatePdf'])->name('certificates.pdf');
    Route::get('/profile', [StudentDashboardController::class, 'profile'])->name('profile.index');
    Route::post('/logout', [PortalAuthController::class, 'logout'])
        ->defaults('role', 'student')
        ->name('logout');
});

Route::get('/teacher/login', [PortalAuthController::class, 'showLogin'])
    ->defaults('role', 'teacher')
    ->name('teacher.login');
Route::post('/teacher/login', [PortalAuthController::class, 'login'])
    ->defaults('role', 'teacher')
    ->name('teacher.login.submit');

Route::middleware(['portal:teacher'])->prefix('teacher')->name('teacher.')->group(function () {
    Route::get('/dashboard', [TeacherDashboardController::class, 'index'])->name('dashboard');
    Route::get('/batches', [TeacherDashboardController::class, 'batches'])->name('batches.index');
    Route::get('/students', [TeacherDashboardController::class, 'students'])->name('students.index');
    Route::get('/exams', [TeacherDashboardController::class, 'exams'])->name('exams.index');
    Route::get('/exams/create', [TeacherDashboardController::class, 'createExam'])->name('exams.create');
    Route::post('/exams', [TeacherDashboardController::class, 'storeExam'])->name('exams.store');
    Route::get('/exams/{exam}/edit', [TeacherDashboardController::class, 'editExam'])->name('exams.edit');
    Route::put('/exams/{exam}', [TeacherDashboardController::class, 'updateExam'])->name('exams.update');
    Route::get('/exams/{exam}/builder', [TeacherDashboardController::class, 'examBuilder'])->name('exams.builder');
    Route::put('/exams/{exam}/builder', [TeacherDashboardController::class, 'updateExamBuilder'])->name('exams.builder.update');
    Route::get('/series', [TeacherDashboardController::class, 'series'])->name('series.index');
    Route::get('/series/create', [TeacherDashboardController::class, 'createSeries'])->name('series.create');
    Route::post('/series', [TeacherDashboardController::class, 'storeSeries'])->name('series.store');
    Route::get('/series/{examSeries}/edit', [TeacherDashboardController::class, 'editSeries'])->name('series.edit');
    Route::put('/series/{examSeries}', [TeacherDashboardController::class, 'updateSeries'])->name('series.update');
    Route::get('/series/{examSeries}/builder', [TeacherDashboardController::class, 'seriesBuilder'])->name('series.builder');
    Route::put('/series/{examSeries}/builder', [TeacherDashboardController::class, 'updateSeriesBuilder'])->name('series.builder.update');
    Route::get('/materials', [TeacherDashboardController::class, 'materials'])->name('materials.index');
    Route::get('/materials/create', [TeacherDashboardController::class, 'createMaterial'])->name('materials.create');
    Route::post('/materials', [TeacherDashboardController::class, 'storeMaterial'])->name('materials.store');
    Route::get('/attendance', [TeacherDashboardController::class, 'attendance'])->name('attendance.index');
    Route::post('/attendance', [TeacherDashboardController::class, 'storeAttendance'])->name('attendance.store');
    Route::get('/classes', [TeacherDashboardController::class, 'classes'])->name('classes.index');
    Route::get('/questions', [TeacherDashboardController::class, 'questions'])->name('questions.index');
    Route::get('/questions/create', [TeacherDashboardController::class, 'createQuestion'])->name('questions.create');
    Route::post('/questions', [TeacherDashboardController::class, 'storeQuestion'])->name('questions.store');
    Route::get('/questions/import', [TeacherDashboardController::class, 'importQuestions'])->name('questions.import');
    Route::get('/questions/import/sample', [TeacherDashboardController::class, 'downloadQuestionImportSample'])->name('questions.import.sample');
    Route::post('/questions/import', [TeacherDashboardController::class, 'storeQuestionImport'])->name('questions.import.store');
    Route::get('/profile', [TeacherDashboardController::class, 'profile'])->name('profile.index');
    Route::post('/logout', [PortalAuthController::class, 'logout'])
        ->defaults('role', 'teacher')
        ->name('logout');
});

Route::get('/staff/login', [PortalAuthController::class, 'showLogin'])
    ->defaults('role', 'staff')
    ->name('staff.login');
Route::post('/staff/login', [PortalAuthController::class, 'login'])
    ->defaults('role', 'staff')
    ->name('staff.login.submit');

Route::middleware(['portal:staff'])->prefix('staff')->name('staff.')->group(function () {
    Route::get('/dashboard', [StaffDashboardController::class, 'index'])->name('dashboard');
    Route::get('/leads', [StaffDashboardController::class, 'leads'])->name('leads.index');
    Route::get('/admissions', [StaffDashboardController::class, 'admissions'])->name('admissions.index');
    Route::get('/students', [StaffDashboardController::class, 'students'])->name('students.index');
    Route::get('/fees', [StaffDashboardController::class, 'fees'])->name('fees.index');
    Route::get('/exams', [StaffDashboardController::class, 'exams'])->name('exams.index');
    Route::get('/materials', [StaffDashboardController::class, 'materials'])->name('materials.index');
    Route::get('/certificates', [StaffDashboardController::class, 'certificates'])->name('certificates.index');
    Route::post('/logout', [PortalAuthController::class, 'logout'])
        ->defaults('role', 'staff')
        ->name('logout');
});

Route::get('/sitemap.xml', [SeoToolController::class, 'sitemap'])->name('sitemap.xml');
Route::get('/robots.txt', [SeoToolController::class, 'robots'])->name('robots.txt');

Route::get('/storage/{path}', function (string $path) {
    $tenant = app(TenantManager::class)->current();
    $root = $tenant
        ? storage_path('app/public/' . trim($tenant->storage_path ?: ('tenants/' . $tenant->slug), '/'))
        : storage_path('app/public');

    $file = realpath($root . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path));
    $rootReal = realpath($root);

    abort_unless($file && $rootReal && str_starts_with($file, $rootReal) && File::exists($file), 404);

    return Response::file($file);
})->where('path', '.*')->name('tenant.storage');

Route::get('/super-admin/login', [SuperAdminAuthController::class, 'showLogin'])->name('super-admin.login');
Route::post('/super-admin/login', [SuperAdminAuthController::class, 'login'])->name('super-admin.login.submit');

Route::middleware(['super_admin'])->prefix('super-admin')->name('super-admin.')->group(function () {
    Route::get('/', fn () => redirect()->route('super-admin.dashboard'));
    Route::get('/dashboard', [SuperAdminDashboardController::class, 'index'])->name('dashboard');

    Route::resource('plans', SuperAdminPlanController::class)->except(['show']);
    Route::resource('tenants', SuperAdminTenantController::class);
    Route::put('tenants/{tenant}/status', [SuperAdminTenantController::class, 'updateStatus'])->name('tenants.status');
    Route::post('tenants/{tenant}/provision', [SuperAdminTenantController::class, 'provision'])->name('tenants.provision');
    Route::post('tenants/{tenant}/subscription', [SuperAdminTenantController::class, 'extendSubscription'])->name('tenants.subscription');
    Route::post('tenants/{tenant}/domains', [SuperAdminTenantController::class, 'addDomain'])->name('tenants.domains.store');
    Route::post('domains/{domain}/verify', [SuperAdminTenantController::class, 'verifyDomain'])->name('domains.verify');
    Route::post('/logout', [SuperAdminAuthController::class, 'logout'])->name('logout');
});

/*
|--------------------------------------------------------------------------
| Admin Auth Routes
|--------------------------------------------------------------------------
*/

Route::get('/admin/login', [AuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'login'])->name('admin.login.submit');

/*
|--------------------------------------------------------------------------
| Admin Protected Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', function () {
        return redirect()->route('admin.dashboard');
    });

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::post('/editor/upload-image', [EditorUploadController::class, 'uploadImage'])
        ->name('editor.upload-image');

    /*
    |--------------------------------------------------------------------------
    | Website CMS
    |--------------------------------------------------------------------------
    */

    Route::resource('courses', AdminCourseController::class)->except(['show']);

    Route::resource('leads', AdminLeadController::class)
        ->only(['index', 'show', 'update', 'destroy']);

    Route::resource('locations', SeoLocationController::class)->except(['show']);
    Route::resource('pages', AdminWebsitePageController::class)->except(['show']);
    Route::resource('blogs', AdminBlogController::class)->except(['show']);

    Route::resource('gallery', AdminGalleryController::class)
        ->parameters(['gallery' => 'galleryItem'])
        ->except(['show']);

    Route::resource('testimonials', AdminTestimonialController::class)->except(['show']);

    Route::get('/settings', [CoachingSettingController::class, 'edit'])->name('settings.index');
    Route::put('/settings', [CoachingSettingController::class, 'update'])->name('settings.update');

    Route::resource('seo', AdminSeoMetaController::class)
        ->parameters(['seo' => 'seoMeta'])
        ->except(['show']);

    /*
    |--------------------------------------------------------------------------
    | Admissions CRM
    |--------------------------------------------------------------------------
    */

    Route::resource('admissions', AdminAdmissionController::class);
    Route::resource('students', AdminStudentController::class);
    Route::resource('parents', AdminParentController::class);

    /*
    |--------------------------------------------------------------------------
    | Academics
    |--------------------------------------------------------------------------
    */

    Route::resource('teachers', AdminTeacherController::class)->except(['show']);
    Route::resource('subjects', AdminSubjectController::class)->except(['show']);

    /*
    |--------------------------------------------------------------------------
    | Batch Builder Custom Routes
    |--------------------------------------------------------------------------
    | Important: These routes should stay before Route::resource('batches', ...)
    */

    Route::get('batches/{batch}/builder', [AdminBatchController::class, 'builder'])
        ->name('batches.builder');

    Route::post('batches/{batch}/students', [AdminBatchController::class, 'syncStudents'])
        ->name('batches.students.sync');

    Route::post('batches/{batch}/teachers', [AdminBatchController::class, 'syncTeachers'])
        ->name('batches.teachers.sync');

    /*
    |--------------------------------------------------------------------------
    | Batches Resource
    |--------------------------------------------------------------------------
    | Do not use ->except(['show']) because Batch Profile needs admin.batches.show
    */

    Route::resource('batches', AdminBatchController::class);

    Route::get('attendance', [AdminAttendanceController::class, 'index'])
        ->name('attendance.index');

    Route::post('attendance', [AdminAttendanceController::class, 'store'])
        ->name('attendance.store');

    Route::resource('online-classes', AdminOnlineClassController::class)->except(['show']);
    Route::resource('study-materials', AdminStudyMaterialController::class)->except(['show']);

    Route::resource('questions', AdminQuestionController::class)->except(['show']);
    Route::get('exams/{exam}/builder', [AdminExamController::class, 'builder'])
        ->name('exams.builder');
    Route::put('exams/{exam}/builder', [AdminExamController::class, 'updateBuilder'])
        ->name('exams.builder.update');
    Route::resource('exams', AdminExamController::class)->except(['show']);
    Route::get('exam-series/{examSeries}/builder', [AdminExamSeriesController::class, 'builder'])
        ->name('exam-series.builder');
    Route::put('exam-series/{examSeries}/builder', [AdminExamSeriesController::class, 'updateBuilder'])
        ->name('exam-series.builder.update');
    Route::resource('exam-series', AdminExamSeriesController::class)
        ->parameters(['exam-series' => 'examSeries'])
        ->except(['show']);
    Route::resource('results', AdminExamResultController::class)
        ->parameters(['results' => 'result'])
        ->except(['show']);
    Route::get('marksheets', [AdminMarksheetController::class, 'index'])
        ->name('marksheets.index');

    Route::resource('batch-fee-plans', AdminBatchFeePlanController::class)
    ->parameters(['batch-fee-plans' => 'batchFeePlan'])
    ->except(['show']);

    Route::get('fee-collections/{feePayment}/receipt', [AdminFeeCollectionController::class, 'receipt'])
    ->name('fee-collections.receipt');

    Route::resource('fee-collections', AdminFeeCollectionController::class)
        ->except(['edit', 'update']);
        
    Route::get('fee-collections/{feePayment}/receipt/pdf', [AdminFeeCollectionController::class, 'downloadPdf'])
    ->name('fee-collections.receipt.pdf');    

    Route::get('fees', [AdminFeeDashboardController::class, 'index'])
    ->name('fees.index');  

    Route::get('invoices', function () {
        return redirect()->route('admin.fee-collections.index');
    })->name('invoices.index');

    Route::get('payments', function () {
        return redirect()->route('admin.fee-collections.index');
    })->name('payments.index');

    Route::get('fee-reports', [AdminFeeDashboardController::class, 'index'])
        ->name('fee-reports.index');
    
    Route::get('invoice-settings', [AdminInvoiceSettingController::class, 'edit'])
    ->name('invoice-settings.edit');

    Route::put('invoice-settings', [AdminInvoiceSettingController::class, 'update'])
        ->name('invoice-settings.update');

    Route::get('id-cards', [AdminIdCardController::class, 'index'])
    ->name('id-cards.index');

    Route::get('id-cards/{student}', [AdminIdCardController::class, 'show'])
        ->name('id-cards.show');

    Route::get('id-cards/{student}/pdf', [AdminIdCardController::class, 'downloadPdf'])
        ->name('id-cards.pdf');

    Route::get('certificates/{certificate}/pdf', [AdminCertificateController::class, 'downloadPdf'])
        ->name('certificates.pdf');

    Route::resource('certificates', AdminCertificateController::class)
        ->except(['edit', 'update']);

    Route::resource('users', AdminUserController::class)->except(['show']);
    Route::get('roles', [AdminUserController::class, 'roles'])->name('roles.index');
    Route::get('domains', [AdminTenantDomainController::class, 'index'])->name('domains.index');
    Route::post('domains', [AdminTenantDomainController::class, 'store'])->name('domains.store');
    Route::post('domains/{domain}/verify', [AdminTenantDomainController::class, 'verify'])->name('domains.verify');

        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    });

/*
|--------------------------------------------------------------------------
| Dynamic Frontend Page Route
|--------------------------------------------------------------------------
*/

Route::get('/{slug}', [DynamicPageController::class, 'show'])
    ->where('slug', '^(?!super-admin|admin|student|teacher|staff|courses|admission|contact|results|blogs|gallery|gallary|testimonials|sitemap\.xml|robots\.txt|lead-submit|storage).*$')
    ->name('dynamic.page');
