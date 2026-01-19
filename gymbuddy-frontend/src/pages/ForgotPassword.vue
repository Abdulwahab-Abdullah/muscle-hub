<template>
  <div class="auth-wrapper p-3">
    <div class="auth-card">
      <!-- Header -->
      <div class="text-center mb-4">
        <h3 class="mb-2">{{ t("forgotPassword.title") }}</h3>
        <p class="text-muted">{{ t("forgotPassword.subtitle") }}</p>
      </div>

      <!-- Info Card -->
      <div class="alert alert-info border-0 shadow-sm">
        <div class="d-flex align-items-start">
          <i class="bi bi-info-circle-fill fs-4 me-3"></i>
          <div>
            <h6 class="alert-heading mb-2">
              {{ t("forgotPassword.notAvailable") }}
            </h6>
            <p class="mb-2 small">
              {{ t("forgotPassword.description") }}
            </p>
            <ul class="small mb-0 ps-3">
              <li>{{ t("forgotPassword.reason1") }}</li>
              <li>{{ t("forgotPassword.reason2") }}</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Contact Admin Card -->
      <div class="card border-primary mt-3">
        <div class="card-body">
          <h6 class="card-title">
            <i class="bi bi-headset me-2"></i>
            {{ t("forgotPassword.contactTitle") }}
          </h6>
          <p class="card-text small text-muted mb-3">
            {{ t("forgotPassword.contactDescription") }}
          </p>
          <a
            href="mailto:musclehub@mscle.com"
            class="btn btn-sm btn-primary text-white"
          >
            <i class="bi bi-envelope me-2"></i>
            {{ t("forgotPassword.contactButton") }}
          </a>
        </div>
      </div>

      <!-- Back to Login -->
      <div class="text-center mt-4">
        <button class="btn btn-outline-secondary" @click="goBack">
          <i class="bi bi-arrow-left me-2"></i>
          {{ t("forgotPassword.backToLogin") }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useI18n } from "vue-i18n";
import { useRouter } from "vue-router";

const { t, locale } = useI18n();
const router = useRouter();

const goBack = () => {
  router.push(`/${locale.value}/auth`);
};
</script>

<style scoped>
.auth-wrapper {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(
    135deg,
    var(--primary-color) 0%,
    var(--primary-dark) 100%
  );
  transition: background 0.3s ease;
}

.auth-card {
  background: var(--card-bg);
  border-radius: 12px;
  box-shadow: var(--shadow-lg);
  padding: 2rem;
  width: 100%;
  max-width: 480px;
  border: 1px solid var(--card-border);
  transition: all 0.3s ease;
}

.auth-card:hover {
  box-shadow: var(--card-hover-shadow);
}

.alert-info {
  background-color: var(--status-info-bg);
  border: 1px solid var(--primary-light);
  color: var(--text-color);
  border-radius: 8px;
}

.alert-info i {
  color: var(--primary-color);
}

.card-title {
  color: var(--primary-color);
  font-size: 1rem;
  font-weight: 600;
}

.card {
  background: var(--card-bg);
  border: 1px solid var(--card-border);
  transition: all 0.3s ease;
}

.card:hover {
  border-color: var(--primary-color);
  transform: translateY(-2px);
}

.btn-primary {
  background-color: var(--btn-primary-bg);
  border-color: var(--btn-primary-bg);
  transition: all 0.3s ease;
}

.btn-primary:hover {
  background-color: var(--btn-primary-hover);
  border-color: var(--btn-primary-hover);
  transform: translateY(-2px);
  box-shadow: var(--shadow-md);
}

.btn-outline-secondary {
  color: var(--text-color);
  border-color: var(--border-color);
  transition: all 0.3s ease;
}

.btn-outline-secondary:hover {
  background-color: var(--primary-light);
  border-color: var(--primary-color);
  color: var(--primary-color);
  transform: translateY(-2px);
}

.text-muted {
  color: var(--text-muted) !important;
}

h3 {
  color: var(--heading-color);
  font-weight: 700;
}

p {
  color: var(--text-color);
}

.small {
  color: var(--text-muted);
}

.alert-heading {
  color: var(--text-color);
  font-weight: 600;
}

/* RTL Support */
[dir="rtl"] .me-2 {
  margin-right: 0 !important;
  margin-left: 0.5rem !important;
}

[dir="rtl"] .me-3 {
  margin-right: 0 !important;
  margin-left: 1rem !important;
}

[dir="rtl"] .ps-3 {
  padding-left: 0 !important;
  padding-right: 1rem !important;
}
</style>
