<?php

namespace App\Enums;

use App\Enums\Concerns\HasLabel;

/**
 * Every capability the system recognises. Administrators may re-assign these
 * to roles at runtime, but the catalogue itself is defined here so policies
 * can reference constants instead of magic strings.
 */
enum Permission: string
{
    use HasLabel;

    case ViewCustomers = 'view customers';
    case CreateCustomers = 'create customers';
    case EditCustomers = 'edit customers';
    case DeleteCustomers = 'delete customers';

    case ViewProjects = 'view projects';
    case CreateProjects = 'create projects';
    case EditProjects = 'edit projects';
    case DeleteProjects = 'delete projects';

    case ManageTasks = 'manage tasks';

    case ManageChangeRequests = 'manage change requests';
    case ManageBugs = 'manage bugs';
    case ManageMeetings = 'manage meetings';
    case LogCommunications = 'log communications';
    case ManageSupport = 'manage support';
    case ManageMaintenance = 'manage maintenance';
    case ManageDocuments = 'manage documents';

    case ManageQuotations = 'manage quotations';
    case ApproveQuotations = 'approve quotations';

    case ManageInvoices = 'manage invoices';
    case RecordPayments = 'record payments';
    case ViewFinancialReports = 'view financial reports';
    case ViewReports = 'view reports';

    case ManageStaff = 'manage staff';
    case ManageRoles = 'manage roles';
    case ManageSystemSettings = 'manage system settings';
    case ManageCustomerPortal = 'manage customer portal';
    case ViewActivityLogs = 'view activity logs';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_map(fn (self $p): string => $p->value, self::cases());
    }

    /**
     * Grouping used by the Roles & Permissions settings screen.
     */
    public function group(): string
    {
        return match ($this) {
            self::ViewCustomers, self::CreateCustomers, self::EditCustomers, self::DeleteCustomers => 'Customers',
            self::ViewProjects, self::CreateProjects, self::EditProjects, self::DeleteProjects, self::ManageTasks => 'Projects',
            self::ManageChangeRequests, self::ManageBugs, self::ManageMeetings, self::LogCommunications,
            self::ManageSupport, self::ManageMaintenance, self::ManageDocuments => 'Delivery',
            self::ManageQuotations, self::ApproveQuotations => 'Sales',
            self::ManageInvoices, self::RecordPayments, self::ViewFinancialReports, self::ViewReports => 'Finance',
            self::ManageStaff, self::ManageRoles, self::ManageSystemSettings,
            self::ManageCustomerPortal, self::ViewActivityLogs => 'System',
        };
    }
}
