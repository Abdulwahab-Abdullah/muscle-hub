<template>
  <div class="container py-4 page-custom">
    <!-- Header -->
    <div class="text-center mb-4">
      <h1 class="fw-bold">{{ t("exercise.title") }}</h1>
      <p class="text-muted">
        {{ t("exercise.subtitle") }}
      </p>
    </div>

    <!-- Filters -->
    <div class="filters-wrapper mb-4">
      <button
        v-for="muscle in muscles"
        :key="muscle"
        class="custom-btn"
        :class="{ active: selectedMuscle === muscle }"
        @click="selectedMuscle = muscle"
      >
        {{ muscle }}
      </button>
    </div>

    <!-- Skeleton -->
    <SkeletonComponent v-if="loading" type="exercise-card" />

    <!-- Exercises -->
    <div v-else class="row g-3">
      <div
        v-for="exercise in filteredExercises"
        :key="exercise.id"
        class="col-lg-3 col-md-4 col-sm-6"
      >
        <div class="card exercise-card h-100 shadow-sm">
          <div class="exercise-image-wrapper">
            <img
              :src="exercise.image"
              class="exercise-image"
              :alt="exercise.name"
            />
          </div>

          <div class="card-body d-flex flex-column">
            <h6 class="fw-bold text-light text-center exercise-name">
              {{ exercise.name }}
            </h6>

            <button
              class="btn mt-auto btn-action"
              :class="isAdded(exercise.id) ? 'btn-remove' : 'btn-add'"
              @click="toggleExercise(exercise)"
            >
              {{
                isAdded(exercise.id) ? t("exercise.remove") : t("exercise.add")
              }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Toast -->
    <div
      v-if="toastMessage"
      class="toast-message position-fixed bottom-0 end-0 m-3 p-3 rounded text-white"
    >
      {{ toastMessage }}
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import api from "../lib/axios";
import SkeletonComponent from "../components/SkeletonComponent.vue";
import { useI18n } from "vue-i18n";

const { t } = useI18n();

const muscles = [
  "All",
  "Chest",
  "Back",
  "Legs",
  "Shoulders",
  "Biceps",
  "Triceps",
  "Forearms",
  "Abs",
];
const selectedMuscle = ref("All");
const exercises = ref([]);
const workoutPlan = ref([]);
const toastMessage = ref("");
const loading = ref(true);

// -------------------- Fetch Exercises --------------------
const fetchExercises = async () => {
  try {
    loading.value = true;
    const res = await api.get("/exercises");
    exercises.value = res.data.data;
  } catch (err) {
    showToast("Failed to fetch exercises ❌");
  } finally {
    loading.value = false;
  }
};

// -------------------- LocalStorage --------------------
const loadWorkoutPlan = () => {
  const stored = JSON.parse(localStorage.getItem("workoutPlan") || "[]");
  workoutPlan.value = stored;
};

const isAdded = (id) => workoutPlan.value.some((e) => e.exercise_id === id);

const toggleExercise = (exercise) => {
  const exerciseId = Number(exercise.id);

  if (!exerciseId || exerciseId === 0) {
    console.error("❌ Invalid exercise ID:", exercise);
    showToast("Invalid exercise ❌");
    return;
  }

  let plan = [...workoutPlan.value];

  if (isAdded(exerciseId)) {
    plan = plan.filter((e) => e.exercise_id !== exerciseId);
    showToast(`${exercise.name} removed ❌`);
  } else {
    plan.push({
      exercise_id: exerciseId,
      name: exercise.name,
      sets: 3,
      reps: 10,
      weight: 0,
      day: "Monday",
    });
    showToast(`${exercise.name} added ✅`);
  }

  workoutPlan.value = plan;
  localStorage.setItem("workoutPlan", JSON.stringify(plan));
};

const showToast = (msg) => {
  toastMessage.value = msg;
  setTimeout(() => (toastMessage.value = ""), 2500);
};

const filteredExercises = computed(() => {
  if (selectedMuscle.value === "All") return exercises.value;
  return exercises.value.filter((e) => e.muscle === selectedMuscle.value);
});

onMounted(() => {
  fetchExercises();
  loadWorkoutPlan();
});
</script>

<style scoped lang="scss">
// Header
h1:first-child {
  color: var(--primary-color);
}
/* Filters */
.filters-wrapper {
  display: flex;
  gap: 0.5rem;
  justify-content: center;
  flex-wrap: wrap;
}

.custom-btn {
  padding: 0.5rem 1.2rem;
  border-radius: 25px;
  border: 2px solid var(--primary-color);
  background: transparent;
  color: var(--primary-color);
  font-weight: 600;
  transition: 0.3s;
  cursor: pointer;
}

.custom-btn:hover {
  background: var(--primary-light);
}

.custom-btn.active {
  background: var(--primary-color);
  color: #fff;
  box-shadow: 0 4px 12px rgba(56, 182, 255, 0.3);
}

/* Exercise Cards - حجم موحد */
.exercise-card {
  border: 2px solid var(--primary-light);
  border-radius: 12px;
  transition: 0.3s;
  overflow: hidden;
  position: relative;
}

.exercise-card:hover {
  border-color: var(--primary-color);
  box-shadow: 0 6px 16px rgba(56, 182, 255, 0.2);
  transform: translateY(-5px);
}

.exercise-card:hover .exercise-image-wrapper::after {
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(56, 182, 255, 0.15);
  pointer-events: none;
}

.exercise-card:hover .exercise-name {
  color: var(--primary-color);
  font-weight: 700;
}

/* صورة موحدة الحجم */
.exercise-image-wrapper {
  width: 100%;
  height: 200px;
  overflow: hidden;
  background: #f8f9fa;
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
  transition: 0.3s;
}

.exercise-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

/* اسم التمرين */
.exercise-name {
  color: #333;
  font-size: 0.95rem;
  min-height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 10px;
}

/* Buttons */
.btn-action {
  border-radius: 8px;
  font-weight: 600;
  padding: 8px;
  font-size: 0.9rem;
  transition: 0.3s;
}

.btn-add {
  background: var(--primary-color);
  color: white;
  border: none;
}

.btn-add:hover {
  background: var(--secondary-color);
  transform: scale(1.05);
}

.btn-remove {
  background: transparent;
  border: 2px solid #dc3545;
  color: #dc3545;
}

.btn-remove:hover {
  background: #dc3545;
  color: white;
}

/* Toast */
.toast-message {
  background-color: var(--primary-color);
  animation:
    fadein 0.3s,
    fadeout 0.3s 2.2s;
  font-weight: 500;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

@keyframes fadein {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes fadeout {
  to {
    opacity: 0;
    transform: translateY(20px);
  }
}

/* Responsive */
@media (max-width: 768px) {
  .exercise-image-wrapper {
    height: 180px;
  }

  .exercise-name {
    font-size: 0.85rem;
    min-height: 35px;
  }
}
</style>
