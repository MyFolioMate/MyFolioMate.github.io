<script lang="ts">
  import { goto } from '$app/navigation';
  import { fetchApi } from '$lib/api';

  let error: string | null = null;
  let success: string | null = null;
  let loading = false;

  // Form data
  let title = '';
  let about = '';
  let contact_info = '';
  let theme_color = '#000000';
  let design_template: 'classic' | 'modern' | 'minimal' | 'creative' | 'corporate' = 'classic';
  let education = '';
  let achievements = '';
  let social_links = '';
  let skills: { name: string; description: string }[] = [];
  let projects: Project[] = [];

  // Skills-related variables
  interface Skill {
    name: string;
    description: string;
  }
  let newSkill: Skill = { name: '', description: '' };

  function addSkill() {
    if (newSkill.name && newSkill.description) {
      skills = [...skills, newSkill];
      newSkill = { name: '', description: '' };
    }
  }

  function removeSkill(index: number) {
    skills = skills.filter((_, i) => i !== index);
  }

  // Projects-related variables
  interface Project {
    title: string;
    description: string;
    url: string;
  }
  let newProject: Project = { title: '', description: '', url: '' };

  function addProject() {
    if (newProject.title && newProject.description && newProject.url) {
      projects = [...projects, newProject];
      newProject = { title: '', description: '', url: '' };
    }
  }

  function removeProject(index: number) {
    projects = projects.filter((_, i) => i !== index);
  }

  async function handleCreate(e: SubmitEvent) {
    e.preventDefault();
    error = null;
    success = null;
    loading = true;

    try {
      const data = await fetchApi('/api/createportfolio', {
        method: 'POST',
        body: JSON.stringify({
          title,
          about,
          skills,
          contact_info,
          theme_color,
          design_template,
          education,
          achievements,
          social_links,
          projects
        }),
        credentials: 'include'
      });

      if (!data.success) {
        throw new Error(data.error || 'Failed to create portfolio');
      }

      success = 'Portfolio created successfully';
      goto('/profile');
    } catch (e) {
      error = e instanceof Error ? e.message : 'Failed to create portfolio';
    } finally {
      loading = false;
    }
  }
</script>

<div class="bg-white border-b sticky top-0 z-50 shadow-sm">
  <div class="container mx-auto px-4 max-w-7xl">
    <div class="flex justify-between items-center h-16">
      <h1 class="text-2xl font-bold text-gray-900 flex items-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-3 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
        </svg>
        Create Your Portfolio
      </h1>
      <a 
        href="/profile" 
        class="text-gray-600 hover:text-gray-900 flex items-center"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        Back to Profile
      </a>
    </div>
  </div>
</div>

