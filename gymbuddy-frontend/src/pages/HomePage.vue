<template>
  <div class="home-page">
    <div class="sliders p-5">
      <transition name="fade" mode="out-in">
        <div class="slide" :key="currentIndex">
          <img
            :src="slides[currentIndex]?.src"
            :alt="slides[currentIndex]?.alt"
          />
          <div class="slide-text">
            <h1>{{ t(`home.slides.${currentIndex}.title`) }}</h1>
            <p>{{ t(`home.slides.${currentIndex}.subtitle`) }}</p>
          </div>
        </div>
      </transition>
    </div>

    <section class="services p-5">
      <div class="container">
        <h5 class="main-heading">
          <i class="fa-solid fa-dumbbell"></i> {{ t("home.services.heading") }}
        </h5>

        <div class="row justify-content-center g-4">
          <div
            class="col-12 col-md-6 col-lg-4"
            v-for="(service, idx) in services"
            :key="idx"
          >
            <div class="card service-card p-0">
              <img
                :src="service.src"
                class="card-img-top"
                :alt="t(`home.services.cards.${idx}.title`)"
              />
              <div class="card-body">
                <h5 class="card-title">
                  {{ t(`home.services.cards.${idx}.title`) }}
                </h5>
                <p class="card-text">
                  {{ t(`home.services.cards.${idx}.text`) }}
                </p>
                <button class="btn btn-primary" @click="goToPage(service.path)">
                  {{ t(`home.services.cards.${idx}.button`) }}
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="intro p-5">
      <div class="container">
        <div class="row align-items-center g-5">
          <div class="col-lg-6">
            <img src="../photos/intro.jpg" alt="Muscle Hub Introduction" />
          </div>
          <div class="col-lg-6">
            <h2 class="mb-4" v-html="t('home.intro.heading')"></h2>
            <ul>
              <li v-for="fIdx in 5" :key="fIdx">
                <i :class="featureIcons[fIdx]"></i>
                <span>
                  <strong>{{ t(`home.intro.features.${fIdx}.title`) }}</strong>
                  {{ t(`home.intro.features.${fIdx}.text`) }}
                </span>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </section>

    <section class="how-it-works p-5">
      <div class="container text-center">
        <h5 class="main-heading">
          <i class="fa-solid fa-dumbbell"></i>
          {{ t("home.howItWorks.heading") }}
        </h5>

        <div class="row justify-content-center">
          <div
            class="col-md-3 col-sm-6 mb-4"
            v-for="(icon, sIdx) in stepIcons"
            :key="sIdx"
          >
            <div class="work-box">
              <i :class="icon"></i>
              <h5>{{ t(`home.howItWorks.steps.${sIdx}.title`) }}</h5>
              <p>{{ t(`home.howItWorks.steps.${sIdx}.text`) }}</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="dashboard-preview p-5">
      <h5 class="main-heading text-center">
        <i class="fa-solid fa-dumbbell"></i>
        {{ t("home.dashboardPreview.heading") }}
      </h5>
      <div class="container">
        <div class="row justify-content-center g-4">
          <div
            class="col-md-6"
            v-for="(prev, pIdx) in dashboardPreviews"
            :key="pIdx"
          >
            <div class="preview-card">
              <img
                loading="lazy"
                :src="prev.src"
                :alt="t(`home.dashboardPreview.cards.${pIdx}.title`)"
              />
              <div class="text">
                <i :class="prev.icon"></i>
                <h5>{{ t(`home.dashboardPreview.cards.${pIdx}.title`) }}</h5>
                <p>{{ t(`home.dashboardPreview.cards.${pIdx}.text`) }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="testimonials p-5">
      <div class="container">
        <h5 class="main-heading text-center">
          <i class="fa-solid fa-dumbbell"></i>
          {{ t("home.testimonials.heading") }}
        </h5>

        <transition name="fade" mode="out-in">
          <div class="row flex-lg-row-reverse g-5" :key="indexofTesti">
            <div class="col-lg-4">
              <div class="image">
                <img
                  :src="testimonials[indexofTesti]?.src"
                  :alt="testimonials[indexofTesti]?.alt"
                  loading="eager"
                />
              </div>
            </div>

            <div class="col-lg-7 d-flex flex-column justify-content-center">
              <div class="title">
                <h3 class="name">
                  {{ t(`home.testimonials.cards.${indexofTesti}.name`) }}
                </h3>
                <p class="role">
                  {{ t(`home.testimonials.cards.${indexofTesti}.role`) }}
                </p>
              </div>
              <div class="content">
                <p>
                  {{ t(`home.testimonials.cards.${indexofTesti}.content`) }}
                </p>
              </div>
            </div>
          </div>
        </transition>

        <div class="testi-indicators">
          <span
            v-for="(_, idx) in testimonials"
            :key="idx"
            :class="['indicator', { active: idx === indexofTesti }]"
            @click="indexofTesti = idx"
          ></span>
        </div>
      </div>
    </section>

    <section class="choose-plan p-5">
      <div class="container text-center">
        <h5 class="main-heading">
          <i class="fa-solid fa-dumbbell"></i>
          {{ t("home.choosePlan.heading") }}
        </h5>
        <p class="mb-5">{{ t("home.choosePlan.text") }}</p>

        <div class="row justify-content-center">
          <div class="col-md-8 col-lg-6">
            <div class="plan-card">
              <div class="badge">{{ t("home.choosePlan.plan.badge") }}</div>
              <h3 class="plan-title">{{ t("home.choosePlan.plan.title") }}</h3>
              <ul>
                <li v-for="(pFeat, pfIdx) in planIcons" :key="pfIdx">
                  <i :class="pFeat"></i>
                  {{ t(`home.choosePlan.plan.features.${pfIdx}`) }}
                </li>
              </ul>
              <button class="btn btn-primary btn-lg">
                {{ t("home.choosePlan.plan.button") }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="faq p-5">
      <div class="container">
        <h5 class="main-heading text-center">{{ t("home.faq.heading") }}</h5>
        <div class="accordion" id="faqAccordion">
          <div
            class="accordion-item"
            v-for="(item, idx) in tms('home.faq.items')"
            :key="idx"
          >
            <h2 class="accordion-header">
              <button
                class="accordion-button collapsed"
                type="button"
                data-bs-toggle="collapse"
                :data-bs-target="'#collapse' + idx"
                aria-expanded="false"
              >
                {{ rt(item.question) }}
              </button>
            </h2>
            <div
              :id="'collapse' + idx"
              class="accordion-collapse collapse"
              data-bs-parent="#faqAccordion"
            >
              <div class="accordion-body">{{ rt(item.answer) }}</div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="blog p-5">
      <div class="container">
        <h5 class="main-heading text-center">
          <i class="fa-solid fa-dumbbell"></i> {{ t("home.blog.heading") }}
        </h5>
        <div class="row g-4 justify-content-center">
          <div
            class="col-md-6 col-lg-4"
            v-for="(post, idx) in blogPosts"
            :key="idx"
          >
            <div class="card h-100">
              <img :src="post.src" :alt="post.alt" class="card-img-top" />
              <div class="card-body">
                <h5 class="card-title">
                  {{ t(`home.blog.posts[${idx}].title`) }}
                </h5>
                <p class="card-text">
                  {{ t(`home.blog.posts[${idx}].excerpt`) }}
                </p>
                <a
                  :href="post.link"
                  target="_blank"
                  class="btn btn-primary text-white"
                >
                  {{ t("home.blog.button") }}
                </a>
              </div>
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
import { ref, onMounted, onUnmounted } from "vue";
import { useRouter } from "vue-router";
import { useUserStore } from "../stores/userStore";
import { useI18n } from "vue-i18n";

// استيراد الصور
import homePage1 from "../photos/homePage1.jpg";
import homePage2 from "../photos/homePage2.jpg";
import homePage3 from "../photos/homePage3.jpg";
import card1 from "../photos/card1.jpg";
import card2 from "../photos/card2.jpg";
import card3 from "../photos/card3.jpg";
import cardWork from "../photos/cardwork.jpg";
import cardProfile from "../photos/cardprofile.jpg";
import Nutiration from "../photos/cardNutrition.jpg";
import Progress from "../photos/cardProgress.jpg";
import testimonials1 from "../photos/Testimonials1.jpg";
import testimonials2 from "../photos/Testimonials2.jpg";
import testimonials3 from "../photos/Testimonials3.jpg";

const { t, tm, rt } = useI18n();
const tms = tm as any;
const router = useRouter();
const userStore = useUserStore();

// إعداد السلايدر
const slides = [
  { src: homePage1, alt: "Slide 1" },
  { src: homePage2, alt: "Slide 2" },
  { src: homePage3, alt: "Slide 3" },
];

// الخدمات
const services = [
  { src: card1, path: "/exercise" },
  { src: card2, path: "/nutrition" },
  { src: card3, path: "/progress" },
];

// أيقونات المقدمة
const featureIcons = [
  "fas fa-bolt",
  "fas fa-brain",
  "fas fa-chart-line",
  "fas fa-fire-alt",
  "fas fa-shield-alt",
];

// أيقونات كيف يعمل النظام
const stepIcons = [
  "fas fa-user-plus",
  "fas fa-sliders-h",
  "fas fa-dumbbell",
  "fas fa-chart-line",
];

// معاينة لوحة التحكم
const dashboardPreviews = [
  { src: cardWork, icon: "fas fa-dumbbell" },
  { src: Nutiration, icon: "fas fa-apple-alt" },
  { src: Progress, icon: "fas fa-chart-line" },
  { src: cardProfile, icon: "fas fa-user" },
];

// آراء العملاء
const testimonials = [
  { src: testimonials1, alt: "testi1" },
  { src: testimonials2, alt: "testi2" },
  { src: testimonials3, alt: "testi3" },
];

// أيقونات الخطة
const planIcons = [
  "fas fa-dumbbell",
  "fas fa-apple-alt",
  "fas fa-chart-line",
  "fas fa-trophy",
];

// المدونة
const blogPosts = [
  { src: homePage3, alt: "blog1", link: "https://www.healthline.com/fitness" },
  {
    src: Nutiration,
    alt: "blog2",
    link: "https://www.healthline.com/nutrition",
  },
  { src: Progress, alt: "blog3", link: "https://www.menshealth.com/fitness" },
];

// المتغيرات التفاعلية
const currentIndex = ref(0);
const indexofTesti = ref(0);
let intervalId: any = null;

const goToPage = (path: string) => {
  if (userStore.user) {
    router.push(path);
  } else {
    router.push({ path: "/auth", query: { redirect: path } });
  }
};

const changeSlider = () => {
  intervalId = setInterval(() => {
    currentIndex.value = (currentIndex.value + 1) % slides.length;
  }, 5000);
};

onMounted(() => {
  changeSlider();
});

onUnmounted(() => {
  if (intervalId) clearInterval(intervalId);
});
</script>

<style scoped>
/* أضف هنا الـ CSS الخاص بك للـ fade transition والـ indicators */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.5s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
