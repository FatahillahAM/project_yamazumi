<?php

use App\Models\AnalysisJob;
use App\Models\SimulationResult;
use App\Models\SimulationStation;
use App\Models\SimulationAction;
use App\Models\StationResult;
use Livewire\Component;

new
    #[Title('Simulation')]
    class extends Component {

    public $taktTime;
    public $operators;
    public $target;

    // Chart
    public $stations = [];
    public $beforeData = [];
    public $afterData = [];

    // Metrics
    public $metrics = [];

    // Actions
    public $kaizen = [];
    public $transfers = [];

    public $simulation;

    public function mount()
    {

        $job = AnalysisJob::latest()->first();
        if (!$job)
            return;

        $this->taktTime = $job->takt_time;
        $this->target = $job->target_output ?? 0;
        $this->operators = $job->operators ?? 0;


        $this->simulation = SimulationResult::where('job_id', $job->id)->first();
        if (!$this->simulation)
            return;

        $stationsBefore = StationResult::where('job_id', $job->id)
            ->orderBy('station_order')
            ->get();


        $stationsAfter = SimulationStation::where('simulation_id', $this->simulation->id)
            ->orderBy('station_name')
            ->get();

        $this->stationRiskBefore = SimulationAction::where('simulation_id', $this->simulation->id)
            ->get()
            ->groupBy('station_from')
            ->map(fn($actions) => $actions->first()->risk_stasiun ?? 'Low Risk')
            ->toArray();

        $this->stationRiskAfter = SimulationStation::where('simulation_id', $this->simulation->id)
            ->orderBy('station_name')
            ->pluck('risk_after', 'station_name')
            ->toArray();
            
        $this->stations = $stationsBefore
            ->pluck('station_name')
            ->toArray();

        $this->beforeData = $stationsBefore
            ->pluck('mean_ct')
            ->map(fn($v) => round($v, 2))
            ->toArray();

        $this->afterData = $stationsAfter
            ->pluck('mean_ct_after')
            ->map(fn($v) => round($v, 2))
            ->toArray();

        $s = $this->simulation;

        $this->metrics = [

            [
                'label' => 'Line Efficiency',
                'before' => round($s->le_before, 2) . '%',
                'after' => round($s->le_after, 2) . '%',
            ],

            [
                'label' => 'Balance Delay',
                'before' => round($s->bd_before, 2) . '%',
                'after' => round($s->bd_after, 2) . '%',
            ],

            [
                'label' => 'Smoothness Index',
                'before' => round($s->si_before, 2),
                'after' => round($s->si_after, 2),
            ],

            [
                'label' => 'Neck Time (Mean)',
                'before' => round($s->neck_before, 1) . 's',
                'after' => round($s->neck_after, 1) . 's',
            ],

            [
                'label' => 'Neck Time Robust',
                'before' => round($s->neck_robust_before, 1) . 's',
                'after' => round($s->neck_robust_after, 1) . 's',
            ],
            // [
            //     'label' => 'Line Risk Scrore',
            //     'before' => round($this->stationRiskBefore, 2),
            //     'after' => round($this->stationRiskAfter, 2),
            // ],
            [
                'label' => 'Output / Hari',
                'before' => round($job->output_harian, 1) . 'pcs',
                'after' => round($job->line_output_hari, 1) . 'pcs',
            ],

        ];

        $actions = SimulationAction::where('simulation_id', $this->simulation->id)->get();
        $this->kaizen = $actions
            ->where('action_type', 'kaizen')
            ->map(fn($a) => [
                'station' => $a->station_from,
                'task' => $a->elemen_kerja,
                'action' => $a->metode,
                'saving' =>
                    '-' . round($a->saving, 1) . 's penghematan (' .
                    $a->durasi_before . 's → ' .
                    $a->durasi_after . 's)'
            ])
            ->values()
            ->toArray();

        $this->transfers = $actions
            ->where('action_type', 'redistribution')
            ->map(fn($a) => [
                'task' => $a->elemen_kerja,
                'time' => round($a->durasi_before, 1) . 's',
                'from' => $a->station_from,
                'to' => $a->station_to
            ])
            ->values()
            ->toArray();

    }
};