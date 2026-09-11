<?php

/*
|--------------------------------------------------------------------------
| Application Navigation
|--------------------------------------------------------------------------
|
| The sidebar is data-driven. Items whose `route` does not yet exist render
| as disabled "Soon" entries, so the full product map stays visible while
| the system is built out phase by phase.
|
*/

return [
    'groups' => [
        [
            'label' => 'Overview',
            'items' => [
                ['label' => 'Dashboard', 'icon' => '▦', 'route' => 'dashboard'],
            ],
        ],
        [
            'label' => 'Customers',
            'items' => [
                ['label' => 'Customers', 'icon' => '🏢', 'route' => 'customers.index', 'active' => 'customers.*'],
                ['label' => 'Communication', 'icon' => '💬', 'route' => 'communications.index'],
            ],
        ],
        [
            'label' => 'Projects',
            'items' => [
                ['label' => 'All Projects', 'icon' => '📁', 'route' => 'projects.index', 'active' => 'projects.index'],
                ['label' => 'My Projects', 'icon' => '📌', 'route' => 'projects.mine'],
                ['label' => 'Tasks', 'icon' => '✓', 'route' => 'tasks.index', 'active' => 'tasks.*'],
                ['label' => 'Meetings', 'icon' => '🤝', 'route' => 'meetings.index', 'active' => 'meetings.*'],
                ['label' => 'Drawings', 'icon' => '🎨', 'route' => 'drawings.index', 'active' => 'drawings.*'],
                ['label' => 'Calendar', 'icon' => '🗓', 'route' => 'calendar'],
            ],
        ],
        [
            'label' => 'Sales',
            'items' => [
                ['label' => 'Leads', 'icon' => '✦', 'route' => 'leads.index', 'active' => 'leads.*'],
                ['label' => 'Quotations', 'icon' => '📝', 'route' => 'quotations.index', 'active' => 'quotations.*'],
                ['label' => 'Change Requests', 'icon' => '⇄', 'route' => 'change-requests.index', 'active' => 'change-requests.*'],
            ],
        ],
        [
            'label' => 'Finance',
            'items' => [
                ['label' => 'Invoices', 'icon' => '🧾', 'route' => 'invoices.index', 'active' => 'invoices.index'],
                ['label' => 'Payments', 'icon' => '💳', 'route' => 'payments.index', 'active' => 'payments.*'],
                ['label' => 'Outstanding', 'icon' => '⏳', 'route' => 'invoices.outstanding'],
            ],
        ],
        [
            'label' => 'Support',
            'items' => [
                ['label' => 'Tickets', 'icon' => '🎫', 'route' => 'support.index', 'active' => 'support.*'],
                ['label' => 'Bugs', 'icon' => '🐞', 'route' => 'bugs.index', 'active' => 'bugs.*'],
                ['label' => 'Maintenance', 'icon' => '🛠', 'route' => 'maintenance.index', 'active' => 'maintenance.*'],
            ],
        ],
        [
            'label' => 'Workspace',
            'items' => [
                ['label' => 'Reports', 'icon' => '📊', 'route' => 'reports'],
                ['label' => 'Documents', 'icon' => '📄', 'route' => null],
            ],
        ],
        [
            'label' => 'System',
            'items' => [
                ['label' => 'Staff', 'icon' => '👥', 'route' => 'staff.index', 'active' => 'staff.*'],
                ['label' => 'Roles & Permissions', 'icon' => '🔑', 'route' => 'system.roles'],
                ['label' => 'Activity Logs', 'icon' => '📜', 'route' => 'system.activity'],
                ['label' => 'Settings', 'icon' => '⚙', 'route' => 'system.settings'],
            ],
        ],
    ],
];
