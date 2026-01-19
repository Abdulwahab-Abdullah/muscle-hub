<template>
  <div class="dashboard-page page-custom">
    <div class="container py-4">
      <!-- Header -->
      <div class="text-center mb-4">
        <h1 class="fw-bold">{{ t("dashboard.title") }}</h1>
        <p class="text-muted">{{ t("dashboard.subtitle") }}</p>
      </div>

      <!-- Stats Loader -->
      <SkeletonLoader v-if="isLoadingStats" type="dashboard-stats" />

      <!-- Stats Grid -->
      <div v-else class="stats-grid">
        <div class="stat-card">
          <div class="stat-icon">🔥</div>
          <h3>{{ t("dashboard.stats.calories") }}</h3>
          <p class="stat-value">{{ calories }} kcal</p>
          <small class="text-muted">
            {{ t("dashboard.stats.totalCalories") }}
          </small>
        </div>

        <div class="stat-card">
          <div class="stat-icon">⚖️</div>
          <h3>{{ t("dashboard.stats.currentWeight") }}</h3>
          <p class="stat-value primary">{{ currentWeight }} kg</p>
          <small class="text-muted">
            {{ t("dashboard.stats.latestProgress") }}
          </small>
        </div>

        <div class="stat-card">
          <div class="stat-icon">🏆</div>
          <h3>{{ t("dashboard.stats.targetWeight") }}</h3>
          <p class="stat-value">{{ stats.target_weight || 0 }} kg</p>
          <small class="text-muted">
            {{ stats.remaining_weight?.toFixed(1) || 0 }}
            {{ t("dashboard.stats.remaining") }}
          </small>
        </div>

        <div class="stat-card">
          <div class="stat-icon">📊</div>
          <h3>{{ t("dashboard.stats.progress") }}</h3>
          <p class="stat-value success">{{ progressPercent }}%</p>
          <div class="progress-bar-mini">
            <div
              class="progress-fill"
              :style="{ width: progressPercent + '%' }"
            ></div>
          </div>
        </div>
      </div>

      <!-- Chart Section -->
      <div class="chart-section mb-5">
        <SkeletonLoader v-if="isLoadingChart" type="chart" />
        <div v-else>
          <div class="section-header">
            <h4 class="section-title">
              <i class="fa-solid fa-chart-line"></i>
              {{ t("dashboard.chart.title") }}
            </h4>

            <div class="chart-legend">
              <span class="legend-item">
                <span class="legend-dot primary"></span>
                {{ t("dashboard.chart.weightLabel") }}
              </span>
            </div>
          </div>

          <div class="chart-container">
            <canvas ref="chartCanvas" width="400" height="400"></canvas>
          </div>
        </div>
      </div>

      <!-- Meals Loader -->
      <SkeletonLoader v-if="isLoadingMeals" type="table" :rows="5" />

      <!-- Meals Table -->
      <div v-else class="meals-table">
        <h2 class="section-title">
          <i class="fa-solid fa-utensils me-2"></i>
          {{ t("dashboard.meals.title") }}
        </h2>

        <table v-if="recentMeals.length > 0">
          <thead>
            <tr>
              <th>{{ t("dashboard.meals.type") }}</th>
              <th>{{ t("dashboard.meals.name") }}</th>
              <th>{{ t("dashboard.meals.calories") }}</th>
            </tr>
          </thead>

          <tbody>
            <tr v-for="meal in recentMeals" :key="meal.id">
              <td>
                <span
                  class="meal-type-badge"
                  :class="`type-${meal.type.toLowerCase()}`"
                >
                  {{ getMealIcon(meal.type) }} {{ meal.type }}
                </span>
              </td>
              <td>{{ meal.name }}</td>
              <td class="calories-cell">{{ meal.calories }} kcal</td>
            </tr>
          </tbody>
        </table>

        <div v-else class="empty-meals">
          <p>{{ t("dashboard.meals.empty") }}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { ref, computed, onMounted, nextTick } from "vue";
import SkeletonLoader from "../components/SkeletonComponent.vue";
import api from "../lib/axios";
import { Chart, registerables } from "chart.js";
import { useI18n } from "vue-i18n";

Chart.register(...registerables);

interface Meal {
  id: number;
  name: string;
  type: string;
  calories: number;
  protein: number;
  carbs: number;
  fat: number;
  quantity: number;
}

const { t } = useI18n();

// Total Calories
const calories = ref(0);
// Loaders
const isLoadingStats = ref(true);
const isLoadingChart = ref(true);
const isLoadingMeals = ref(true);

// Data - نفس بيانات Progress
const stats = ref<any>({});
const startWeight = ref(0);
const currentWeight = ref(0);
const recentMeals = ref<Meal[]>([]);

// Chart refs
const chartCanvas = ref<HTMLCanvasElement | null>(null);
let chartInstance: Chart | null = null;

