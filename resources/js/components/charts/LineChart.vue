<template>
  <div>
    <canvas ref="chartRef"></canvas>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted, watch } from 'vue';
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Legend,
  Filler,
  type ChartOptions,
  type ChartData
} from 'chart.js';

ChartJS.register(
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Legend,
  Filler
);

interface Props {
  data: ChartData<'line'>;
  options?: ChartOptions<'line'>;
  height?: number;
}

const props = withDefaults(defineProps<Props>(), {
  height: 300
});

const chartRef = ref<HTMLCanvasElement>();
let chart: ChartJS<'line'> | null = null;

const defaultOptions: ChartOptions<'line'> = {
  responsive: true,
  maintainAspectRatio: false,
  scales: {
    y: {
      beginAtZero: true,
      grid: {
        color: '#f3f4f6'
      }
    },
    x: {
      grid: {
        color: '#f3f4f6'
      }
    }
  },
  plugins: {
    legend: {
      position: 'top',
    },
    tooltip: {
      mode: 'index',
      intersect: false,
    }
  },
  interaction: {
    mode: 'nearest',
    axis: 'x',
    intersect: false
  }
};

const createChart = () => {
  if (chartRef.value) {
    chart = new ChartJS(chartRef.value, {
      type: 'line',
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