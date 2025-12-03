<template>
  <div>
    <canvas ref="chartRef"></canvas>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted, watch } from 'vue';
import {
  Chart as ChartJS,
  ArcElement,
  Tooltip,
  Legend,
  type ChartOptions,
  type ChartData
} from 'chart.js';

ChartJS.register(ArcElement, Tooltip, Legend);

interface Props {
  data: ChartData<'doughnut'>;
  options?: ChartOptions<'doughnut'>;
  height?: number;
}

const props = withDefaults(defineProps<Props>(), {
  height: 300
});

const chartRef = ref<HTMLCanvasElement>();
let chart: ChartJS<'doughnut'> | null = null;

const defaultOptions: ChartOptions<'doughnut'> = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      position: 'bottom',
    },
    tooltip: {
      callbacks: {
        label: function(context) {
          const label = context.label || '';
          const value = context.parsed;
          const total = context.dataset.data.reduce((a, b) => a + b, 0);
          const percentage = ((value / total) * 100).toFixed(1);
          return `${label}: ${percentage}%`;
        }
      }
    }
  }
};

const createChart = () => {
  if (chartRef.value) {
    chart = new ChartJS(chartRef.value, {
      type: 'doughnut',
      data: props.data,
      options: { ...defaultOptions, ...props.options }
    });
  }
};

const destroyChart = () => {
  if (chart) {
    chart.destroy();
    chart = null;
  }
};

watch(() => props.data, (newData) => {
  if (chart) {
    chart.data = newData;
    chart.update();
  }
}, { deep: true });

onMounted(() => {
  createChart();
});

onUnmounted(() => {
  destroyChart();
});
</script>