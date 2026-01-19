<template>
  <div class="p-6 text-center calories-page page-custom">
    <!-- Page Title -->
    <div class="mb-6">
      <h1 class="font-bold">{{ t("calories.title") }}</h1>
      <p class="text-muted">{{ t("calories.subtitle") }}</p>
    </div>

    <!-- Skeleton Loading -->
    <transition name="fade-slide" mode="out-in">
      <div v-if="isInitialLoading" class="skeleton-container" key="skeleton">
        <div class="skeleton-box">
          <div class="skeleton-line skeleton-title"></div>
          <div class="skeleton-line skeleton-input"></div>
          <div class="skeleton-line skeleton-input"></div>
          <div class="skeleton-line skeleton-input"></div>
          <div class="skeleton-line skeleton-select"></div>
          <div class="skeleton-line skeleton-input"></div>
          <div class="skeleton-buttons">
            <div class="skeleton-radio"></div>
            <div class="skeleton-radio"></div>
          </div>
          <div class="skeleton-button"></div>
        </div>
      </div>

      <!-- Calculator Form -->
      <div
        v-else-if="showForm"
        class="rounded shadow mb-5 p-6 max-w-lg calories-form p-3"
        key="form"
      >
        <form @submit.prevent="calculateCalories">
          <div class="mb-4">
            <label class="block">{{ t("calories.form.age") }}</label>
            <input
              type="text"
              v-model="age"
              class="px-4 py-2"
              :placeholder="t('calories.form.agePlaceholder')"
            />
          </div>

          <div class="mb-4">
            <label class="block">{{ t("calories.form.weight") }}</label>
            <input
              type="text"
              v-model="weight"
              class="px-4 py-2"
              :placeholder="t('calories.form.weightPlaceholder')"
            />
          </div>

          <div class="mb-4">
            <label class="block">{{ t("calories.form.height") }}</label>
            <input
              type="text"
              v-model="height"
              class="px-4 py-2"
              :placeholder="t('calories.form.heightPlaceholder')"
            />
          </div>

          <div class="mb-4">
            <label class="block">{{ t("calories.form.activity") }}</label>
            <select class="px-4 py-2 w-75" v-model="activityLevel">
              <option value="" disabled selected>
                {{ t("calories.form.activityPlaceholder") }}
              </option>
              <option value="sedentary">
                {{ activityLevels[0] }}
              </option>
              <option value="light">
                {{ activityLevels[1] }}
              </option>
              <option value="moderate">
                {{ activityLevels[2] }}
              </option>
              <option value="active">
                {{ activityLevels[3] }}
              </option>
              <option value="very-active">
                {{ activityLevels[4] }}
              </option>
            </select>
          </div>

          <div class="mb-4">
            <label class="block">{{ t("calories.form.targetWeight") }}</label>
            <input
              type="text"
              v-model="targetWeight"
              class="px-4 py-2"
              :placeholder="t('calories.form.targetWeightPlaceholder')"
            />
          </div>

          <div class="mb-4 sex-group">
            <label class="block">{{ t("calories.form.sex") }}</label>
            <div class="sex-options">
              <label>
                <input type="radio" v-model="sex" name="sex" value="male" />
                <span class="icon-man"><i class="fa-solid fa-mars"></i></span>
                {{ t("calories.form.male") }}
              </label>
              <label>
                <input type="radio" v-model="sex" name="sex" value="female" />
                <span class="icon-woman">
                  <i class="fa-solid fa-venus"></i>
                </span>
                {{ t("calories.form.female") }}
              </label>
            </div>
          </div>

          <div class="mb-4 goal-group">
            <label class="block">{{ t("calories.form.goal") }}</label>
            <div class="goal-options">
              <label>
                <input type="radio" v-model="goal" name="goal" value="loss" />
                🔥 {{ t("calories.form.loss") }}
              </label>
              <label>
                <input
                  type="radio"
                  v-model="goal"
                  name="goal"
                  value="maintain"
                />
                ⚖️ {{ t("calories.form.maintain") }}
              </label>
              <label>
                <input type="radio" v-model="goal" name="goal" value="gain" />
                💪 {{ t("calories.form.gain") }}
              </label>
            </div>
          </div>

          <button
            type="submit"
            class="text-white"
            :disabled="isSubmitting"
            :class="{ 'opacity-50 cursor-not-allowed': isSubmitting }"
          >
            {{
              isSubmitting
                ? t("calories.buttons.processing")
                : t("calories.buttons.calculate")
            }}
          </button>
        </form>
      </div>

      <!-- Result Display -->
      <div v-else-if="dailyCalories !== null" key="result">
        <div class="my-5 p-4 daily-calories-result">
          <h2>{{ t("calories.result.title") }}</h2>
          <p>{{ dailyCalories.toFixed(0) }} kcal/day</p>
          <button class="btn mt-4 edit-btn" @click="showForm = true">
            {{ t("calories.buttons.edit") }}
          </button>
        </div>
      </div>
    </transition>
  </div>
