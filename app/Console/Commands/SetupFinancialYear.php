<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SetupFinancialYear extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fy:setup {--years=5 : Number of years to create in advance}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup financial years for long-term projects';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $years = (int) $this->option('years');
        
        $this->info("Setting up Financial Years for next $years years...");
        
        // Create FYs for current + next years
        $currentYear = now()->year;
        $createdFYs = \App\Services\FinancialYearManager::createFYRange($currentYear, $currentYear + $years);
        
        // Activate current FY
        $activeFY = \App\Services\FinancialYearManager::activateCurrentFY();
        
        $this->info("✅ Created " . count($createdFYs) . " financial years");
        $this->info("✅ Activated FY: " . $activeFY->name);
        
        // Show created FYs
        $tableData = [];
        foreach ($createdFYs as $fy) {
            $tableData[] = [
                $fy->name,
                $fy->start_date->format('Y-m-d'),
                $fy->end_date->format('Y-m-d'),
                $fy->id === $activeFY->id ? 'ACTIVE' : 'Future'
            ];
        }
        
        $this->table(['Name', 'Start Date', 'End Date', 'Status'], $tableData);
        
        $this->info("\n💡 Pro Tip: Set up a cron job to run this command every April 1st:");
        $this->line("0 0 1 4 * php artisan fy:setup --years=3");
        
        return 0;
    }
}
