<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\DocumentDownloadController;
use App\Http\Controllers\DocumentPdfController;
use App\Http\Controllers\Portal\DocumentDownloadController as PortalDocumentDownloadController;
use App\Http\Controllers\Portal\InvoicePdfController as PortalInvoicePdfController;
use App\Http\Controllers\Portal\LoginController as PortalLoginController;
use App\Livewire\Bugs\BugForm as BugFormPage;
use App\Livewire\Bugs\BugIndex;
use App\Livewire\Bugs\BugShow;
use App\Livewire\Calendar\CalendarView;
use App\Livewire\ChangeRequests\ChangeRequestForm as ChangeRequestFormPage;
use App\Livewire\ChangeRequests\ChangeRequestIndex;
use App\Livewire\ChangeRequests\ChangeRequestShow;
use App\Livewire\Communications\CommunicationIndex;
use App\Livewire\Customers\CustomerForm as CustomerFormPage;
use App\Livewire\Customers\CustomerIndex;
use App\Livewire\Customers\CustomerShow;
use App\Livewire\Dashboard;
use App\Livewire\Invoices\InvoiceForm as InvoiceFormPage;
use App\Livewire\Invoices\InvoiceIndex;
use App\Livewire\Invoices\InvoiceShow;
use App\Livewire\Leads\LeadIndex;
use App\Livewire\Leads\LeadShow;
use App\Livewire\Maintenance\MaintenanceForm as MaintenanceFormPage;
use App\Livewire\Maintenance\MaintenanceIndex;
use App\Livewire\Meetings\MeetingForm as MeetingFormPage;
use App\Livewire\Meetings\MeetingIndex;
use App\Livewire\Meetings\MeetingShow;
use App\Livewire\Notifications\NotificationIndex;
use App\Livewire\Payments\PaymentIndex;
use App\Livewire\Portal\Invoices;
use App\Livewire\Portal\Projects;
use App\Livewire\Portal\Quotations;
use App\Livewire\Portal\Tickets;
use App\Livewire\Projects\ProjectForm as ProjectFormPage;
use App\Livewire\Projects\ProjectIndex;
use App\Livewire\Projects\ProjectShow;
use App\Livewire\Quotations\QuotationForm as QuotationFormPage;
use App\Livewire\Quotations\QuotationIndex;
use App\Livewire\Quotations\QuotationShow;
use App\Livewire\Reports\ReportsOverview;
use App\Livewire\Staff\StaffForm as StaffFormPage;
use App\Livewire\Staff\StaffIndex;
use App\Livewire\Support\TicketForm as TicketFormPage;
use App\Livewire\Support\TicketIndex;
use App\Livewire\Support\TicketShow;
use App\Livewire\System\ActivityLogViewer;
use App\Livewire\System\RoleMatrix;
use App\Livewire\System\SettingsForm;
use App\Livewire\Tasks\TaskIndex;
use Illuminate\Support\Facades\Route;

// Public marketing landing page. Signed-in staff go straight to their dashboard.
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }

    return view('marketing.home');
})->name('home');

