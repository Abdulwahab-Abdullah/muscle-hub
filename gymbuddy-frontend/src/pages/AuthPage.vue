<template>
  <div class="auth-wrapper p-3">
    <div class="auth-card">
      <!-- Toggle Buttons -->
      <div class="auth-toggle mb-4">
        <button
          class="btn"
          :class="[
            activeTab === 'login' ? 'btn-primary' : 'btn-outline-primary',
            'me-2',
          ]"
          @click="activeTab = 'login'"
        >
          {{ t("authPage.login") }}
        </button>
        <button
          class="btn"
          :class="[
            activeTab === 'register' ? 'btn-primary' : 'btn-outline-primary',
          ]"
          @click="activeTab = 'register'"
        >
          {{ t("authPage.signup") }}
        </button>
      </div>

      <!-- Show the correct form -->
      <LoginForm v-if="activeTab === 'login'" />
      <RegisterForm v-else />
    </div>
  </div>
</template>

<script setup lang="ts">
import { onMounted, ref, watch } from "vue";
import { useI18n } from "vue-i18n";
import LoginForm from "../components/auth/LoginForm.vue";
import RegisterForm from "../components/auth/RegisterForm.vue";

const { t } = useI18n();
const activeTab = ref<"login" | "register">("login");

onMounted(() => {
  const savedTab = localStorage.getItem("activeTab") as
    | "login"
    | "register"
    | null;
  if (savedTab) activeTab.value = savedTab;
});

watch(activeTab, (newVal: string) => {
  localStorage.setItem("activeTab", newVal);
});
</script>
