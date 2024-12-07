<script lang="ts">
  import { onMount } from 'svelte';
  
  const features = [
    {
      icon: '🎨',
      title: 'Professional Templates',
      description: 'Choose from a variety of carefully crafted templates designed for different professions'
    },
    {
      icon: '⚡',
      title: 'Easy Customization',
      description: 'Personalize your portfolio with an intuitive interface and real-time preview'
    },
    {
      icon: '📱',
      title: 'Responsive Design',
      description: 'Your portfolio looks perfect on all devices, from mobile to desktop'
    },
    {
      icon: '🔍',
      title: 'SEO Optimized',
      description: 'Get discovered easily with search engine optimized portfolio pages'
    },
    {
      icon: '🛠️',
      title: 'Project Showcase',
      description: 'Highlight your best work with dedicated project sections and galleries'
    },
    {
      icon: '🔄',
      title: 'Regular Updates',
      description: 'Stay current with continuously updated templates and features'
    }
  ];
</script>

<div class="min-h-screen">
  <!-- Hero Section -->
  <div class="bg-gradient-to-r from-blue-600 to-blue-800 text-white">
    <div class="container mx-auto px-4 py-20">
      <div class="max-w-3xl mx-auto text-center">
        <h1 class="text-5xl font-bold mb-6">Create Your Professional Portfolio</h1>
        <p class="text-xl mb-8 text-blue-100">
          Showcase your work with a beautifully designed portfolio website. Easy to customize, professional templates.
        </p>
        <div class="flex justify-center gap-4">
          <a 
            href="/create-portfolio" 
            class="px-8 py-3 bg-white text-blue-600 rounded-lg font-semibold hover:bg-blue-50 transition"
          >
            Create Portfolio
          </a>
          <a 
            href="/templates" 
            class="px-8 py-3 border-2 border-white text-white rounded-lg font-semibold hover:bg-white/10 transition"
          >
            View Templates
          </a>
        </div>
      </div>
    </div>
  </div>

  <!-- Features Section -->
  <div class="py-20 bg-gray-50">
    <div class="container mx-auto px-4">
      <h2 class="text-3xl font-bold text-center mb-12">Everything You Need</h2>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 max-w-6xl mx-auto">
        {#each features as feature}
          <div class="bg-white p-6 rounded-lg shadow-sm hover:shadow-md transition">
            <div class="text-3xl mb-4">{feature.icon}</div>
            <h3 class="text-xl font-semibold mb-2">{feature.title}</h3>
            <p class="text-gray-600">{feature.description}</p>
          </div>
        {/each}
      </div>
    </div>
  </div>

  <!-- CTA Section -->
  <div class="bg-white py-20">
    <div class="container mx-auto px-4 text-center">
      <h2 class="text-3xl font-bold mb-4">Ready to Build Your Portfolio?</h2>
      <p class="text-xl text-gray-600 mb-8 max-w-2xl mx-auto">
        Join thousands of professionals who trust us with their online presence. Start building your portfolio today.
      </p>
      <a 
        href="/register" 
        class="inline-block px-8 py-3 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition"
      >
        Get Started Free
      </a>
    </div>
  </div>
</div>
