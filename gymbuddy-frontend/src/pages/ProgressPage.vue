<template>
  <div
    class="container py-4 progress-page page-custom"
    :class="{ rtl: $i18n.locale === 'ar' }"
  >
    <div class="text-center mb-5">
      <h1 class="fw-bold gradient-text">{{ $t("progress.title") }}</h1>
      <p class="text-muted">{{ $t("progress.subtitle") }}</p>
    </div>

    <div v-if="loading" class="skeleton-wrapper">
      <div class="skeleton-grid">
        <div v-for="i in 4" :key="i" class="skeleton-card"></div>
      </div>
      <div class="skeleton-chart"></div>
      <div class="skeleton-form"></div>
    </div>

    <div v-show="!loading">
      <div class="mb-5">
        <div class="row g-4">
          <div
            class="col-md-3 col-sm-6"
            v-for="(card, idx) in statsCards"
            :key="idx"
          >
            <div class="stat-card h-100" :class="`stat-${card.type}`">
              <div class="stat-icon">{{ card.icon }}</div>
              <h6 class="stat-title">{{ card.title }}</h6>
              <p class="stat-value" :class="card.valueClass">
                {{ card.value }}
              </p>
              <small class="stat-subtitle">{{ card.subtitle }}</small>
              <div v-if="card.showProgress" class="progress-bar-mini">
                <div
                  class="progress-fill"
                  :style="{ width: card.progressWidth + '%' }"
                ></div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="status-banner mb-4" :class="`status-${stats.status}`">
        <div class="status-content">
          <span class="status-icon">{{ getStatusIcon(stats.status) }}</span>
          <div class="status-text">
            <h5 class="text-black">
              {{ $t(`progress.status.${getStatusKey(stats.status)}`) }}
            </h5>
            <p>{{ $t(`progress.status.${getStatusKey(stats.status)}Msg`) }}</p>
          </div>
        </div>
      </div>

      <div class="estimates-section mb-5">
        <div class="row g-4">
          <div
            class="col-md-3 col-6"
            v-for="(card, idx) in estimateCards"
            :key="idx"
          >
            <div
              class="stat-card p-3 shadow-sm rounded-4 text-center border-0 h-100"
            >
              <div class="estimate-icon mb-2">
                <i :class="[card.icon, 'fs-4 text-primary']"></i>
              </div>
              <h6 class="estimate-title fw-semibold text-muted mb-1">
                {{ card.title }}
              </h6>
              <p class="estimate-value fw-bold fs-5 mb-0">{{ card.value }}</p>
              <small
                v-if="card.subtitle"
                class="estimate-subtitle text-secondary d-block mt-1"
              >
                {{ card.subtitle }}
              </small>
            </div>
          </div>
        </div>
      </div>

      <div class="chart-section full-width mb-5">
        <div class="section-header">
          <h4 class="section-title">
            <i class="fa-solid fa-chart-line"></i>
            {{ $t("dashboard.chart.title") }}
          </h4>
        </div>
        <div class="chart-container" style="position: relative; height: 40vh">
          <canvas ref="chartCanvas"></canvas>
        </div>
      </div>

      <div class="log-section mb-5">
        <h4 class="section-title">
          <i class="fa-solid fa-pen-to-square"></i>
          {{ $t("progress.form.title") }}
        </h4>
        <div class="log-form">
          <div class="row g-3 align-items-end">
            <div class="col-lg-2 col-md-4 col-6">
              <label>{{ $t("progress.form.date") }}</label>
              <input type="date" v-model="newLog.date" class="form-control" />
            </div>
            <div class="col-lg-2 col-md-4 col-6">
              <label>{{ $t("progress.form.weight") }}</label>
              <input
                type="number"
                step="0.1"
                v-model.number="newLog.weight"
                class="form-control"
              />
            </div>
            <div class="col-lg-2 col-md-4 col-6">
              <label>{{ $t("progress.form.muscle") }}</label>
              <input
                type="number"
                step="0.1"
                v-model.number="newLog.muscle_mass"
                class="form-control"
              />
            </div>
            <div class="col-lg-2 col-md-4 col-6">
              <label>{{ $t("progress.form.calories") }}</label>
              <input
                type="number"
                v-model.number="newLog.calories"
                class="form-control"
              />
            </div>
            <div class="col-lg-2 col-md-4 col-6">
              <label class="text-nowrap">{{
                $t("progress.form.workout")
              }}</label>
              <select v-model="newLog.workout_completed" class="form-control">
                <option :value="true">
                  ✅ {{ $i18n.locale === "ar" ? "نعم" : "Yes" }}
                </option>
                <option :value="false">
                  ❌ {{ $i18n.locale === "ar" ? "لا" : "No" }}
                </option>
              </select>
            </div>
            <div class="col-lg-2 col-md-12 col-12">
              <button
                class="btn btn-log w-100 mt-2 mt-lg-0 pt-3 d-flex justify-content-center align-items-center"
                @click="logProgress"
                :disabled="logging"
              >
                <i class="fa-solid fa-check me-2"></i>
                {{
                  logging
                    ? $t("progress.form.saving")
                    : $t("progress.form.submit")
                }}
              </button>
            </div>
          </div>
        </div>
      </div>

      <div class="history-section">
        <h4 class="section-title">
          <i class="fa-solid fa-clock-rotate-left"></i>
          {{ $t("progress.history.title") }}
        </h4>
        <div v-if="progressHistory.length" class="table-responsive">
          <table class="table table-hover modern-table">
            <thead>
              <tr>
                <th>{{ $t("progress.form.date") }}</th>
                <th>{{ $t("progress.form.weight") }}</th>
                <th>{{ $i18n.locale === "ar" ? "التغيير" : "Change" }}</th>
                <th>{{ $t("progress.form.muscle") }}</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(record, i) in progressHistory" :key="record.id">
                <td class="fw-semibold">{{ formatDate(record.date) }}</td>
                <td class="weight-cell">{{ record.weight }} kg</td>
                <td>
                  <span
                    :class="
                      getChangeClassSafe(
                        record.weight,
                        i === 0 ? weight : progressHistory[i - 1].weight,
                      )
                    "
                    class="change-badge"
                  >
                    {{
                      calculateChangeSafe(
                        record.weight,
                        i === 0 ? weight : progressHistory[i - 1].weight,
                      )
                    }}
                  </span>
                </td>
                <td>{{ record.muscle_mass || "-" }} kg</td>
                <td class="text-end">
                  <button class="btn-delete" @click="deleteRecord(record.id)">
                    <i class="fa-solid fa-trash"></i>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div v-else class="empty-state">
          <div class="empty-icon">📝</div>
          <p>{{ $t("progress.history.empty") }}</p>
          <small>{{ $t("progress.history.startToday") }}</small>
        </div>
      </div>
    </div>

    <transition name="toast">
      <div v-if="toastMessage" class="toast-notification" :class="toastType">
        <span>{{ toastMessage }}</span>
      </div>
    </transition>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, nextTick } from "vue";
