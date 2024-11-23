<script lang="ts">
  import { page } from '$app/stores';
  import { goto } from '$app/navigation';
  import { onMount } from 'svelte';

  const username = $page.params.username;
  let error: string | null = null;
  let success: string | null = null;
  
  // Form data
  let title = '';
  let about = '';
  let skills = '';
  let contact_info = '';
  let theme_color = '#000000';
  let design_template = 'classic';
  
  onMount(async () => {
    // Check if user is logged in and is the owner
    const userData = localStorage.getItem('user');
    if (!userData) {
      goto('/login');
      return;
    }
    
    const user = JSON.parse(userData);
    if (user.username !== username) {
      goto(`/${username}`);
      return;
    }

    // Fetch current portfolio data
    try {
      const res = await fetch(`/api/portfolio/${username}`);
      const data = await res.json();
      
      if (data.error) {
        throw new Error(data.error);
      }

      // Populate form with existing data
      title = data.title || '';
      about = data.about || '';
      skills = data.skills || '';
      contact_info = data.contact_info || '';
      theme_color = data.theme_color || '#000000';
      design_template = data.design_template || 'classic';
    } catch (e) {
      error = e instanceof Error ? e.message : 'Failed to load portfolio';
    }
  });

  async function handleSubmit(e: SubmitEvent) {
    e.preventDefault();
    error = null;
    success = null;

    const userData = localStorage.getItem('user');
    if (!userData) {
        error = 'Not authenticated';
        return;
    }
    const user = JSON.parse(userData);

    try {
        const response = await fetch('/api/updateportfolio', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                user_id: user.id,
                title,
                about,
                skills,
                contact_info,
                theme_color,
                design_template
            })
        });

        const data = await response.json();

        if (!data.success) {
            throw new Error(data.error);
        }

        goto(`/${username}`);
    } catch (e) {
        error = e instanceof Error ? e.message : 'Failed to update portfolio';
    }
  }
</script>

<div class="container mx-auto px-4 py-8">
  <h1 class="text-3xl font-bold mb-8">Edit Portfolio</h1>

  {#if error}
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
      {error}
    </div>
  {/if}

  {#if success}
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
      {success}
    </div>
  {/if}

  <form on:submit={handleSubmit} class="space-y-6">
    <div>
      <label for="title" class="block text-sm font-medium text-gray-700">Portfolio Title</label>
      <input
        id="title"
        type="text"
        bind:value={title}
        required
        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md"
      />
    </div>

    <div>
      <label for="about" class="block text-sm font-medium text-gray-700">About Me</label>
      <textarea
        id="about"
        bind:value={about}
        rows="4"
        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md"
      ></textarea>
    </div>

    <div>
      <label for="skills" class="block text-sm font-medium text-gray-700">Skills (comma-separated)</label>
      <input
        id="skills"
        type="text"
        bind:value={skills}
        placeholder="e.g., JavaScript, Python, React"
        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md"
      />
    </div>

    <div>
      <label for="contact_info" class="block text-sm font-medium text-gray-700">Contact Information</label>
      <textarea
        id="contact_info"
        bind:value={contact_info}
        rows="2"
        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md"
      ></textarea>
    </div>

    <div>
      <label for="theme_color" class="block text-sm font-medium text-gray-700">Theme Color</label>
      <input
        id="theme_color"
        type="color"
        bind:value={theme_color}
        class="mt-1 block w-full"
      />
    </div>

    <div>
      <label for="design_template" class="block text-sm font-medium text-gray-700">Portfolio Design</label>
      <select
        id="design_template"
        bind:value={design_template}
        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md"
      >
        <option value="classic">Classic</option>
        <option value="modern">Modern</option>
        <option value="minimal">Minimal</option>
        <option value="creative">Creative</option>
        <option value="corporate">Corporate</option>
      </select>
    </div>

    <div class="flex justify-end space-x-4">
      <a
        href={`/${username}`}
        class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
      >
        Cancel
      </a>
      <button
        type="submit"
        class="px-4 py-2 border border-transparent rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700"
      >
        Save Changes
      </button>
    </div>
  </form>
</div>
