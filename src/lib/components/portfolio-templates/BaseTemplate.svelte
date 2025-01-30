<script lang="ts">
  import PortfolioHeader from './PortfolioHeader.svelte';
  import PortfolioFooter from './PortfolioFooter.svelte';
  import ScrollToTop from '../ScrollToTop.svelte';

  export let portfolio: {
    title: string;
    about: string;
    skills: string[];
    contact_info: string;
    theme_color: string;
    education: string;
    achievements: string;
    social_links: {
      github?: string;
      linkedin?: string;
      twitter?: string;
      [key: string]: string | undefined;
    };
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

  .skill-item {
    position: relative;
    z-index: 1;
  }

  .skill-details {
    position: absolute;
    left: 0;
    bottom: 0;
    width: 100%;
    background: white;
    padding: 0.75rem;
    border-radius: 0.5rem;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    opacity: 0;
    visibility: hidden;
    transition: all 0.2s ease;
    z-index: 10;
    transform: translateY(100%);
    margin-bottom: 0.5rem;
  }

  .skill-item:hover .skill-details {
    opacity: 1;
    visibility: visible;
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
  
  <PortfolioFooter {portfolio} theme_color={portfolio.theme_color} />
  <ScrollToTop />
</div>