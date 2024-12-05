<script lang="ts">
  export let portfolio: {
    theme_color: string;
    user: {
      full_name: string;
      email: string;
    };
  };

  const currentYear = new Date().getFullYear();
</script>

<footer class="bg-white/80 backdrop-blur-sm mt-20">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
      <!-- Contact Info -->
      <div>
        <h3 class="text-lg font-semibold mb-4" style="color: {portfolio.theme_color}">
          Contact
        </h3>
        <p class="text-gray-600">{portfolio.user.email}</p>
      </div>

      <!-- Quick Links -->
      <div>
        <h3 class="text-lg font-semibold mb-4" style="color: {portfolio.theme_color}">
          Quick Links
        </h3>
        <ul class="space-y-2">
          <li>
            <a href="#about" class="text-gray-600 hover:text-gray-900 transition-colors">About</a>
          </li>
          <li>
            <a href="#skills" class="text-gray-600 hover:text-gray-900 transition-colors">Skills</a>
          </li>
          <li>
            <a href="#projects" class="text-gray-600 hover:text-gray-900 transition-colors">Projects</a>
          </li>
          <li>
            <a href="#contact" class="text-gray-600 hover:text-gray-900 transition-colors">Contact</a>
          </li>
        </ul>
      </div>

      <!-- Copyright -->
      <div>
        <h3 class="text-lg font-semibold mb-4" style="color: {portfolio.theme_color}">
          Legal
        </h3>
        <p class="text-gray-600">
          © {currentYear} {portfolio.user.full_name}.<br>
          All rights reserved.
        </p>
      </div>
    </div>
  </div>
</footer>