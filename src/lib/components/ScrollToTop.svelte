<script lang="ts">
  import { onMount } from 'svelte';
  
  let showButton = false;
  
  function scrollToTop() {
    window.scrollTo({
      top: 0,
      behavior: 'smooth'
    });
  }
  
  function handleScroll() {
    showButton = window.scrollY > 500;
  }
  
  onMount(() => {
    window.addEventListener('scroll', handleScroll);
    return () => window.removeEventListener('scroll', handleScroll);
  });
</script>

{#if showButton}
  <button
    on:click={scrollToTop}
    class="fixed bottom-8 right-8 bg-white p-3 rounded-full shadow-lg hover:shadow-xl transition-all duration-300 z-50 group"
    aria-label="Scroll to top"
  >
    <svg 
      class="w-6 h-6 text-gray-600 group-hover:text-gray-900 transition-colors" 
      fill="none" 
      stroke="currentColor" 
      viewBox="0 0 24 24"
    >
      <path 
        stroke-linecap="round" 
        stroke-linejoin="round" 
        stroke-width="2" 
        d="M5 10l7-7m0 0l7 7m-7-7v18"
      />
    </svg>
  </button>
{/if}

<style>
  button {
    opacity: 0.8;
  }
  button:hover {
    opacity: 1;
  }
</style> 