<script lang="ts">
  import { onMount } from 'svelte';
  import { goto } from '$app/navigation';

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
      const response = await fetch('/api/explore');
      const data = await response.json();
      
      if (data.success) {
        portfolios = [...examplePortfolios, ...data.portfolios.map((p: {
          user_id: number;
          username: string;
          full_name: string;
          title?: string;
          image?: string;
          design_template: string;
          likes?: number;
        }) => ({
          id: p.user_id,
          username: p.username,
          name: p.full_name,
          role: p.title || 'Portfolio Owner',
          image: p.image || '/default-profile.jpg',
          template: p.design_template,
          likes: p.likes || 0
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

  async function handleLike(portfolioId: number) {
    try {
      // Check authentication status from server
      const userResponse = await fetch('/api/user');
      const userData = await userResponse.json();
      
      if (userData.error) {
        goto('/login');
        return;
      }

      const response = await fetch('/api/togglelike', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          portfolio_id: portfolioId
        })
      });

      const data = await response.json();
      if (data.success) {
        // Refresh portfolios to get updated like count
        fetchPortfolios();
      }
    } catch (err) {
      console.error('Failed to toggle like:', err);
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
        {#each portfolios.filter(p => 
          p.name.toLowerCase().includes(searchQuery.toLowerCase()) ||
          p.role.toLowerCase().includes(searchQuery.toLowerCase())
        ) as portfolio}
          <div class="bg-white rounded-lg shadow-sm overflow-hidden border hover:shadow-md transition">
            <img
              src={portfolio.image}
              alt={portfolio.name}
              class="w-full h-48 object-cover"
            />
            <div class="p-4">
              <div class="flex justify-between items-start mb-3">
                <div>
                  <h3 class="text-xl font-semibold">{portfolio.name}</h3>
                  <p class="text-gray-600">{portfolio.role}</p>
                </div>
                <button 
                  class="text-gray-400 hover:text-red-500"
                  on:click={() => handleLike(portfolio.id)}
                >
                  <div class="flex items-center">
                    <span class="mr-1">{portfolio.likes}</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                  </div>
                </button>
              </div>
              <div class="flex justify-between items-center">z
                <span class="text-sm text-blue-500">{portfolio.template}</span>
                <a
                  href={`/${portfolio.username}-${portfolio.id}`}
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
  </div>
</div> 