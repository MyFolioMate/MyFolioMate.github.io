<script lang="ts">
  import { onMount } from 'svelte';
  import { goto } from '$app/navigation';
  import { fetchApi } from '$lib/api';

  let searchQuery = '';
  let portfolios: Portfolio[] = [];
  let loading = true;
  let error: string | null = null;
  let filteredPortfolios: Portfolio[] = [];

  // Update filtered portfolios whenever search query or portfolios change
  $: filteredPortfolios = portfolios.filter(portfolio => {
    const searchLower = searchQuery.toLowerCase();
    return (
      portfolio.name.toLowerCase().includes(searchLower) ||
      portfolio.title.toLowerCase().includes(searchLower) ||
      portfolio.about.toLowerCase().includes(searchLower)
    );
  });

  interface Portfolio {
    username: string;
    name: string;
    title: string;
    template: string;
    about: string;
    user_id: number;
  }

  async function fetchPortfolios() {
    try {
      const data = await fetchApi('/api/explore', {
        credentials: 'include'
      });
      
      if (data.success) {
        portfolios = data.portfolios.map((p: any) => ({
          username: p.username,
          name: p.full_name,
          title: p.title || 'My Portfolio',
          template: p.design_template || 'classic',
          about: p.about,
          user_id: p.user_id
        }));
      } else {
        error = data.error;
      }
    } catch (err) {
      error = err instanceof Error ? err.message : 'Failed to fetch portfolios';
    } finally {
      loading = false;
    }
  }

  onMount(fetchPortfolios);
</script>

<div class="container mx-auto px-4 py-8">
  <div class="max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-8">
      <h1 class="text-3xl font-bold">Explore Portfolios</h1>
      
      <!-- Search -->
      <div class="flex gap-4">
        <input
          type="text"
          bind:value={searchQuery}
          placeholder="Search portfolios..."
          class="px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 w-64"
        />
      </div>
    </div>

    {#if loading}
      <div class="text-center py-8">Loading portfolios...</div>
    {:else if error}
      <div class="text-center py-8 text-red-500">{error}</div>
    {:else}
      <!-- Show message when no results found -->
      {#if filteredPortfolios.length === 0}
        <div class="text-center py-8 text-gray-500">
          No portfolios found matching "{searchQuery}"
        </div>
      {:else}
        <!-- Portfolios Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          {#each filteredPortfolios as portfolio}
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
              <div class="p-6">
                <div class="flex items-center mb-4">
                  <div class="ml-4">
                    <h3 class="text-lg font-semibold">{portfolio.name}</h3>
                    <p class="text-gray-600">{portfolio.title}</p>
                  </div>
                </div>
                <div class="flex justify-between items-center">
                  <span class="text-sm text-blue-500">{portfolio.template}</span>
                  <a
                    href={`/${portfolio.username}/${portfolio.user_id}`}
                    class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition"
                  >
                    View Portfolio
                  </a>
                </div>
              </div>
            </div>
          {/each}
        </div>
      {/if}
    {/if}
  </div>
</div> 