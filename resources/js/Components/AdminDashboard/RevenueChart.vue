
<script setup>
import { onMounted, ref } from 'vue';
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";

// Chart.js needs to be imported and initialized
const chartCanvas = ref(null);
let chart = null;

// Sample data - would be replaced with actual data from backend
const chartData = {
  labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
  datasets: [
    {
      label: 'Revenue',
      data: [1200, 1900, 3000, 5000, 2000, 3000, 7000, 3500, 6500, 4500, 8000, 9500],
      borderColor: '#9b87f5',
      backgroundColor: 'rgba(155, 135, 245, 0.1)',
      tension: 0.4,
      fill: true,
    },
    {
      label: 'Orders',
      data: [800, 1200, 1800, 3200, 1500, 1900, 3800, 2200, 3900, 2800, 4200, 5100],
      borderColor: '#33C3F0',
      backgroundColor: 'rgba(51, 195, 240, 0.1)',
      tension: 0.4,
      fill: true,
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
          type: 'line',
          data: chartData,
          options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
              legend: {
                position: 'top',
                labels: {
                  color: '#fff',
                  font: {
                    family: 'Figtree',
                  }
                }
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
                  color: 'rgba(255, 255, 255, 0.05)',
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
                  },
                  callback: function(value) {
                    return '$' + value.toLocaleString();
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
      <CardTitle class="text-lg font-medium text-white">Revenue Overview</CardTitle>
    </CardHeader>
    <CardContent>
      <div class="h-[300px] w-full">
        <canvas ref="chartCanvas" height="300"></canvas>
      </div>
    </CardContent>
  </Card>
</template>
