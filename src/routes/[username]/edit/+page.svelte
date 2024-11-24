<script lang="ts">
  import { page } from '$app/stores';
  import { goto } from '$app/navigation';
  import { onMount } from 'svelte';

  const username = $page.params.username;
  let error: string | null = null;
  let success: string | null = null;
  let loading = true;
  let user: any = null;
  
  // Form data
  let title = '';
  let about = '';
  let skills = '';  // Will be converted to/from array
  let contact_info = '';
  let theme_color = '#000000';
  let design_template: 'classic' | 'modern' | 'minimal' | 'creative' | 'corporate' = 'classic';
  let education = '';
  let achievements = '';
  let social_links = '';
  
  onMount(async () => {
    try {
      const userResponse = await fetch('/api/user', {
        credentials: 'include'
      });
      
      if (!userResponse.ok) {
        throw new Error('Failed to fetch user data');
      }
      
      const userData = await userResponse.json();
      
      if (userData.error || userData.username !== username) {
        goto('/login');
        return;
      }
      
      user = userData;

      const portfolioRes = await fetch(`/api/portfolio/${username}`, {
        credentials: 'include'
      });
      
      if (!portfolioRes.ok) {
        throw new Error('Failed to fetch portfolio data');
      }
      
      const data = await portfolioRes.json();
      
      // Set default values if data exists but properties are null
      if (data && !data.error) {
        title = data.title || 'My Portfolio';
        about = data.about || 'Welcome to my portfolio';
        skills = Array.isArray(data.skills) ? data.skills.join(', ') : (data.skills || '');
        contact_info = data.contact_info || 'Email: ' + user.email;
        theme_color = data.theme_color || '#000000';
        design_template = data.design_template || 'classic';
        education = data.education || '';
        achievements = data.achievements || '';
        social_links = data.social_links || '';
      }
    } catch (e) {
      error = e instanceof Error ? e.message : 'Failed to load portfolio';
    } finally {
      loading = false;
    }
  });

  async function handleSubmit(e: SubmitEvent) {
    e.preventDefault();
    error = null;
    success = null;

    try {
      const response = await fetch('/api/updateportfolio', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          user_id: user.id,
          title,
          about,
          skills: skills.split(',').map(s => s.trim()),
          contact_info,
          theme_color,
          design_template,
          education,
          achievements,
          social_links
        })
      });

      if (!response.ok) {
        throw new Error('Failed to update portfolio');
      }

      success = 'Portfolio updated successfully';
    } catch (e) {
      error = e instanceof Error ? e.message : 'Failed to update portfolio';
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

      <div>
        <label for="education" class="block text-sm font-medium text-gray-700">Education</label>
        <textarea
          id="education"
          bind:value={education}
          rows="4"
          placeholder="Your educational background"
          class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md"
        ></textarea>
      </div>

      <div>
        <label for="achievements" class="block text-sm font-medium text-gray-700">Achievements</label>
        <textarea
          id="achievements"
          bind:value={achievements}
          rows="4"
          placeholder="Your notable achievements"
          class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md"
        ></textarea>
      </div>

      <div>
        <label for="social_links" class="block text-sm font-medium text-gray-700">
          Social Links (comma-separated)
        </label>
        <input
          id="social_links"
          type="text"
          bind:value={social_links}
          placeholder="e.g., https://linkedin.com/in/username, https://github.com/username"
          class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md"
        />
      </div>

      <div class="flex justify-end space-x-4">
        <a
          href="/profile"
          class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
        >
          Cancel
        </a>
        <a
          href={`/${username}`}
          class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
        >
          View Portfolio
        </a>
        <button
          type="submit"
          class="px-4 py-2 border border-transparent rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700"
        >
          Save
        </button>
      </div>
    </form>
  </div>
{/if}
