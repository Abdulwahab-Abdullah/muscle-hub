<template>
  <div class="contact-page">
    <!-- Hero Section -->
    <section class="hero py-5 text-center">
      <h5 class="main-heading mb-5 text-center">
        <i class="fa-solid fa-dumbbell"></i>
        {{ t("contact.title") }}
      </h5>
      <p class="lead text-muted mb-2">
        {{ t("contact.subtitle") }}
      </p>
    </section>

    <!-- Contact Form -->
    <section class="contact-form pb-5 pt-0">
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-md-8">
            <form @submit.prevent="handleSubmit" class="p-4 shadow-sm rounded">
              <div class="mb-3">
                <label class="form-label">
                  {{ t("contact.form.name") }}
                </label>
                <input
                  type="text"
                  v-model="form.name"
                  class="form-control"
                  required
                />
              </div>

              <div class="mb-3">
                <label class="form-label">
                  {{ t("contact.form.email") }}
                </label>
                <input
                  type="email"
                  v-model="form.email"
                  class="form-control"
                  required
                />
              </div>

              <div class="mb-3">
                <label class="form-label">
                  {{ t("contact.form.message") }}
                </label>
                <textarea
                  v-model="form.message"
                  class="form-control"
                  rows="5"
                  required
                  @input="limitWords"
                ></textarea>

                <div class="text-end mt-1">
                  <small :class="isOverLimit ? 'text-danger' : 'text-muted'">
                    {{ wordCount }} / 2000
                  </small>
                </div>
              </div>

              <button type="submit" class="btn btn-primary w-100">
                {{ t("contact.form.send") }}
                <i class="fas fa-paper-plane ms-2"></i>
              </button>
            </form>
          </div>
        </div>
      </div>
    </section>

    <!-- Contact Info -->
    <section class="contact-info py-5">
      <div class="container text-center">
        <h5 class="main-heading mb-5 text-center">
          <i class="fa-solid fa-dumbbell"></i>
          {{ t("contact.infoTitle") }}
        </h5>

        <p><i class="fas fa-map-marker-alt me-2"></i> Riyadh, Saudi Arabia</p>
        <p><i class="fas fa-envelope me-2"></i> support@musclehub.com</p>
        <p><i class="fas fa-phone me-2"></i> +966 555 123 456</p>
      </div>
    </section>
  </div>
</template>

<script lang="ts" setup>
import { ref, computed } from "vue";
import api from "../lib/axios";
import Swal from "sweetalert2";
import { useI18n } from "vue-i18n";

const MAX_WORDS = 2000;

const { t } = useI18n();

const form = ref({
  name: "",
  email: "",
  message: "",
});

const wordCount = computed(() => {
  if (!form.value.message) return 0;

  return form.value.message.trim().split(/\s+/).filter(Boolean).length;
});

const isOverLimit = computed(() => wordCount.value > MAX_WORDS);
const successMessage = ref("");

function limitWords() {
  const words = form.value.message.trim().split(/\s+/).filter(Boolean);

  if (words.length > MAX_WORDS) {
    form.value.message = words.slice(0, MAX_WORDS).join(" ");
  }
}
async function handleSubmit() {
  if (wordCount.value > MAX_WORDS) {
    Swal.fire(
      "Message Too Long",
      "Please limit your message to 2000 words.",
      "warning",
    );
    return;
  }
  if (wordCount.value <= 2) {
    Swal.fire(
      "Message Too Short",
      "Please enter at least 2 words in your message.",
      "warning",
    );
    return;
  }

  try {
    await api.post("/contact", form.value);

    Swal.fire(
      "Message Sent",
      "Thank you for contacting us! We'll get back to you shortly.",
      "success",
    );

    form.value = { name: "", email: "", message: "" };
  } catch (error) {
    Swal.fire(
      "Submission Failed",
      "There was an error sending your message. Please try again later.",
      "error",
    );
  }
}
</script>