import api from "../lib/axios";
import Chart from "chart.js/auto";
import Swal from "sweetalert2";
import { useI18n } from "vue-i18n";
const { t, locale } = useI18n();

// --- Refs ---
const stats = ref({});
const weight = ref(null);
const progressHistory = ref([]);
const loading = ref(true);
const loadingHistory = ref(true);
const logging = ref(false);
const toastMessage = ref("");
const toastType = ref("success");
const chartCanvas = ref(null);
let chartInstance = null;

const newLog = ref({
  date: new Date().toISOString().split("T")[0],
  weight: null,
  muscle_mass: null,
  calories: null,
  workout_completed: false,
});

// --- Fetch Stats ---
const fetchStats = async () => {
  loading.value = true;
  try {
    const res = await api.get("/progress/stats");
    stats.value = res.data.data || {};

    const res2 = await api.get("/goals");
    weight.value = Number(res2.data.goal?.start_weight) || 0;

    await nextTick();
    renderChart();
  } catch (err) {
    console.error(err);
    showToast("Failed to load stats ❌", "error");
  } finally {
    loading.value = false;
  }
};

// --- Fetch Weekly Chart Data ---
const weeklyChartData = ref([]);
const fetchWeeklyChart = async () => {
  try {
    const res = await api.get("/progress/weekly-chart");
    weeklyChartData.value = res.data.data || [];
    console.log("Weekly chart data:", weeklyChartData.value); // للتأكد
    await nextTick(); // تأكد أن الـ canvas موجود في الـ DOM
    renderWeeklyChart();
  } catch (err) {
    console.error(err);
    console.error("Chart data error:", err);
  }
};