// Meal icons
const getMealIcon = (type: string) => {
  const icons: Record<string, string> = {
    Breakfast: "🍳",
    Lunch: "🍽️",
    Dinner: "🌙",
    Snack: "🍪",
  };
  return icons[type] || "🍴";
};

// Format Date
const formatDate = (date: string) => {
  if (!date) return "-";
  return new Date(date).toLocaleDateString("en-US", {
    month: "short",
    day: "numeric",
    year: "numeric",
  });
};

// Computed - Progress Percentage
const progressPercent = computed(() => {
  const target = Number(stats.value.target_weight) || startWeight.value;
  if (!startWeight.value || startWeight.value === target) return 0;

  const progress =
    ((currentWeight.value - startWeight.value) / (target - startWeight.value)) *
    100;
  return Math.min(Math.max(Math.round(progress), 0), 100);
});

// Fetch Stats - نفس API من Progress
const fetchStats = async () => {
  isLoadingStats.value = true;
  try {
    const response = await api.get("/goals");
    calories.value = Math.round(response.data.goal?.calories) || 0;

    // نفس الـ API من صفحة Progress
    const res = await api.get("/progress/stats");
    stats.value = res.data.data || {};

    // جلب الوزن البداية من Goals
    const res2 = await api.get("/goals");
    startWeight.value = Number(res2.data.goal?.start_weight) || 0;

    // الوزن الحالي من آخر Progress
    currentWeight.value = stats.value.recent_progress?.length
      ? Number(
          stats.value.recent_progress[stats.value.recent_progress.length - 1]
            .weight,
        ) || startWeight.value
      : startWeight.value;
  } catch (err) {
    console.error(err);
  } finally {
    isLoadingStats.value = false;
  }
};

// Render Chart - نفس الشارت من Progress
const renderChart = () => {
  if (!chartCanvas.value) {
    console.warn("Canvas not found!");
    return;
  }

  const recentData = stats.value.recent_progress || [];
  if (startWeight.value === 0 && recentData.length === 0) {
    console.warn("No data to render");
    return;
  }

  if (chartInstance) {
    chartInstance.destroy();
    chartInstance = null;
  }

  const ctx = chartCanvas.value.getContext("2d");
  if (!ctx) {
    console.warn("Cannot get canvas context");
    return;
  }

  const weightData = [
    startWeight.value,
    ...recentData.map((p: any) => Number(p.weight)),
  ];
  const labels = ["Start", ...recentData.map((p: any) => formatDate(p.date))];

  console.log("Rendering chart with data:", weightData, labels);

  const gradient = ctx.createLinearGradient(0, 0, 0, 400);
  gradient.addColorStop(0, "rgba(56, 182, 255, 0.3)");
  gradient.addColorStop(1, "rgba(56, 182, 255, 0.0)");

  chartInstance = new Chart(ctx, {
    type: "line",
    data: {
      labels,
      datasets: [
        {
          label: "Weight (kg)",
          data: weightData,
          borderColor: "#38b6ff",
          backgroundColor: gradient,
          tension: 0.3,
          fill: true,
          pointRadius: 5,
          pointBackgroundColor: "#38b6ff",
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: { legend: { display: false } },
      scales: { y: { beginAtZero: false } },
    },
  });

  console.log("Chart rendered successfully");
};

// Fetch Meals
const fetchMeals = async () => {
  isLoadingMeals.value = true;
  try {
    const res = await api.get("/meals/today");
    const meals = res.data.meals || [];
    recentMeals.value = meals.map((meal: any) => ({
      id: meal.id,
      name: meal.name,
      type: meal.type,
      calories: Math.round(meal.calories * (meal.quantity || 1)),
      protein: meal.protein,
      carbs: meal.carbs,
      fat: meal.fat,
      quantity: meal.quantity,
    }));
  } catch (err) {
    console.error(err);
    recentMeals.value = [];
  } finally {
    isLoadingMeals.value = false;
  }
};

// Mounted
onMounted(async () => {
  console.log("Dashboard mounted");

  // جلب البيانات أولاً
  await fetchStats();
  await fetchMeals();

  console.log("Stats fetched, waiting for canvas...");

  // انتظار للتأكد من ظهور الـ canvas
  isLoadingChart.value = false;

  await nextTick();

  // محاولة رسم الشارت عدة مرات إذا فشل
  let attempts = 0;
  const maxAttempts = 5;

  const tryRenderChart = () => {
    if (chartCanvas.value) {
      console.log("Canvas found, rendering chart");
      renderChart();
    } else if (attempts < maxAttempts) {
      attempts++;
      console.log(`Canvas not ready, attempt ${attempts}/${maxAttempts}`);
      setTimeout(tryRenderChart, 100);
    } else {
      console.error("Failed to render chart after multiple attempts");
    }
  };

  tryRenderChart();
});
</script>
