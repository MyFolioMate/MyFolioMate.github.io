<script lang="ts">
	import '../app.css';
	import { page } from '$app/stores';
	
	// Check if it's a portfolio route
	$: isMainRoute = $page.url.pathname === '/' || 
	                 $page.url.pathname.startsWith('/explore') ||
	                 $page.url.pathname.startsWith('/profile') ||
	                 $page.url.pathname.startsWith('/templates') ||
	                 $page.url.pathname.startsWith('/privacy') ||
	                 $page.url.pathname.startsWith('/terms') ||
	                 $page.url.pathname.startsWith('/login') ||
	                 $page.url.pathname.startsWith('/register');
</script>

{#if isMainRoute}
  <div class="flex flex-col min-h-screen">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm">
      <div class="container mx-auto px-4">
        <div class="flex justify-between items-center h-16">
          <a href="/" class="text-xl font-bold text-gray-800">Portfolio Hub</a>
          <div class="flex items-center gap-6">
            <a href="/explore" class="text-gray-600 hover:text-gray-900">Explore</a>
            <a href="/templates" class="text-gray-600 hover:text-gray-900">Templates</a>
            <a href="/profile" class="text-gray-600 hover:text-gray-900">Profile</a>
            <a 
              href="/register" 
              class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
            >
              Get Started
            </a>
          </div>
        </div>
      </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-grow">
      <slot />
    </main>

    <!-- Simplified Footer -->
    <footer class="bg-gray-800 text-gray-400 py-8">
      <div class="container mx-auto px-4">
        <div class="flex justify-between items-center">
          <p>© {new Date().getFullYear()} Portfolio Hub. All rights reserved.</p>
          <div class="flex gap-6">
            <a href="/privacy" class="hover:text-white">Privacy</a>
            <a href="/terms" class="hover:text-white">Terms</a>
          </div>
        </div>
      </div>
    </footer>
  </div>
{:else}
  <slot />
{/if}
