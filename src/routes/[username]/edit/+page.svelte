<script lang="ts">
  import { page } from '$app/stores';
  import { goto } from '$app/navigation';
  import { onMount } from 'svelte';
  import { fetchApi } from '$lib/api';

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
      const userData = await fetchApi('/api/user', {
        credentials: 'include'
      });
      
      if (userData.error || userData.username !== username) {
        goto('/login');
        return;
      }
      
      user = userData;

      const portfolioData = await fetchApi(`/api/portfolio/${username}`, {
        credentials: 'include'
      });
      
      if (portfolioData && !portfolioData.error) {
        title = portfolioData.title || 'My Portfolio';
        about = portfolioData.about || 'Welcome to my portfolio';
        skills = Array.isArray(portfolioData.skills) ? portfolioData.skills.join(', ') : (portfolioData.skills || '');
        contact_info = portfolioData.contact_info || 'Email: ' + user.email;
        theme_color = portfolioData.theme_color || '#000000';
        design_template = portfolioData.design_template || 'classic';
        education = portfolioData.education || '';
        achievements = portfolioData.achievements || '';
        social_links = portfolioData.social_links || '';
      }

      // Add this to the onMount function after loading portfolio data
      const projectsData = await fetchApi(`/api/projects/${username}`);
      projects = projectsData.success ? projectsData.data : [];
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
      const data = await fetchApi('/api/updateportfolio', {
        method: 'POST',
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

      if (!data.success) {
        throw new Error(data.error);
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
        const data = await fetchApi('/api/projects', {
            method: 'POST',
            body: JSON.stringify({
                user_id: user.id,
                title: projectTitle,
                description: projectDescription,
                project_url: projectUrl,
                image_url: '' // Add empty string as it's required by the database
            })
        });

        if (!data.success) {
            throw new Error(data.error);
        }

        // Clear form
        projectTitle = '';
        projectDescription = '';
        projectUrl = '';
        success = 'Project added successfully';
        
        // Reload projects
        const projectsData = await fetchApi(`/api/projects/${username}`);
        projects = projectsData.success ? projectsData.data : [];
    } catch (e) {
        error = e instanceof Error ? e.message : 'Failed to add project';
    }
  }

  async function deleteProject(projectId: number) {
    if (!confirm('Are you sure you want to delete this project?')) return;

    try {
      const data = await fetchApi(`/api/projects/${projectId}`, {
        method: 'DELETE'
      });

      if (!data.success) {
        throw new Error(data.error);
      }

      const projectsData = await fetchApi(`/api/projects/${username}`);
      projects = projectsData.success ? projectsData.data : [];
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
      const data = await fetchApi(`/api/projects/${projectId}`, {
        method: 'PUT',
        body: JSON.stringify({
          id: projectId,
          title: editProjectTitle,
          description: editProjectDescription,
          project_url: editProjectUrl,
          user_id: user.id,
          username: username
        })
      });

      if (!data.success) {
        throw new Error(data.error);
      }

      editingProject = null;
      success = 'Project updated successfully';
      
      const projectsData = await fetchApi(`/api/projects/${username}`);
      projects = projectsData.success ? projectsData.data : [];
    } catch (e) {
      error = e instanceof Error ? e.message : 'Failed to update project';
    }
  }
</script>

{#if loading}
  <div class="flex justify-center items-center min-h-screen">
    <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-blue-500"></div>
  </div>
{:else if error}
  <div class="container mx-auto px-4 py-8">
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded" role="alert">
      <p class="font-bold">Error</p>
      <p>{error}</p>
    </div>
  </div>
{:else}
  <div class="min-h-screen bg-gray-50">
    <!-- Top Navigation Bar with Preview -->
    <div class="bg-white border-b sticky top-0 z-50 shadow-sm">
      <div class="container mx-auto px-4 max-w-7xl">
        <div class="flex justify-between items-center h-16">
          <h1 class="text-xl font-bold text-gray-900">Editing: {title || 'My Portfolio'}</h1>
          <div class="flex items-center space-x-4">
            <a href="/profile" class="text-gray-600 hover:text-gray-900">
              ← Back to Profile
            </a>
            <a 
              href={`/${username}`} 
              target="_blank"
              class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
              <span>Preview Portfolio</span>
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
              </svg>
            </a>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content Area -->
    <div class="container mx-auto px-4 max-w-7xl py-8">
      <!-- Success/Error Messages -->
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

      <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Main Form Area -->
        <div class="lg:col-span-3 space-y-6">
          <!-- Basic Information Card -->
          <div class="bg-white rounded-lg shadow-md">
            <div class="p-6">
              <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                Basic Information
              </h2>
              <form on:submit={handleSubmit} class="space-y-6">
                <!-- Basic fields -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div>
                    <label for="title" class="block text-sm font-medium text-gray-700">Portfolio Title</label>
                    <input
                      id="title"
                      type="text"
                      bind:value={title}
                      required
                      class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                    />
                  </div>
                  <div>
                    <label for="theme_color" class="block text-sm font-medium text-gray-700">Theme Color</label>
                    <div class="mt-1 flex items-center space-x-3">
                      <input
                        id="theme_color"
                        type="color"
                        bind:value={theme_color}
                        class="h-10 w-20 p-1 border border-gray-300 rounded"
                      />
                      <span class="text-sm text-gray-500">{theme_color}</span>
                    </div>
                  </div>
                </div>

                <!-- About section -->
                <div>
                  <label for="about" class="block text-sm font-medium text-gray-700">About Me</label>
                  <textarea
                    id="about"
                    bind:value={about}
                    rows="4"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                  ></textarea>
                </div>

                <!-- Add after the About section, before the Save Button -->
                <div class="space-y-6">
                  <!-- Skills -->
                  <div>
                    <label for="skills" class="block text-sm font-medium text-gray-700">Skills (comma-separated)</label>
                    <input
                      id="skills"
                      type="text"
                      bind:value={skills}
                      placeholder="React, TypeScript, Node.js"
                      class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                    />
                  </div>

                  <!-- Contact Info -->
                  <div>
                    <label for="contact_info" class="block text-sm font-medium text-gray-700">Contact Information</label>
                    <textarea
                      id="contact_info"
                      bind:value={contact_info}
                      rows="2"
                      class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                    ></textarea>
                  </div>

                  <!-- Education -->
                  <div>
                    <label for="education" class="block text-sm font-medium text-gray-700">Education</label>
                    <textarea
                      id="education"
                      bind:value={education}
                      rows="3"
                      class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                    ></textarea>
                  </div>

                  <!-- Achievements -->
                  <div>
                    <label for="achievements" class="block text-sm font-medium text-gray-700">Achievements</label>
                    <textarea
                      id="achievements"
                      bind:value={achievements}
                      rows="3"
                      class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                    ></textarea>
                  </div>

                  <!-- Social Links -->
                  <div>
                    <label for="social_links" class="block text-sm font-medium text-gray-700">Social Links</label>
                    <textarea
                      id="social_links"
                      bind:value={social_links}
                      rows="2"
                      placeholder="GitHub: https://github.com/username&#10;LinkedIn: https://linkedin.com/in/username"
                      class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                    ></textarea>
                  </div>
                </div>

                <!-- Save Button -->
                <div class="flex justify-end pt-4">
                  <button
                    type="submit"
                    class="px-6 py-2 bg-blue-600 text-white rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                  >
                    Save Changes
                  </button>
                </div>
              </form>
            </div>
          </div>

          <!-- Projects Section -->
          <div class="bg-white rounded-lg shadow-md">
            <div class="p-6">
              <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                Projects
              </h2>
              
              <!-- Add New Project Form -->
              <div class="mb-8 border-b pb-6">
                <form on:submit={handleProjectSubmit} class="space-y-4">
                  <div>
                    <label for="projectTitle" class="block text-sm font-medium text-gray-700">Project Title</label>
                    <input
                      id="projectTitle"
                      type="text"
                      bind:value={projectTitle}
                      required
                      class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                    />
                  </div>

                  <div>
                    <label for="projectDescription" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea
                      id="projectDescription"
                      bind:value={projectDescription}
                      required
                      rows="3"
                      class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                    ></textarea>
                  </div>

                  <div>
                    <label for="projectUrl" class="block text-sm font-medium text-gray-700">Project URL</label>
                    <input
                      id="projectUrl"
                      type="url"
                      bind:value={projectUrl}
                      required
                      placeholder="https://"
                      class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                    />
                  </div>

                  <div class="flex justify-end">
                    <button
                      type="submit"
                      class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                    >
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                      </svg>
                      Add Project
                    </button>
                  </div>
                </form>
              </div>

              <!-- Projects Grid -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {#each projects as project}
                  <div class="border rounded-lg p-4 hover:shadow-md transition-shadow">
                    {#if editingProject === project.id}
                      <!-- Edit Project Form -->
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
                      <h3 class="font-semibold text-lg mb-2">{project.title}</h3>
                      <p class="text-gray-600 text-sm mb-4">{project.description}</p>
                      <div class="flex justify-between items-center">
                        <a href={project.project_url} target="_blank" class="text-blue-600 hover:text-blue-800 text-sm">
                          View Project →
                        </a>
                        <div class="space-x-2">
                          <button 
                            on:click={() => startEditing(project)}
                            class="text-gray-600 hover:text-gray-800"
                          >
                            Edit
                          </button>
                          <button 
                            on:click={() => deleteProject(project.id)}
                            class="text-red-600 hover:text-red-800"
                          >
                            Delete
                          </button>
                        </div>
                      </div>
                    {/if}
                  </div>
                {/each}
              </div>
            </div>
          </div>
        </div>

        <!-- Right Column - Design & Template -->
        <div class="lg:col-span-1">
          <div class="bg-white rounded-lg shadow p-6 space-y-6">
            <h2 class="text-xl font-semibold text-gray-900 pb-4 border-b">Design Settings</h2>
            <div class="space-y-4">
              <div>
                <label for="design_template" class="block text-sm font-medium text-gray-700">Template Style</label>
                <select
                  id="design_template"
                  bind:value={design_template}
                  class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                >
                  <option value="classic">Classic</option>
                  <option value="modern">Modern</option>
                  <option value="minimal">Minimal</option>
                  <option value="creative">Creative</option>
                  <option value="corporate">Corporate</option>
                </select>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
{/if}
