<script lang="ts">
    import { onMount } from 'svelte';
    import { goto } from '$app/navigation';
    import { 
      Classic, 
      Modern, 
      Minimal, 
      Creative, 
      Corporate 
    } from '$lib/components/portfolio-templates/index.js';
    
    interface PageData {
      username: string;
      id: string;
    }
    
    export let data: PageData;
    const { username, id } = data;
    
    let loading = true;
    let error: string | null = null;
    let portfolio: any = null;
    let projects: any[] = [];
  
    onMount(async () => {
      try {
        const portfolioRes = await fetch(`/api/portfolio/${username}/${id}`, {
          credentials: 'include'
        });
        
        if (!portfolioRes.ok) {
          if (portfolioRes.status === 404) {
            throw new Error('Portfolio not found');
          }
          throw new Error('Failed to fetch portfolio');
        }
        
        const data = await portfolioRes.json();
        
        if (!data.success) {
          throw new Error(data.error || 'Failed to fetch portfolio data');
        }
        
        portfolio = {
          ...data.data,
          title: data.data.title || 'My Portfolio',
          about: data.data.about || 'Welcome to my portfolio',
          skills: Array.isArray(data.data.skills) ? data.data.skills : [],
          contact_info: data.data.contact_info || 'Email: ',
          theme_color: data.data.theme_color || '#000000',
          design_template: data.data.design_template || 'classic',
          education: data.data.education || '',
          achievements: data.data.achievements || '',
          social_links: data.data.social_links || ''
        };
  
        const projectsRes = await fetch(`/api/projects/${username}/${id}`);
        if (projectsRes.ok) {
          const projectsData = await projectsRes.json();
          projects = projectsData.success ? projectsData.data : [];
        }
      } catch (e) {
        error = e instanceof Error ? e.message : 'Failed to load portfolio';
        if (error === 'Portfolio not found') {
          goto('/404');
        }
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
    <div class="flex justify-center items-center min-h-screen">
      <div class="text-center">
        <h1 class="text-4xl font-bold text-gray-800 mb-4">Portfolio Not Found</h1>
        <p class="text-gray-600 mb-8">The portfolio you're looking for doesn't exist or you don't have permission to view it.</p>
        <a href="/" class="text-blue-600 hover:text-blue-800">Return Home</a>
      </div>
    </div>
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