<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Standard Modules
    |--------------------------------------------------------------------------
    |
    | Define the modules here. The SyncPermissionsCommand will automatically
    | generate standard permissions for each module in the format:
    | "{module} list", "{module} view", "{module} store", "{module} update", "{module} delete".
    |
    */
    'modules' => [
        'diagnoses',
        'medical_experiences',
        'roles',
        'settings',
        'case_templates',
        'case_notes',
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Permissions
    |--------------------------------------------------------------------------
    |
    | Define any custom permissions here that do not fit the standard CRUD
    | pattern. These will be created as-is and synced to the super_admin role.
    |
    */
    'custom_permissions' => [
        // 'access admin panel',
    ],
];
