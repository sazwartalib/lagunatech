<?php

namespace App\Enums;

use App\Enums\Concerns\HasLabel;

/**
 * Canonical staff roles. Stored as Spatie roles under the `web` guard;
 * this enum is the single source of truth for seeding and role checks.
 */
enum Role: string
{
    use HasLabel;

    case SuperAdmin = 'super_admin';
    case Admin = 'admin';
    case ProjectManager = 'project_manager';
    case Developer = 'developer';
    case Designer = 'designer';
    case Finance = 'finance';
    case Support = 'support';

    /**
     * Default permission set granted to the role when seeding.
     *
     * @return list<string>
     */
    public function permissions(): array
    {
        return match ($this) {
            self::SuperAdmin, self::Admin => Permission::values(),
            self::ProjectManager => [
                Permission::ViewCustomers->value,
                Permission::CreateCustomers->value,
                Permission::EditCustomers->value,
                Permission::ViewProjects->value,
                Permission::CreateProjects->value,
                Permission::EditProjects->value,
                Permission::ManageTasks->value,
                Permission::ManageChangeRequests->value,
                Permission::ManageBugs->value,
                Permission::ManageMeetings->value,
                Permission::LogCommunications->value,
                Permission::ManageSupport->value,
                Permission::ManageMaintenance->value,
                Permission::ManageDocuments->value,
                Permission::ManageCustomerPortal->value,
                Permission::ManageQuotations->value,
                Permission::ManageInvoices->value,
                Permission::ViewFinancialReports->value,
                Permission::ViewReports->value,
                Permission::ViewActivityLogs->value,
            ],
            self::Developer, self::Designer => [
                Permission::ViewCustomers->value,
                Permission::ViewProjects->value,
                Permission::ManageTasks->value,
                Permission::ManageBugs->value,
                Permission::ManageMeetings->value,
                Permission::LogCommunications->value,
                Permission::ManageDocuments->value,
            ],
            self::Finance => [
                Permission::ViewCustomers->value,
                Permission::ViewProjects->value,
                Permission::ManageChangeRequests->value,
                Permission::ManageMaintenance->value,
                Permission::ManageQuotations->value,
                Permission::ApproveQuotations->value,
                Permission::ManageInvoices->value,
                Permission::RecordPayments->value,
                Permission::ViewFinancialReports->value,
                Permission::ViewReports->value,
            ],
            self::Support => [
                Permission::ViewCustomers->value,
                Permission::ViewProjects->value,
                Permission::ManageTasks->value,
                Permission::ManageBugs->value,
                Permission::LogCommunications->value,
                Permission::ManageMeetings->value,
                Permission::ManageSupport->value,
                Permission::ManageDocuments->value,
            ],
        };
    }

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_map(fn (self $r): string => $r->value, self::cases());
    }
}
