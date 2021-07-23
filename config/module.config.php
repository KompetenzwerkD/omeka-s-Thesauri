<?php
namespace Thesauri;

use Laminas\Router\Http\Segment;
use Laminas\Router\Http\Literal;

return [
    'view_manager' => [
        'template_path_stack' => [
            dirname(__DIR__) . '/view',
        ],
    ],
    'controllers' => [
        'invokables' => [
            Controller\IndexController::class => Controller\IndexController::class,
        ],
    ],
    'navigation' => [
        'AdminModule' => [
            [
                'label' => 'Thesauri',
                'route' => 'admin/thesauri',
                'resource' => Controller\IndexController::class,
            ],
        ],
    ],
    'router' => [
        'routes' => [
            'admin' => [
                'child_routes' => [
                    'thesauri' => [
                        'type' => \Laminas\Router\Http\Literal::class,
                        'options' => [
                            'route' => '/thesauri',
                            'defaults' => [
                                '__NAMESPACE__' => 'Thesauri\Controller',
                                'controller' => Controller\IndexController::class,
                                'action' => 'index',
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes' => [
                            'add' => [
                                'type' => Literal::class,
                                'options' => [
                                    'route' => '/add',
                                    'defaults' => [
                                        'action' => 'add',
                                    ],
                                ],
                            ],
                            'create-concept' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route' => '/create-concept/:id',
                                    'constraints' => [
                                        'id' => '\d+',
                                    ],
                                    'defaults' => [
                                        'action' => 'createConcept'
                                    ],
                                ],
                            ],
                            'delete-confirm' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route' => '/delete-confirm/:id',
                                    'defaults' => [
                                        'action' => 'deleteConfirm',                            
                                    ],
                                ],
                            ]     ,                       
                            'delete-thesaurus' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route' => '/delete-thesaurus/:id',
                                    'defaults' => [
                                        'action' => 'deleteThesaurus',                            
                                    ]
                                ]
                            ]                            
                        ],
                    ],
                ]
            ]
        ]
    ]
];