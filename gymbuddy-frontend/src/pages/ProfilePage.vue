<template>
  <div class="profile-page page-custom" :class="{ rtl: $i18n.locale === 'ar' }">
    <div class="container py-4">
      <div v-if="isLoading" class="loading-container">
        <div class="spinner"></div>
        <p>{{ $t("profile.loading") }}</p>
      </div>

      <template v-else>
        <div class="profile-header">
          <div class="profile-avatar">
            <img
              v-if="user.photo"
              :src="user.photo"
              alt="User Photo"
              class="avatar-img"
            />
            <div v-else class="avatar-placeholder">
              {{ user.initials }}
            </div>
            <div class="status-badge">
              <i class="fa-solid fa-check"></i>
            </div>
          </div>

          <div class="profile-info">
            <h1>{{ user.name }}</h1>
            <p class="user-email">{{ user.email }}</p>
            <div class="user-meta">
              <span class="meta-item">
                <i class="fa-solid fa-calendar"></i>
                {{ $t("profile.joined") }} {{ formatJoinDate(user.created_at) }}
              </span>
              <span class="meta-item">
                <i class="fa-solid fa-fire"></i>
                {{ stats.totalRecords || 0 }}
                {{
                  stats.totalRecords === 1
                    ? $t("profile.day")
                    : $t("profile.days")
                }}
                {{ $t("profile.tracked") }}
              </span>
            </div>
          </div>

          <router-link to="/settings" class="btn-edit">
            <i class="fa-solid fa-gear"></i>
            {{ $t("profile.editProfile") }}
          </router-link>
        </div>

        <div class="stats-overview">
          <div class="stat-box">
            <div class="stat-icon">🎯</div>
            <div class="stat-content">
              <h3>{{ todayMeals.length }}</h3>
              <p>{{ $t("profile.stats.mealsToday") }}</p>
            </div>
          </div>
          <div class="stat-box">
            <div class="stat-icon">📊</div>
            <div class="stat-content">
              <h3>{{ stats.totalRecords || 0 }}</h3>
              <p>{{ $t("profile.stats.progressEntries") }}</p>
            </div>
          </div>
          <div class="stat-box">
            <div class="stat-icon">🔥</div>
            <div class="stat-content">
              <h3>{{ totalCaloriesToday }}</h3>
              <p>{{ $t("profile.stats.caloriesToday") }}</p>
            </div>
          </div>
          <div class="stat-box">
            <div class="stat-icon">⚖️</div>
            <div class="stat-content">
              <h3>{{ weightChange }} kg</h3>
              <p>{{ $t("profile.stats.weightChanged") }}</p>
            </div>
          </div>
        </div>

        <div class="goals-section">
          <h2 class="section-title">
            <i class="fa-solid fa-bullseye me-2"></i>
            {{ $t("profile.goals.title") }}
          </h2>
          <div class="goals-grid">
            <div class="goal-card">
              <div class="goal-header">
                <h4>
                  {{ $t("profile.goals.weightGoal") }} ({{ goalTypeLabel }})
                </h4>
                <span class="goal-badge">{{ progressPercent }}% Complete</span>
              </div>
              <div class="goal-progress">
                <div class="progress-labels">
                  <span>{{ weight || 0 }} kg</span>
                  <span class="current-weight"
                    >{{ stats.current_weight || 0 }} kg</span
                  >
                  <span>{{ stats.target_weight || 0 }} kg</span>
                </div>
                <div class="progress-bar">
                  <div
                    class="progress-fill"
                    :style="{ width: progressPercent + '%' }"
                  ></div>
                </div>
                <div class="goal-details-mini">
                  <span
                    >{{ stats.remaining_weight?.toFixed(1) || 0 }} kg
                    {{ $t("profile.goals.remaining") }}</span
                  >
                  <span v-if="stats.estimated_days_remaining">
                    {{ $t("profile.goals.eta") }}:
                    {{ stats.estimated_days_remaining }}
                    {{ $t("profile.days") }}
                  </span>
                </div>
              </div>
            </div>

            <div class="goal-card">
              <div class="goal-header">
                <h4>{{ $t("profile.goals.dailyCalories") }}</h4>
                <span class="goal-badge calories"
                  >{{ Math.round(stats.daily_calories) || 0 }} kcal</span
                >
              </div>
              <div class="goal-details">
                <div class="detail-item">
                  <span class="detail-label">{{
                    $t("profile.goals.activityLevel")
                  }}</span>
                  <span class="detail-value">{{
                    goal?.activity_level || "Not set"
                  }}</span>
                </div>
                <div class="detail-item">
                  <span class="detail-label">{{
                    $t("profile.goals.goalType")
                  }}</span>
                  <span class="detail-value">{{ goalTypeLabel }}</span>
                </div>
                <div class="detail-item">
                  <span class="detail-label">{{
                    $t("profile.goals.weeklyProgress")
                  }}</span>
                  <span class="detail-value"
                    >{{
                      stats.avg_weight_change_per_week?.toFixed(2) || 0
                    }}
                    kg/week</span
                  >
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="achievements-section">
          <h2 class="section-title">
            <i class="fa-solid fa-trophy me-2"></i>
            {{ $t("profile.achievements.title") }}
          </h2>
          <div class="achievements-grid">
            <div
              v-for="achievement in achievements"
              :key="achievement.id"
              class="achievement-card"
              :class="{ unlocked: achievement.unlocked }"
            >
              <div class="achievement-icon">{{ achievement.icon }}</div>
              <h4>{{ achievement.title }}</h4>
              <p>{{ achievement.description }}</p>
              <div v-if="!achievement.unlocked" class="locked-badge">
                <i class="fa-solid fa-lock"></i>
                {{ $t("profile.achievements.locked") }}
              </div>
              <div v-else class="unlocked-badge">
                <i class="fa-solid fa-check"></i>
                {{ $t("profile.achievements.unlocked") }}
              </div>
            </div>
          </div>
        </div>

        <div class="activity-section">
          <h2 class="section-title">
            <i class="fa-solid fa-clock-rotate-left me-2"></i>
            {{ $t("profile.activity.title") }}
          </h2>
          <div class="activity-timeline">
            <div
              v-for="activity in recentActivity"
              :key="activity.id"
              class="activity-item"
            >
              <div class="activity-icon" :class="activity.type">
                <i :class="activity.iconClass"></i>
              </div>
              <div class="activity-content">
                <h4>{{ activity.title }}</h4>
                <p>{{ activity.description }}</p>
                <span class="activity-time">{{ activity.time }}</span>
              </div>
            </div>
            <div v-if="recentActivity.length === 0" class="empty-activity">
              <p>{{ $t("profile.activity.empty") }}</p>
            </div>
          </div>
        </div>
      </template>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { ref, computed, onMounted, onUnmounted } from "vue";
