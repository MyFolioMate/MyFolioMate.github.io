<script lang="ts">
  import { onMount } from 'svelte';
  
  let portfolios = [];
  let testimonials = [
    { name: 'Sarah Chen', role: 'UX Designer', text: 'This platform helped me showcase my work effectively to potential clients.' },
    { name: 'James Miller', role: 'Frontend Developer', text: 'The best portfolio builder for developers. Clean and professional.' },
    { name: 'Maria Garcia', role: 'Graphic Designer', text: 'Simple to use yet powerful. Exactly what I needed.' }
  ];
  
  onMount(async () => {
    // Fetch featured portfolios
  });
</script>

<div class="container mx-auto px-4 py-8">
  <!-- Hero Section -->
  <div class="text-center mb-16">
    <h1 class="text-5xl font-bold mb-4">Create Your Professional Portfolio</h1>
    <p class="text-xl text-gray-600 mb-8">Stand out from the crowd with a stunning portfolio website</p>
    <div class="flex justify-center gap-4">
      <a href="/register" class="bg-blue-500 text-white px-8 py-3 rounded-lg hover:bg-blue-600 transition">
        Get Started Free
      </a>
      <a href="#features" class="border border-gray-300 px-8 py-3 rounded-lg hover:bg-gray-50 transition">
        Learn More
      </a>
    </div>
  </div>

  <!-- Features Section -->
  <div id="features" class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-16">
    <div class="p-6 border rounded-lg">
      <div class="text-blue-500 text-2xl mb-4">📱</div>
      <h3 class="text-xl font-semibold mb-2">Responsive Design</h3>
      <p class="text-gray-600">Your portfolio looks great on all devices, from mobile to desktop.</p>
    </div>
    <div class="p-6 border rounded-lg">
      <div class="text-blue-500 text-2xl mb-4">🎨</div>
      <h3 class="text-xl font-semibold mb-2">Customizable Themes</h3>
      <p class="text-gray-600">Choose from various themes or create your own unique style.</p>
    </div>
    <div class="p-6 border rounded-lg">
      <div class="text-blue-500 text-2xl mb-4">⚡</div>
      <h3 class="text-xl font-semibold mb-2">Easy to Use</h3>
      <p class="text-gray-600">Set up your portfolio in minutes with our intuitive interface.</p>
    </div>
  </div>

  <!-- Featured Portfolios -->
  <h2 class="text-3xl font-bold mb-8">Featured Portfolios</h2>
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-16">
    <!-- Featured portfolios -->
    <div class="bg-gray-50 p-4 rounded-lg text-center">
      <p class="text-gray-500">Featured portfolios coming soon...</p>
    </div>
  </div>

  <!-- Testimonials -->
  <h2 class="text-3xl font-bold mb-8">What Our Users Say</h2>
  <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-16">
    {#each testimonials as testimonial}
      <div class="bg-white p-6 rounded-lg shadow-sm border">
        <p class="text-gray-600 mb-4">"{testimonial.text}"</p>
        <div class="font-semibold">{testimonial.name}</div>
        <div class="text-sm text-gray-500">{testimonial.role}</div>
      </div>
    {/each}
  </div>

  <!-- CTA Section -->
  <div class="bg-blue-50 rounded-xl p-8 text-center mb-16">
    <h2 class="text-3xl font-bold mb-4">Ready to Create Your Portfolio?</h2>
    <p class="text-gray-600 mb-6">Join thousands of professionals who trust us with their online presence</p>
    <a href="/register" class="bg-blue-500 text-white px-8 py-3 rounded-lg hover:bg-blue-600 inline-block transition">
      Create Your Portfolio
    </a>
  </div>
</div>
