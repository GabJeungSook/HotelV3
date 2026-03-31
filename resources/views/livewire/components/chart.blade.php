<div>
    <div class="rounded-xl bg-white p-5 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-sm font-semibold text-gray-800">Guest Statistics</h2>
                <p class="text-xs text-gray-400">Check-in trends over time</p>
            </div>
            <select wire:model.live="chartFilter" class="rounded-lg border-gray-200 bg-white text-xs font-medium text-gray-600 shadow-sm focus:ring-[#009EF5] focus:border-[#009EF5] py-1.5 pl-3 pr-8">
                <option value="year">This Year</option>
                <option value="month">This Month</option>
                <option value="week">This Week</option>
            </select>
        </div>
        <div id="chartContainer" class="relative h-[280px] w-full">
            <canvas id="guestChart"></canvas>
        </div>
    </div>

    <script>
    document.addEventListener('livewire:initialized', function () {
        let guestChart;

        function renderChart(labels, data) {
            const oldCanvas = document.getElementById('guestChart');
            const parent = oldCanvas.parentNode;
            oldCanvas.remove();

            const newCanvas = document.createElement('canvas');
            newCanvas.id = 'guestChart';
            parent.appendChild(newCanvas);

            const ctx = newCanvas.getContext('2d');

            const gradient = ctx.createLinearGradient(0, 0, 0, 280);
            gradient.addColorStop(0, 'rgba(0, 158, 245, 0.8)');
            gradient.addColorStop(1, 'rgba(0, 158, 245, 0.3)');

            guestChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Guests',
                        data: data,
                        backgroundColor: gradient,
                        borderRadius: 6,
                        borderSkipped: false,
                        barPercentage: 0.6,
                        categoryPercentage: 0.7,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(0,0,0,0.04)', drawBorder: false },
                            ticks: { font: { size: 11 }, color: '#9ca3af', padding: 8 },
                            border: { display: false }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { font: { size: 11 }, color: '#9ca3af', padding: 4 },
                            border: { display: false }
                        }
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#1f2937',
                            titleFont: { size: 12 },
                            bodyFont: { size: 12 },
                            padding: 10,
                            cornerRadius: 8,
                            displayColors: false,
                        }
                    }
                }
            });
        }

        Livewire.on('chartUpdated', (params) => {
            renderChart(params[0], params[1]);
        });

        renderChart(
            @json(array_keys($guests_by_month)),
            @json(array_values($guests_by_month))
        );
    });
    </script>
</div>
