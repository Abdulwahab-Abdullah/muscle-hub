<template>
  <div class="container py-4 page-custom">
    <div class="text-center mb-4">
      <h1 class="fw-bold">{{ $t("workouts.title") }}</h1>
      <p class="text-muted">{{ $t("workouts.subtitle") }}</p>
      <p class="text-info fw-bold">
        {{ $t("workouts.totalExercises") }}: {{ totalExercises }}
      </p>
    </div>

    <!-- VIEW MODE: Table by Days -->
    <div v-if="!editMode && workouts.length > 0">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-primary">{{ planName }}</h3>
        <button class="btn btn-custom" @click="editMode = true">
          ✏️ {{ $t("workouts.editPlan") }}
        </button>
      </div>

      <div v-for="day in activeDays" :key="day" class="mb-4">
        <h4 class="day-header text-capitalize">
          {{ t(`workouts.days.${day.toLowerCase()}`) }}
        </h4>

        <div class="table-responsive">
          <table
            class="table table-hover table-bordered align-middle text-center"
          >
            <thead class="table-header">
              <tr>
                <th>#</th>
                <th>{{ t("workouts.table.exercise") }}</th>
                <th>{{ t("workouts.table.sets") }}</th>
                <th>{{ t("workouts.table.reps") }}</th>
                <th>{{ t("workouts.table.weight") }}</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(w, i) in workoutsByDay[day]" :key="i">
                <td>{{ i + 1 }}</td>
                <td class="fw-bold">{{ w.name }}</td>
                <td>{{ w.sets }}</td>
                <td>{{ w.reps }}</td>
                <td>{{ w.weight || "-" }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Skeleton -->
    <SkeletonComponent v-if="loading" type="table" />

    <!-- EDIT MODE: Cards -->
    <div v-else-if="editMode || workouts.length === 0">
      <!-- Plan Name Input -->
      <div class="mb-4 text-center">
        <input
          v-model="planName"
          type="text"
          class="form-control custom-input"
          placeholder="Plan name e.g. Full Body"
        />
      </div>

      <!-- Workout Cards -->
      <div v-if="workouts.length" class="row g-3 mb-4">
        <div
          v-for="(w, i) in workouts"
          :key="w.exercise_id + '-' + i"
          class="col-md-6"
        >
          <div class="card shadow-sm p-3 workout-card">
            <h5 class="fw-bold mb-3 text-primary">{{ w.name }}</h5>

            <!-- Day -->
            <label class="form-label">Day</label>
            <select v-model="w.day" class="form-select mb-3">
              <option v-for="d in days" :key="d" :value="d">{{ d }}</option>
            </select>

            <!-- Sets / Reps / Weight -->
            <div class="row g-2 mb-3">
              <div class="col-4">
                <label class="form-label">Sets</label>
                <input
                  type="number"
                  min="1"
                  v-model.number="w.sets"
                  class="form-control"
                />
              </div>
              <div class="col-4">
                <label class="form-label">Reps</label>
                <input
                  type="number"
                  min="1"
                  v-model.number="w.reps"
                  class="form-control"
                />
              </div>
              <div class="col-4">
                <label class="form-label">Weight (kg)</label>
                <input
                  type="number"
                  min="0"
                  step="0.5"
                  v-model.number="w.weight"
                  class="form-control"
                  placeholder="0"
                />
              </div>
            </div>

            <!-- Remove -->
            <button class="btn btn-remove w-100" @click="removeWorkout(i)">
              ❌ Remove
            </button>
          </div>
        </div>
      </div>

      <div v-else class="text-center text-muted mb-4">
        No exercises selected yet. Go to Exercise Library to add some!
      </div>

      <!-- Action Buttons -->
      <div class="text-center mt-4">
        <button
          class="btn btn-save px-4 me-2"
          :disabled="!canSave || saving"
          @click="savePlan"
        >
          💾 {{ hasSavedPlan ? "Update Plan" : "Save Plan" }}
        </button>
        <button
          v-if="hasSavedPlan"
          class="btn btn-cancel px-4 me-2"
          @click="cancelEdit"
        >
          ❌ Cancel
        </button>
        <button class="btn btn-clear px-4 mx-2" @click="clearPlan">
          🗑️ Clear All
        </button>
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
import { ref, computed, onMounted, watch } from "vue";
import api from "../lib/axios";
import SkeletonComponent from "../components/SkeletonComponent.vue";
import { useI18n } from "vue-i18n";
import Swal from "sweetalert2";

const { t } = useI18n();
const workouts = ref([]);
const exercises = ref([]);
const planName = ref("");
const saving = ref(false);
const toastMessage = ref("");
const currentPlanId = ref(null);
const editMode = ref(false);
const loading = ref(true);

const days = [
  "Monday",
  "Tuesday",
  "Wednesday",
  "Thursday",
  "Friday",
  "Saturday",
  "Sunday",
];

// -------------------- Fetch Exercises --------------------
const fetchExercises = async () => {
  try {
    const res = await api.get("/exercises");
    exercises.value = res.data.data;
    console.log("✅ Exercises loaded:", exercises.value.length);
  } catch (err) {
    showToast("Failed to load exercises ❌");
  }
};

// -------------------- Load Workouts from localStorage --------------------
const loadWorkoutPlan = () => {
  const stored = JSON.parse(localStorage.getItem("workoutPlan") || "[]");

  if (exercises.value.length === 0) {
    console.warn("⚠️ Exercises not loaded yet!");
    return;
  }

  workouts.value = stored.map((w) => {
    const exerciseId = Number(w.exercise_id);
    const exercise = exercises.value.find((e) => e.id === exerciseId);

    return {
      exercise_id: exerciseId,
      name: exercise?.name || "Unknown Exercise",
      sets: Number(w.sets) || 3,
      reps: Number(w.reps) || 10,
      weight: Number(w.weight) || 0,
      day: w.day || "Monday",
    };
  });
};

// -------------------- Load Plan from Database --------------------
const loadPlanFromDB = async () => {
  try {
    loading.value = true;
    const res = await api.get("/workout-plans");
    const plans = res.data.data || [];

    if (plans.length > 0) {
      const plan = plans[0]; // بلان واحدة فقط
      currentPlanId.value = plan.id;
      planName.value = plan.plan_name;

      const loadedWorkouts = plan.workouts.map((w) => {
        const exerciseId = Number(w.exercise_id || w.exercise?.id || w.id);
        const exercise = exercises.value.find((e) => e.id === exerciseId);

        return {
          exercise_id: exerciseId,
          name: exercise?.name || `Unknown Exercise (ID: ${exerciseId})`,
          sets: Number(w.sets) || 3,
          reps: Number(w.reps) || 10,
          weight: Number(w.weight) || 0,
          day: w.day || "Monday",
        };
      });

      workouts.value = loadedWorkouts;
      localStorage.setItem("workoutPlan", JSON.stringify(loadedWorkouts));
      editMode.value = false;
      showToast("Plan loaded ✅");
    } else {
      editMode.value = true;
    }
  } catch (err) {
    console.error("Failed to load plan from DB:", err);
    loadWorkoutPlan();
    editMode.value = workouts.value.length === 0;
  } finally {
    loading.value = false;
  }
};

// -------------------- Computed Properties --------------------
const totalExercises = computed(() => workouts.value.length);
const canSave = computed(() => planName.value.trim() && workouts.value.length);
const hasSavedPlan = computed(() => currentPlanId.value !== null);

const workoutsByDay = computed(() => {
  const table = {};
  workouts.value.forEach((w) => {
    if (!table[w.day]) table[w.day] = [];
    table[w.day].push(w);
  });
  return table;
});

const activeDays = computed(() => {
  return days.filter((day) => workoutsByDay.value[day]?.length > 0);
});

// -------------------- Helpers --------------------
const removeWorkout = (i) => workouts.value.splice(i, 1);

const clearPlan = async () => {
  if (workouts.value.length === 0) {
    showToast("No exercises to clear ❌");
    return;
  }
  const result = await Swal.fire({
    title: "Are you sure?",
    text: "Do you want to clear all exercises?",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Yes, clear it!",
    cancelButtonText: "Cancel",
    reverseButtons: true,
  });

  if (result.isConfirmed) {
    workouts.value = [];
    planName.value = "";
    localStorage.removeItem("workoutPlan");
    editMode.value = true;

    Swal.fire({
      icon: "success",
      title: "Plan cleared ✅",
      showConfirmButton: false,
      timer: 1500,
    });
  }
};

const cancelEdit = () => {
  loadPlanFromDB();
};

const showToast = (msg) => {
  toastMessage.value = msg;
  setTimeout(() => (toastMessage.value = ""), 2500);
};

// -------------------- Auto-save --------------------
watch(
  workouts,
  (newVal) => {
    if (editMode.value) {
      localStorage.setItem("workoutPlan", JSON.stringify(newVal));
    }
  },
  { deep: true },
);

// -------------------- Save Plan --------------------
const savePlan = async () => {
  try {
    saving.value = true;

    const unique = [];
    const seen = new Set();
    for (const w of workouts.value) {
      const key = `${w.exercise_id}-${w.day}`;
      if (seen.has(key)) {
        showToast("Duplicate exercise in same day ❌");
        saving.value = false;
        return;
      }
      seen.add(key);
      unique.push({
        exercise_id: w.exercise_id,
        day: w.day,
        sets: w.sets,
        reps: w.reps,
        weight: w.weight || 0,
      });
    }

    if (currentPlanId.value) {
      await api.put(`/workout-plans/${currentPlanId.value}`, {
        plan_name: planName.value,
        workouts: unique,
      });
      showToast("Plan updated 💪");
    } else {
      const res = await api.post("/workout-plans", {
        plan_name: planName.value,
        workouts: unique,
      });
      currentPlanId.value = res.data.data.id;
      showToast("Plan saved 💪");
    }

    localStorage.setItem("workoutPlan", JSON.stringify(workouts.value));
    await loadPlanFromDB();
    editMode.value = false;
  } catch (err) {
    console.error(err);
    showToast("Failed to save plan ❌");
  } finally {
    saving.value = false;
  }
};

// -------------------- Mounted --------------------
onMounted(async () => {
  await fetchExercises();
  await loadPlanFromDB();

  if (workouts.value.length === 0) {
    loadWorkoutPlan();
    editMode.value = workouts.value.length === 0;
  }
});
</script>

<style scoped>
button {
  background-color: var(--primary-color);
}
</style>