</template>

<script lang="ts" setup>
import { onMounted, ref, computed } from "vue";
import { useI18n } from "vue-i18n";
import api from "../lib/axios";
import Swal from "sweetalert2";
import { useLoadingStore } from "../stores/loadingStore";

const { t, locale } = useI18n();
const loader = useLoadingStore();

// Activity Levels - يمكنك تركها بالإنجليزي أو ترجمتها
const activityLevels = computed(() => [
  locale.value === "ar"
    ? "قليل النشاط (لا توجد تمارين)"
    : "Sedentary (little or no exercise)",
  locale.value === "ar"
    ? "نشاط خفيف (1-3 أيام/أسبوع)"
    : "Lightly active (light exercise/sports 1-3 days/week)",
  locale.value === "ar"
    ? "نشاط متوسط (3-5 أيام/أسبوع)"
    : "Moderately active (moderate exercise/sports 3-5 days/week)",
  locale.value === "ar"
    ? "نشط جداً (6-7 أيام/أسبوع)"
    : "Active (hard exercise/sports 6-7 days a week)",
  locale.value === "ar"
    ? "نشاط مكثف (تمارين يومية وعمل شاق)"
    : "Very active (very hard exercise/sports & physical job)",
]);

// Reactive form fields
const age = ref<number | null>(null);
const sex = ref<string>("");
const weight = ref<number | null>(null);
const targetWeight = ref<number | null>(null);
const height = ref<number | null>(null);
const activityLevel = ref<string>("");
const goal = ref<string>("");
const dailyCalories = ref<number | null>(null);
const isInitialLoading = ref<boolean>(true);
const isSubmitting = ref<boolean>(false);
const showForm = ref<boolean>(false);
const existingNutritionPlanId = ref<number | null>(null);
const userId = ref<number | null>(null);
const originalProfile = ref({
  age: null,
  weight: null,
  height: null,
  sex: null,
});

const selectedMeals = ref<
  Array<{ name: string; calories: number; type?: string }>
>([]);

const fetchCalories = async () => {
  isInitialLoading.value = true;
  try {
    const response = await api.get("/goals");
    const savedCalories = Math.round(response.data.goal?.calories || 0);

    try {
      const nutritionResponse = await api.get("/nutrition-plans");
      if (nutritionResponse.data && nutritionResponse.data.length > 0) {
        existingNutritionPlanId.value = nutritionResponse.data[0].id;
      }
    } catch (err) {
      console.log("No existing nutrition plan found");
    }

    if (savedCalories) {
      dailyCalories.value = savedCalories;
      showForm.value = false;
    } else {
      showForm.value = true;
    }
  } catch (error) {
    console.error("Error fetching data:", error);
    showForm.value = true;
  } finally {
    isInitialLoading.value = false;
  }
};

