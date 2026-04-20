<?php

return [
    'modules' => [
        'ispo' => [
            'label' => 'System ISPO',
            'roles' => [
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
    ],
];
