<script lang="ts">
  import { goto } from '$app/navigation';
  import { fetchApi } from '$lib/api';
  
  let email = '';
  let password = '';
  let error: string | null = null;

  async function handleLogin(e: SubmitEvent) {
    e.preventDefault();
    try {
      const data = await fetchApi('/api/login', {
        method: 'POST',
        body: JSON.stringify({ email, password }),
        credentials: 'include',
        encrypt: true
      });

      if (data.error) {
        throw new Error(data.error);
      }

      goto('/profile');
    } catch (e) {
      error = e instanceof Error ? e.message : 'Login failed';
    }
  }
</script>

<div class="py-16 bg-gray-50">
  <div class="max-w-md mx-auto px-4">
    <div class="bg-white rounded-lg shadow p-8">
      <div class="mb-8">
        <h2 class="text-center text-3xl font-bold">Sign in to your account</h2>
      </div>

      {#if error}
        <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
          {error}
        </div>
      {/if}

      <form class="space-y-6" on:submit={handleLogin}>
        <div class="space-y-4">
          <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
            <input
              id="email"
              type="email"
              bind:value={email}
              required
              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md"
            />
          </div>

          <div>
            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
            <input
              id="password"
              type="password"
              bind:value={password}
              required
              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md"
            />
          </div>
        </div>

        <button
          type="submit"
          class="w-full py-2 px-4 border border-transparent rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700"
        >
          Sign in
        </button>

        <div class="text-center">
          <a href="/register" class="text-blue-600 hover:text-blue-800">
            Don't have an account? Register
          </a>
        </div>
      </form>
    </div>
  </div>
</div> 