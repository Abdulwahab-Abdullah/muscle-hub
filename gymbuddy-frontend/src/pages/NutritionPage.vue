<template>
  <div class="container py-4 page-custom">
    <div class="text-center mb-4">
      <h1 class="fw-bold">
        {{ t("nutrition.title") }}
      </h1>
      <p class="text-muted">{{ t("nutrition.subtitle") }}</p>
    </div>

    <div v-if="!loading" class="macros-summary mb-4">
      <div class="macro-card" :class="getMacroStatus('calories')">
        <div class="macro-header">
          <h5>{{ t("nutrition.macros.calories") }}</h5>
          <span class="macro-status">{{ getStatusMessage("calories") }}</span>
        </div>
        <p class="macro-value">
          {{ totalCalories.toFixed(0) }}
          <span class="target"
            >/ {{ nutritionPlan.daily_calories || 2000 }}</span
          >
        </p>
        <div class="macro-progress">
          <div
            class="progress-bar"
            :style="{ width: getProgressWidth('calories') }"
          ></div>
        </div>
      </div>

      <div class="macro-card" :class="getMacroStatus('protein')">
        <div class="macro-header">
          <h5>{{ t("nutrition.macros.protein") }}</h5>
          <span class="macro-status">{{ getStatusMessage("protein") }}</span>
        </div>
        <p class="macro-value">
          {{ totalProtein.toFixed(1) }}
          <span class="target"
            >/ {{ nutritionPlan.protein_target || 150 }}g</span
          >
        </p>
        <div class="macro-progress">
          <div
            class="progress-bar"
            :style="{ width: getProgressWidth('protein') }"
          ></div>
        </div>
      </div>

      <div class="macro-card" :class="getMacroStatus('carbs')">
        <div class="macro-header">
          <h5>{{ t("nutrition.macros.carbs") }}</h5>
          <span class="macro-status">{{ getStatusMessage("carbs") }}</span>
        </div>
        <p class="macro-value">
          {{ totalCarbs.toFixed(1) }}
          <span class="target">/ {{ nutritionPlan.carbs_target || 200 }}g</span>
        </p>
        <div class="macro-progress">
          <div
            class="progress-bar"
            :style="{ width: getProgressWidth('carbs') }"
          ></div>
        </div>
      </div>

      <div class="macro-card" :class="getMacroStatus('fat')">
        <div class="macro-header">
          <h5>{{ t("nutrition.macros.fat") }}</h5>
          <span class="macro-status">{{ getStatusMessage("fat") }}</span>
        </div>
        <p class="macro-value">
          {{ totalFat.toFixed(1) }}
          <span class="target">/ {{ nutritionPlan.fat_target || 70 }}g</span>
        </p>
        <div class="macro-progress">
          <div
            class="progress-bar"
            :style="{ width: getProgressWidth('fat') }"
          ></div>
        </div>
      </div>
    </div>

    <SkeletonComponent v-if="loading" type="table" />

    <div v-else class="meals-container">
      <div
        v-for="mealType in ['Breakfast', 'Lunch', 'Dinner', 'Snacks']"
        :key="mealType"
        class="meal-section"
      >
        <div class="section-header">
          <h4>
            {{
              mealType === "Breakfast"
                ? "🍳 " + t("nutrition.meals.breakfast")
                : mealType === "Lunch"
                  ? "🍽️ " + t("nutrition.meals.lunch")
                  : mealType === "Dinner"
                    ? "🌙 " + t("nutrition.meals.dinner")
                    : "🍪 " + t("nutrition.meals.snacks")
            }}
          </h4>
          <button class="btn-add-meal" @click="openMealLibrary(mealType)">
            <i class="fa-solid fa-plus"></i> {{ t("nutrition.meals.addMeal") }}
          </button>
        </div>

        <div v-if="getMealsByType(mealType).length === 0" class="empty-state">
          {{ t("nutrition.meals.empty") }}
        </div>

        <div v-else class="meals-list">
          <div
            v-for="(meal, i) in getMealsByType(mealType)"
            :key="i"
            class="meal-card"
          >
            <div class="meal-info">
              <h6>{{ meal.name }}</h6>
              <p class="meal-macros">
                {{ (meal.calories * meal.localQuantity).toFixed(1) }} cal | P:
                {{ (meal.protein * meal.localQuantity).toFixed(1) }}g | C:
                {{ (meal.carbs * meal.localQuantity).toFixed(1) }}g | F:
                {{ (meal.fat * meal.localQuantity).toFixed(1) }}g
              </p>
            </div>
            <div class="meal-actions">
              <input
                type="number"
                v-model.number="meal.localQuantity"
                min="1"
                class="qty-input"
              />
              <button class="btn-remove" @click="removeMeal(mealType, i)">
                <i class="fa-solid fa-trash"></i>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div v-if="!loading" class="action-buttons">
      <button
        class="btn btn-save"
        :disabled="!canSave || saving"
        @click="savePlan"
      >
        💾
        {{
          hasSavedPlan
            ? t("nutrition.actions.update")
            : t("nutrition.actions.save")
        }}
      </button>
      <button class="btn btn-reset" @click="resetDay">
        🗑️ {{ t("nutrition.actions.reset") }}
      </button>
    </div>

    <div v-if="showMealLibrary" class="modal-overlay" @click="closeMealLibrary">
      <div class="modal-content" @click.stop>
        <div class="modal-header">
          <h4>{{ t("nutrition.meals.addMeal") }} ({{ currentMealType }})</h4>
          <button class="btn-close" @click="closeMealLibrary">×</button>
        </div>
        <div class="modal-body">
          <input
            type="text"
            v-model="searchQuery"
            class="search-input"
            placeholder="Search meals..."
          />

          <div class="meals-grid">
            <div
              v-for="meal in filteredMeals"
              :key="meal.id"
              class="library-meal-card"
            >
              <h6>{{ meal.name }}</h6>
              <p class="meal-stats">
                {{ meal.calories }} cal | P: {{ meal.protein }}g | C:
                {{ meal.carbs }}g | F: {{ meal.fat }}g
              </p>
              <div class="meal-add-section modern">
                <div class="portion-toggle">
                  <button
                    :class="{ active: meal.unit === '100g' }"
                    @click="meal.unit = '100g'"
                  >
                    100g
                  </button>
                  <button
                    :class="{ active: meal.unit === '1g' }"
                    @click="meal.unit = '1g'"
                  >
                    1g
                  </button>
                </div>
                <input
                  type="number"
                  v-model.number="meal.quantity"
                  min="1"
                  class="qty-input-small"
                />
                <button class="btn-add-to-plan" @click="addMealToPlan(meal)">
                  <i class="fa-solid fa-plus"></i>
                </button>
              </div>
            </div>
          </div>

          <div class="custom-meal-section">
            <h5>Or Create Custom Meal</h5>
            <div class="custom-meal-form">
              <div
                class="form-group"
                v-for="field in ['name', 'calories', 'protein', 'carbs', 'fat']"
                :key="field"
              >
                <label :for="'meal-' + field">{{
                  field.charAt(0).toUpperCase() + field.slice(1)
                }}</label>
                <input
                  :id="'meal-' + field"
                  type="text"
                  v-if="field === 'name'"
                  v-model="customMeal.name"
                  placeholder="Enter meal name"
                  class="form-input"
                />
                <input
                  :id="'meal-' + field"
                  type="number"
                  v-else
                  v-model.number="customMeal[field]"
                  placeholder="0"
                  class="form-input"
                />
              </div>
              <button class="btn-add-custom" @click="addCustomMeal">
                Create & Add
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div v-if="toastMessage" class="toast-message">{{ toastMessage }}</div>

    <div class="go-up" v-show="showGoUp">
      <button @click="scrollToTop">
        <i class="fa-solid fa-circle-up fa-2x"></i>
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from "vue";
import api from "../lib/axios";
import SkeletonComponent from "../components/SkeletonComponent.vue";
import { useI18n } from "vue-i18n";