/*
|--------------------------------------------------------------------------
| Guest
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function (): void {
    Route::get('login', [LoginController::class, 'create'])->name('login');
    Route::post('login', [LoginController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('login.store');

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])->name('password.store');
});

/*
|--------------------------------------------------------------------------
| Authenticated staff
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'active'])->group(function (): void {
    Route::post('logout', [LoginController::class, 'destroy'])->name('logout');

    Route::get('dashboard', Dashboard::class)->name('dashboard');

    // Customers
    Route::get('customers', CustomerIndex::class)->name('customers.index');
    Route::get('customers/create', CustomerFormPage::class)->name('customers.create');
    Route::get('customers/{customer}', CustomerShow::class)->name('customers.show');
    Route::get('customers/{customer}/edit', CustomerFormPage::class)->name('customers.edit');

    // Projects
    Route::get('projects', ProjectIndex::class)->name('projects.index');
    Route::get('projects/mine', ProjectIndex::class)->name('projects.mine')->defaults('mineOnly', true);
    Route::get('projects/create', ProjectFormPage::class)->name('projects.create');
    Route::get('projects/{project}', ProjectShow::class)->name('projects.show');
    Route::get('projects/{project}/edit', ProjectFormPage::class)->name('projects.edit');

    // Tasks
    Route::get('tasks', TaskIndex::class)->name('tasks.index');

    // Quotations
    Route::get('quotations', QuotationIndex::class)->name('quotations.index');
    Route::get('quotations/create', QuotationFormPage::class)->name('quotations.create');
    Route::get('quotations/{quotation}', QuotationShow::class)->name('quotations.show');
    Route::get('quotations/{quotation}/edit', QuotationFormPage::class)->name('quotations.edit');
    Route::get('quotations/{quotation}/pdf', [DocumentPdfController::class, 'quotation'])->name('quotations.pdf');

    // Invoices
    Route::get('invoices', InvoiceIndex::class)->name('invoices.index');
    Route::get('invoices/outstanding', InvoiceIndex::class)->name('invoices.outstanding')->defaults('outstandingOnly', true);
    Route::get('invoices/create', InvoiceFormPage::class)->name('invoices.create');
    Route::get('invoices/{invoice}', InvoiceShow::class)->name('invoices.show');
    Route::get('invoices/{invoice}/edit', InvoiceFormPage::class)->name('invoices.edit');
    Route::get('invoices/{invoice}/pdf', [DocumentPdfController::class, 'invoice'])->name('invoices.pdf');

    // Payments
    Route::get('payments', PaymentIndex::class)->name('payments.index');

    // Leads
    Route::get('leads', LeadIndex::class)->name('leads.index');
    Route::get('leads/{lead}', LeadShow::class)->name('leads.show');

    // Change requests
    Route::get('change-requests', ChangeRequestIndex::class)->name('change-requests.index');
    Route::get('change-requests/create', ChangeRequestFormPage::class)->name('change-requests.create');
    Route::get('change-requests/{changeRequest}', ChangeRequestShow::class)->name('change-requests.show');
    Route::get('change-requests/{changeRequest}/edit', ChangeRequestFormPage::class)->name('change-requests.edit');

    // Bugs
    Route::get('bugs', BugIndex::class)->name('bugs.index');
    Route::get('bugs/create', BugFormPage::class)->name('bugs.create');
    Route::get('bugs/{bug}', BugShow::class)->name('bugs.show');
    Route::get('bugs/{bug}/edit', BugFormPage::class)->name('bugs.edit');

    // Communication log
    Route::get('communications', CommunicationIndex::class)->name('communications.index');

    // Meetings
    Route::get('meetings', MeetingIndex::class)->name('meetings.index');
    Route::get('meetings/create', MeetingFormPage::class)->name('meetings.create');
    Route::get('meetings/{meeting}', MeetingShow::class)->name('meetings.show');
    Route::get('meetings/{meeting}/edit', MeetingFormPage::class)->name('meetings.edit');

    // Support tickets
    Route::get('support', TicketIndex::class)->name('support.index');
    Route::get('support/create', TicketFormPage::class)->name('support.create');
    Route::get('support/{ticket}', TicketShow::class)->name('support.show');

    // Maintenance
    Route::get('maintenance', MaintenanceIndex::class)->name('maintenance.index');
    Route::get('maintenance/create', MaintenanceFormPage::class)->name('maintenance.create');
    Route::get('maintenance/{plan}/edit', MaintenanceFormPage::class)->name('maintenance.edit');

    // Documents
    Route::get('documents/{document}/download', DocumentDownloadController::class)->name('documents.download');

    // Calendar & reports
    Route::get('calendar', CalendarView::class)->name('calendar');
    Route::get('reports', ReportsOverview::class)->name('reports');

    // Notifications
    Route::get('notifications', NotificationIndex::class)->name('notifications.index');

    /*
    |----------------------------------------------------------------------
    | System administration
    |----------------------------------------------------------------------
    */
    Route::get('staff', StaffIndex::class)->name('staff.index');
    Route::get('staff/create', StaffFormPage::class)->name('staff.create');
    Route::get('staff/{staff}/edit', StaffFormPage::class)->name('staff.edit');

    Route::get('system/roles', RoleMatrix::class)->name('system.roles');
    Route::get('system/activity', ActivityLogViewer::class)->name('system.activity');
    Route::get('system/settings', SettingsForm::class)->name('system.settings');
});

/*
|--------------------------------------------------------------------------
| Customer Portal
|--------------------------------------------------------------------------
*/
Route::prefix('portal')->name('portal.')->group(function (): void {
    Route::middleware('guest:customer')->group(function (): void {
        Route::get('login', [PortalLoginController::class, 'create'])->name('login');
        Route::post('login', [PortalLoginController::class, 'store'])->middleware('throttle:6,1')->name('login.store');
    });

    Route::middleware(['auth:customer', 'customer.active'])->group(function (): void {
        Route::post('logout', [PortalLoginController::class, 'destroy'])->name('logout');

        Route::get('/', App\Livewire\Portal\Dashboard::class)->name('dashboard');
        Route::get('projects', Projects::class)->name('projects');
        Route::get('projects/{project}', App\Livewire\Portal\ProjectShow::class)->name('projects.show');
        Route::get('quotations', Quotations::class)->name('quotations');
        Route::get('invoices', Invoices::class)->name('invoices');
        Route::get('invoices/{invoice}/pdf', PortalInvoicePdfController::class)->name('invoices.pdf');
        Route::get('tickets', Tickets::class)->name('tickets');
        Route::get('documents/{document}/download', PortalDocumentDownloadController::class)->name('documents.download');
    });
});
