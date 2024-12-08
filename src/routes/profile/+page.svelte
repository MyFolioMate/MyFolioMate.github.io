<script lang="ts">
  import { onMount } from 'svelte';
  import { goto } from '$app/navigation';
  import { fetchApi } from '$lib/api';

  let user: any = null;
  let loading = true;
  let error: string | null = null;
  let success: string | null = null;
  let isEditing = false;

  // Editable fields
  let editableUser = {
    full_name: '',
    email: '',
    username: ''
  };

  let showDeleteModal = false;
  let hasPortfolio = false;

  onMount(async () => {
    try {
      const userData = await fetchApi('/api/user', {
        credentials: 'include'
      });
      
      if (userData.error) {
        goto('/login');
        return;
      }
      
      user = userData;
      editableUser = {
        full_name: userData.full_name,
        email: userData.email,
        username: userData.username
      };

      // Check if user has a portfolio
      const portfolioData = await fetchApi(`/api/portfolio/${userData.username}`, {
        credentials: 'include'
      });
      hasPortfolio = portfolioData.success;

    } catch (e) {
      error = e instanceof Error ? e.message : 'Failed to load user data';
    } finally {
      loading = false;
    }
  });

  async function handleUpdateProfile(e: SubmitEvent) {
    e.preventDefault();
    error = null;
    success = null;

    try {
      const data = await fetchApi('/api/updateprofile', {
        method: 'POST',
        body: JSON.stringify(editableUser),
        credentials: 'include'
      });

      if (!data.success) {
        throw new Error(data.error);
      }

      // Update both user and editableUser with the returned data
      user = data.user;
      editableUser = {
        full_name: data.user.full_name,
        email: data.user.email,
        username: data.user.username
      };

      success = 'Profile updated successfully';
      isEditing = false;

      // Force reload user data from server
      const userData = await fetchApi('/api/user', {
        credentials: 'include'
      });
      
      if (!userData.error) {
        user = userData;
      }
    } catch (e) {
      error = e instanceof Error ? e.message : 'Failed to update profile';
    }
  }

  async function handleDeletePortfolio() {
    try {
      const data = await fetchApi('/api/deleteportfolio', {
        method: 'DELETE',
        credentials: 'include'
      });

      if (!data.success) {
        throw new Error(data.error);
      }

      success = 'Portfolio deleted successfully';
      showDeleteModal = false;
      hasPortfolio = false;
    } catch (e) {
      error = e instanceof Error ? e.message : 'Failed to delete portfolio';
    }
  }

  // Add logout function
  async function handleLogout() {
    try {
      const data = await fetchApi('/api/logout', {
        credentials: 'include'
      });

      if (data.success) {
        goto('/login');
      }
    } catch (e) {
      error = e instanceof Error ? e.message : 'Failed to logout';
    }
  }
</script>

<div class="container mx-auto px-4 py-8">
  <div class="max-w-3xl mx-auto">
    {#if loading}
      <div class="text-center py-8">Loading...</div>
    {:else if error}
      <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
        <p>{error}</p>
      </div>
    {:else if user}
      <!-- Success Message -->
      {#if success}
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
          <p>{success}</p>
        </div>
      {/if}

      <!-- Profile Header -->
      <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex justify-between items-center mb-6">
          <h1 class="text-2xl font-bold">My Profile</h1>
          <div class="flex gap-4">
            <button
              on:click={() => isEditing = !isEditing}
              class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700"
            >
              {isEditing ? 'Cancel' : 'Edit Profile'}
            </button>
            <button
              on:click={handleLogout}
              class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700"
            >
              Logout
            </button>
          </div>
        </div>

        {#if isEditing}
          <!-- Edit Form -->
          <form on:submit={handleUpdateProfile} class="space-y-4">
            <div>
              <label for="full_name" class="block text-sm font-medium text-gray-700">Full Name</label>
              <input
                id="full_name"
                type="text"
                bind:value={editableUser.full_name}
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm"
              />
            </div>

            <div>
              <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
              <input
                id="username"
                type="text"
                bind:value={editableUser.username}
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm"
              />
            </div>

            <div>
              <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
              <input
                id="email"
                type="email"
                bind:value={editableUser.email}
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm"
              />
            </div>

            <div class="flex justify-end">
              <button
                type="submit"
                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700"
              >
                Save Changes
              </button>
            </div>
          </form>
        {:else}
          <!-- Profile Display -->
          <div class="space-y-4">
            <div>
              <h3 class="text-sm font-medium text-gray-500">Full Name</h3>
              <p class="mt-1 text-lg text-gray-900">{user.full_name}</p>
            </div>

            <div>
              <h3 class="text-sm font-medium text-gray-500">Username</h3>
              <p class="mt-1 text-lg text-gray-900">{user.username}</p>
            </div>

            <div>
              <h3 class="text-sm font-medium text-gray-500">Email</h3>
              <p class="mt-1 text-lg text-gray-900">{user.email}</p>
            </div>
          </div>
        {/if}
      </div>

      <!-- Portfolio Actions -->
      <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-semibold mb-4">Portfolio Management</h2>
        <div class="flex gap-4">
          {#if hasPortfolio}
            <a
              href={`/${user.username}/edit`}
              class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700"
            >
              Edit Portfolio
            </a>
            <a
              href={`/${user.username}/${user.id}`}
              class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200"
            >
              View Portfolio
            </a>
            <button
              on:click={() => showDeleteModal = true}
              class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700"
            >
              Delete Portfolio
            </button>
          {:else}
            <a
              href="/create-portfolio"
              class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700"
            >
              Create Portfolio
            </a>
          {/if}
        </div>
      </div>
    {/if}
  </div>
</div>

<!-- Add the delete confirmation modal -->
{#if showDeleteModal}
  <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md mx-4">
      <h3 class="text-xl font-bold mb-4">Delete Portfolio</h3>
      <p class="text-gray-600 mb-6">
        Are you sure you want to delete your portfolio? This action cannot be undone.
      </p>
      <div class="flex justify-end gap-4">
        <button
          on:click={() => showDeleteModal = false}
          class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200"
        >
          Cancel
        </button>
        <button
          on:click={handleDeletePortfolio}
          class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700"
        >
          Delete Portfolio
        </button>
      </div>
    </div>
  </div>
{/if}
