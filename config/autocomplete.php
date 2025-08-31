<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Autocomplete Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration options for autocomplete functionality
    | across the application.
    |
    */

    'default_limit' => env('AUTOCOMPLETE_DEFAULT_LIMIT', 10),
    
    'min_characters' => env('AUTOCOMPLETE_MIN_CHARS', 1),
    
    'debounce_delay' => env('AUTOCOMPLETE_DEBOUNCE', 300),
    
    'cache_duration' => env('AUTOCOMPLETE_CACHE_DURATION', 300), // 5 minutes
    
    /*
    |--------------------------------------------------------------------------
    | Autocomplete Sources Configuration
    |--------------------------------------------------------------------------
    |
    | Define the data sources and their configurations for different
    | autocomplete fields.
    |
    */
    
    'sources' => [
        'contact_methods' => [
            'model' => \App\Models\ContactMethod::class,
            'search_fields' => ['name'],
            'display_field' => 'name',
            'value_field' => 'id',
            'limit' => 10,
            'cache_key' => 'autocomplete_contact_methods',
        ],
        
        'block_units' => [
            'model' => \App\Models\BlockUnit::class,
            'search_fields' => ['unit_code', 'unit_name', 'owners_name'],
            'display_field' => 'unit_code',
            'value_field' => 'id',
            'limit' => 10,
            'cache_key' => 'autocomplete_block_units',
            'additional_fields' => ['unit_name', 'owners_name'],
            'display_format' => '{unit_code} - {unit_name}',
        ],
    ],
];
