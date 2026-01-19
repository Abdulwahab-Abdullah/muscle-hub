<template>
  <div
    class="settings-page page-custom"
    :class="{ rtl: $i18n.locale === 'ar' }"
  >
    <div class="container py-4">
      <div class="text-center mb-4">
        <h1 class="fw-bold">{{ $t("settings.title") }}</h1>
        <p class="text-muted">{{ $t("settings.subtitle") }}</p>
      </div>

      <div v-if="isLoading" class="loading-container">
        <div class="spinner"></div>
        <p>{{ $t("settings.loading") }}</p>
      </div>

      <template v-else>
        <div class="settings-section">
          <h2 class="section-title">
            <i class="fa-solid fa-user me-2"></i>
            {{ $t("settings.sections.profile") }}
          </h2>
          <div class="settings-card">
            <div class="profile-photo-section">
              <div class="photo-preview">
                <img v-if="profilePhoto" :src="profilePhoto" alt="Profile" />
                <div v-else class="photo-placeholder">
                  <i class="fa-solid fa-user"></i>
                </div>
              </div>
              <div class="photo-actions">
                <button
                  class="btn-upload"
                  @click="triggerFileInput"
                  :disabled="isUploadingPhoto"
                >
                  <i
                    class="fa-solid"
                    :class="
                      isUploadingPhoto ? 'fa-spinner fa-spin' : 'fa-camera'
                    "
                  ></i>
                  {{
                    isUploadingPhoto
                      ? $t("settings.actions.uploading")
                      : profilePhoto
                        ? $t("settings.actions.changePhoto")
                        : $t("settings.actions.uploadPhoto")
                  }}
                </button>

                <input
                  ref="fileInput"
                  type="file"
                  accept="image/*"
                  @change="handlePhotoUpload"
                  style="display: none"
                />
                <button
                  v-if="profilePhoto"
                  class="btn-remove"
                  @click="removePhoto"
                  :disabled="isDeletingPhoto"
                >
                  <i
                    class="fa-solid"
                    :class="isDeletingPhoto ? 'fa-spinner fa-spin' : 'fa-trash'"
                  ></i>
                  {{
                    isDeletingPhoto
                      ? $t("settings.actions.removing")
                      : $t("settings.actions.removePhoto")
                  }}
                </button>
              </div>
            </div>

            <div class="form-group">
              <label>{{ $t("settings.labels.fullName") }}</label>
              <input v-model="profile.name" type="text" disabled />
              <small class="text-muted">{{
                $t("settings.placeholders.nameChangeNote")
              }}</small>
            </div>

            <div class="form-group">
              <label>{{ $t("settings.labels.email") }}</label>
              <input v-model="profile.email" type="email" disabled />
              <small class="text-muted">{{
                $t("settings.placeholders.emailChangeNote")
              }}</small>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label>{{ $t("settings.labels.age") }}</label>
                <input
                  v-model.number="profile.age"
                  type="number"
                  min="14"
                  max="100"
                />
              </div>
              <div class="form-group">
                <label>{{ $t("settings.labels.height") }}</label>
                <input
                  v-model.number="profile.height"
                  type="number"
                  step="0.1"
                />
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label>{{ $t("settings.labels.weight") }}</label>
                <input
                  v-model.number="profile.weight"
                  type="number"
                  step="0.1"
                />
              </div>
              <div class="form-group">
                <label>{{ $t("settings.labels.gender") }}</label>
                <select v-model="profile.sex">
                  <option value="">
                    {{ $t("settings.labels.selectGender") }}
                  </option>
                  <option value="male">{{ $t("settings.labels.male") }}</option>
                  <option value="female">
                    {{ $t("settings.labels.female") }}
                  </option>
                </select>
              </div>
            </div>

            <button class="btn-save" @click="saveProfile" :disabled="isSaving">
              <i class="fa-solid fa-check"></i>
              {{
                isSaving
                  ? $t("settings.actions.saving")
                  : $t("settings.actions.saveProfile")
              }}
            </button>
          </div>
        </div>

        <div class="settings-section">
          <h2 class="section-title">
            <i class="fa-solid fa-bullseye me-2"></i>
            {{ $t("settings.sections.goals") }}
          </h2>
          <div class="settings-card">
            <div class="form-group">
              <label>{{ $t("settings.labels.goalType") }}</label>
              <select v-model="goals.goal_type">
                <option value="">{{ $t("settings.labels.selectGoal") }}</option>
                <option value="loss">{{ $t("calories.form.loss") }}</option>
                <option value="maintain">
                  {{ $t("calories.form.maintain") }}
                </option>
                <option value="gain">{{ $t("calories.form.gain") }}</option>
              </select>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label>{{ $t("settings.labels.startWeight") }}</label>
                <input
                  v-model.number="goals.start_weight"
                  type="number"
                  step="0.1"
                />
                <small class="text-muted">{{
                  $t("settings.placeholders.startWeightNote")
                }}</small>
              </div>
              <div class="form-group">
                <label>{{ $t("settings.labels.targetWeight") }}</label>
                <input v-model.number="goals.target" type="number" step="0.1" />
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label>{{ $t("settings.labels.dailyCalories") }}</label>
                <input v-model.number="goals.calories" type="number" />
              </div>
              <div class="form-group">
                <label>{{ $t("settings.labels.activityLevel") }}</label>
                <select v-model="goals.activity_level">
                  <option value="">
                    {{ $t("settings.labels.selectLevel") }}
                  </option>
                  <option value="sedentary">
                    {{ $t("calories.form.sedentary") }}
                  </option>
                  <option value="light">{{ $t("calories.form.light") }}</option>
                  <option value="moderate">
                    {{ $t("calories.form.moderate") }}
                  </option>
                  <option value="active">
                    {{ $t("calories.form.active") }}
                  </option>
                </select>
              </div>
            </div>

            <div class="form-group">
              <label>{{ $t("settings.labels.startDate") }}</label>
              <input v-model="goals.start_date" type="date" :max="today" />
              <small class="text-muted">{{
                $t("settings.placeholders.startDateNote")
              }}</small>
            </div>

            <button
              class="btn-save"
              @click="saveGoals"
              :disabled="isSavingGoals"
            >
              <i class="fa-solid fa-check"></i>
              {{
                isSavingGoals
                  ? $t("settings.actions.updating")
                  : $t("settings.actions.updateGoals")
              }}
            </button>
          </div>
        </div>

        <div class="settings-section">
          <h2 class="section-title">
            <i class="fa-solid fa-bell me-2"></i>
            {{ $t("settings.sections.notifications") }}
          </h2>
          <div class="settings-card">
            <div class="toggle-option">
              <div class="toggle-info">
                <h4>{{ $t("settings.notifications.mealReminders") }}</h4>
                <p>{{ $t("settings.notifications.mealRemindersDesc") }}</p>
              </div>
              <label class="toggle-switch">
                <input v-model="notifications.mealReminders" type="checkbox" />
                <span class="slider"></span>
              </label>
            </div>

            <div class="toggle-option">
              <div class="toggle-info">
                <h4>{{ $t("settings.notifications.progressUpdates") }}</h4>
                <p>{{ $t("settings.notifications.progressUpdatesDesc") }}</p>
              </div>
              <label class="toggle-switch">
                <input
                  v-model="notifications.progressUpdates"
                  type="checkbox"
                />
                <span class="slider"></span>
              </label>
            </div>

            <div class="toggle-option">
              <div class="toggle-info">
                <h4>{{ $t("settings.notifications.goalAchievements") }}</h4>
                <p>{{ $t("settings.notifications.goalAchievementsDesc") }}</p>
              </div>
              <label class="toggle-switch">
                <input
                  v-model="notifications.goalAchievements"
                  type="checkbox"
                />
                <span class="slider"></span>
              </label>
            </div>

            <button class="btn-save" @click="saveNotifications">
              <i class="fa-solid fa-check"></i>
              {{ $t("settings.actions.savePreferences") }}
            </button>
          </div>
        </div>

        <div class="settings-section">
          <h2 class="section-title">
            <i class="fa-solid fa-lock me-2"></i>
            {{ $t("settings.sections.security") }}
          </h2>
          <div class="settings-card">
            <div class="form-group">
              <label>{{ $t("settings.labels.currentPassword") }}</label>
              <input v-model="security.currentPassword" type="password" />
            </div>
            <div class="form-group">
              <label>{{ $t("settings.labels.newPassword") }}</label>
              <input v-model="security.newPassword" type="password" />
            </div>
            <div class="form-group">
              <label>{{ $t("settings.labels.confirmPassword") }}</label>
              <input v-model="security.confirmPassword" type="password" />
            </div>

            <button
              class="btn-save"
              @click="changePassword"
              :disabled="isChangingPassword"
            >
              <i class="fa-solid fa-key"></i>
              {{
                isChangingPassword
                  ? $t("settings.actions.updating")
                  : $t("settings.actions.changePassword")
              }}
            </button>
          </div>
        </div>

        <div class="settings-section">
          <h2 class="section-title danger">
            <i class="fa-solid fa-exclamation-triangle me-2"></i>
            {{ $t("settings.sections.danger") }}
          </h2>
          <div class="settings-card danger-card">
            <div class="danger-option">
              <div>
                <h4>{{ $t("settings.danger.clearMealsTitle") }}</h4>
                <p>{{ $t("settings.danger.clearMealsDesc") }}</p>
              </div>
              <button
                class="btn-danger"
                @click="clearTodayMeals"
                :disabled="isClearing"
              >
                <i class="fa-solid fa-trash"></i>
                {{
                  isClearing
                    ? $t("settings.actions.removing")
                    : $t("settings.actions.clearMeals")
                }}
              </button>
            </div>

            <div class="danger-option">
              <div>
                <h4>{{ $t("settings.danger.logoutTitle") }}</h4>
                <p>{{ $t("settings.danger.logoutDesc") }}</p>
              </div>
              <button
                class="btn-danger"
                @click="logout"
                :disabled="isLoggingOut"
              >
                <i class="fa-solid fa-right-from-bracket"></i>
                {{
                  isLoggingOut
                    ? $t("settings.actions.updating")
                    : $t("settings.actions.logout")
                }}
              </button>
            </div>
          </div>
        </div>
      </template>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { ref, onMounted, computed } from "vue";