const renderWeeklyChart = () => {
  if (!chartCanvas.value) {
    console.warn("Canvas not ready yet!");
    return;
  }

  if (chartInstance.value) chartInstance.value.destroy();

  const ctx = chartCanvas.value.getContext("2d");
  chartInstance.value = new Chart(ctx, {
    type: "line",
    data: {
      labels: weeklyChartData.value.map((d) => d.week),
      datasets: [
        {
          label: "Weight (kg)",
          data: weeklyChartData.value.map((d) => d.avg_weight),
          borderColor: "#4caf50",
          backgroundColor: "rgba(76, 175, 80, 0.2)",
          tension: 0.4,
          fill: true,
        },
      ],
    },
    options: {
      responsive: true,
      plugins: {
        legend: { display: true, position: "top" },
      },
    },
  });
};

// --- Fetch History ---
const fetchHistory = async () => {
  loadingHistory.value = true;
  try {
    const res = await api.get("/progress");
    // ترتيب من الأقدم للأحدث لضمان compare صح
    progressHistory.value = (res.data.data || []).sort(
      (a, b) => new Date(a.date) - new Date(b.date),
    );
  } catch (err) {
    console.error(err);
    showToast("Failed to load history ❌", "error");
  } finally {
    loadingHistory.value = false;
  }
};

// --- Log Progress ---
const logProgress = async () => {
  if (!newLog.value.weight) {
    showToast("Please enter your weight ❌", "error");
    return;
  }

  logging.value = true;
  try {
    await api.post("/progress", newLog.value);
    showToast("Progress logged successfully! ✅", "success");
    // إعادة ضبط الفورم
    newLog.value = {
      date: new Date().toISOString().split("T")[0],
      weight: null,
      muscle_mass: null,
      calories: null,
      workout_completed: false,
    };
    await fetchStats();
    await fetchHistory();
    renderChart();
  } catch (err) {
    console.error(err);
    showToast("Failed to log progress ❌", "error");
  } finally {
    logging.value = false;
  }
};

// --- Delete Record ---
const deleteRecord = async (id) => {
  const result = await Swal.fire({
    title: "Are you sure?",
    text: "You won't be able to revert this!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Yes, delete it!",
    cancelButtonText: "Cancel",
  });

  if (result.isConfirmed) {
    try {
      await api.delete(`/progress/${id}`);
      showToast("Record deleted ✅", "success");
      await fetchStats();
      await fetchHistory();
      renderChart();
    } catch (err) {
      console.error(err);
      showToast("Failed to delete ❌", "error");
    }
  }
};

// --- Chart ---
const renderChart = () => {
  if (!chartCanvas.value) return;

  const recentData = stats.value.recent_progress || [];
  const startWeight = Number(weight.value) || 0;

  // إذا ما فيه بيانات، لا ترسم
  if (startWeight === 0 && recentData.length === 0) return;

  if (chartInstance) chartInstance.destroy();

  const ctx = chartCanvas.value.getContext("2d");
  const weightData = [startWeight, ...recentData.map((p) => Number(p.weight))];
  const labels = ["Start", ...recentData.map((p) => formatDate(p.date))];

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
      maintainAspectRatio: false, // <--- مهم ليمتد بعرض container
      plugins: { legend: { display: false } },
      scales: { y: { beginAtZero: false } },
    },
  });
};

// --- Helpers ---
const formatDate = (date) =>
  new Date(date).toLocaleDateString("en-US", {
    month: "short",
    day: "numeric",
    year: "numeric",
  });

const safeNumber = (value) =>
  value !== undefined && value !== null ? Number(value) : 0;

