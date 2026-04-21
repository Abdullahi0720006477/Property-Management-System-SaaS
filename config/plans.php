<?php
define('SUBSCRIPTION_PLANS', [
    'trial' => [
        'name' => 'Free Trial',
        'price_monthly' => 0,
        'price_yearly' => 0,
        'currency' => 'KES',
        'duration_days' => 14,
        'max_properties' => 2,
        'max_units' => 10,
        'max_users' => 2,
        'features' => ['properties', 'units', 'tenants', 'leases', 'payments', 'basic_reports'],
        'badge_color' => '#94A3B8'
    ],
    'starter' => [
        'name' => 'Starter',
        'price_monthly' => 2500,
        'price_yearly' => 25000,
        'currency' => 'KES',
        'max_properties' => 10,
        'max_units' => 50,
        'max_users' => 5,
        'features' => ['properties', 'units', 'tenants', 'leases', 'payments', 'maintenance', 'expenses', 'basic_reports', 'email_notifications'],
        'badge_color' => '#3B82F6'
    ],
    'professional' => [
        'name' => 'Professional',
        'price_monthly' => 7500,
        'price_yearly' => 75000,
        'currency' => 'KES',
        'max_properties' => 50,
        'max_units' => 500,
        'max_users' => 20,
        'features' => ['properties', 'units', 'tenants', 'leases', 'payments', 'maintenance', 'expenses', 'advanced_reports', 'email_notifications', 'document_storage', 'api_access', 'priority_support'],
        'badge_color' => '#F59E0B'
    ],
    'enterprise' => [
        'name' => 'Enterprise',
        'price_monthly' => 20000,
        'price_yearly' => 200000,
        'currency' => 'KES',
        'max_properties' => -1,
        'max_units' => -1,
        'max_users' => -1,
        'features' => ['all'],
        'badge_color' => '#8B5CF6'
    ]
]);
