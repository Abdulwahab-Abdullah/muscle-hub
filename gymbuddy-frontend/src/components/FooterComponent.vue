<template>
  <footer class="footer py-5 text-white text-center">
    <div class="container">
      <div class="row">
        <!-- Logo & Description -->
        <div
          class="col-md-4 mb-2 mb-md-0 d-flex flex-column align-items-center justify-content-center"
        >
          <h3 class="fw-bold position-relative">
            <i class="fa-solid fa-dumbbell position-absolute"></i>
            {{ $t("logo.appName") }}
          </h3>
          <p class="mb-1">
            {{ $t("footer.description") }}
          </p>
        </div>

        <!-- Social Media -->
        <div class="col-md-4 mb-3">
          <h5 class="fw-bold mb-2">{{ $t("footer.followUs") }}</h5>
          <ul
            class="list-unstyled d-flex justify-content-between mb-0 w-50 mx-auto"
          >
            <li>
              <a href="#" class="facebook"><i class="fab fa-facebook-f"></i></a>
            </li>
            <li>
              <a href="#" class="instagram"><i class="fab fa-instagram"></i></a>
            </li>
            <li>
              <a href="#" class="twitter"><i class="fab fa-x-twitter"></i></a>
            </li>
            <li>
              <a href="#" class="linkedin"
                ><i class="fab fa-linkedin-in"></i
              ></a>
            </li>
          </ul>
        </div>
        <!-- Info -->
        <div class="col-md-4 mt-md-0">
          <h5 class="fw-bold mb-2">{{ $t("footer.infoTitle") }}</h5>
          <div class="text-white-50">
            <p class="mb-1">{{ $t("footer.ownerName") }}</p>
            <p class="mb-1">+966506481744</p>
            <p>abdulwahabyahya1@gmail.com</p>
          </div>
        </div>
      </div>

      <hr class="my-4" style="border-color: rgba(255, 255, 255, 0.2)" />

      <div class="text-center">
        <p class="mb-0">{{ $t("footer.copyright") }}</p>
      </div>
    </div>
  </footer>
</template>

<style scoped lang="scss">
h3 {
  width: fit-content;
  i {
    left: -2rem;
    top: 50%;
    transform: translateY(-50%);
    font-size: 1.5rem;
  }
}
</style>