import { useRouter } from "vue-router";
import Swal from "sweetalert2";
import api from "../lib/axios";

const router = useRouter();

// Loading States
const isLoading = ref(true);
const isSaving = ref(false);
const isSavingGoals = ref(false);
const isChangingPassword = ref(false);
const isClearing = ref(false);
const isLoggingOut = ref(false);
const isUploadingPhoto = ref(false);
const isDeletingPhoto = ref(false);

// Profile Data
const profile = ref({
  name: "",
  email: "",
  age: null,
  height: null,
  weight: null,
  sex: "",
});

// Profile Photo
const profilePhoto = ref<string | null>(null);
const fileInput = ref<HTMLInputElement | null>(null);

// Goals Data
const goals = ref({
  goal_type: "",
  activity_level: "",
  target: null,
  calories: null,
  start_weight: null,
  start_date: "",
});

// Security
const security = ref({
  currentPassword: "",
  newPassword: "",
  confirmPassword: "",
});

// Notifications
const notifications = ref({
  mealReminders: true,
  progressUpdates: true,
  goalAchievements: true,
});

// Computed
const today = computed(() => new Date().toISOString().split("T")[0]);

// ===== Photo Functions =====
const triggerFileInput = () => {
  if (!isUploadingPhoto.value) {
    fileInput.value?.click();
  }
};

