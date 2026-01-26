<template>
  <nav class="navbar navbar-expand-lg homepage-navbar sticky-top">
    <div class="container">
      <router-link
        :to="`/${locale}/home`"
        class="navbar-brand d-flex align-items-center"
      >
        <img class="logo" src="../../public/Muscle.png" alt="Logo" />
        <div v-html="t('logo.navbar')" class="logo-text ms-2 text-light"></div>
      </router-link>

      <button
        class="navbar-toggler"
        type="button"
        data-bs-toggle="collapse"
        data-bs-target="#navbarSupportedContent"
        aria-controls="navbarSupportedContent"
        aria-expanded="false"
        aria-label="Toggle navigation"
      >
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mx-auto mb-lg-0 align-items-center">
          <li class="nav-item">
            <router-link
              :to="`/${locale}/home`"
              class="nav-link text-nowrap px-3"
              exact-active-class="active"
            >
              {{ t("navbar.home") }}
            </router-link>
          </li>
          <li class="nav-item">
            <router-link
              :to="`/${locale}/auth`"
              class="nav-link text-nowrap px-3"
              exact-active-class="active"
            >
              {{ t("navbar.loginRegister") }}
            </router-link>
          </li>
          <li class="nav-item">
            <router-link
              :to="`/${locale}/about`"
              class="nav-link text-nowrap px-3"
              exact-active-class="active"
            >
              {{ t("navbar.aboutFeatures") }}
            </router-link>
          </li>
          <li class="nav-item">
            <router-link
              :to="`/${locale}/contact`"
              class="nav-link text-nowrap px-3"
              exact-active-class="active"
            >
              {{ t("navbar.contactUs") }}
            </router-link>
          </li>
        </ul>

        <div class="nav-controls d-flex align-items-center gap-3">
          <LanguageSwitcher />
          <DarkModeToggle />
        </div>
      </div>
    </div>
  </nav>
</template>

<style scoped>
.nav-controls {
  padding: 0.75rem 1rem;
  margin: 0 auto;
  width: fit-content;
}
</style>

<script setup lang="ts">
import { useI18n } from "vue-i18n";
import DarkModeToggle from "./DarkModeToggle.vue";
import LanguageSwitcher from "./LanguageSwitcher.vue";

const { t, locale } = useI18n();
</script>