<div class="min-h-screen bg-gray-50">
  <div class="container mx-auto px-4 max-w-7xl py-8">
    {#if error || success}
      <div class="mb-6 sticky top-16 z-40">
        {#if error}
          <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-md" role="alert">
            <p class="font-bold">Error</p>
            <p>{error}</p>
          </div>
        {/if}
        {#if success}
          <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-md" role="alert">
            <p class="font-bold">Success</p>
            <p>{success}</p>
          </div>
        {/if}
      </div>
    {/if}

    <form on:submit={handleCreate} class="space-y-8">
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-6">
          <!-- Basic Information Card -->
          <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-3 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
              </svg>
              Basic Information
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Portfolio Title</label>
                <input
                  id="title"
                  type="text"
                  bind:value={title}
                  required
                  class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                />
              </div>
              <div>
                <label for="theme_color" class="block text-sm font-medium text-gray-700 mb-2">Theme Color</label>
                <div class="flex items-center space-x-4">
                  <input
                    type="color"
                    bind:value={theme_color}
                    class="h-12 w-12 p-1 border border-gray-300 rounded"
                  />
                  <span class="text-sm text-gray-500">{theme_color}</span>
                </div>
              </div>
            </div>

            <div class="mt-6">
              <label for="about" class="block text-sm font-medium text-gray-700 mb-2">About Me</label>
              <textarea
                id="about"
                bind:value={about}
                rows="4"
                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
              ></textarea>
            </div>
          </div>

          <!-- Contact & Social Information -->
          <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-3 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
              </svg>
              Contact & Social Links
            </h2>
            <div class="space-y-4">
              <div>
                <label for="contact_info" class="block text-sm font-medium text-gray-700 mb-2">Contact Information</label>
                <textarea
                  id="contact_info"
                  bind:value={contact_info}
                  rows="3"
                  class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                ></textarea>
              </div>
              <div>
                <label for="social_links" class="block text-sm font-medium text-gray-700 mb-2">Social Links</label>
                <textarea
                  id="social_links"
                  bind:value={social_links}
                  rows="3"
                  class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                ></textarea>
              </div>
            </div>
          </div>

          <!-- Skills Management -->
          <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-3 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19H6v-.531a3.374 3.374 0 00-.988-2.407l-.548-.547z" />
              </svg>
              Skills Management
            </h2>
            <div class="space-y-4">
              {#each skills as skill, index}
                <div class="flex justify-between items-center">
                  <div>
                    <p class="font-medium">{skill.name}</p>
                    <p class="text-sm text-gray-600">{skill.description}</p>
                  </div>
                  <button on:click={() => removeSkill(index)} class="text-red-500">Remove</button>
                </div>
              {/each}
              <div class="flex space-x-4">
                <input type="text" bind:value={newSkill.name} placeholder="Skill Name" class="border rounded px-2 py-1" />
                <input type="text" bind:value={newSkill.description} placeholder="Skill Description" class="border rounded px-2 py-1" />
                <button on:click={addSkill} class="bg-blue-500 text-white px-4 py-2 rounded">Add Skill</button>
              </div>
            </div>
          </div>

          <!-- Projects Management -->
          <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-6">Projects</h2>
            <div class="space-y-4">
              {#each projects as project, index}
                <div class="flex justify-between items-center">
                  <div>
                    <p class="font-medium">{project.title}</p>
                    <p class="text-sm text-gray-600">{project.description}</p>
                    <a href={project.url} target="_blank" class="text-blue-500">{project.url}</a>
                  </div>
                  <button on:click={() => removeProject(index)} class="text-red-500">Remove</button>
                </div>
              {/each}
              <div class="flex space-x-4">
                <input type="text" bind:value={newProject.title} placeholder="Project Title" class="border rounded px-2 py-1" />
                <input type="text" bind:value={newProject.description} placeholder="Project Description" class="border rounded px-2 py-1" />
                <input type="text" bind:value={newProject.url} placeholder="Project URL" class="border rounded px-2 py-1" />
                <button on:click={addProject} class="bg-blue-500 text-white px-4 py-2 rounded">Add Project</button>
              </div>
            </div>
          </div>
        </div>

        <div class="space-y-6">
          <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-3 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19H6v-.531a3.374 3.374 0 00-.988-2.407l-.548-.547z" />
              </svg>
              Education & Achievements
            </h2>
            <div class="space-y-4">
              <div>
                <label for="education" class="block text-sm font-medium text-gray-700 mb-2">Education</label>
                <textarea
                  id="education"
                  bind:value={education}
                  rows="3"
                  class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                  placeholder="Your educational background"
                ></textarea>
              </div>
              <div>
                <label for="achievements" class="block text-sm font-medium text-gray-700 mb-2">Achievements</label>
                <textarea
                  id="achievements"
                  bind:value={achievements}
                  rows="3"
                  class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                  placeholder="Awards, recognitions, etc."
                ></textarea>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Save Changes Button -->
      <div class="flex justify-end">
        <button
          type="submit"
          class="px-8 py-3 bg-blue-600 text-white rounded-md shadow-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
        >
          Create Portfolio
        </button>
      </div>
    </form>
  </div>
</div> 