const handlePhotoUpload = async (event: Event) => {
  const target = event.target as HTMLInputElement;
  const file = target.files?.[0];
  if (!file) return;

  // Validation
  if (file.size > 5 * 1024 * 1024) {
    return Swal.fire("Error", "File must be less than 5MB", "error");
  }
  if (!file.type.startsWith("image/")) {
    return Swal.fire("Error", "Please upload an image file", "error");
  }

  isUploadingPhoto.value = true;

  try {
    // إنشاء FormData لإرسال الملف
    const formData = new FormData();
    formData.append("avatar", file);

    // إرسال الصورة للباك إند
    const res = await api.post("/auth/upload-avatar", formData, {
      headers: {
        "Content-Type": "multipart/form-data",
      },
    });

    if (res.data.success) {
      // حفظ رابط الصورة من الباك إند
      profilePhoto.value = res.data.avatar_url;

      // تخزين في localStorage للاستخدام السريع
      localStorage.setItem("profilePhoto", profilePhoto.value);

      // تحديث الصورة في الـ Navbar
      window.dispatchEvent(
        new CustomEvent("profilePhotoUpdated", {
          detail: { newPhotoUrl: profilePhoto.value },
        }),
      );

      Swal.fire({
        title: "Success",
        text: "Photo uploaded successfully! 📸",
        icon: "success",
        timer: 2000,
        showConfirmButton: false,
      });
    }
  } catch (err: any) {
    console.error("Photo upload error:", err);
    Swal.fire(
      "Error",
      err.response?.data?.message || "Failed to upload photo ❌",
      "error",
    );
  } finally {
    isUploadingPhoto.value = false;
    // مسح الـ input file عشان نقدر نرفع نفس الصورة مرة ثانية
    if (fileInput.value) {
      fileInput.value.value = "";
    }
  }
};