const calculateChangeSafe = (current, previous) => {
  const safeCurrent = Number(current);
  const safePrevious =
    previous != null && !isNaN(previous)
      ? Number(previous)
      : Number(weight.value);
  const change = safeCurrent - safePrevious;
  return change > 0
    ? `+${change.toFixed(1)} kg`
    : change < 0
      ? `${change.toFixed(1)} kg`
      : "0 kg";
};

const getChangeClassSafe = (current, previous) => {
  const safeCurrent = Number(current);
  const safePrevious =
    previous != null && !isNaN(previous)
      ? Number(previous)
      : Number(weight.value);
  const change = safeCurrent - safePrevious;

  if (stats.value.goal_type === "loss")
    return change < 0
      ? "change-good"
      : change > 0
        ? "change-bad"
        : "change-neutral";
  if (stats.value.goal_type === "gain")
    return change > 0
      ? "change-good"
      : change < 0
        ? "change-bad"
        : "change-neutral";
  return "change-neutral";
};

const calculateMuscleChange = (current, previous) => {
  const safeCurrent = Number(current || 0);
  const safePrevious = Number(previous || 0);
  const change = safeCurrent - safePrevious;

  if (change > 0) return `+${change.toFixed(1)} kg`;
  if (change < 0) return `${change.toFixed(1)} kg`;
  return "0 kg";
};

const getMuscleChangeClass = (current, previous) => {
  const safeCurrent = Number(current || 0);
  const safePrevious = Number(previous || 0);
  const change = safeCurrent - safePrevious;

  if (change > 0) return "change-good";
  if (change < 0) return "change-bad";
  return "change-neutral";
};

const calculateChange = (current, previous) => {
  if (previous == null) return "-";
  const change = current - previous;
  return change > 0
    ? `+${change.toFixed(1)} kg`
    : change < 0
      ? `${change.toFixed(1)} kg`
      : "0 kg";
};

const getChangeClass = (
  current,
  previous,
  goalType,
  muscleCurrent = null,
  musclePrevious = null,
) => {
  if (previous == null) return "change-neutral";
  const change = current - previous;

  if (goalType === "loss") {
    return change < 0
      ? "change-good"
      : change > 0
        ? "change-bad"
        : "change-neutral";
  }

  if (goalType === "gain") {
    // ذكي: لو الوزن نقص لكن العضلات زادت → أخضر
    if (change > 0) return "change-good"; // زيادة الوزن → أخضر
    if (change < 0) {
      if (
        muscleCurrent != null &&
        musclePrevious != null &&
        muscleCurrent > musclePrevious
      ) {
        return "change-good"; // العضلات زادت → أخضر
      } else {
        return "change-bad"; // العضلات ما زادت → أحمر
      }
    }
    return "change-neutral"; // ما تغير → رمادي
  }

  return "change-neutral";
};

const getStatusIcon = (status) =>
  ({ excellent: "🔥", on_track: "✅", attention_needed: "⚠️" })[status] || "❓";

const getStatusTitle = (status) =>
  ({
    excellent: "Excellent Progress!",
    on_track: "You're On Track",
    attention_needed: "Needs Attention",
  })[status] || "Keep Going";

const getStatusMessage = (status) =>
  ({
    excellent: "You're crushing it! Keep going strong 💪",
    on_track: "Steady progress. Stay consistent!",
    attention_needed: "Time to refocus. You got this!",
  })[status] || "Keep tracking your progress";

const showToast = (msg, type = "success") => {
  toastMessage.value = msg;
  toastType.value = type;
  setTimeout(() => (toastMessage.value = ""), 3000);
};

// أضف هذه الدالة داخل الـ Script setup
const getStatusKey = (s) => {
  if (s === "attention_needed") return "attention";
  if (s === "on_track") return "onTrack";
  return s; // ستكون excellent
};

