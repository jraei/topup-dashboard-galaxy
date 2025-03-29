
<script setup>
import { onMounted, ref } from 'vue';
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";

// Chart.js needs to be imported and initialized
const chartCanvas = ref(null);
let chart = null;

// Sample data - would be replaced with actual data from backend
const chartData = {
  labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
  datasets: [
    {
      label: 'Orders This Week',
      data: [65, 78, 52, 91, 43, 56, 61],
      backgroundColor: [
        'rgba(155, 135, 245, 0.8)',
        'rgba(51, 195, 240, 0.8)',
        'rgba(155, 135, 245, 0.8)',
        'rgba(51, 195, 240, 0.8)',
        'rgba(155, 135, 245, 0.8)',
        'rgba(51, 195, 240, 0.8)',
        'rgba(155, 135, 245, 0.8)'
      ],
      borderColor: [
        'rgba(155, 135, 245, 1)',
        'rgba(51, 195, 240, 1)',
        'rgba(155, 135, 245, 1)',
        'rgba(51, 195, 240, 1)',
        'rgba(155, 135, 245, 1)',
        'rgba(51, 195, 240, 1)',
        'rgba(155, 135, 245, 1)'
      ],
      borderWidth: 1,
      borderRadius: 4,
    }
  ]
};

// Create and initialize the chart
onMounted(() => {
  if (typeof window !== 'undefined') {
    import('chart.js').then((ChartModule) => {
      const { Chart, registerables } = ChartModule.default;
      Chart.register(...registerables);

      // Destroy existing chart if it exists
      if (chart) {
        chart.destroy();
      }

      // Initialize new chart
      if (chartCanvas.value) {
        chart = new Chart(chartCanvas.value.getContext('2d'), {
          type: 'bar',
          data: chartData,
          options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
              legend: {
                display: false
              },
              tooltip: {
                backgroundColor: 'rgba(37, 44, 62, 0.9)',
                titleColor: '#fff',
                bodyColor: '#fff',
                borderColor: 'rgba(155, 135, 245, 0.3)',
                borderWidth: 1,
                bodyFont: {
                  family: 'Figtree',
                },
                titleFont: {
                  family: 'Figtree',
                  weight: 'bold'
                }
              }
            },
            scales: {
              x: {
                grid: {
                  display: false,
                },
                ticks: {
                  color: '#a1a1aa',
                  font: {
                    family: 'Figtree',
                  }
                }
              },
              y: {
                beginAtZero: true,
                grid: {
                  color: 'rgba(255, 255, 255, 0.05)',
                },
                ticks: {
                  color: '#a1a1aa',
                  font: {
                    family: 'Figtree',
                  }
                }
              }
            }
          }
        });
      }
    });
  }
});
</script>

<template>
  <Card class="bg-gradient-to-br from-dark-card to-dark-lighter border-gray-700 hover:shadow-card-glow transition-all h-full">
    <CardHeader class="pb-2">
      <CardTitle class="text-lg font-medium text-white">Weekly Orders</CardTitle>
    </CardHeader>
    <CardContent>
      <div class="h-[300px] w-full">
        <canvas ref="chartCanvas" height="300"></canvas>
      </div>
    </CardContent>
  </Card>
</template>
