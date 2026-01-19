<template>
  <div class="about-page">
    <!-- Hero Section -->
    <section class="hero py-5 text-center text-white">
      <h1 class="fw-bold mb-3">{{ $t("about.title") }}</h1>
      <p class="lead mb-4">
        {{ $t("about.subtitle") }}
      </p>
    </section>

    <!-- Features Section -->
    <section class="features py-5">
      <div class="container">
        <h5 class="main-heading mb-5 text-center">
          <i class="fa-solid fa-dumbbell"></i> {{ t("about.featuresTitle") }}
        </h5>
        <div class="row g-4 justify-content-center">
          <div class="col-md-4" v-for="(icon, idx) in featureIcons" :key="idx">
            <div class="feature-card p-4 text-center shadow-sm h-100">
              <i :class="icon + ' fa-2x mb-3'"></i>

              <h5 class="fw-bold mb-2">
                {{ t(`about.features.${idx}.title`) }}
              </h5>
              <p class="text-muted">
                {{ t(`about.features.${idx}.desc`) }}
              </p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="cta-section p-5">
      <div class="container text-center">
        <h2>{{ t("home.cta.heading") }}</h2>
        <p>{{ t("home.cta.text") }}</p>
        <button class="btn btn-lg" @click="router.push('/auth')">
          {{ t("home.cta.button") }}
        </button>
      </div>
    </section>
  </div>
</template>

<script lang="ts" setup>
import { useRouter } from "vue-router";
import { useI18n } from "vue-i18n";
const router = useRouter();
const { t } = useI18n();
const featureIcons = [
  "fas fa-dumbbell",
  "fas fa-apple-alt",
  "fas fa-chart-line",
  "fas fa-bolt",
  "fas fa-shield-alt",
];
</script>

<style scoped lang="scss">
h1:first-of-type {
  color: var(--primary-color);
}
.hero {
  background-image: url("../photos/homePage1.jpg");
  background-size: cover;
  background-position: center;
  min-height: 500px;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.7);
}
</style>