import api from "../lib/axios";
import { useI18n } from "vue-i18n";

interface User {
  id: number;
  name: string;
  email: string;
  age: number | null;
  height: number | null;
  weight: number | null;
  sex: string | null;
  created_at: string;
  initials: string;
  photo: string | null;
}

interface Goal {
  id: number;
  goal_type: string;
  activity_level: string | null;
  target: number | null;
  calories: number | null;
  start_weight: number | null;
  start_date: string | null;
}

interface Stats {
  start_weight: number;
  current_weight: number;
  target_weight: number;
  weight_change: number;
  remaining_weight: number;
  progress_percentage: number;
  avg_weight_change_per_week: number;
  estimated_days_remaining: number | null;
  daily_calories: number;
  totalRecords: number;
  recent_progress: any[];
}

interface Meal {
  id: number;
  name: string;
  type: string;
  calories: number;
  quantity: number;
}

interface Progress {
  id: number;
  weight: number;
  date: string;
  workout_completed: boolean;
}

interface Activity {
  id: string;
  type: string;
  iconClass: string;
  title: string;
  description: string;
  time: string;
}

interface Achievement {
  id: number;
  icon: string;
  title: string;
  description: string;
  unlocked: boolean;
}

// States
const isLoading = ref(true);
const user = ref<User>({
  id: 0,
  name: "",
  email: "",
  age: null,
  height: null,
  weight: null,
  sex: null,
  created_at: "",
  initials: "",
  photo: localStorage.getItem("profilePhoto") || null,
});
const goal = ref<Goal | null>(null);
const stats = ref<Partial<Stats>>({});
const todayMeals = ref<Meal[]>([]);
const progressList = ref<Progress[]>([]);
const recentActivity = ref<Activity[]>([]);
const weight = ref(0);
const achievements = ref<Achievement[]>([]);
const { t } = useI18n();

// Computed
const goalTypeLabel = computed(() => {
  if (!goal.value) return t("calories.form.activityPlaceholder"); // أو أي نص افتراضي
  const types: Record<string, string> = {
    loss: t("calories.form.loss"),
    gain: t("calories.form.gain"),
    maintain: t("calories.form.maintain"),
  };
  return types[goal.value.goal_type] || "---";
});

const progressPercent = computed(() =>
  Math.round(stats.value.progress_percentage || 0),
);

const weightChange = computed(() =>
  (stats.value.weight_change || 0).toFixed(1),
);

const totalCaloriesToday = computed(() =>
  todayMeals.value.reduce(
    (sum, meal) => sum + meal.calories * meal.quantity,
    0,
  ),
);

// Functions
const formatJoinDate = (date: string) => {
  if (!date) return "Recently";
  return new Date(date).toLocaleDateString("en-US", {
    month: "short",
    year: "numeric",
  });
};

const formatTimeAgo = (date: string) => {
  const now = new Date();
  const past = new Date(date);
  const diffMs = now.getTime() - past.getTime();
  const diffMins = Math.floor(diffMs / 60000);
  const diffHours = Math.floor(diffMins / 60);
  const diffDays = Math.floor(diffHours / 24);
  if (diffMins < 60) return `${diffMins} min${diffMins !== 1 ? "s" : ""} ago`;
  if (diffHours < 24)
    return `${diffHours} hour${diffHours !== 1 ? "s" : ""} ago`;
  return `${diffDays} day${diffDays !== 1 ? "s" : ""} ago`;
};

