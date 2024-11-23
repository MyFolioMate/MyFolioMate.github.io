<script lang="ts">
  interface Project {
    id: number;
    title: string;
    description: string;
    project_url: string;
  }

  import { page } from '$app/stores';
  import { onMount } from 'svelte';

  const username = $page.params.username;
  const id = $page.params.id;
  let projects: Project[] = [];
  let error: string | null = null;
  let success: string | null = null;

  // Form fields
  let title = '';
  let description = '';
  let project_url = '';

  onMount(async () => {
    await loadProjects();
  });

  async function loadProjects() {
    try {
      const response = await fetch(`/api/projects/${id}/${username}`);
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      const data = await response.json();
      if (data.error) {
        throw new Error(data.error);
      }
      projects = data;
    } catch (e) {
      error = e instanceof Error ? e.message : 'Failed to load projects';
    }
  }

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
      const response = await fetch(`/api/projects/${id}/${username}`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          title,
          description,
          project_url
        })
      });

      const data = await response.json();
      if (!data.success) {
        throw new Error(data.error);
      }

      success = 'Project added successfully';
      title = '';
      description = '';
      project_url = '';
      await loadProjects();
    } catch (e) {
      error = e instanceof Error ? e.message : 'Failed to add project';
    }
  }

  async function deleteProject(projectId: number) {
    if (!confirm('Are you sure you want to delete this project?')) return;

    try {
      const response = await fetch(`/api/projects/${id}/${username}/${projectId}`, {
        method: 'DELETE'
      });

      const data = await response.json();
      if (!data.success) {
        throw new Error(data.error);
      }

      await loadProjects();
      success = 'Project deleted successfully';
    } catch (e) {
      error = e instanceof Error ? e.message : 'Failed to delete project';
    }
  }
</script>

<div class="container mx-auto px-4 py-8">
  <div class="flex justify-between items-center mb-8">
    <h1 class="text-3xl font-bold">Manage Projects</h1>
    <a
      href={`/${username}-${id}`}
      class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
    >
      Back to Portfolio
    </a>
  </div>

  {#if error}
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
      <p>{error}</p>
    </div>
  {/if}

  {#if success}
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
      <p>{success}</p>
    </div>
  {/if}

  <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
    <div>
      <h2 class="text-2xl font-bold mb-4">Add New Project</h2>
      <form on:submit={handleSubmit} class="space-y-4">
        <div>
          <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
          <input
            type="text"
            id="title"
            bind:value={title}
            required
            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md"
          />
        </div>

        <div>
          <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
          <textarea
            id="description"
            bind:value={description}
            required
            rows="3"
            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md"
          ></textarea>
        </div>

        <div>
          <label for="project_url" class="block text-sm font-medium text-gray-700">Project URL</label>
          <input
            type="url"
            id="project_url"
            bind:value={project_url}
            required
            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md"
          />
        </div>

        <button
          type="submit"
          class="w-full px-4 py-2 border border-transparent rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700"
        >
          Add Project
        </button>
      </form>
    </div>

    <div>
      <h2 class="text-2xl font-bold mb-4">Current Projects</h2>
      <div class="space-y-4">
        {#each projects as project}
          <div class="border rounded-lg p-4">
            <h3 class="text-xl font-bold mb-2">{project.title}</h3>
            <p class="text-gray-600 mb-4">{project.description}</p>
            <div class="flex justify-between items-center">
              <a href={project.project_url} target="_blank" class="text-blue-500 hover:underline">
                View Project
              </a>
              <button
                on:click={() => deleteProject(project.id)}
                class="text-red-600 hover:text-red-800"
              >
                Delete
              </button>
            </div>
          </div>
        {/each}
      </div>
    </div>
  </div>
</div>