const calculateCalories = async () => {
  if (isSubmitting.value) return;

  if (
    !age.value ||
    !weight.value ||
    !height.value ||
    !activityLevel.value ||
    !goal.value ||
    !sex.value
  ) {
    Swal.fire({
      title: t("calories.alerts.required"),
      icon: "warning",
      confirmButtonColor: "#38b6ff",
    });
    return;
  }

  isSubmitting.value = true;
  loader.start();

  const bmr =
    10 * (weight.value || 0) +
    6.25 * (height.value || 0) -
    5 * (age.value || 0) +
    (sex.value === "male" ? 5 : -161);

  let activityMultiplier = 1.2;
  switch (activityLevel.value) {
    case "light":
      activityMultiplier = 1.375;
      break;
    case "moderate":
      activityMultiplier = 1.55;
      break;
    case "active":
      activityMultiplier = 1.725;
      break;
    case "very-active":
      activityMultiplier = 1.9;
      break;
  }

  dailyCalories.value = bmr * activityMultiplier;
  if (goal.value === "loss") dailyCalories.value -= 500;
  else if (goal.value === "gain") dailyCalories.value += 500;

  const proteinTarget = Math.round((dailyCalories.value * 0.3) / 4);
  const carbsTarget = Math.round((dailyCalories.value * 0.4) / 4);
  const fatTarget = Math.round((dailyCalories.value * 0.3) / 9);

  showForm.value = false;

  try {
    const profileChanged =
      age.value !== originalProfile.value.age ||
      weight.value !== originalProfile.value.weight ||
      height.value !== originalProfile.value.height ||
      sex.value !== originalProfile.value.sex;

    if (profileChanged) {
      await api.post("/auth/profile", {
        age: age.value,
        weight: weight.value,
        height: height.value,
        sex: sex.value,
      });
    }

    await api.post("/goals", {
      goal_type: goal.value,
      activity_level: activityLevel.value,
      target: targetWeight.value || weight.value,
      calories: dailyCalories.value,
      start_weight: weight.value,
      start_date: new Date().toISOString().split("T")[0],
    });

    const mealsData =
      selectedMeals.value.length > 0
        ? selectedMeals.value.map((m) => ({
            name: m.name,
            calories: m.calories,
            type: m.type || "Breakfast",
            protein: (m as any).protein || 0,
            carbs: (m as any).carbs || 0,
            fat: (m as any).fat || 0,
            quantity: (m as any).quantity || 1,
          }))
        : [
            {
              name: "Breakfast",
              calories: 0,
              type: "Breakfast",
              protein: 0,
              carbs: 0,
              fat: 0,
              quantity: 1,
            },
            {
              name: "Lunch",
              calories: 0,
              type: "Lunch",
              protein: 0,
              carbs: 0,
              fat: 0,
              quantity: 1,
            },
            {
              name: "Dinner",
              calories: 0,
              type: "Dinner",
              protein: 0,
              carbs: 0,
              fat: 0,
              quantity: 1,
            },
          ];

    const nutritionPlanData = {
      plan_name: "My Nutrition Plan",
      goal:
        goal.value === "loss"
          ? "cut"
          : goal.value === "gain"
            ? "bulk"
            : "maintain",
      daily_calories: Math.round(dailyCalories.value),
      protein_target: proteinTarget,
      carbs_target: carbsTarget,
      fat_target: fatTarget,
      user_id: userId.value,
      meals: mealsData,
    };

    if (existingNutritionPlanId.value) {
      await api.put(
        `/nutrition-plans/${existingNutritionPlanId.value}`,
        nutritionPlanData,
      );
    } else {
      const response = await api.post("/nutrition-plans", nutritionPlanData);
      existingNutritionPlanId.value = response.data.data?.id || null;
    }

    Swal.fire({
      title: t("calories.alerts.successTitle"),
      text: t("calories.alerts.successText"),
      icon: "success",
      confirmButtonColor: "#38b6ff",
    });
  } catch (error: any) {
    console.error("Error saving data:", error);
    Swal.fire({
      title: t("calories.alerts.errorTitle"),
      text: t("calories.alerts.errorText"),
      icon: "error",
      confirmButtonColor: "#38b6ff",
    });
  } finally {
    isSubmitting.value = false;
    loader.stop();
  }
};

async function fetchInfo() {
  try {
    const response = await api.get("/auth/user-info");
    const profile = response.data;

    userId.value = profile.id;
    age.value = profile.age;
    weight.value = profile.weight;
    height.value = profile.height;
    sex.value = profile.sex;

    originalProfile.value = {
      age: profile.age,
      weight: profile.weight,
      height: profile.height,
      sex: profile.sex,
    };
  } catch (error) {
    console.error("Error fetching profile info:", error);
  }
}

onMounted(() => {
  fetchInfo();
  fetchCalories();
});
</script>
