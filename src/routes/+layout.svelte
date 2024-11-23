<script lang="ts">
	import '../app.css';
	let { children } = $props();
</script>

<div class="min-h-screen flex flex-col">
  <!-- Header -->
  <header class="bg-white shadow-sm">
    <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16">
      <div class="flex justify-between items-center h-full">
        <div class="flex items-center">
          <a href="/" class="text-xl font-bold text-gray-900">Portfolio Hub</a>
          <div class="hidden md:flex items-center space-x-8 ml-10">
            <a href="/explore" class="text-gray-600 hover:text-gray-900">Explore</a>
            <a href="/templates" class="text-gray-600 hover:text-gray-900">Templates</a>
            <!-- <a href="/pricing" class="text-gray-600 hover:text-gray-900">Pricing</a> -->
          </div>
        </div>
        <div class="flex items-center space-x-4">
          <a href="/profile" class="px-4 py-2 text-gray-700 hover:text-gray-900">Profile</a>
          <button class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
            Get Started
          </button>
        </div>
      </div>
    </nav>
  </header>

  <!-- Main content -->
  <main class="flex-grow bg-gray-50">
    <slot />
  </main>

  <!-- Footer -->
  <footer class="bg-gray-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
        <div>
          <h3 class="text-white text-lg font-semibold mb-4">Portfolio Hub</h3>
          <p class="text-gray-400">Create and showcase your professional portfolio with ease.</p>
        </div>
        <div>
          <h4 class="text-white text-sm font-semibold mb-4">Product</h4>
          <ul class="space-y-2">
            <li><a href="/features" class="text-gray-400 hover:text-white">Features</a></li>
            <li><a href="/templates" class="text-gray-400 hover:text-white">Templates</a></li>
            <li><a href="/pricing" class="text-gray-400 hover:text-white">Pricing</a></li>
          </ul>
        </div>
        <div>
          <h4 class="text-white text-sm font-semibold mb-4">Company</h4>
          <ul class="space-y-2">
            <li><a href="/about" class="text-gray-400 hover:text-white">About</a></li>
            <li><a href="/blog" class="text-gray-400 hover:text-white">Blog</a></li>
            <li><a href="/careers" class="text-gray-400 hover:text-white">Careers</a></li>
          </ul>
        </div>
        <div>
          <h4 class="text-white text-sm font-semibold mb-4">Support</h4>
          <ul class="space-y-2">
            <li><a href="/help" class="text-gray-400 hover:text-white">Help Center</a></li>
            <li><a href="/contact" class="text-gray-400 hover:text-white">Contact</a></li>
            <li><a href="/privacy" class="text-gray-400 hover:text-white">Privacy</a></li>
          </ul>
        </div>
      </div>
      <div class="mt-8 pt-8 border-t border-gray-700">
        <p class="text-gray-400 text-sm text-center">
          © {new Date().getFullYear()} Portfolio Hub. All rights reserved.
        </p>
      </div>
    </div>
  </footer>
</div>