const removePhoto = async () => {
  const result = await Swal.fire({
    title: "Are you sure?",
    text: "Do you want to remove your profile photo?",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Yes, remove it",
    cancelButtonText: "Cancel",
    confirmButtonColor: "#dc3545",
  });

  if (!result.isConfirmed) return;

  isDeletingPhoto.value = true;

  try {
    // حذف من السيرفر
    const res = await api.delete("/auth/delete-avatar");

    if (res.data.success) {
      // مسح من الـ state و localStorage
      profilePhoto.value = null;
      localStorage.removeItem("profilePhoto");

      // تحديث الـ Navbar
      window.dispatchEvent(
        new CustomEvent("profilePhotoUpdated", {
          detail: { newPhotoUrl: null },
        }),
      );

      Swal.fire({
        title: "Removed!",
        text: "Photo removed successfully 🗑️",
        icon: "success",
        timer: 2000,
        showConfirmButton: false,
      });
    }
  } catch (err: any) {
    console.error("Remove photo error:", err);
    Swal.fire(
      "Error",
      err.response?.data?.message || "Failed to remove photo",
      "error",
    );
  } finally {
    isDeletingPhoto.value = false;
  }
};

// ===== Fetch Data =====
const fetchUserInfo = async () => {
  try {
    const res = await api.get("/auth/user-info");

    profile.value = {
      name: res.data.name || "",
      email: res.data.email || "",
      age: res.data.age || null,
      height: res.data.height || null,
      weight: res.data.weight || null,
      sex: res.data.sex || "",
    };

    // جلب صورة الملف الشخصي من الباك إند
    if (res.data.photo) {
      profilePhoto.value = res.data.photo;
      // حفظها في localStorage للاستخدام السريع لاحقاً
      localStorage.setItem("profilePhoto", res.data.photo);
    } else {
      // لو ما في صورة في الباك إند، شيل من localStorage
      localStorage.removeItem("profilePhoto");
      profilePhoto.value = null;
    }
  } catch (err) {
    console.error("Failed to fetch user info:", err);
    Swal.fire("Error", "Failed to load user information", "error");
  }
};

