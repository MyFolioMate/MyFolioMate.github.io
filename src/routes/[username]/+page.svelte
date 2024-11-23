<script lang="ts">
  import { page } from '$app/stores';
  import { onMount } from 'svelte';
  import Classic from '$lib/components/portfolio-templates/Classic.svelte';
  import Modern from '$lib/components/portfolio-templates/Modern.svelte';
  import Minimal from '$lib/components/portfolio-templates/Minimal.svelte';
  import Creative from '$lib/components/portfolio-templates/Creative.svelte';
  import Corporate from '$lib/components/portfolio-templates/Corporate.svelte';
  
  interface Portfolio {
    title: string;
    about: string;
    skills: string;
    contact_info: string;
    full_name: string;
    email: string;
    theme_color: string;
    design_template: 'classic' | 'modern' | 'minimal' | 'creative' | 'corporate';
    education: string;
    experience: string;
    achievements: string;
    social_links: string;
  }

  interface Project {
    title: string;
    description: string;
    project_url: string;
  }

  const username = $page.params.username;
  let portfolio: Portfolio | null = null;
  let projects: Project[] = [];
  let error: string | null = null;
  
  onMount(async () => {
    try {
      const portfolioRes = await fetch(`/api/portfolio/${username}`);
      if (!portfolioRes.ok) {
        throw new Error(`HTTP error! status: ${portfolioRes.status}`);
      }
      const portfolioData = await portfolioRes.json();
      
      if (portfolioData.error) {
        throw new Error(portfolioData.error);
      }
      portfolio = portfolioData;

      const projectsRes = await fetch(`/api/projects/${username}`);
      if (!projectsRes.ok) {
        throw new Error(`HTTP error! status: ${projectsRes.status}`);
      }
      const projectsData = await projectsRes.json();
      
      if (projectsData.error) {
        throw new Error(projectsData.error);
      }
      projects = projectsData;
    } catch (e) {
      error = e instanceof Error ? e.message : 'An error occurred';
      console.error('Error:', e);
    }
  });

  const templates = {
    classic: Classic,
    modern: Modern,
    minimal: Minimal,
    creative: Creative,
    corporate: Corporate
  };
</script>

{#if error}
  <div class="container mx-auto px-4 py-8">
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
      <p>{error}</p>
    </div>
  </div>
{:else if portfolio}
  <svelte:component 
    this={templates[portfolio.design_template || 'classic']} 
    {portfolio} 
    {projects} 
  />
{:else}
  <div class="text-center py-8">Loading...</div>
{/if} 