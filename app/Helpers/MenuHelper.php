<?php

namespace App\Helpers;

class MenuHelper
{
    public static function getMenus(): array
    {
        return [
            // ===== MAIN =====
            [
                'type'       => 'section',
                'label'      => 'Main',
            ],
            [
                'type'       => 'single',
                'title'      => 'Dashboard',
                'route'      => 'dashboard',
                'permission' => null, // null = always visible
                'icon'       => 'dashboard',
            ],

            // ===== USER MANAGEMENT =====
            [
                'type'       => 'section',
                'label'      => 'User Management',
            ],
            [
                'type'       => 'group',
                'title'      => 'User Management',
                'icon'       => 'users',
                'permission' => null,
                'match'      => ['users.*', 'role.*', 'permission.*'],
                'children'   => [
                    [
                        'title'      => 'Users',
                        'route'      => 'users.index',
                        'match'      => 'users.*',
                        'permission' => 'user-list',
                    ],
                    [
                        'title'      => 'Roles',
                        'route'      => 'role.index',
                        'match'      => 'role.*',
                        'permission' => 'role-list',
                    ],
                    [
                        'title'      => 'Permissions',
                        'route'      => 'permission.index',
                        'match'      => 'permission.*',
                        'permission' => 'permission-list',
                    ],
                ],
            ],

            // ===== VENUE MANAGEMENT =====
            [
                'type'       => 'section',
                'label'      => 'Venue',
            ],
            [
                'type'       => 'group',
                'title'      => 'Venue Management',
                'icon'       => 'location',
                'permission' => null,
                'match'      => ['venue.*', 'location.*'],
                'children'   => [
                    [
                        'title'      => 'Locations',
                        'route'      => 'location.index',
                        'match'      => 'location.*',
                        'permission' => 'location-list',
                    ],
                    [
                        'title'      => 'Venues',
                        'route'      => 'venue.index',
                        'match'      => 'venue.*',
                        'permission' => 'venue-list',
                    ],
                ],
            ],

            // ===== TOKENS =====
            [
                'type'       => 'section',
                'label'      => 'Tokens',
            ],
            [
                'type'       => 'single',
                'title'      => 'Tokens',
                'route'      => 'tokens.index',
                'match'      => 'tokens.*',
                'permission' => 'token-list',
                'icon'       => 'token',
            ],
            [
                'type'       => 'single',
                'title'      => 'Working Ladies',
                'route'      => 'working-lady.index',
                'match'      => 'working-lady.*',
                'permission' => 'working-lady-list',
                'icon'       => 'lady',
            ],
            [
                'type'       => 'single',
                'title'      => 'Site Admin',
                'route'      => 'tokens.print',
                'match'      => 'tokens.print',
                'permission' => 'site-admin',
                'icon'       => 'admin',
            ],

            // ===== FACIAL RECOGNITION =====
            [
                'type'       => 'section',
                'label'      => 'Facial Recognition',
            ],
            [
                'type'       => 'group',
                'title'      => 'Facial Recognition',
                'icon'       => 'face',
                'permission' => null,
                'match'      => ['facial-recognition.*'],
                'children'   => [
                    [
                        'title'      => 'Users',
                        'route'      => 'facial-recognition.users',
                        'match'      => 'facial-recognition.users',
                        'permission' => 'facial-recognition-list',
                    ],
                    [
                        'title'      => 'Manual Mappings',
                        'route'      => 'facial-recognition.manual-mappings',
                        'match'      => 'facial-recognition.manual-mappings',
                        'permission' => 'facial-recognition-mapping',
                    ],
                ],
            ],
        ];
    }
}
