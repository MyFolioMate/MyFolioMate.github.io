<script lang="ts">
  import { onMount } from 'svelte';
  import { 
    Classic, 
    Modern, 
    Minimal, 
    Creative, 
    Corporate 
  } from '$lib/components/portfolio-templates/index.js';
  
  interface PageData {
    username: string;
  }
  
  export let data: PageData;
  const { username } = data;
  
  let loading = true;
  let error: string | null = null;
  let portfolio: any = null;
  let projects: any[] = [];

  onMount(async () => {
    try {
      console.log('Fetching portfolio for username:', username);
      const portfolioRes = await fetch(`/api/portfolio/${username}`);
      console.log('Portfolio response status:', portfolioRes.status);
      
      if (!portfolioRes.ok) {
        const errorText = await portfolioRes.text();
        console.error('Portfolio fetch error:', errorText);
        throw new Error(`Failed to fetch portfolio: ${errorText}`);
      }
      
      const data = await portfolioRes.json();
      console.log('Portfolio data:', data);
      
      if (data.success) {
        const portfolioData = data.data;
        portfolio = {
          ...portfolioData,
          title: portfolioData.title || 'My Portfolio',
          about: portfolioData.about || 'Welcome to my portfolio',
          skills: Array.isArray(portfolioData.skills) ? portfolioData.skills : [],
          contact_info: portfolioData.contact_info || 'Email: ',
          theme_color: portfolioData.theme_color || '#000000',
          design_template: portfolioData.design_template || 'classic',
          education: portfolioData.education || '',
          achievements: portfolioData.achievements || '',
          social_links: portfolioData.social_links || ''
        };

        const projectsRes = await fetch(`/api/projects/${username}`);
        if (projectsRes.ok) {
          const projectsData = await projectsRes.json();
          projects = projectsData.success ? projectsData.data : [];
        }
      } else {
        console.error('Portfolio error:', data.error);
        throw new Error(data.error || 'Failed to fetch portfolio data');
      }
    } catch (e) {
      console.error('Portfolio error details:', e);
      error = e instanceof Error ? e.message : 'Failed to load portfolio';
    } finally {
      loading = false;
    }
  });
</script>

{#if loading}
  <div class="flex justify-center items-center min-h-screen">
    <div class="animate-spin rounded-full h-32 w-32 border-t-2 border-b-2 border-gray-900"></div>
  </div>
{:else if error}
  <div class="text-red-500 text-center py-8">{error}</div>
{:else if portfolio}
  {#if portfolio.design_template === 'modern'}
    <Modern {portfolio} {projects} />
  {:else if portfolio.design_template === 'minimal'}
    <Minimal {portfolio} {projects} />
  {:else if portfolio.design_template === 'creative'}
    <Creative {portfolio} {projects} />
  {:else if portfolio.design_template === 'corporate'}
    <Corporate {portfolio} {projects} />
  {:else}
    <Classic {portfolio} {projects} />
  {/if}
{/if} 