const fetchGoals = async () => {
  try {
    const res = await api.get("/goals");
    if (res.data.success && res.data.goal) {
      const goalData = res.data.goal;
      goals.value = {
        goal_type: goalData.goal_type || "",
        activity_level: goalData.activity_level || "",
        target: goalData.target || null,
        calories: goalData.calories || null,
        start_weight: goalData.start_weight || null,
        start_date: goalData.start_date || "",
      };
    }
  } catch (err) {
    console.error("Failed to fetch goals:", err);
  }
};

// ===== Save Profile =====
const saveProfile = async () => {
  isSaving.value = true;
  try {
    const payload: any = {};
    if (profile.value.age !== null) payload.age = profile.value.age;
    if (profile.value.height !== null) payload.height = profile.value.height;
    if (profile.value.weight !== null) payload.weight = profile.value.weight;
    if (profile.value.sex) payload.sex = profile.value.sex;

    const res = await api.post("/auth/profile", payload);

    if (res.data.success) {
      Swal.fire({
        title: "Success",
        text: "Profile updated successfully! ✅",
        icon: "success",
        timer: 2000,
        showConfirmButton: false,
      });
    } else {
      Swal.fire(
        "Error",
        res.data.message || "Failed to update profile",
        "error",
      );
    }
  } catch (err: any) {
    console.error("Save profile error:", err);
    if (err.response?.status === 429) {
      Swal.fire("Error", "Too many update attempts. Try again later.", "error");
    } else {
      Swal.fire(
        "Error",
        err.response?.data?.message || "Failed to update profile ❌",
        "error",
      );
    }
  } finally {
    isSaving.value = false;
  }
};

// ===== Save Goals =====
const saveGoals = async () => {
  if (!goals.value.goal_type)
    return Swal.fire("Warning", "Please select a goal type! ⚠️", "warning");

  isSavingGoals.value = true;
  try {
    const payload: any = { goal_type: goals.value.goal_type };
    if (goals.value.activity_level)
      payload.activity_level = goals.value.activity_level;
    if (goals.value.target !== null) payload.target = goals.value.target;
    if (goals.value.calories !== null) payload.calories = goals.value.calories;
    if (goals.value.start_weight !== null)
      payload.start_weight = goals.value.start_weight;
    if (goals.value.start_date) payload.start_date = goals.value.start_date;

    const res = await api.post("/goals", payload);
    if (res.data.success) {
      Swal.fire({
        title: "Success",
        text: "Goals updated successfully! 🎯",
        icon: "success",
        timer: 2000,
        showConfirmButton: false,
      });
    } else {
      Swal.fire("Error", res.data.message || "Failed to update goals", "error");
    }
  } catch (err: any) {
    console.error("Save goals error:", err);
    Swal.fire(
      "Error",
      err.response?.data?.message || "Failed to update goals ❌",
      "error",
    );
  } finally {
    isSavingGoals.value = false;
  }
};

// ===== Save Notifications =====
const saveNotifications = () => {
  localStorage.setItem("notifications", JSON.stringify(notifications.value));
  Swal.fire({
    title: "Success",
    text: "Notification preferences saved! 🔔",
    icon: "success",
    timer: 2000,
    showConfirmButton: false,
  });
};

