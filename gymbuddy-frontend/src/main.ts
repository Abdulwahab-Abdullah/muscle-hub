import { createApp } from "vue";
import { createPinia } from "pinia";
import App from "./App.vue";
import router from "./router/index";
import i18n from "./i18n";
import { useUserStore } from "./stores/userStore";
import api from "./lib/axios";

// ✅ Styles
import "bootstrap/dist/css/bootstrap.min.css";
import "bootstrap/dist/js/bootstrap.bundle.min.js";
import "normalize.css";
import "@fortawesome/fontawesome-free/css/all.min.css";
import "./assets/scss/main.css";

const app = createApp(App);
app.use(createPinia());
app.use(router);
app.use(i18n); // ✅ إضافة i18n
app.mount("#app");

// Load user from localStorage
const userStore = useUserStore();
userStore.loadUser();

// Init CSRF cookie for SPA
(async () => {
  try {
    await api.get("http://localhost:8000/sanctum/csrf-cookie");
    // console.log("✅ CSRF cookie set");
  } catch (error) {
    console.error("❌ Failed to set CSRF cookie", error);
  }
})();