const getAchievementIcon = (key: string): string => {
  const iconMap: Record<string, string> = {
    first_steps: "🎯",
    week_warrior: "🔥",
    consistency_king: "💪",
    goal_crusher: "⚖️",
    target_reached: "🏆",
    data_devotee: "📊",
  };
  return iconMap[key] || "🌟";
};

const buildRecentActivity = () => {
  const activities: Activity[] = [];

  todayMeals.value.slice(0, 3).forEach((meal) => {
    activities.push({
      id: `meal-${meal.id}`,
      type: "meal",
      iconClass: "fa-solid fa-utensils",
      title: `Logged ${meal.type}`,
      description: `${meal.name} - ${meal.calories * meal.quantity} kcal`,
      time: formatTimeAgo(new Date().toISOString()),
    });
  });

  stats.value.recent_progress
    ?.slice(-5)
    .reverse()
    .forEach((prog: any) => {
      activities.push({
        id: `progress-${prog.date}`,
        type: "progress",
        iconClass: "fa-solid fa-weight-scale",
        title: "Progress Update",
        description: `Weight: ${prog.weight} kg`,
        time: formatTimeAgo(prog.date),
      });
    });

  achievements.value
    .filter((a) => a.unlocked)
    .slice(0, 2)
    .forEach((ach) => {
      activities.push({
        id: `achievement-${ach.id}`,
        type: "achievement",
        iconClass: "fa-solid fa-trophy",
        title: "Achievement Unlocked!",
        description: `${ach.title} - ${ach.description}`,
        time: "Recently",
      });
    });

  recentActivity.value = activities.slice(0, 10);
};

// Fetch functions
const fetchUserInfo = async () => {
  try {
    const res = await api.get("/auth/user-info");
    const userData = res.data;
    user.value = {
      ...userData,
      initials: userData.name
        .split(" ")
        .map((n: string) => n[0])
        .join("")
        .toUpperCase()
        .slice(0, 2),
      photo: localStorage.getItem("profilePhoto") || userData.photo || null,
    };
  } catch (err) {
    console.error("Failed to fetch user info:", err);
  }
};

const fetchGoal = async () => {
  try {
    const res = await api.get("/goals");
    if (res.data.success && res.data.goal) goal.value = res.data.goal;
  } catch (err) {
    console.error(err);
  }
};

const fetchStats = async () => {
  try {
    const res2 = await api.get("/goals");
    weight.value = Number(res2.data.goal?.start_weight) || 0;
    const res = await api.get("/progress/stats");
    if (res.data.success) stats.value = res.data.data;
  } catch (err) {
    console.error(err);
    stats.value = {
      current_weight: user.value.weight || 0,
      target_weight: goal.value?.target || 0,
      weight_change: 0,
      remaining_weight: 0,
      progress_percentage: 0,
      totalRecords: 0,
      recent_progress: [],
    };
  }
};

const fetchTodayMeals = async () => {
  try {
    const res = await api.get("/meals/today");
    todayMeals.value = res.data.meals || [];
  } catch (err) {
    console.error(err);
    todayMeals.value = [];
  }
};

const fetchProgress = async () => {
  try {
    const res = await api.get("/progress");
    if (res.data.success) progressList.value = res.data.data || [];
  } catch (err) {
    console.error(err);
    progressList.value = [];
  }
};

const fetchAchievements = async () => {
  try {
    const res = await api.get("/achievements");
    if (res.data.success && res.data.achievements) {
      achievements.value = res.data.achievements.map((ach: any) => ({
        id: ach.id,
        icon: getAchievementIcon(ach.key),
        title: ach.title,
        description: ach.description,
        unlocked: !!ach.pivot?.unlocked_at,
      }));
    }
  } catch (err) {
    console.error("Failed to fetch achievements:", err);
    achievements.value = [];
  }
};

// Handle profile photo updates
const handlePhotoUpdate = (e: CustomEvent) => {
  if (e.detail?.newPhotoUrl !== undefined) {
    user.value.photo = e.detail.newPhotoUrl;
    localStorage.setItem("profilePhoto", e.detail.newPhotoUrl || "");
  } else user.value.photo = localStorage.getItem("profilePhoto") || null;
};

const handleStorageChange = (e: StorageEvent) => {
  if (e.key === "profilePhoto") user.value.photo = e.newValue || null;
};

// Lifecycle
onMounted(async () => {
  window.addEventListener(
    "profilePhotoUpdated",
    handlePhotoUpdate as EventListener,
  );
  window.addEventListener("storage", handleStorageChange);
  isLoading.value = true;
  try {
    await Promise.all([
      fetchUserInfo(),
      fetchGoal(),
      fetchStats(),
      fetchTodayMeals(),
      fetchProgress(),
      fetchAchievements(),
    ]);
    buildRecentActivity();
  } catch (err) {
    console.error(err);
  } finally {
    isLoading.value = false;
  }
});

onUnmounted(() => {
  window.removeEventListener(
    "profilePhotoUpdated",
    handlePhotoUpdate as EventListener,
  );
  window.removeEventListener("storage", handleStorageChange);
});
</script>
