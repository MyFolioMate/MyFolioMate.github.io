<script lang="ts">
  import PortfolioHeader from './PortfolioHeader.svelte';
  import PortfolioFooter from './PortfolioFooter.svelte';

  export let portfolio: {
    title: string;
    about: string;
    skills: string[];
    contact_info: string;
    theme_color: string;
    education: string;
    achievements: string;
    social_links: string;
    user: {
      full_name: string;
      email: string;
    };
  };
  
  export let projects: {
    id: number;
    title: string;
    description: string;
    project_url: string;
  }[];
</script>

<style>
  :global(:root) {
    --theme-color: var(--user-theme-color, #000000);
  }
  
  :global(.fade-in) {
    animation: fadeIn 0.6s ease-out;
  }
  
  :global(.slide-up) {
    animation: slideUp 0.8s ease-out;
  }
  
  :global(.scale-in) {
    animation: scaleIn 0.5s ease-out;
  }
  
  @keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
  }
  
  @keyframes slideUp {
    from { 
      transform: translateY(30px);
      opacity: 0;
    }
    to { 
      transform: translateY(0);
      opacity: 1;
    }
  }
  
  @keyframes scaleIn {
    from { 
      transform: scale(0.95);
      opacity: 0;
    }
    to { 
      transform: scale(1);
      opacity: 1;
    }
  }
</style>

<svelte:head>
  <style>
    :root {
      --user-theme-color: {portfolio.theme_color};
    }
  </style>
</svelte:head>

<div class="flex flex-col min-h-screen">
  <PortfolioHeader {portfolio} />
  
  <main class="flex-grow">
    <slot {portfolio} {projects} />
  </main>
  
  <PortfolioFooter {portfolio} />
</div>