// ===== Change Password =====
const changePassword = async () => {
  if (!security.value.currentPassword)
    return Swal.fire("Warning", "Enter current password! ⚠️", "warning");
  if (!security.value.newPassword)
    return Swal.fire("Warning", "Enter new password! ⚠️", "warning");
  if (security.value.newPassword.length < 6)
    return Swal.fire(
      "Warning",
      "Password must be at least 6 characters! ⚠️",
      "warning",
    );
  if (security.value.newPassword !== security.value.confirmPassword)
    return Swal.fire("Error", "Passwords don't match! ❌", "error");

  const result = await Swal.fire({
    title: "Are you sure?",
    text: "Change your password?",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Yes, change it",
    cancelButtonText: "Cancel",
  });
  if (!result.isConfirmed) return;

  isChangingPassword.value = true;
  try {
    const res = await api.post("/auth/change-password", {
      current_password: security.value.currentPassword,
      new_password: security.value.newPassword,
      new_password_confirmation: security.value.confirmPassword,
    });

    if (res.data.success) {
      Swal.fire({
        title: "Success",
        text: "Password changed successfully! 🔐",
        icon: "success",
        timer: 2000,
        showConfirmButton: false,
      });
      security.value = {
        currentPassword: "",
        newPassword: "",
        confirmPassword: "",
      };
    } else {
      Swal.fire(
        "Error",
        res.data.message || "Failed to change password ❌",
        "error",
      );
    }
  } catch (err: any) {
    console.error("Change password error:", err);
    if (err.response?.status === 404)
      Swal.fire(
        "Info",
        "Password change not available yet. Contact support. 💬",
        "info",
      );
    else if (err.response?.status === 401)
      Swal.fire("Error", "Current password is incorrect! ❌", "error");
    else
      Swal.fire(
        "Error",
        err.response?.data?.message || "Failed to change password ❌",
        "error",
      );
  } finally {
    isChangingPassword.value = false;
  }
};

// ===== Clear Today's Meals =====
const clearTodayMeals = async () => {
  const result = await Swal.fire({
    title: "Are you sure?",
    text: "Delete all meals logged today? This cannot be undone.",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Yes, clear them",
    cancelButtonText: "Cancel",
    confirmButtonColor: "#dc3545",
  });
  if (!result.isConfirmed) return;

  isClearing.value = true;
  try {
    const plansRes = await api.get("/nutrition-plans");
    const plans = plansRes.data;
    if (!plans || plans.length === 0) {
      return Swal.fire("Error", "No nutrition plan found", "error");
    }

    const planId = plans[0].id;
    await api.delete(`/nutrition-plans/${planId}/meals-today`);

    Swal.fire({
      title: "Success",
      text: "Today's meals cleared successfully! 🗑️",
      icon: "success",
      timer: 2000,
      showConfirmButton: false,
    });
  } catch (err: any) {
    console.error("Clear meals error:", err);
    Swal.fire(
      "Error",
      err.response?.data?.message || "Failed to clear meals ❌",
      "error",
    );
  } finally {
    isClearing.value = false;
  }
};

// ===== Logout =====
const logout = async () => {
  const result = await Swal.fire({
    title: "Are you sure?",
    text: "Do you want to logout?",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Yes, logout",
    cancelButtonText: "Cancel",
  });
  if (!result.isConfirmed) return;

  isLoggingOut.value = true;
  try {
    await api.post("/auth/logout");
    localStorage.clear();
    Swal.fire({
      title: "Logged out",
      text: "You have been logged out successfully 👋",
      icon: "success",
      timer: 2000,
      showConfirmButton: false,
    });
    setTimeout(() => {
      router.push("/login");
    }, 1000);
  } catch (err) {
    console.error("Logout error:", err);
    localStorage.clear();
    router.push("/login");
  } finally {
    isLoggingOut.value = false;
  }
};

// ===== Load Notifications =====
const loadNotifications = () => {
  const saved = localStorage.getItem("notifications");
  if (saved) {
    try {
      notifications.value = JSON.parse(saved);
    } catch {
      console.error("Failed to parse notifications");
    }
  }
};

// ===== Initialize =====
onMounted(async () => {
  isLoading.value = true;
  try {
    await Promise.all([fetchUserInfo(), fetchGoals()]);
    loadNotifications();
  } catch (err) {
    console.error("Error loading settings:", err);
    Swal.fire("Error", "Failed to load settings", "error");
  } finally {
    isLoading.value = false;
  }
});
</script>
