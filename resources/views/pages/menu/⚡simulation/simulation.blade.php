<div class="flex flex-col gap-6">
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="lg" class="font-semibold">
                Simulasi Robust Balancing
            </flux:heading>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 font-medium">
                Analisis Variabilitas & Robust Line Balancing ·
                {{ $target }} pcs/hari · {{ $operators }} Operator
            </p>
        </div>

        <div class="flex gap-2">
            Fungsi Objektif Z:
            <flux:badge size="sm" rounded icon="exclamation-triangle" variant="micro" color="amber">
                {{-- {{ $z_score_used }} --}}
            </flux:badge>
        </div>
    </div>


    {{-- MAIN GRID --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">

        {{-- CHART --}}
        <flux:card class="bg-white dark:bg-neutral-900 shadow-sm xl:col-span-3 overflow-hidden">

            <flux:heading size="md" class="font-semibold">
                Perbandingan Optimasi
                <flux:subheading class="text-xs text-neutral-500">
                    Sebelum vs Sesudah Robust Balancing
                </flux:subheading>
            </flux:heading>

            <div wire:ignore class="mt-4 border-t flex justify-center items-center w-full">
                <div id="comparisonChart" class="h-70 w-full"></div>
            </div>

        </flux:card>


        {{-- METRICS --}}
        <flux:card class="bg-white dark:bg-neutral-900 shadow-sm">

            <flux:heading size="md" class="mb-4 font-semibold">
                Perbandingan Metrics
                <flux:subheading class="flex items-center text-xs text-neutral-500">
                        Sebelum vs Sesudah Robust Balancing
                </flux:subheading>
            </flux:heading>

            <div class="border-t pt-2"></div>
            <flux:table>
                <flux:table.rows>

                    @foreach($metrics as $metric)

                        @php
                            $label = $metric['label'];
                            $before = (float) preg_replace('/[^0-9.]/', '', $metric['before']);
                            $after = (float) preg_replace('/[^0-9.]/', '', $metric['after']);

                            $improved = false;

                            /* KPI Direction Rules */
                            if (in_array($label, ['Line Efficiency', 'Output / Hari'])) {
                                $improved = $after > $before;
                            } else {
                                $improved = $after < $before;
                            }

                            $status = $improved ? 'Improved' : 'Decreased';
                            $color = $improved ? 'text-green-500' : 'text-red-500';
                            $icon = $improved ? 'up' : 'down';
                        @endphp

                        <flux:table.row class="hover:bg-slate-50 dark:hover:bg-neutral-800 transition">
                            <flux:table.cell class="py-3">
                                <div class="flex flex-col gap-1">
                                    <span class="text-xs uppercase font-semibold">
                                        {{ $metric['label'] }}
                                    </span>
                                    <span class="line-through text-red-400 text-sm">
                                        {{ $metric['before'] }}
                                    </span>
                                </div>
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:icon.arrow-right variant="micro" />
                            </flux:table.cell>
                            <flux:table.cell class="py-3">
                                <div class="flex flex-col items-center">
                                    <span class="font-semibold text-lg">
                                        {{ $metric['after'] }}
                                    </span>
                                    <span class="text-xs flex items-center gap-1 {{ $color }}">
                                        @if($icon === 'up')
                                            <flux:icon.arrow-up variant="micro" />
                                        @else
                                            <flux:icon.arrow-down variant="micro" />
                                        @endif
                                        {{ $status }}
                                    </span>
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        </flux:card>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {

        const taktTime = @json($taktTime);
        const stations = @json($stations);
        const comparisonBefore = @json($beforeData);
        const comparisonAfter = @json($afterData);

        const comparisonOptions = {

            chart: {
                type: 'area',
                height: 450,
                animations: { enabled: true, easing: 'easeinout', speed: 800 }
            },

            series: [
                { name: 'Sebelum', data: comparisonBefore },
                { name: 'Sesudah', data: comparisonAfter }
            ],

            colors: ['#94a3b8', '#16a34a'],

            stroke: {
                curve: 'smooth',
                width: 3
            },

            fill: {
                type: 'gradient',
                gradient: {
                    shade: 'light',
                    type: 'vertical',
                    shadeIntensity: 0.5,
                    gradientToColors: ['#cbd5e1', '#bbf7d0'],
                    opacityFrom: 0.4,
                    opacityTo: 0.1,
                    stops: [0, 100]
                }
            },

            markers: {
                size: 6,
                strokeColors: '#fff',
                strokeWidth: 2,
                hover: { size: 8 }
            },

            xaxis: {
                categories: stations,
                labels: {
                    fontWeight: 600,
                    fontSize: '12px',
                    rotate: 45
                }
            },

            yaxis: {
                min: 0,
                labels: {
                    formatter: val => val + ' s'
                }
            },

            tooltip: {
                shared: true,
                intersect: false,
                theme: 'dark'
            },

            grid: {
                borderColor: 'rgba(255,255,255,0.1)',
                strokeDashArray: 4,
                padding: { left: 20, right: 60 }
            },

            annotations: {
                yaxis: [
                    {
                        y: taktTime,
                        borderColor: '#ef4444',
                        borderWidth: 1,
                        strokeDashArray: 4,
                        label: {
                            text: 'Takt Time = ' + taktTime.toFixed(1) + 's',
                            offsetY: -2,
                            style: {
                                background: '#ef4444',
                                color: '#fff',
                                fontSize: '10px',
                                fontWeight: 500
                            }
                        }
                    }
                ]
            },

            legend: {
                position: 'bottom'
            }

        };

        new ApexCharts(
            document.querySelector("#comparisonChart"),
            comparisonOptions
        ).render();

    });
</script>