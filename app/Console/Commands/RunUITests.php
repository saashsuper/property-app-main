<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RunUITests extends Command
{
    protected $signature = 'test:ui';
    protected $description = 'Run all UI validation tests';

    public function handle()
    {
        $this->info('🚀 Running PROMAN UI Test Suite');
        $this->line('================================');

        // Clear caches
        $this->info('📝 Clearing caches...');
        $this->call('view:clear');
        $this->call('config:clear');
        $this->call('route:clear');

        $this->line('');
        $this->info('🧪 Running UI Validation Tests...');
        $this->line('--------------------------------');

        $tests = [
            'Login Page' => 'test_login_page_contains_required_elements',
            'Dashboard Page' => 'test_dashboard_page_contains_required_elements',
            'Users Page' => 'test_users_page_contains_required_elements',
            'Block Inspections Page' => 'test_block_inspections_page_contains_required_elements',
            'Work Orders Page' => 'test_work_orders_page_contains_required_elements',
        ];

        $passed = 0;
        $total = count($tests);

        foreach ($tests as $name => $filter) {
            $this->line("Testing {$name}...");
            
            $exitCode = $this->call('test', [
                '--filter' => $filter
            ]);

            if ($exitCode === 0) {
                $passed++;
                $this->info("✅ {$name} - PASSED");
            } else {
                $this->error("❌ {$name} - FAILED");
            }
            $this->line('');
        }

        $this->line('================================');
        if ($passed === $total) {
            $this->info("✅ All UI Tests Passed! ({$passed}/{$total})");
        } else {
            $this->error("❌ Some Tests Failed ({$passed}/{$total})");
        }

        return $passed === $total ? 0 : 1;
    }
}
