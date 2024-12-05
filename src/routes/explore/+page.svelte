<script lang="ts">
  import { onMount } from 'svelte';
  import { goto } from '$app/navigation';
  import { fetchApi } from '$lib/api';

  // Example portfolios (keeping these as fallback)
  let examplePortfolios = [
    {
      id: 1,
      username: 'sarah_chen',
      name: 'Sarah Chen',
      role: 'UX Designer',
      image: '/profiles/sarah.jpg',
      template: 'Minimal',
      likes: 234
    },
    {
      id: 2,
      username: 'james_dev',
      name: 'James Miller',
      role: 'Frontend Developer',
      image: '/profiles/james.jpg',
      template: 'Developer',
      likes: 186
    }
  ];

  let portfolios = [...examplePortfolios];
  let searchQuery = '';
  let selectedFilter = 'Latest';
  let loading = true;
  let error: string | null = null;

  async function fetchPortfolios() {
    try {
      const data = await fetchApi('/api/explore');
      
      if (data.success) {
        portfolios = [...examplePortfolios, ...data.portfolios.map((p: {
          user_id: number;
          username: string;
          full_name: string;
          title?: string;
          image?: string;
          design_template: string;
        }) => ({
          id: p.user_id,
          username: p.username,
          name: p.full_name,
          role: p.title || 'Portfolio Owner',
          image: p.image || '/default-profile.jpg',
          template: p.design_template,
        }))];
      } else {
        error = data.error;
      }
    } catch (err) {
      error = 'Failed to fetch portfolios';
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
      
      <!-- Search and Filter -->
      <div class="flex gap-4">
        <input
          type="text"
          bind:value={searchQuery}
          placeholder="Search portfolios..."
          class="px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
        />
        <select
          bind:value={selectedFilter}
          class="px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
          <option>Latest</option>
          <option>Most Liked</option>
          <option>Trending</option>
        </select>
      </div>
    </div>

    {#if loading}
      <div class="text-center py-8">Loading portfolios...</div>
    {:else if error}
      <div class="text-center py-8 text-red-500">{error}</div>
    {:else}
      <!-- Portfolios Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        {#each portfolios as portfolio}
          <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="p-6">
              <div class="flex items-center mb-4">
                <img
                  src={portfolio.image}
                  alt={portfolio.name}
                  class="w-12 h-12 rounded-full object-cover"
                />
                <div class="ml-4">
                  <h3 class="text-lg font-semibold">{portfolio.name}</h3>
                  <p class="text-gray-600">{portfolio.role}</p>
                </div>
              </div>
              <div class="flex justify-between items-center">
                <span class="text-sm text-blue-500">{portfolio.template}</span>
                <a
                  href={`/${portfolio.username}/${portfolio.id}`}
                  class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition"
                  on:click|stopPropagation
                >
                  View Portfolio
                </a>
              </div>
            </div>
          </div>
        {/each}
      </div>
    {/if}
  </div>
</div> 