<script lang="ts">
    import PortfolioHeader from './PortfolioHeader.svelte';
    import PortfolioFooter from './PortfolioFooter.svelte';
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
  
  .glass-card {
    background: rgba(255, 255, 255, 0.7);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
  }

  .hover-lift {
    transition: all 0.3s ease;
  }

  .hover-lift:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 30px -10px rgba(0, 0, 0, 0.15);
  }

  .gradient-text {
    background-clip: text;
    -webkit-background-clip: text;
    color: transparent;
    background-size: 200% auto;
    animation: textShine 3s ease-in-out infinite alternate;
  }

  @keyframes textShine {
    0% { background-position: 0% 50%; }
    100% { background-position: 100% 50%; }
  }
</style>

<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
  <div class="max-w-6xl mx-auto px-4 py-16">
    <!-- Hero Section -->
    <header class="glass-card sticky top-0 z-50 mb-16">
      <div class="max-w-6xl mx-auto px-4 py-4">
        <div class="flex justify-between items-center">
          <h2 class="text-xl font-bold gradient-text"
              style="background-image: linear-gradient(135deg, {portfolio.theme_color}, {portfolio.theme_color}99);">
            {portfolio.user?.full_name || 'Portfolio'}
          </h2>
          <nav class="flex space-x-8">
            <button 
              on:click={() => document.getElementById('about')?.scrollIntoView({ behavior: 'smooth' })}
              class="text-gray-600 hover:text-gray-900 transition-colors">About</button>
            <button 
              on:click={() => document.getElementById('skills')?.scrollIntoView({ behavior: 'smooth' })}
              class="text-gray-600 hover:text-gray-900 transition-colors">Skills</button>
            <button 
              on:click={() => document.getElementById('projects')?.scrollIntoView({ behavior: 'smooth' })}
              class="text-gray-600 hover:text-gray-900 transition-colors">Projects</button>
            <button 
              on:click={() => document.getElementById('contact')?.scrollIntoView({ behavior: 'smooth' })}
              class="text-gray-600 hover:text-gray-900 transition-colors">Contact</button>
          </nav>
        </div>
      </div>
    </header>
    <section id="about" class="glass-card rounded-3xl shadow-2xl p-12 mb-16 fade-in">
      <h1 class="text-6xl font-bold mb-8 gradient-text" 
          style="background-image: linear-gradient(135deg, {portfolio.theme_color}, {portfolio.theme_color}99);">
        {portfolio.title || 'My Portfolio'}
      </h1>
      <p class="text-xl text-gray-600 mb-8 leading-relaxed max-w-3xl">{portfolio.about}</p>
      {#if portfolio.social_links}
        <div class="flex space-x-6">
          {#each portfolio.social_links.split(',') as link}
            <a href={link.trim()} 
               target="_blank" 
               class="hover-lift px-6 py-3 rounded-full text-white text-sm font-medium transition-all"
               style="background-color: {portfolio.theme_color}">
              {link.includes('linkedin') ? 'LinkedIn' : 
               link.includes('github') ? 'GitHub' : 
               link.includes('twitter') ? 'Twitter' : 'Link'}
            </a>
          {/each}
        </div>
      {/if}
    </section>

    <!-- After the Hero Section and before Skills Section -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-16">
      <!-- Education Section -->
      <div class="glass-card rounded-3xl shadow-xl p-10 slide-up">
        <h2 class="text-3xl font-bold mb-8" style="color: {portfolio.theme_color}">Education</h2>
        <div class="prose prose-lg max-w-none">
          {@html portfolio.education || 'No education listed yet.'}
        </div>
      </div>

      <!-- Achievements Section -->
      <div class="glass-card rounded-3xl shadow-xl p-10 slide-up">
        <h2 class="text-3xl font-bold mb-8" style="color: {portfolio.theme_color}">Achievements</h2>
        <div class="prose prose-lg max-w-none">
          {@html portfolio.achievements || 'No achievements listed yet.'}
        </div>
      </div>
    </div>

    <!-- Skills Section -->
    <section id="skills" class="glass-card rounded-3xl shadow-xl p-10 mb-16 slide-up">
      <h2 class="text-3xl font-bold mb-8" style="color: {portfolio.theme_color}">Skills & Expertise</h2>
      <div class="flex flex-wrap gap-4">
        {#if portfolio.skills}
          {#each portfolio.skills as skill}
            <div class="group relative">
              <span class="px-6 py-3 rounded-full text-white font-medium hover-lift"
                    style="background: linear-gradient(135deg, {portfolio.theme_color}, {portfolio.theme_color}99)">
                {skill.name}
              </span>
              {#if skill.description}
                <div class="absolute z-10 bottom-full mb-2 left-0 hidden group-hover:block bg-white p-4 rounded-lg shadow-xl text-sm min-w-[200px] backdrop-blur-sm bg-white/90">
                  <p class="text-gray-700">{skill.description}</p>
                </div>
              {/if}
            </div>
          {/each}
        {/if}
      </div>
    </section>

    <!-- Projects Grid -->
    <section id="projects" class="glass-card rounded-3xl shadow-xl p-10 mb-16">
      <h2 class="text-3xl font-bold mb-10" style="color: {portfolio.theme_color}">Featured Projects</h2>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        {#each projects as project}
          <div class="hover-lift rounded-2xl overflow-hidden bg-white">
            <div class="p-8">
              <h3 class="text-xl font-bold mb-4">{project.title}</h3>
              <p class="text-gray-600 mb-6 line-clamp-3">{project.description}</p>
              <a href={project.project_url} 
                 target="_blank" 
                 class="inline-flex items-center space-x-2 font-medium transition-colors"
                 style="color: {portfolio.theme_color}">
                <span>View Project</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                        d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
              </a>
            </div>
          </div>
        {/each}
      </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="glass-card rounded-3xl shadow-xl p-10">
      <h2 class="text-3xl font-bold mb-8" style="color: {portfolio.theme_color}">Get in Touch</h2>
      <div class="prose prose-lg max-w-none">
        {@html portfolio.contact_info}
      </div>
    </section>
  </div>
</div> 