const { t } = useI18n();
const nutritionPlan = ref({});
const mealsLibrary = ref([]);
const selectedMeals = ref([]);
const goalOfUser = ref("");
const customMeal = ref({
  name: "",
  type: "Breakfast",
  calories: 0,
  protein: 0,
  carbs: 0,
  fat: 0,
  quantity: 1,
});

const loading = ref(true);
const saving = ref(false);
const toastMessage = ref("");
const currentPlanId = ref(null);
const showMealLibrary = ref(false);
const currentMealType = ref("");
const searchQuery = ref("");

const filteredMeals = computed(() => {
  if (!searchQuery.value) return mealsLibrary.value;
  return mealsLibrary.value.filter((m) =>
    m.name.toLowerCase().includes(searchQuery.value.toLowerCase()),
  );
});

const totalCalories = computed(() =>
  selectedMeals.value.reduce(
    (sum, m) => sum + Number(m.calories || 0) * (m.localQuantity || 1),
    0,
  ),
);
const totalProtein = computed(() =>
  selectedMeals.value.reduce(
    (sum, m) => sum + Number(m.protein || 0) * (m.localQuantity || 1),
    0,
  ),
);
const totalCarbs = computed(() =>
  selectedMeals.value.reduce(
    (sum, m) => sum + Number(m.carbs || 0) * (m.localQuantity || 1),
    0,
  ),
);
const totalFat = computed(() =>
  selectedMeals.value.reduce(
    (sum, m) => sum + Number(m.fat || 0) * (m.localQuantity || 1),
    0,
  ),
);

