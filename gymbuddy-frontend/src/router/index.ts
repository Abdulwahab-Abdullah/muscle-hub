import { createRouter, createWebHistory } from "vue-router";
import api from "../lib/axios";
import { useUserStore } from "../stores/userStore";
import i18n from "../i18n";

const router = createRouter({
  history: createWebHistory(),
  routes: [
    {
      path: "/",
      redirect: () => {
        const locale = i18n.global.locale.value;
        return `/${locale}/home`;
      },
    },

    // English Routes
    {
      path: "/en/home",
      name: "HomeEn",
      component: () => import("../pages/HomePage.vue"),
      meta: { title: "Home", locale: "en" },
    },
    {
      path: "/en/auth",
      name: "AuthEn",
      component: () => import("../pages/AuthPage.vue"),
      meta: { title: "Auth", locale: "en" },
    },
    {
      path: "/en/forgot-password",
      name: "ForgotPasswordEn",
      component: () => import("../pages/ForgotPassword.vue"),
      meta: { title: "Forgot Password", locale: "en" },
    },
    {
      path: "/en/about",
      name: "AboutEn",
      component: () => import("../pages/AboutAndFeatursPage.vue"),
      meta: { title: "About & Features", locale: "en" },
    },
    {
      path: "/en/contact",
      name: "ContactEn",
      component: () => import("../pages/ContactPage.vue"),
      meta: { title: "Contact", locale: "en" },
    },
    {
      path: "/en/dashboard",
      name: "DashboardEn",
      component: () => import("../pages/DashboardPage.vue"),
      meta: { requiresAuth: true, title: "Dashboard", locale: "en" },
    },
    {
      path: "/en/profile",
      name: "ProfileEn",
      component: () => import("../pages/ProfilePage.vue"),
      meta: { requiresAuth: true, title: "Profile", locale: "en" },
    },
    {
      path: "/en/exercise",
      name: "ExerciseEn",
      component: () => import("../pages/ExercisePage.vue"),
      meta: { requiresAuth: true, title: "Exercise", locale: "en" },
    },
    {
      path: "/en/my-workouts",
      name: "MyWorkoutsEn",
      component: () => import("../pages/MyWorkouts.vue"),
      meta: { requiresAuth: true, title: "My Workouts", locale: "en" },
    },
    {
      path: "/en/calculate-calories",
      name: "CalorieCalculatorEn",
      component: () => import("../pages/CalCaloriesPage.vue"),
      meta: { requiresAuth: true, title: "Calories Calculator", locale: "en" },
    },
    {
      path: "/en/nutrition",
      name: "NutritionEn",
      component: () => import("../pages/NutritionPage.vue"),
      meta: { requiresAuth: true, title: "Nutrition", locale: "en" },
    },
    {
      path: "/en/progress",
      name: "ProgressEn",
      component: () => import("../pages/ProgressPage.vue"),
      meta: { requiresAuth: true, title: "Progress", locale: "en" },
    },
    {
      path: "/en/settings",
      name: "SettingsEn",
      component: () => import("../pages/SettingsPage.vue"),
      meta: { requiresAuth: true, title: "Settings", locale: "en" },
    },
    {
      path: "/en/ai-chat",
      name: "AiChatEn",
      component: () => import("../pages/AiChat.vue"),
      meta: { requiresAuth: true, title: "AI Chat", locale: "en" },
    },

    // Arabic Routes
    {
      path: "/ar/home",
      name: "HomeAr",
      component: () => import("../pages/HomePage.vue"),
      meta: { title: "الرئيسية", locale: "ar" },
    },
    {
      path: "/ar/auth",
      name: "AuthAr",
      component: () => import("../pages/AuthPage.vue"),
      meta: { title: "تسجيل الدخول", locale: "ar" },
    },
    {
      path: "/ar/forgot-password",
      name: "ForgotPasswordAr",
      component: () => import("../pages/ForgotPassword.vue"),
      meta: { title: "نسيت كلمة المرور", locale: "ar" },
    },
    {
      path: "/ar/about",
      name: "AboutAr",
      component: () => import("../pages/AboutAndFeatursPage.vue"),
      meta: { title: "عن ماسل هب", locale: "ar" },
    },
    {
      path: "/ar/contact",
      name: "ContactAr",
      component: () => import("../pages/ContactPage.vue"),
      meta: { title: "اتصل بنا", locale: "ar" },
    },
    {
      path: "/ar/dashboard",
      name: "DashboardAr",
      component: () => import("../pages/DashboardPage.vue"),
      meta: { requiresAuth: true, title: "لوحة التحكم", locale: "ar" },
    },
    {
      path: "/ar/profile",
      name: "ProfileAr",
      component: () => import("../pages/ProfilePage.vue"),
      meta: { requiresAuth: true, title: "الملف الشخصي", locale: "ar" },
    },
    {
      path: "/ar/exercise",
      name: "ExerciseAr",
      component: () => import("../pages/ExercisePage.vue"),
      meta: { requiresAuth: true, title: "التمارين", locale: "ar" },
    },
    {
      path: "/ar/my-workouts",
      name: "MyWorkoutsAr",
      component: () => import("../pages/MyWorkouts.vue"),
      meta: { requiresAuth: true, title: "تماريني", locale: "ar" },
    },
    {
      path: "/ar/calculate-calories",
      name: "CalorieCalculatorAr",
      component: () => import("../pages/CalCaloriesPage.vue"),
      meta: { requiresAuth: true, title: "حاسبة السعرات", locale: "ar" },
    },
    {
      path: "/ar/nutrition",
      name: "NutritionAr",
      component: () => import("../pages/NutritionPage.vue"),
      meta: { requiresAuth: true, title: "التغذية", locale: "ar" },
    },
    {
      path: "/ar/progress",
      name: "ProgressAr",
      component: () => import("../pages/ProgressPage.vue"),
      meta: { requiresAuth: true, title: "التقدم", locale: "ar" },
    },
    {
      path: "/ar/settings",
      name: "SettingsAr",
      component: () => import("../pages/SettingsPage.vue"),
      meta: { requiresAuth: true, title: "الإعدادات", locale: "ar" },
    },
    {
      path: "/ar/ai-chat",
      name: "AiChatAr",
      component: () => import("../pages/AiChat.vue"),
      meta: { requiresAuth: true, title: "المساعد الذكي", locale: "ar" },
    },

    // Fallback for old routes (redirect to locale-prefixed versions)
    {
      path: "/home",
      redirect: () => `/${i18n.global.locale.value}/home`,
    },
    {
      path: "/auth",
      redirect: () => `/${i18n.global.locale.value}/auth`,
    },
    {
      path: "/forgot-password",
      redirect: () => `/${i18n.global.locale.value}/forgot-password`,
    },
    {
      path: "/about",
      redirect: () => `/${i18n.global.locale.value}/about`,
    },
    {
      path: "/contact",
      redirect: () => `/${i18n.global.locale.value}/contact`,
    },
    {
      path: "/dashboard",
      redirect: () => `/${i18n.global.locale.value}/dashboard`,
    },
    {
      path: "/profile",
      redirect: () => `/${i18n.global.locale.value}/profile`,
    },
    {
      path: "/exercise",
      redirect: () => `/${i18n.global.locale.value}/exercise`,
    },
    {
      path: "/my-workouts",
      redirect: () => `/${i18n.global.locale.value}/my-workouts`,
    },
    {
      path: "/calculate-calories",
      redirect: () => `/${i18n.global.locale.value}/calculate-calories`,
    },
    {
      path: "/nutrition",
      redirect: () => `/${i18n.global.locale.value}/nutrition`,
    },
    {
      path: "/progress",
      redirect: () => `/${i18n.global.locale.value}/progress`,
    },
    {
      path: "/settings",
      redirect: () => `/${i18n.global.locale.value}/settings`,
    },
    {
      path: "/ai-chat",
      redirect: () => `/${i18n.global.locale.value}/ai-chat`,
    },
  ],
});

