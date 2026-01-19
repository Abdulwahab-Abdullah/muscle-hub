<template>
  <div class="auth-wrapper">
    <div class="auth-card">
      <h2 class="text-center mb-4">{{ t("authLogin.title") }}</h2>
      <p class="text-center text-muted mb-4">
        {{ t("authLogin.subtitle") }}
      </p>

      <form @submit.prevent="handleLogin">
        <!-- Email -->
        <div class="mb-3">
          <label for="email" class="form-label">{{
            t("authLogin.email")
          }}</label>
          <input
            type="email"
            id="email"
            v-model="email"
            class="form-control"
            :placeholder="t('authLogin.emailPlaceholder')"
            required
          />
        </div>

        <!-- Password -->
        <div class="mb-3">
          <label for="password" class="form-label">{{
            t("authLogin.password")
          }}</label>
          <input
            type="password"
            id="password"
            v-model="password"
            class="form-control"
            :placeholder="t('authLogin.passwordPlaceholder')"
            required
          />
        </div>

        <!-- Remember Me -->
        <div class="form-check mb-3">
          <input
            type="checkbox"
            class="form-check-input"
            id="rememberMe"
            v-model="rememberMe"
          />
          <label class="form-check-label" for="rememberMe">
            {{ t("authLogin.rememberMe") }}
          </label>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn btn-primary w-100">
          {{ t("authLogin.loginButton") }}
        </button>
      </form>

      <div class="text-center my-3">
        <router-link
          :to="`/${$i18n.locale}/forgot-password`"
          class="text-muted small text-decoration-none"
        >
          {{ t("authPage.forgotPassword") }}
        </router-link>
      </div>
    </div>
  </div>
</template>

<script lang="ts" setup>
import axios from "axios";
import { ref } from "vue";
import { useI18n } from "vue-i18n";
import { useRouter } from "vue-router";
import Swal from "sweetalert2";
import { useUserStore } from "../../stores/userStore";
import api from "../../lib/axios";

const { t, locale } = useI18n();
const router = useRouter();
const email = ref("");
const password = ref("");
const rememberMe = ref(false);
const userStore = useUserStore();

async function handleLogin() {
  try {
    const res = await api.post("/auth/login", {
      email: email.value,
      password: password.value,
      remember_me: rememberMe.value,
    });

    if (res.data.token) {
      localStorage.setItem("auth_token", res.data.token);
    }

    userStore.setUser(res.data.user);

    Swal.fire({
      icon: "success",
      title: t("authLogin.successTitle"),
      text: res.data.message,
      confirmButtonColor: "#3085d6",
    });

    router.push(`/${locale.value}/dashboard`);
  } catch (error) {
    if (axios.isAxiosError(error)) {
      Swal.fire({
        icon: "error",
        title: t("authLogin.failedTitle"),
        text: error.response?.data.message || t("authLogin.genericError"),
      });
    }
  }
}
</script>
