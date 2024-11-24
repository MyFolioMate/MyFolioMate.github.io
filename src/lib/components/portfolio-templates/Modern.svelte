<script lang="ts">
  import { onMount } from 'svelte';
  export let portfolio;
  export let projects;
  
  let mounted = false;
  
  onMount(() => {
    mounted = true;
  });
</script>

<style>
  .animated-bg {
    background: linear-gradient(45deg, 
      var(--theme-color)05, 
      var(--theme-color)10, 
      var(--theme-color)05
    );
    background-size: 400% 400%;
    animation: gradientBG 15s ease infinite;
  }
  
  @keyframes gradientBG {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
  }
  
  .project-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
  }
  
  .project-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1);
  }
  
  .skill-tag {
    transition: transform 0.2s ease;
  }
  
  .skill-tag:hover {
    transform: scale(1.05);
  }
</style>

<div class="min-h-screen bg-gray-100">
  <div class="max-w-6xl mx-auto px-4 py-16">
    <!-- Hero Section -->
    <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-xl p-12 mb-12 fade-in">
      <h1 class="text-5xl font-bold mb-6" style="color: {portfolio.theme_color}">
        {portfolio.title || 'My Portfolio'}
      </h1>
      <p class="text-xl text-gray-600 mb-8">{portfolio.about}</p>
      {#if portfolio.social_links}
        <div class="flex space-x-4">
          {#each portfolio.social_links.split(',') as link}
            <a 
              href={link.trim()} 
              target="_blank" 
              class="text-gray-500 hover:text-gray-900 transition-colors"
              style="color: {portfolio.theme_color}"
            >
              {link.includes('linkedin') ? 'LinkedIn' : 
               link.includes('github') ? 'GitHub' : 
               link.includes('twitter') ? 'Twitter' : 'Link'}
            </a>
          {/each}
        </div>
      {/if}
    </div>

    <!-- Skills Section -->
    <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-xl p-8 mb-12 slide-up">
      <h2 class="text-3xl font-bold mb-6" style="color: {portfolio.theme_color}">Skills</h2>
      <div class="flex flex-wrap gap-4">
        {#if portfolio.skills}
          {#each portfolio.skills as skill}
            <span 
              class="px-4 py-2 rounded-full text-white transition-opacity hover:opacity-90"
              style="background-color: {portfolio.theme_color}"
            >
              {skill}
            </span>
          {/each}
        {/if}
      </div>
    </div>

    <!-- Experience & Education -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
      <div class="bg-white rounded-2xl shadow-xl p-8">
        <h2 class="text-3xl font-bold mb-6" style="color: {portfolio.theme_color}">Education</h2>
        <div class="prose text-gray-600">
          {@html portfolio.education || 'No education listed yet.'}
        </div>
      </div>

      <div class="bg-white rounded-2xl shadow-xl p-8">
        <h2 class="text-3xl font-bold mb-6" style="color: {portfolio.theme_color}">Achievements</h2>
        <div class="prose text-gray-600">
          {@html portfolio.achievements || 'No achievements listed yet.'}
        </div>
      </div>
    </div>

    <!-- Projects -->
    <div class="bg-white rounded-2xl shadow-xl p-8 mb-12">
      <h2 class="text-3xl font-bold mb-8" style="color: {portfolio.theme_color}">Projects</h2>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        {#each projects as project}
          <div class="bg-gray-50 rounded-xl p-6 hover:shadow-lg transition-all duration-300">
            <h3 class="text-xl font-bold mb-3">{project.title}</h3>
            <p class="text-gray-600 mb-4">{project.description}</p>
            <a 
              href={project.project_url} 
              target="_blank" 
              class="inline-flex items-center transition-colors"
              style="color: {portfolio.theme_color}"
            >
              View Project →
            </a>
          </div>
        {/each}
      </div>
    </div>

    <!-- Contact -->
    <div class="bg-white rounded-2xl shadow-xl p-8">
      <h2 class="text-3xl font-bold mb-6" style="color: {portfolio.theme_color}">Contact</h2>
      <div class="prose text-gray-600">
        {@html portfolio.contact_info}
      </div>
    </div>
  </div>
</div> 