/* 🔐 Route Guard */
router.beforeEach(async (to, from, next) => {
  const userStore = useUserStore();

  // تحديث اللغة بناءً على الرابط
  if (to.meta.locale) {
    i18n.global.locale.value = to.meta.locale as string;
    localStorage.setItem("locale", to.meta.locale as string);

    // تحديث اتجاه الصفحة
    document.documentElement.dir = to.meta.locale === "ar" ? "rtl" : "ltr";
    document.documentElement.lang = to.meta.locale as string;
  }

  // إذا الصفحة تتطلب تسجيل دخول
  if (to.meta.requiresAuth) {
    try {
      if (userStore.user) return next();
      const response = await api.get("/api/user");
      userStore.setUser(response.data);
      return next();
    } catch (error: any) {
      userStore.clearUser();
      const locale = to.meta.locale || i18n.global.locale.value;
      return next({
        path: `/${locale}/auth`,
        query: { redirect: to.fullPath },
      });
    }
  }

  // إذا المستخدم مسجل دخول وحاول يدخل صفحة عامة
  if (userStore.user && !to.meta.requiresAuth) {
    const currentLocale = to.meta.locale || i18n.global.locale.value;
    const allowedPaths = [
      `/${currentLocale}/dashboard`,
      `/${currentLocale}/profile`,
      `/${currentLocale}/exercise`,
    ];

    if (!allowedPaths.includes(to.path)) {
      return next(`/${currentLocale}/dashboard`);
    }
  }

  next();
});

router.afterEach((to) => {
  document.title = to.meta.title
    ? `${to.meta.title} | Muscle Hub`
    : "Muscle Hub";
});

export default router;
