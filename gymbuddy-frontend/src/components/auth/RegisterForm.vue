<template>
  <div class="d-flex justify-content-center align-items-center vh-100">
    <div class="auth-card">
      <h2 class="text-center mb-4">{{ t("authRegister.title") }}</h2>
      <form @submit.prevent="handleRegister">
        <div class="mb-3">
          <label for="name" class="form-label">{{
            t("authRegister.name")
          }}</label>
          <input
            type="text"
            id="name"
            v-model="form.name"
            class="form-control"
            :placeholder="t('authRegister.namePlaceholder')"
            required
            min="3"
          />
        </div>

        <div v-if="nameLengthError" class="alert alert-danger">
          {{ t("authRegister.errors.nameTooShort") }}
        </div>

        <div class="mb-3">
          <label for="email" class="form-label">{{
            t("authRegister.email")
          }}</label>
          <input
            type="email"
            id="email"
            v-model="form.email"
            class="form-control"
            :placeholder="t('authRegister.emailPlaceholder')"
            required
          />
        </div>

        <div class="mb-3">
          <label for="password" class="form-label">{{
            t("authRegister.password")
          }}</label>
          <input
            type="password"
            id="password"
            v-model="form.password"
            class="form-control"
            :placeholder="t('authRegister.passwordPlaceholder')"
            required
            min="6"
          />
        </div>

        <div v-if="passwordsLengthError" class="alert alert-danger">
          {{ t("authRegister.errors.passwordTooShort") }}
        </div>

        <div class="mb-3">
          <label for="confirmPassword" class="form-label">
            {{ t("authRegister.confirmPassword") }}
          </label>
          <input
            type="password"
            id="confirmPassword"
            v-model="form.confirmPassword"
            class="form-control"
            :placeholder="t('authRegister.confirmPasswordPlaceholder')"
            required
          />
        </div>

        <div v-if="unmatchedPasswordError" class="alert alert-danger">
          {{ t("authRegister.errors.passwordsDoNotMatch") }}
        </div>

        <button type="submit" class="btn btn-primary w-100">
          {{ t("authRegister.registerButton") }}
        </button>
      </form>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { ref, watch } from "vue";
import { useI18n } from "vue-i18n";
import { useRouter } from "vue-router";
import Swal from "sweetalert2";
import { useUserStore } from "../../stores/userStore";
import api from "../../lib/axios";
import axios from "axios";

const { t, locale } = useI18n();
const router = useRouter();
const userStore = useUserStore();

const form = ref({
  name: "",
  email: "",
  password: "",
  confirmPassword: "",
});

const unmatchedPasswordError = ref(false);
const passwordsLengthError = ref(false);
const nameLengthError = ref(false);

watch(
  () => form.value.name,
  (name) => {
    nameLengthError.value = name.length > 0 && name.length < 3;
  },
);

watch(
  () => form.value.password,
  (password) => {
    passwordsLengthError.value = password.length > 0 && password.length < 6;
  },
);

watch(
  () => form.value.confirmPassword,
  (confirmPassword) => {
    unmatchedPasswordError.value = form.value.password !== confirmPassword;
  },
);

async function handleRegister() {
  if (
    nameLengthError.value ||
    passwordsLengthError.value ||
    unmatchedPasswordError.value
  ) {
    return;
  }

  try {
    const res = await api.post("/auth/register", {
      name: form.value.name,
      email: form.value.email,
      password: form.value.password,
      password_confirmation: form.value.confirmPassword,
    });

    if (res.data.token) {
      localStorage.setItem("auth_token", res.data.token);
    }

    userStore.setUser(res.data.user);

    Swal.fire({
      icon: "success",
      title: t("authRegister.successTitle"),
      text: "Welcome to Muscle Hub 💪",
      confirmButtonColor: "#38b6ff",
    });

    router.push(`/${locale.value}/dashboard`);
  } catch (error) {
    if (axios.isAxiosError(error)) {
      Swal.fire({
        icon: "error",
        title: t("authRegister.failedTitle"),
        text: error.response?.data?.message || t("authRegister.genericError"),
      });
    }
  }
}
</script>