// --- Computed ---
const statsCards = computed(() => {
  // جلب القيم من البيانات المستلمة
  const startWeight = Number(weight.value) || 0;
  const currentWeight = stats.value.current_weight || startWeight;
  const targetWeight = Number(stats.value.target_weight) || 0;
  const progressPercent = stats.value.progress_percentage || 0;

  // تحديد وحدة الوزن بناءً على اللغة
  const unit = locale.value === "ar" ? "كجم" : "kg";

  return [
    {
      icon: "🎯",
      title: t("progress.stats.startWeight"),
      value: `${startWeight} ${unit}`,
      subtitle:
        t("progress.form.date") + ": " + (stats.value.start_date || "-"),
      type: "start",
    },
    {
      icon: "⚖️",
      title: t("progress.stats.currentWeight"),
      value: `${currentWeight} ${unit}`,
      subtitle: "", // يمكنك إضافة تاريخ آخر قياس هنا
      type: "current",
      valueClass: "text-primary",
    },
    {
      icon: "🏆",
      title: t("progress.stats.targetWeight"),
      value: `${targetWeight} ${unit}`,
      subtitle: `${stats.value.remaining_weight?.toFixed(1)} ${t("progress.stats.toGo")}`,
      type: "target",
    },
    {
      icon: "📊",
      title: t("progress.stats.progress"),
      value: `${progressPercent}%`,
      subtitle: t("progress.stats.ofGoal"),
      type: "progress",
      valueClass: "text-success",
      showProgress: true,
      progressWidth: progressPercent > 100 ? 100 : progressPercent,
    },
  ];
});

const smartEstimate = computed(() => {
  const history = progressHistory.value;

  if (!history || history.length < 2) {
    return {
      text: "Calculating...",
      weeks: null,
      months: null,
    };
  }

  const latest = history[history.length - 1];
  const previous = history[history.length - 2];

  const currentWeight = Number(latest.weight);
  const targetWeight = Number(stats.value.target_weight);

  if (!currentWeight || !targetWeight) {
    return {
      text: "Calculating...",
      weeks: null,
      months: null,
    };
  }

  const weightDiff = Math.abs(targetWeight - currentWeight);

  const weightChange = Math.abs(
    Number(latest.weight) - Number(previous.weight),
  );

  const daysDiff =
    (new Date(latest.date) - new Date(previous.date)) / (1000 * 60 * 60 * 24);

  if (daysDiff <= 0 || weightChange === 0) {
    return {
      text: "Calculating...",
      weeks: null,
      months: null,
    };
  }

  // معدل أسبوعي ذكي (واقعي)
  let weeklyRate = (weightChange / daysDiff) * 7;
  weeklyRate = Math.min(Math.max(weeklyRate, 0.25), 1); // حدود واقعية

  const weeksLeft = Math.ceil(weightDiff / weeklyRate);
  const monthsLeft = Math.ceil(weeksLeft / 4.345);

  return {
    weeks: weeksLeft,
    months: monthsLeft,
    text:
      monthsLeft <= 2
        ? `${weeksLeft} weeks`
        : `${monthsLeft} months (~${weeksLeft} weeks)`,
  };
});

const estimateCards = computed(() => {
  // تعريف الوحدات بناءً على اللغة المفعّلة
  const isAr = locale.value === "ar";
  const unitDay = isAr ? "يوم" : "Days";
  const unitWeek = isAr ? "أسبوع" : "Weeks";
  const unitKgPerWeek = isAr ? "كجم / أسبوع" : "kg/week";

  return [
    {
      icon: "fa-solid fa-calendar-days",
      title: t("progress.estimates.daysPassed"),
      value: `${stats.value.days_passed || 0} ${unitDay}`,
    },
    {
      icon: "fa-solid fa-chart-line",
      title: t("progress.estimates.recentPace"),
      value: stats.value.weekly_rate
        ? `${stats.value.weekly_rate.toFixed(2)} ${unitKgPerWeek}`
        : "-",
    },
    {
      icon: "fa-solid fa-hourglass-half",
      title: t("progress.estimates.timeLeft"),
      value: `~ ${stats.value.weeks_left || 0} ${unitWeek}`,
    },
    {
      icon: "fa-solid fa-heart-pulse",
      title: t("progress.estimates.bmi"),
      value: stats.value.current_bmi?.toFixed(1) || "-",
      subtitle: stats.value.bmi_category || "", // تصنيف الـ BMI مثل: "وزن مثالي"
    },
  ];
});

onMounted(() => {
  fetchStats();
  fetchHistory();
  fetchWeeklyChart(); // هذا الجديد
});
</script>
