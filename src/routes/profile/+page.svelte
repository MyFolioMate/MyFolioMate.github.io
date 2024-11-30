<script lang="ts">
  import { onMount } from 'svelte';
  import { goto } from '$app/navigation';
  import { fetchApi } from '$lib/api';

  let user: any = null;
  let portfolio: any = null;
  let error: string | null = null;
  let loading = true;

  async function checkAuth() {
    try {
      const data = await fetchApi('/api/user', {
        credentials: 'include'
      });
      
      if (data.error) {
        throw new Error(data.error);
      }
      
      user = data;
      await checkPortfolio();
    } catch (e) {
      error = e instanceof Error ? e.message : 'Authentication failed';
      goto('/login');
    } finally {
      loading = false;
    }
  }

  async function checkPortfolio() {
    try {
      const data = await fetchApi(`/api/portfolio/${user.username}`, {
        credentials: 'include'
      });
      
      if (data.success) {
        portfolio = data.data;
      } else {
        throw new Error(data.error || 'Failed to fetch portfolio');
      }
    } catch (e) {
      console.error('Failed to fetch portfolio:', e);
      portfolio = null;
    }
  }

  onMount(checkAuth);

  async function handleLogout() {
    try {
      const data = await fetchApi('/api/logout', {
        credentials: 'include'
      });
      goto('/');
    } catch (e) {
      error = e instanceof Error ? e.message : 'Logout failed';
    }
  }
</script>

{#if loading}
  <div class="flex justify-center items-center min-h-screen">
    <div class="text-gray-600">Loading...</div>
  </div>
{:else if error}
  <div class="container mx-auto px-4 py-8">
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
      {error}
    </div>
  </div>
{:else}
  <div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
      <div class="bg-white shadow rounded-lg">
        <!-- Profile Header -->
        <div class="px-6 py-4 border-b border-gray-200">
          <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-900">Profile</h1>
            <button
              on:click={handleLogout}
              class="px-4 py-2 text-red-600 hover:text-red-800 transition-colors"
            >
              Logout
            </button>
          </div>
        </div>

        {#if user}
          <!-- User Info -->
          <div class="px-6 py-4 border-b border-gray-200">
            <div class="space-y-4">
              <div>
                <h2 class="text-lg font-medium text-gray-900">Username</h2>
                <p class="mt-1 text-gray-600">{user.username}</p>
              </div>
              <div>
                <h2 class="text-lg font-medium text-gray-900">Email</h2>
                <p class="mt-1 text-gray-600">{user.email}</p>
              </div>
              <div>
                <h2 class="text-lg font-medium text-gray-900">Full Name</h2>
                <p class="mt-1 text-gray-600">{user.full_name}</p>
              </div>
            </div>
          </div>

          <!-- Portfolio Management -->
          <div class="px-6 py-4">
            <div class="flex justify-between items-center">
              <h2 class="text-lg font-medium text-gray-900">Portfolio</h2>
              {#if portfolio}
                <div class="space-x-4">
                  <a 
                    href="/{user.username}/{user.id}" 
                    target="_blank"
                    class="inline-block px-4 py-2 text-blue-600 hover:text-blue-800"
                  >
                    View Portfolio
                  </a>
                  <a 
                    href="/{user.username}/edit" 
                    class="inline-block px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
                  >
                    Manage Portfolio
                  </a>
                </div>
              {:else}
                <a 
                  href="/{user.username}/edit" 
                  class="inline-block px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700"
                >
                  Create Portfolio
                </a>
              {/if}
            </div>
          </div>
        {/if}
      </div>
    </div>
  </div>
{/if}
