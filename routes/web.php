<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DepartmentController;

// ─── Authentication ───────────────────────────────────────────────────────────
Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/verify-otp',  [AuthController::class, 'showOTP'])->name('otp.show');
Route::post('/verify-otp', [AuthController::class, 'verifyOTP'])->name('otp.verify');
Route::post('/resend-otp', [AuthController::class, 'resendOTP'])->name('otp.resend');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/forgot-password',          [AuthController::class, 'showForgotPassword'])->name('password.request');
Route::post('/forgot-password',         [AuthController::class, 'sendResetLink'])->name('password.email');
Route::get('/reset-password/{token}',   [AuthController::class, 'showResetPassword'])->name('password.reset');
Route::post('/reset-password',          [AuthController::class, 'resetPassword'])->name('password.update');

// Customer Ticket Portal
Route::get('/tickets/new', [\App\Http\Controllers\CustomerTicketController::class, 'create'])->name('tickets.create');
Route::post('/tickets', [\App\Http\Controllers\CustomerTicketController::class, 'store'])->name('tickets.store');

// Public Knowledge Base (Self-Service)
Route::get('/kb', [\App\Http\Controllers\KbController::class, 'index'])->name('kb.index');
Route::get('/kb/{slug}', [\App\Http\Controllers\KbController::class, 'show'])->name('kb.show');
Route::get('/api/kb/search', [\App\Http\Controllers\KbController::class, 'ajaxSearch'])->name('api.kb.search');

// Public Careers (Job Openings & Application)
Route::get('/careers', [\App\Http\Controllers\CareersController::class, 'index'])->name('careers.index');
Route::get('/careers/{job}', [\App\Http\Controllers\CareersController::class, 'show'])->name('careers.show');
Route::post('/careers/{job}/apply', [\App\Http\Controllers\CareersController::class, 'apply'])->name('careers.apply');

// Root redirect to login
Route::get('/', fn() => redirect()->route('admin.dashboard'));

