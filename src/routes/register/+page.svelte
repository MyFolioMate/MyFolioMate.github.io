<script lang="ts">
  import { goto } from '$app/navigation';
  import { fetchApi } from '$lib/api';
  
  let username = '';
  let email = '';
  let password = '';
  let full_name = '';
  let error: string | null = null;

  async function handleRegister(e: SubmitEvent) {
    e.preventDefault();
    try {
      const data = await fetchApi('/api/register', {
        method: 'POST',
        body: JSON.stringify({
          username,
          email,
          password,
          full_name
        })
      });

      if (!data.success) {
        throw new Error(data.error);
      }

      goto('/login');
    } catch (e) {
      error = e instanceof Error ? e.message : 'Registration failed';
    }
  }
</script>

<div class="py-16 bg-gray-50">
  <div class="max-w-md mx-auto px-4">
    <div class="bg-white rounded-lg shadow p-8">
      <div class="mb-8">
        <h2 class="text-center text-3xl font-bold">Create your account</h2>
      </div>

      {#if error}
        <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
          {error}
        </div>
      {/if}

      <form class="space-y-6" on:submit={handleRegister}>
        <div class="space-y-4">
          <div>
            <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
            <input
              id="username"
              type="text"
              bind:value={username}
              required
              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md"
            />
          </div>

          <div>
            <label for="full_name" class="block text-sm font-medium text-gray-700">Full Name</label>
            <input
              id="full_name"
              type="text"
              bind:value={full_name}
              required
              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md"
            />
          </div>

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
          Register
        </button>

        <div class="text-center">
          <a href="/login" class="text-blue-600 hover:text-blue-800">
            Already have an account? Sign in
          </a>
        </div>
      </form>
    </div>
  </div>
</div> 