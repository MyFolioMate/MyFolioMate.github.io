<script lang="ts">
  import { page } from '$app/stores';
  import { goto } from '$app/navigation';
  import { onMount } from 'svelte';

  const username = $page.params.username;
  const id = $page.params.id;
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
  
  // Add these to the existing script section
  let projects: any[] = [];
  let projectTitle = '';
  let projectDescription = '';
  let projectUrl = '';
  
  // Add these with the other project-related variables
  let editingProject: number | null = null;
  let editProjectTitle = '';
  let editProjectDescription = '';
  let editProjectUrl = '';
  
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

      // Add this to the onMount function after loading portfolio data
      const projectsRes = await fetch(`/api/projects/${username}`);
      if (projectsRes.ok) {
        const projectsData = await projectsRes.json();
        projects = projectsData.success ? projectsData.data : [];
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

  async function handleProjectSubmit(e: SubmitEvent) {
    e.preventDefault();
    error = null;
    success = null;

    try {
      const response = await fetch('/api/addproject', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          user_id: user.id,
          title: projectTitle,
          description: projectDescription,
          project_url: projectUrl,
          image_url: '' // Optional
        })
      });

      const data = await response.json();
      if (!data.success) {
        throw new Error(data.error);
      }

      success = 'Project added successfully';
      projectTitle = '';
      projectDescription = '';
      projectUrl = '';
      
      // Reload projects
      const projectsRes = await fetch(`/api/projects/${username}`);
      if (projectsRes.ok) {
        const projectsData = await projectsRes.json();
        projects = projectsData.success ? projectsData.data : [];
      }
    } catch (e) {
      error = e instanceof Error ? e.message : 'Failed to add project';
    }
  }

  async function deleteProject(projectId: number) {
    if (!confirm('Are you sure you want to delete this project?')) return;

    try {
      const response = await fetch(`/api/projects/${user.id}/${username}/${projectId}`, {
        method: 'DELETE'
      });

      const data = await response.json();
      if (!data.success) {
        throw new Error(data.error);
      }

      // Reload projects
      const projectsRes = await fetch(`/api/projects/${username}`);
      if (projectsRes.ok) {
        const projectsData = await projectsRes.json();
        projects = projectsData.success ? projectsData.data : [];
      }
      success = 'Project deleted successfully';
    } catch (e) {
      error = e instanceof Error ? e.message : 'Failed to delete project';
    }
  }

  async function handleAddProject(e: Event) {
    e.preventDefault();
    error = null;
    success = null;

    try {
      const response = await fetch('/api/projects', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          title: projectTitle,
          description: projectDescription,
          project_url: projectUrl,
          image_url: '' // Optional, add if you want to support images
        })
      });

      const data = await response.json();
      if (!data.success) {
        throw new Error(data.error || 'Failed to add project');
      }

      // Clear form and show success message
      projectTitle = '';
      projectDescription = '';
      projectUrl = '';
      success = 'Project added successfully';
      
      // Reload projects
      const projectsRes = await fetch(`/api/projects/${username}`);
      if (projectsRes.ok) {
        const projectsData = await projectsRes.json();
        projects = projectsData.success ? projectsData.data : [];
      }
    } catch (e) {
      error = e instanceof Error ? e.message : 'Failed to add project';
    }
  }

  // Add this function to handle editing
  function startEditing(project: any) {
    editingProject = project.id;
    editProjectTitle = project.title;
    editProjectDescription = project.description;
    editProjectUrl = project.project_url;
  }

  function cancelEditing() {
    editingProject = null;
  }

  async function handleEditProject(projectId: number) {
    error = null;
    success = null;

    try {
      const response = await fetch(`/api/projects/${user.id}/${username}/${projectId}`, {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          title: editProjectTitle,
          description: editProjectDescription,
          project_url: editProjectUrl
        })
      });

      const data = await response.json();
      if (!data.success) {
        throw new Error(data.error);
      }

      // Reset editing state
      editingProject = null;
      success = 'Project updated successfully';
      
      // Reload projects
      const projectsRes = await fetch(`/api/projects/${username}`);
      if (projectsRes.ok) {
        const projectsData = await projectsRes.json();
        projects = projectsData.success ? projectsData.data : [];
      }
    } catch (e) {
      error = e instanceof Error ? e.message : 'Failed to update project';
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

    <div class="mt-12 border-t pt-8">
      <h2 class="text-2xl font-bold mb-6">Manage Projects</h2>
      
      <!-- Add Project Form -->
      <div class="bg-gray-50 p-6 rounded-lg mb-8">
        <h3 class="text-lg font-semibold mb-4">Add New Project</h3>
        <form on:submit={handleProjectSubmit} class="space-y-4">
          <div>
            <label for="projectTitle" class="block text-sm font-medium text-gray-700">Title</label>
            <input
              id="projectTitle"
              type="text"
              bind:value={projectTitle}
              required
              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md"
            />
          </div>

          <div>
            <label for="projectDescription" class="block text-sm font-medium text-gray-700">Description</label>
            <textarea
              id="projectDescription"
              bind:value={projectDescription}
              required
              rows="3"
              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md"
            ></textarea>
          </div>

          <div>
            <label for="projectUrl" class="block text-sm font-medium text-gray-700">Project URL</label>
            <input
              id="projectUrl"
              type="url"
              bind:value={projectUrl}
              required
              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md"
            />
          </div>

          <button
            type="submit"
            class="w-full px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
          >
            Add Project
          </button>
        </form>
      </div>

      <!-- Projects List -->
      <div>
        <h3 class="text-lg font-semibold mb-4">Current Projects</h3>
        <div class="space-y-4">
          {#each projects as project}
            <div class="border rounded-lg p-4">
              {#if editingProject === project.id}
                <!-- Edit Form -->
                <form on:submit|preventDefault={() => handleEditProject(project.id)} class="space-y-4">
                  <div>
                    <label for="editTitle" class="block text-sm font-medium text-gray-700">Title</label>
                    <input
                      id="editTitle"
                      type="text"
                      bind:value={editProjectTitle}
                      required
                      class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md"
                    />
                  </div>

                  <div>
                    <label for="editDescription" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea
                      id="editDescription"
                      bind:value={editProjectDescription}
                      required
                      rows="3"
                      class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md"
                    ></textarea>
                  </div>

                  <div>
                    <label for="editUrl" class="block text-sm font-medium text-gray-700">Project URL</label>
                    <input
                      id="editUrl"
                      type="url"
                      bind:value={editProjectUrl}
                      required
                      class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md"
                    />
                  </div>

                  <div class="flex justify-end space-x-2">
                    <button
                      type="button"
                      on:click={cancelEditing}
                      class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
                    >
                      Cancel
                    </button>
                    <button
                      type="submit"
                      class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
                    >
                      Save Changes
                    </button>
                  </div>
                </form>
              {:else}
                <!-- Project Display -->
                <h4 class="text-lg font-medium mb-2">{project.title}</h4>
                <p class="text-gray-600 mb-4">{project.description}</p>
                <div class="flex justify-between items-center">
                  <div class="space-x-4">
                    <a 
                      href={project.project_url} 
                      target="_blank" 
                      class="text-blue-600 hover:text-blue-800"
                    >
                      View Project
                    </a>
                    <button
                      on:click={() => startEditing(project)}
                      class="text-blue-600 hover:text-blue-800"
                    >
                      Edit
                    </button>
                  </div>
                  <button
                    on:click={() => deleteProject(project.id)}
                    class="text-red-600 hover:text-red-800"
                  >
                    Delete
                  </button>
                </div>
              {/if}
            </div>
          {/each}
        </div>
      </div>
    </div>
  </div>
{/if}