const canSave = computed(() => selectedMeals.value.length > 0);
const hasSavedPlan = computed(() => currentPlanId.value !== null);

function getMealsByType(type) {
  return selectedMeals.value.filter((m) => m.type === type);
}

function removeMeal(type, index) {
  const mealsOfType = getMealsByType(type);
  const mealToRemove = mealsOfType[index];
  const globalIndex = selectedMeals.value.indexOf(mealToRemove);
  if (globalIndex > -1) selectedMeals.value.splice(globalIndex, 1);
}

function openMealLibrary(type) {
  currentMealType.value = type;
  showMealLibrary.value = true;
  searchQuery.value = "";
}

function closeMealLibrary() {
  showMealLibrary.value = false;
  customMeal.value = {
    name: "",
    type: currentMealType.value,
    calories: 0,
    protein: 0,
    carbs: 0,
    fat: 0,
    quantity: 1,
  };
}

function addMealToPlan(meal) {
  const unitMultiplier = meal.unit === "1g" ? 1 : 100;

  selectedMeals.value.push({
    id: null,
    name: meal.name,
    type: currentMealType.value,
    calories: (meal.calories * unitMultiplier) / 100,
    protein: (meal.protein * unitMultiplier) / 100,
    carbs: (meal.carbs * unitMultiplier) / 100,
    fat: (meal.fat * unitMultiplier) / 100,
    localQuantity: meal.quantity || 1,
  });

  showToast(`${meal.name} added ✅`);
}

function addCustomMeal() {
  if (!customMeal.value.name) {
    return showToast("Please enter meal name ❌");
  }

  selectedMeals.value.push({
    id: null,
    name: customMeal.value.name,
    type: currentMealType.value,
    calories: Number(customMeal.value.calories || 0),
    protein: Number(customMeal.value.protein || 0),
    carbs: Number(customMeal.value.carbs || 0),
    fat: Number(customMeal.value.fat || 0),
    localQuantity: customMeal.value.quantity || 1,
  });

  showToast(`${customMeal.value.name} added ✅`);
  closeMealLibrary();
}

function getMacroStatus(type) {
  const p = getPercentage(type);
  if (p >= 100) return "status-exceeded";
  if (p >= 80) return "status-warning";
  return "status-normal";
}

function getStatusMessage(type) {
  const p = getPercentage(type);
  if (p >= 100) return "Exceeded!";
  if (p >= 90) return "Almost there";
  if (p >= 80) return "Getting close";
  return "On track";
}

