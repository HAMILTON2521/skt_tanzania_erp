<?php

return [
    'brand' => [
        'name' => 'SKT Tanzania ERP',
        'tagline' => 'Admin Control Center',
    ],

    'navigation' => [
        [
            'section' => 'Overview',
            'items' => [
                [
                    'title' => 'Dashboard',
                    'slug' => 'dashboard',
                    'summary' => 'Monitor approvals, stock alerts, revenue, payroll and finance health from one place.',
                    'route' => 'admin.dashboard',
                    'initials' => 'DB',
                ],
            ],
        ],
        [
            'section' => 'Administration',
            'items' => [
                [
                    'title' => 'Users & Access',
                    'initials' => 'UA',
                    'children' => [
                        [
                            'title' => 'Users',
                            'slug' => 'users',
                            'summary' => 'Manage user accounts, activation and module ownership.',
                            'section' => 'Administration',
                            'route' => 'admin.settings.users.index',
                            'route_params' => [],
                        ],
                        [
                            'title' => 'Roles',
                            'slug' => 'roles',
                            'summary' => 'Control admin, finance, HR, inventory and sales role definitions.',
                            'section' => 'Administration',
                            'route' => 'admin.settings.roles.index',
                            'route_params' => [],
                        ],
                        [
                            'title' => 'Permissions',
                            'slug' => 'permissions',
                            'summary' => 'Fine tune action-level access for every module and workflow.',
                            'section' => 'Administration',
                            'route' => 'admin.settings.permissions.index',
                            'route_params' => [],
                        ],
                    ],
                ],
            ],
        ],
        [
            'section' => 'Operations',
            'items' => [
                [
                    'title' => 'Products & Inventory',
                    'initials' => 'PI',
                    'children' => [
                        ['title' => 'Categories', 'slug' => 'categories', 'summary' => 'Manage product grouping and catalog structure.', 'section' => 'Operations', 'route' => 'admin.inventory.categories.index', 'route_params' => []],
                        ['title' => 'Products', 'slug' => 'products', 'summary' => 'Track items, units, reorder points and product lifecycle.', 'section' => 'Operations', 'route' => 'admin.inventory.products.index', 'route_params' => []],
                        ['title' => 'Inventory', 'slug' => 'inventory', 'summary' => 'Review stock levels, warehouses, adjustments and movement history.', 'section' => 'Operations', 'route' => 'admin.inventory.stock.index', 'route_params' => []],
                    ],
                ],
                [
                    'title' => 'Suppliers',
                    'initials' => 'SU',
                    'children' => [
                        ['title' => 'Suppliers', 'slug' => 'suppliers', 'summary' => 'Maintain supplier profiles, contacts and supply performance.', 'section' => 'Operations', 'route' => 'admin.inventory.suppliers.index', 'route_params' => []],
                    ],
                ],
                [
                    'title' => 'Customers',
                    'initials' => 'CU',
                    'children' => [
                        ['title' => 'Customers', 'slug' => 'customers', 'summary' => 'Manage customer accounts, billing details and account status.', 'section' => 'Operations', 'route' => 'admin.sales.customers.index', 'route_params' => []],
                    ],
                ],
                [
                    'title' => 'Sales',
                    'initials' => 'SA',
                    'children' => [
                        ['title' => 'Quotations', 'slug' => 'quotations', 'summary' => 'Prepare quotations, negotiate terms and convert winning quotes.', 'section' => 'Operations', 'route' => 'admin.sales.quotations.index', 'route_params' => []],
                        ['title' => 'Sales Orders', 'slug' => 'sales-orders', 'summary' => 'Manage confirmed customer orders and fulfillment stages.', 'section' => 'Operations', 'route' => 'admin.sales.orders.index', 'route_params' => []],
                        ['title' => 'Invoices', 'slug' => 'sales-invoices', 'summary' => 'Review invoice issuance, taxes, due dates and collections.', 'section' => 'Operations', 'route' => 'admin.sales.invoices.index', 'route_params' => []],
                        ['title' => 'Receipts', 'slug' => 'receipts', 'summary' => 'Capture customer receipts and settlement confirmations.', 'section' => 'Operations', 'route' => 'admin.sales.receipts.index', 'route_params' => []],
                    ],
                ],
                [
                    'title' => 'Procurement',
                    'initials' => 'PR',
                    'children' => [
                        ['title' => 'Purchase Requests', 'slug' => 'purchase-requests', 'summary' => 'Raise internal demand and route approval workflows.', 'section' => 'Operations', 'route' => 'admin.procurement.purchase-requests.index', 'route_params' => []],
                        ['title' => 'Purchase Orders', 'slug' => 'purchase-orders', 'summary' => 'Issue supplier orders and track downstream fulfillment.', 'section' => 'Operations', 'route' => 'admin.procurement.purchase-orders.index', 'route_params' => []],
                        ['title' => 'Goods Receipts', 'slug' => 'goods-receipts', 'summary' => 'Confirm deliveries against purchase orders and receipt status.', 'section' => 'Operations', 'route' => 'admin.procurement.goods-receipts.index', 'route_params' => []],
                    ],
                ],
            ],
        ],
        [
            'section' => 'People & Finance',
            'items' => [
                [
                    'title' => 'HR',
                    'initials' => 'HR',
                    'children' => [
                        ['title' => 'Departments', 'slug' => 'departments', 'summary' => 'Manage department structure, managers and reporting groups.', 'section' => 'People & Finance', 'route' => 'admin.hr.departments.index', 'route_params' => []],
                        ['title' => 'Employees', 'slug' => 'employees', 'summary' => 'Maintain staff records, contracts and reporting lines.', 'section' => 'People & Finance', 'route' => 'admin.hr.employees.index', 'route_params' => []],
                        ['title' => 'Attendance', 'slug' => 'attendance', 'summary' => 'Review check-ins, absences and time coverage trends.', 'section' => 'People & Finance', 'route' => 'admin.hr.attendance.index', 'route_params' => []],
                        ['title' => 'Leave', 'slug' => 'leave', 'summary' => 'Track leave requests, approvals and balances.', 'section' => 'People & Finance', 'route' => 'admin.hr.leaves.index', 'route_params' => []],
                        ['title' => 'Payroll', 'slug' => 'payroll', 'summary' => 'Run payroll, PAYE, NSSF, WCF and pay-cycle checks.', 'section' => 'People & Finance', 'route' => 'admin.hr.payroll.index', 'route_params' => []],
                    ],
                ],
                [
                    'title' => 'Finance',
                    'initials' => 'FN',
                    'children' => [
                        ['title' => 'Chart of Accounts', 'slug' => 'chart-of-accounts', 'summary' => 'Define account structures and posting controls.', 'section' => 'People & Finance', 'route' => 'admin.finance.chart-of-accounts', 'route_params' => []],
                        ['title' => 'Journal Entries', 'slug' => 'journal-entries', 'summary' => 'Review journals, approval status and balancing checks.', 'section' => 'People & Finance', 'route' => 'admin.finance.journal-entries', 'route_params' => []],
                        ['title' => 'Bank Accounts', 'slug' => 'bank-accounts', 'summary' => 'Manage collection and settlement accounts used for finance payments.', 'section' => 'People & Finance', 'route' => 'admin.finance.bank-accounts.index', 'route_params' => []],
                        ['title' => 'Tax Rates', 'slug' => 'tax-rates', 'summary' => 'Maintain VAT and tax percentages applied to finance invoices.', 'section' => 'People & Finance', 'route' => 'admin.finance.tax-rates.index', 'route_params' => []],
                        ['title' => 'Finance Invoices', 'slug' => 'finance-invoices', 'summary' => 'Manage finance-side invoices, due dates and billing status.', 'section' => 'People & Finance', 'route' => 'admin.finance.invoices.index', 'route_params' => []],
                        ['title' => 'Payments', 'slug' => 'finance-payments', 'summary' => 'Record payment receipts, references and settlement state.', 'section' => 'People & Finance', 'route' => 'admin.finance.payments.index', 'route_params' => []],
                        ['title' => 'Expenses', 'slug' => 'finance-expenses', 'summary' => 'Track operating expenses, approvals and payout readiness.', 'section' => 'People & Finance', 'route' => 'admin.finance.expenses.index', 'route_params' => []],
                        ['title' => 'Financial Reports', 'slug' => 'financial-reports', 'summary' => 'Surface ledgers, trial balance and reporting outputs.', 'section' => 'People & Finance', 'route' => 'admin.finance.reports', 'route_params' => []],
                    ],
                ],
            ],
        ],
        [
            'section' => 'Insights',
            'items' => [
                [
                    'title' => 'Reports',
                    'initials' => 'RP',
                    'children' => [
                        ['title' => 'Reports & Dashboard', 'slug' => 'reports-dashboard', 'summary' => 'Connect cross-module KPIs, alerts and decision support widgets.', 'section' => 'Insights', 'route' => 'admin.reports.index', 'route_params' => []],
                    ],
                ],
            ],
        ],
        [
            'section' => 'System',
            'items' => [
                [
                    'title' => 'Settings',
                    'initials' => 'ST',
                    'children' => [
                        ['title' => 'Company', 'slug' => 'company-settings', 'summary' => 'Review organization identity, timezone and environment details.', 'section' => 'System', 'route' => 'admin.settings.company', 'route_params' => []],
                        ['title' => 'Backup', 'slug' => 'backup-settings', 'summary' => 'Inspect backup destinations, retention rules and health signals.', 'section' => 'System', 'route' => 'admin.settings.backup', 'route_params' => []],
                        ['title' => 'System Health', 'slug' => 'system-health', 'summary' => 'Surface runtime, storage, cache and integration status.', 'section' => 'System', 'route' => 'admin.settings.system', 'route_params' => []],
                    ],
                ],
                [
                    'title' => 'Support',
                    'initials' => 'SP',
                    'children' => [
                        ['title' => 'Notifications', 'slug' => 'notifications', 'summary' => 'Review unread alerts and mark operational updates as read.', 'section' => 'System', 'route' => 'admin.notifications.index', 'route_params' => []],
                        ['title' => 'Audit Logs', 'slug' => 'audit-logs', 'summary' => 'Review activity history across operational modules.', 'section' => 'System', 'route' => 'admin.audit-logs.index', 'route_params' => []],
                        ['title' => 'My Profile', 'slug' => 'my-profile', 'summary' => 'Update your administrator profile and account credentials.', 'section' => 'System', 'route' => 'admin.profile.edit', 'route_params' => []],
                    ],
                ],
            ],
        ],
    ],
];
