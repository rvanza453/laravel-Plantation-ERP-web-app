<?php

return [
    'modules' => [
        'ispo' => [
            'label' => 'HR Modul',
            'roles' => [
                'HR Admin',
                'HR ISPO Officer',
                'HR ISPO Auditor',
                'HR Data Officer',
                'HR Manager',
                'HR Viewer',
                'ISPO Admin',
                'ISPO Auditor',
            ],
        ],
        'sas' => [
            'label' => 'Service Agreement System',
            'roles' => [
                'Admin',
                'Staff',
                'Approver',
                'Legal',
                'QC',
            ],
        ],
        'qc' => [
            'label' => 'QC Complaint System',
            'roles' => [
                'QC Admin',
                'QC Officer',
                'QC Approver',
            ],
        ],
        'pr' => [
            'label' => 'Purchase Request System',
            'roles' => [
                'Admin',
                'Staff',
                'Approver',
                'Purchasing',
                'Warehouse',
                'Finance',
            ],
        ],
        'systemsupport' => [
            'label' => 'System Support',
            'roles' => [
                'Admin IT',
                'Helpdesk',
                'User',
            ],
        ],
        'lab' => [
            'label' => 'Lab System',
            'roles' => [
                'Lab Admin',
                'Lab Sampler',
                'Lab Analyst',
                'Lab Supervisor',
                'Lab Viewer',
            ],
        ],
    ],
];