function getPercentage(type) {
  let current = 0,
    target = 1;
  switch (type) {
    case "calories":
      current = totalCalories.value;
      target = nutritionPlan.value.daily_calories || 2000;
      break;
    case "protein":
      current = totalProtein.value;
      target = nutritionPlan.value.protein_target || 150;
      break;
    case "carbs":
      current = totalCarbs.value;
      target = nutritionPlan.value.carbs_target || 200;
      break;
    case "fat":
      current = totalFat.value;
      target = nutritionPlan.value.fat_target || 70;
      break;
  }
  return (current / target) * 100;
}

function getProgressWidth(type) {
  return `${Math.min(getPercentage(type), 100)}%`;
}

async function resetDay() {
  if (!currentPlanId.value) return;
  try {
    await api.delete(`/nutrition-plans/${currentPlanId.value}/meals-today`);
    const res = await api.get("/meals/today");
    selectedMeals.value = res.data.meals.map((m) => ({
      ...m,
      localQuantity: m.quantity,
    }));

    showToast("All meals for today cleared! 🗑️");
  } catch (err) {
    console.error(err);
    showToast("Failed to clear meals ❌");
  }
}

async function savePlan() {
  if (!canSave.value) return showToast("No meals to save ❌");
  try {
    saving.value = true;
    const payload = {
      plan_name: nutritionPlan.value.plan_name || "My Nutrition Plan",
      goal: goalOfUser.value || "maintain",
      daily_calories: nutritionPlan.value.daily_calories || 2000,
      protein_target: nutritionPlan.value.protein_target || 150,
      carbs_target: nutritionPlan.value.carbs_target || 200,
      fat_target: nutritionPlan.value.fat_target || 70,
      meals: selectedMeals.value.map((m) => ({
        id: m.id || null,
        name: m.name,
        type: m.type,
        calories: Number(m.calories || 0),
        protein: Number(m.protein || 0),
        carbs: Number(m.carbs || 0),
        fat: Number(m.fat || 0),
        quantity: m.localQuantity || 1,
      })),
    };
    if (currentPlanId.value) {
      await api.put(`/nutrition-plans/${currentPlanId.value}`, payload);
      showToast("Plan updated successfully! 💪");
    } else {
      const res = await api.post("/nutrition-plans", payload);
      currentPlanId.value = res.data.data.id;
      showToast("Plan saved successfully! 💪");
    }
  } catch (err) {
    console.error("Save error:", err.response?.data || err);
    showToast("Failed to save plan ❌");
  } finally {
    saving.value = false;
  }
}

function showToast(msg) {
  toastMessage.value = msg;
  setTimeout(() => (toastMessage.value = ""), 3000);
}

const showGoUp = ref(false);
function handleScroll() {
  showGoUp.value = window.scrollY > 100;
}
function scrollToTop() {
  window.scrollTo({ top: 0, behavior: "smooth" });
}

onMounted(async () => {
  window.addEventListener("scroll", handleScroll);
  loading.value = true;
  try {
    const resMeals = await api.get("/meals");
    mealsLibrary.value = (resMeals.data || []).map((m) => ({
      ...m,
      quantity: 1,
      unit: "100g",
    }));

    const resPlan = await api.get("/nutrition-plans");
    const plan = Array.isArray(resPlan.data) ? resPlan.data[0] : resPlan.data;
    nutritionPlan.value = plan || {};
    goalOfUser.value = nutritionPlan.value.goal || "maintain";
    if (nutritionPlan.value.meals) {
      selectedMeals.value = nutritionPlan.value.meals.map((m) => ({
        ...m,
        localQuantity: m.quantity || 1,
      }));
      currentPlanId.value = nutritionPlan.value.id;
    }
  } catch (err) {
    console.error(err);
    showToast("Failed to load data ❌");
  } finally {
    loading.value = false;
  }
});

onUnmounted(() => window.removeEventListener("scroll", handleScroll));
</script>
