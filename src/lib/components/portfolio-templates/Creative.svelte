<script lang="ts">
  import { onMount, tick } from 'svelte';
  import PortfolioFooter from './PortfolioFooter.svelte';
  import ScrollToTop from '../ScrollToTop.svelte';
  export let portfolio;
  export let projects;
  
  let mounted = false;
  
  onMount(async () => {
    mounted = true;
    await tick();
    initParallax();
  });
  
  function initParallax() {
    const elements = document.querySelectorAll('.parallax');
    window.addEventListener('scroll', () => {
      elements.forEach((el) => {
        const element = el as HTMLElement;
        const speed = Number(element.getAttribute('data-speed')) || 0.2;
        const yPos = -(window.scrollY * speed);
        element.style.transform = `translateY(${yPos}px)`;
      });
    });
  }
</script>

<style>
  .animated-border {
    position: relative;
    overflow: hidden;
  }
  
  .animated-border::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 2px;
    background: var(--theme-color);
    transform: scaleX(0);
    transform-origin: right;
    transition: transform 0.5s ease;
  }
  
  .animated-border:hover::after {
    transform: scaleX(1);
    transform-origin: left;
  }
  
  .project-card {
    transition: all 0.3s ease;
  }
  
  .project-card:hover {
    transform: scale(1.02) rotate(-1deg);
  }
</style>

<header class="fixed top-0 left-0 right-0 bg-black text-white z-50">
  <div class="max-w-6xl mx-auto px-4 py-4">
    <div class="flex justify-between items-center">
      <h2 class="text-xl transform hover:rotate-2 transition-transform" 
          style="color: {portfolio.theme_color}">
        {portfolio.user?.full_name || 'Portfolio'}
      </h2>
      <nav class="flex space-x-8">
        <button 
          on:click={() => document.getElementById('about')?.scrollIntoView({ behavior: 'smooth' })}
          class="hover:text-gray-300 transform hover:-rotate-2 transition-all">About</button>
        <button 
          on:click={() => document.getElementById('skills')?.scrollIntoView({ behavior: 'smooth' })}
          class="hover:text-gray-300 transform hover:rotate-2 transition-all">Skills</button>
        <button 
          on:click={() => document.getElementById('projects')?.scrollIntoView({ behavior: 'smooth' })}
          class="hover:text-gray-300 transform hover:-rotate-2 transition-all">Projects</button>
        <button 
          on:click={() => document.getElementById('contact')?.scrollIntoView({ behavior: 'smooth' })}
          class="hover:text-gray-300 transform hover:rotate-2 transition-all">Contact</button>
      </nav>
    </div>
  </div>
</header>

<div class="min-h-screen bg-gray-50 pt-20">
  <div class="max-w-6xl mx-auto px-4 py-16">
    <!-- Header -->
    <header class="mb-24">
      <h1 class="text-5xl font-bold mb-6" style="color: {portfolio.theme_color}">
        {portfolio.title || 'My Portfolio'}
      </h1>
      <p class="text-xl text-gray-600 mb-8">{portfolio.about}</p>
      
      <!-- Social Links -->
      {#if portfolio.social_links}
        <div class="flex space-x-4 mb-8">
          {#each portfolio.social_links.split(',') as link}
            <a 
              href={link.trim()} 
              target="_blank" 
              class="text-gray-600 hover:text-black transition-colors"
            >
              {link.includes('linkedin') ? 'LinkedIn' : 
               link.includes('github') ? 'GitHub' : 
               link.includes('twitter') ? 'Twitter' : 'Link'}
            </a>
          {/each}
        </div>
      {/if}
    </header>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-16 mb-24">
      <!-- Education -->
      <section id="about" class="pt-20">
        <h2 class="text-4xl font-bold mb-8" style="color: {portfolio.theme_color}">Education</h2>
        <div class="prose prose-lg text-gray-800">
          {@html portfolio.education || 'No education listed yet.'}
        </div>
      </section>

      <!-- Skills -->
      <section id="skills">
        <h2 class="text-4xl font-bold mb-8" style="color: {portfolio.theme_color}">Skills</h2>
        <div class="flex flex-wrap gap-4">
          {#if portfolio.skills}
            {#each portfolio.skills as skill}
              <div class="group relative">
                <span class="px-6 py-3 bg-black text-white text-lg transform hover:rotate-2 transition-transform">
                  {skill.name}
                </span>
                {#if skill.description}
                  <div class="absolute z-10 bottom-full mb-2 left-0 hidden group-hover:block bg-black text-white p-4 transform -rotate-1">
                    <p class="text-sm">{skill.description}</p>
                  </div>
                {/if}
              </div>
            {/each}
          {/if}
        </div>
      </section>
    </div>

    <!-- Projects -->
    <section id="projects" class="mb-24">
      <h2 class="text-4xl font-bold mb-12" style="color: {portfolio.theme_color}">Projects</h2>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
        {#each projects as project}
          <div class="group relative p-8 border-2 border-black hover:bg-black transition-colors">
            <h3 class="text-2xl font-bold mb-4 group-hover:text-white">{project.title}</h3>
            <p class="text-gray-600 mb-6 group-hover:text-gray-300">{project.description}</p>
            <a 
              href={project.project_url} 
              target="_blank" 
              class="inline-block text-lg group-hover:text-white"
            >
              View Project →
            </a>
          </div>
        {/each}
      </div>
    </section>

    <!-- Achievements -->
    <section class="mb-24">
      <h2 class="text-4xl font-bold mb-8" style="color: {portfolio.theme_color}">Achievements</h2>
      <div class="prose prose-lg text-gray-800 max-w-none">
        {@html portfolio.achievements || 'No achievements listed yet.'}
      </div>
    </section>

    <!-- Contact -->
    <section id="contact">
      <h2 class="text-4xl font-bold mb-8" style="color: {portfolio.theme_color}">Contact</h2>
      <div class="text-xl text-gray-800">{portfolio.contact_info}</div>
    </section>
  </div>
</div> 

<PortfolioFooter {portfolio} theme_color={portfolio.theme_color} />
<ScrollToTop /> 