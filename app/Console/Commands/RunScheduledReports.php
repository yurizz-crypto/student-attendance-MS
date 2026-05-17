<?php

namespace App\Console\Commands;

use App\Livewire\Admin\Reports\Index;
use App\Mail\ScheduledReportMail;
use App\Models\ReportConfiguration;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class RunScheduledReports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:run-scheduled-reports';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Runs scheduled system reports and emails them to configured recipients';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dueReports = ReportConfiguration::whereNotNull('next_run_at')
            ->where('next_run_at', '<=', now())
            ->whereNotNull('email_to')
            ->get();

        $this->info("Found {$dueReports->count()} scheduled reports due.");

        foreach ($dueReports as $config) {
            $this->info("Processing report: {$config->name}");

            try {
                // Initialize the Livewire component to reuse its logic
                $component = new Index;
                $component->reportType = $config->type;
                $component->dateRange = $config->filters['dateRange'] ?? 'this_month';
                $component->customStartDate = $config->filters['customStartDate'] ?? null;
                $component->customEndDate = $config->filters['customEndDate'] ?? null;
                $component->filterRole = $config->filters['filterRole'] ?? '';
                $component->filterCategory = $config->filters['filterCategory'] ?? '';

                // Get the data using reflection or making the method public.
                // Since getReportData is private, we'll make it public in the component.
                $data = $component->getReportDataForCommand();

                $pdf = Pdf::loadView('exports.pdf.report', [
                    'type' => $config->type,
                    'data' => $data,
                    'date' => now()->format('F d, Y'),
                    'generatedBy' => 'System Scheduler',
                ]);

                $pdfContent = $pdf->output();

                Mail::to($config->email_to)->send(new ScheduledReportMail($config, $pdfContent));

                // Update timestamps
                $config->update([
                    'last_run_at' => now(),
                    'next_run_at' => $this->calculateNextRun($config->schedule_frequency),
                ]);

                $this->info("Successfully sent to {$config->email_to}");

            } catch (\Exception $e) {
                $this->error("Failed to process report {$config->name}: ".$e->getMessage());
            }
        }
    }

    private function calculateNextRun($frequency)
    {
        return match ($frequency) {
            'daily' => now()->addDay()->startOfDay()->addHours(8),
            'weekly' => now()->addWeek()->startOfWeek()->addHours(8),
            'monthly' => now()->addMonth()->startOfMonth()->addHours(8),
            default => null,
        };
    }
}