// ─── ERP Admin ────────────────────────────────────────────────────────────────
Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // ── Module 1: Users & Roles ───────────────────────────────────────────────
    Route::get('users/import', [UserController::class, 'importForm'])->name('users.import.form');
    Route::post('users/import', [UserController::class, 'import'])->name('users.import');
    Route::post('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggleStatus');
    Route::resource('users', UserController::class);
    Route::resource('roles', RoleController::class)->except(['show', 'create']);
    Route::resource('departments', DepartmentController::class);
    Route::resource('designations', \App\Http\Controllers\DesignationController::class);

    // ── Module 2: Asset Management ────────────────────────────────────────────
    Route::resource('asset-categories', \App\Http\Controllers\AssetCategoryController::class)->except(['show']);
    Route::resource('asset-locations', \App\Http\Controllers\AssetLocationController::class)->except(['show']);
    Route::resource('assets', \App\Http\Controllers\AssetController::class);
    Route::post('assets/{asset}/lifecycle', [\App\Http\Controllers\AssetLifecycleController::class, 'store'])->name('assets.lifecycle.store');
    Route::post('assets/{asset}/documents', [\App\Http\Controllers\AssetDocumentController::class, 'store'])->name('assets.documents.store');
    Route::delete('asset-documents/{assetDocument}', [\App\Http\Controllers\AssetDocumentController::class, 'destroy'])->name('asset-documents.destroy');

    // ── Module 3: HR & Recruitment ────────────────────────────────────────────
    Route::prefix('hr/recruitment')->name('hr.recruitment.')->group(function () {
        Route::resource('jobs', \App\Http\Controllers\JobPostingController::class);
        
        Route::post('applications/bulk-reject', [\App\Http\Controllers\JobApplicationController::class, 'bulkReject'])->name('applications.bulkReject');
        Route::post('jobs/{jobPosting}/applications', [\App\Http\Controllers\JobApplicationController::class, 'store'])->name('applications.store');
        Route::get('applications/{jobApplication}', [\App\Http\Controllers\JobApplicationController::class, 'show'])->name('applications.show');
        Route::put('applications/{jobApplication}/status', [\App\Http\Controllers\JobApplicationController::class, 'updateStatus'])->name('applications.updateStatus');
        Route::get('applications/{jobApplication}/resume', [\App\Http\Controllers\JobApplicationController::class, 'downloadResume'])->name('applications.downloadResume');
        Route::get('applications/{jobApplication}/offer', [\App\Http\Controllers\JobApplicationController::class, 'downloadOffer'])->name('applications.downloadOffer');
        Route::delete('applications/{jobApplication}', [\App\Http\Controllers\JobApplicationController::class, 'destroy'])->name('applications.destroy');

        Route::post('applications/{jobApplication}/interviews', [\App\Http\Controllers\InterviewController::class, 'store'])->name('interviews.store');
        Route::put('interviews/{interview}', [\App\Http\Controllers\InterviewController::class, 'update'])->name('interviews.update');

        Route::post('applications/{jobApplication}/offers', [\App\Http\Controllers\JobOfferController::class, 'store'])->name('offers.store');
        Route::post('applications/{jobApplication}/screen', [\App\Http\Controllers\JobApplicationController::class, 'screen'])->name('applications.screen');
        Route::post('applications/{jobApplication}/onboard', [\App\Http\Controllers\JobApplicationController::class, 'onboard'])->name('applications.onboard');
    });

    // ── Module 4: HR Employee Records ───────────────────────────────────────────
    Route::prefix('hr')->name('hr.')->group(function () {
        Route::post('employees/{user}/profile', [\App\Http\Controllers\EmployeeController::class, 'updateProfile'])->name('employees.updateProfile');
        Route::post('employees/{user}/contracts', [\App\Http\Controllers\EmployeeController::class, 'addContract'])->name('employees.addContract');
        Route::post('employees/{user}/documents', [\App\Http\Controllers\EmployeeController::class, 'addDocument'])->name('employees.addDocument');
        Route::post('employees/{user}/history', [\App\Http\Controllers\EmployeeController::class, 'addHistory'])->name('employees.addHistory');
        Route::resource('employees', \App\Http\Controllers\EmployeeController::class)->parameters(['employees' => 'user'])->only(['index', 'show']);

        Route::get('contracts', [\App\Http\Controllers\ContractController::class, 'index'])->name('contracts.index');
        Route::get('documents', [\App\Http\Controllers\DocumentVaultController::class, 'index'])->name('documents.index');

        // Payroll Processing
        Route::get('payroll/gl', [\App\Http\Controllers\PayrollController::class, 'glPostings'])->name('payroll.gl');
        Route::post('payroll/{payrollRun}/approve', [\App\Http\Controllers\PayrollController::class, 'approve'])->name('payroll.approve');
        Route::put('payroll-entry/{payrollEntry}/allowance', [\App\Http\Controllers\PayrollController::class, 'updateAllowance'])->name('payroll-entry.allowance');
        Route::get('payroll-entry/{payrollEntry}/payslip', [\App\Http\Controllers\PayrollController::class, 'downloadPayslip'])->name('payroll-entry.payslip');
        Route::resource('payroll', \App\Http\Controllers\PayrollController::class);

        // Leave Management
        Route::post('leaves/{leave}/approve', [\App\Http\Controllers\LeaveController::class, 'approve'])->name('leaves.approve');
        Route::post('leaves/{leave}/reject', [\App\Http\Controllers\LeaveController::class, 'reject'])->name('leaves.reject');
        Route::put('leaves/balances/{balance}', [\App\Http\Controllers\LeaveController::class, 'updateBalance'])->name('leaves.updateBalance');
        Route::resource('leaves', \App\Http\Controllers\LeaveController::class);

        // Overtime Tracking
        Route::post('overtime/{overtime}/approve', [\App\Http\Controllers\OvertimeController::class, 'approve'])->name('overtime.approve');
        Route::post('overtime/{overtime}/reject', [\App\Http\Controllers\OvertimeController::class, 'reject'])->name('overtime.reject');
        Route::resource('overtime', \App\Http\Controllers\OvertimeController::class);

        // Goals (OKRs)
        Route::put('goals/{goal}/progress', [\App\Http\Controllers\PerformanceGoalController::class, 'updateProgress'])->name('goals.updateProgress');
        Route::resource('goals', \App\Http\Controllers\PerformanceGoalController::class);

        // Appraisals & 360 Feedbacks
        Route::post('appraisals/cycle', [\App\Http\Controllers\AppraisalController::class, 'storeCycle'])->name('appraisals.storeCycle');
        Route::post('appraisals/invite', [\App\Http\Controllers\AppraisalController::class, 'invite'])->name('appraisals.invite');
        Route::post('appraisals/{appraisal}/self', [\App\Http\Controllers\AppraisalController::class, 'submitSelf'])->name('appraisals.submitSelf');
        Route::post('appraisals/{appraisal}/manager', [\App\Http\Controllers\AppraisalController::class, 'submitManager'])->name('appraisals.submitManager');
        Route::post('appraisals/{appraisal}/calibrate', [\App\Http\Controllers\AppraisalController::class, 'submitCalibration'])->name('appraisals.submitCalibration');
        Route::post('appraisals/{appraisal}/peer-request', [\App\Http\Controllers\AppraisalController::class, 'requestPeer'])->name('appraisals.requestPeer');
        Route::post('peer-feedback/{feedback}/submit', [\App\Http\Controllers\AppraisalController::class, 'submitPeerFeedback'])->name('peer-feedback.submit');
        Route::resource('appraisals', \App\Http\Controllers\AppraisalController::class);

        // Salary Reviews
        Route::post('salary-reviews/{appraisal}', [\App\Http\Controllers\SalaryReviewController::class, 'store'])->name('salary-reviews.store');
        Route::post('salary-reviews/{review}/approve', [\App\Http\Controllers\SalaryReviewController::class, 'approve'])->name('salary-reviews.approve');
        Route::post('salary-reviews/{review}/reject', [\App\Http\Controllers\SalaryReviewController::class, 'reject'])->name('salary-reviews.reject');
        Route::resource('salary-reviews', \App\Http\Controllers\SalaryReviewController::class)->only(['index']);
    });

    // Helpdesk & Support (Customer Ops)
    Route::post('helpdesk/{ticket}/reply', [\App\Http\Controllers\HelpdeskController::class, 'reply'])->name('helpdesk.reply');
    Route::put('helpdesk/{ticket}/update', [\App\Http\Controllers\HelpdeskController::class, 'updateTicket'])->name('helpdesk.updateTicket');
    Route::resource('helpdesk/sla-policies', \App\Http\Controllers\SlaPolicyController::class)->names('sla-policies')->parameters(['sla-policies' => 'slaPolicy']);
    Route::resource('helpdesk/kb-articles', \App\Http\Controllers\KbArticleController::class)->names('kb-articles')->parameters(['kb-articles' => 'kbArticle']);
    Route::resource('helpdesk', \App\Http\Controllers\HelpdeskController::class)->parameters(['helpdesk' => 'ticket']);

    // Global Settings
    Route::get('settings/global', [\App\Http\Controllers\SettingsController::class, 'globalIndex'])->name('settings.global');
    Route::post('settings/global', [\App\Http\Controllers\SettingsController::class, 'globalUpdate'])->name('settings.global.update');
});
