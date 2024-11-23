<script lang="ts">
  import { onMount } from 'svelte';
  import { goto } from '$app/navigation';

  let portfolio: any = null;
  let user: any = null;
  let error: string | null = null;
  let showDeleteConfirm = false;

  onMount(() => {
    user = JSON.parse(localStorage?.getItem('user') ?? 'null');
    if (!user) {
      goto('/login');
    }
    portfolio = JSON.parse(localStorage?.getItem('portfolio') ?? 'null');
  });

  function handleLogout() {
    localStorage.removeItem('user');
    window.location.href = '/';
  }

  async function handleDeletePortfolio() {
    try {
      const response = await fetch(`/api/portfolio/${user.username}`, {
        method: 'DELETE',
        headers: {
          'Content-Type': 'application/json'
        }
      });

      const data = await response.json();
      
      if (data.error) {
        throw new Error(data.error);
      }

      // Clear portfolio from localStorage
      localStorage.removeItem('portfolio');
      portfolio = null;
      showDeleteConfirm = false;
    } catch (e) {
      error = e instanceof Error ? e.message : 'Failed to delete portfolio';
    }
  }
</script>

{#if JSON.parse(localStorage?.getItem('user') ?? 'null')}
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

        <!-- User Info -->
        <div class="px-6 py-4 border-b border-gray-200">
          <div class="space-y-4">
            <div>
              <h2 class="text-lg font-medium text-gray-900">Username</h2>
              <p class="mt-1 text-gray-600">{user?.username}</p>
            </div>
            <div>
              <h2 class="text-lg font-medium text-gray-900">Email</h2>
              <p class="mt-1 text-gray-600">{user?.email}</p>
            </div>
          </div>
        </div>

        <!-- Portfolio Actions -->
        <div class="px-6 py-4">
          <h2 class="text-lg font-medium text-gray-900 mb-4">Portfolio Management</h2>
          {#if portfolio}
            <div class="space-y-6">
              <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <a
                  href={`/${user?.username}`}
                  class="inline-flex justify-center items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 transition-colors"
                >
                  View Portfolio
                </a>
                <a
                  href={`/${user?.username}/edit`}
                  class="inline-flex justify-center items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors"
                >
                  Edit Portfolio
                </a>
                <a
                  href={`/${user?.username}/projects`}
                  class="inline-flex justify-center items-center px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors"
                >
                  Manage Projects
                </a>
              </div>
              
              <!-- Delete Portfolio Section -->
              <div class="border-t pt-6">
                {#if showDeleteConfirm}
                  <div class="bg-red-50 p-4 rounded-md">
                    <p class="text-red-700 mb-4">Are you sure you want to delete your portfolio? This action cannot be undone.</p>
                    <div class="flex space-x-4">
                      <button
                        on:click={handleDeletePortfolio}
                        class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors"
                      >
                        Yes, Delete Portfolio
                      </button>
                      <button
                        on:click={() => showDeleteConfirm = false}
                        class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 transition-colors"
                      >
                        Cancel
                      </button>
                    </div>
                  </div>
                {:else}
                  <button
                    on:click={() => showDeleteConfirm = true}
                    class="text-red-600 hover:text-red-800 transition-colors"
                  >
                    Delete Portfolio
                  </button>
                {/if}
              </div>
            </div>
          {:else}
            <div class="text-center">
              <p class="text-gray-600 mb-4">You haven't created a portfolio yet.</p>
              <a
                href={`/${user?.username}/edit`}
                class="inline-flex justify-center items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors"
              >
                Create Portfolio
              </a>
            </div>
          {/if}
        </div>
      </div>
    </div>
  </div>
{/if}
