<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ExpireJobVacancies extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jobs:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically close job vacancies that have passed their duration.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $expiredJobs = \App\Models\JobVacancy::where('expires_at', '<=', now())
            ->where('job_status', '!=', 'closed')
            ->update(['job_status' => 'closed']);

        $this->info("Closed {$expiredJobs} expired job vacancies.");
    }
}
