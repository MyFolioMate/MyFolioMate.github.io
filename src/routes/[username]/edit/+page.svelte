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
  
  // Skills-related variables
  interface Skill {
    name: string;
    description: string;
  }

  let skillInput = '';
  let skillDescriptionInput = '';
  let skills: Skill[] = [];
  
  // Add near other skill-related variables
  let editingSkill: string | null = null;
  let editSkillName = '';
  let editSkillDescription = '';
  
  // Skills management functions
  function addSkill() {
    const trimmedSkill = skillInput.trim();
    const trimmedDescription = skillDescriptionInput.trim();

    if (trimmedSkill && !skills.some(s => s.name === trimmedSkill)) {
      skills = [...skills, { 
        name: trimmedSkill, 
        description: trimmedDescription 
      }];
      
      // Reset inputs
      skillInput = '';
      skillDescriptionInput = '';
    }
  }

  function removeSkill(skillToRemove: string) {
    skills = skills.filter(skill => skill.name !== skillToRemove);
  }
  
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
        
        // Load skills with descriptions
        skills = Array.isArray(portfolioData.skills) 
          ? portfolioData.skills.map(skill => ({
              name: typeof skill === 'string' ? skill : skill.skill,
              description: typeof skill === 'object' ? skill.description : ''
            }))
          : [];
        
        contact_info = portfolioData.contact_info || 'Email: ' + user.email;
        theme_color = portfolioData.theme_color || '#000000';
        design_template = portfolioData.design_template || 'classic';
        education = portfolioData.education || '';
        achievements = portfolioData.achievements || '';
        social_links = portfolioData.social_links || '';
      }

      // Fetch projects specifically
      const projectsData = await fetchApi(`/api/projects/${username}`);
      
      if (projectsData.success) {
        projects = projectsData.data || [];
      } else {
        error = projectsData.error || 'Failed to load projects';
        projects = [];
      }
    } catch (e) {
      error = e instanceof Error ? e.message : 'Failed to load portfolio and projects';
      projects = [];
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
          skills, // Send skills as an array of objects
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

  async function handleProjectSubmit(e: MouseEvent) {
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
                project_url: projectUrl
            })
        });

        if (!data.success) {
            throw new Error(data.error || 'Failed to add project');
        }

        // Reload projects after successful addition
        const projectsData = await fetchApi(`/api/projects/${username}`);
        projects = projectsData.success ? projectsData.data : [];
        
        // Clear form and show success
        projectTitle = '';
        projectDescription = '';
        projectUrl = '';
        success = 'Project added successfully';
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

  // Add these functions
  function startSkillEditing(skill: Skill) {
    editingSkill = skill.name;
    editSkillName = skill.name;
    editSkillDescription = skill.description;
  }

  function cancelSkillEditing() {
    editingSkill = null;
  }

  function handleEditSkill() {
    if (!editingSkill) return;
    skills = skills.map(s => 
      s.name === editingSkill 
        ? { name: editSkillName, description: editSkillDescription }
        : s
    );
    editingSkill = null;
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
          <h1 class="text-2xl font-bold text-gray-900 flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-3 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
            Editing: {title || 'My Portfolio'}
          </h1>
          <div class="flex items-center space-x-4">
            <a href="/profile" class="text-gray-600 hover:text-gray-900 flex items-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
              </svg>
              Back to Profile
            </a>
            <a 
              href={`/${username}/${user.id}`} 
              target="_blank"
              class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
              Preview Portfolio
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
      <!-- Error/Success Messages -->
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

      <!-- Main Edit Form -->
      <form on:submit={handleSubmit} class="space-y-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
          <!-- Left Column: Basic Information -->
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
                      id="theme_color"
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
                    rows="2"
                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Email, Phone, etc."
                  ></textarea>
                </div>
                <div>
                  <label for="social_links" class="block text-sm font-medium text-gray-700 mb-2">Social Links</label>
                  <textarea
                    id="social_links"
                    bind:value={social_links}
                    rows="2"
                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                    placeholder="GitHub: https://github.com/username&#10;LinkedIn: https://linkedin.com/in/username"
                  ></textarea>
                </div>
              </div>
            </div>

            <!-- Skills Management Component -->
            <div class="bg-white rounded-lg shadow-md p-6">
              <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-3 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                </svg>
                Skills Management
              </h2>

              <!-- Add Skill Form -->
              <div class="space-y-4 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label for="skillName" class="block text-sm font-medium text-gray-700 mb-2">Skill Name</label>
                    <input
                      id="skillName"
                      type="text"
                      bind:value={skillInput}
                      class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                      placeholder="Enter skill name"
                    />
                  </div>
                  <div>
                    <label for="skillDescription" class="block text-sm font-medium text-gray-700 mb-2">Skill Description (Optional)</label>
                    <input
                      id="skillDescription"
                      type="text"
                      bind:value={skillDescriptionInput}
                      class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                      placeholder="Describe your skill"
                    />
                  </div>
                </div>
                <div class="flex justify-end">
                  <button 
                    type="button"
                    on:click={addSkill}
                    class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded transition-colors"
                  >
                    Add Skill
                  </button>
                </div>
              </div>

              <!-- Skills List -->
              <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Your Skills</h3>
                {#if skills.length === 0}
                  <p class="text-gray-500 italic">No skills added yet</p>
                {:else}
                  <div class="space-y-4">
                    {#each skills as skill (skill.name)}
                      {#if editingSkill === skill.name}
                        <!-- Edit Skill Form -->
                        <div class="bg-blue-50 rounded-lg p-4 space-y-4">
                          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                              <label class="block text-sm font-medium text-gray-700 mb-2">Skill Name</label>
                              <input
                                type="text"
                                bind:value={editSkillName}
                                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm"
                              />
                            </div>
                            <div>
                              <label class="block text-sm font-medium text-gray-700 mb-2">Skill Description</label>
                              <input
                                type="text"
                                bind:value={editSkillDescription}
                                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm"
                              />
                            </div>
                          </div>
                          <div class="flex justify-end space-x-2">
                            <button 
                              type="button"
                              on:click={cancelSkillEditing}
                              class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded"
                            >
                              Cancel
                            </button>
                            <button 
                              type="button"
                              on:click={handleEditSkill}
                              class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded"
                            >
                              Save Changes
                            </button>
                          </div>
                        </div>
                      {:else}
                        <!-- Skill Display -->
                        <div class="bg-gray-50 rounded-lg p-4 flex justify-between items-start">
                          <div>
                            <h4 class="font-semibold text-gray-800">{skill.name}</h4>
                            {#if skill.description}
                              <p class="text-sm text-gray-600 mt-1">{skill.description}</p>
                            {/if}
                          </div>
                          <div class="flex space-x-2">
                            <button 
                              type="button"
                              on:click={() => startSkillEditing(skill)}
                              class="text-blue-500 hover:text-blue-700"
                              title="Edit Skill"
                            >
                              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                              </svg>
                            </button>
                            <button 
                              type="button"
                              on:click={() => removeSkill(skill.name)}
                              class="text-red-500 hover:text-red-700"
                              title="Delete Skill"
                            >
                              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                              </svg>
                            </button>
                          </div>
                        </div>
                      {/if}
                    {/each}
                  </div>
                {/if}
              </div>
            </div>

            <!-- Projects Management Component -->
            <div class="bg-white rounded-lg shadow-md p-6">
              <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-3 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19H6v-.531a3.374 3.374 0 00-.988-2.407l-.548-.547z" />
                </svg>
                Projects Management
              </h2>

              <!-- Add Project Form -->
              <div class="space-y-4 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label for="projectTitle" class="block text-sm font-medium text-gray-700 mb-2">Project Title</label>
                    <input
                      id="projectTitle"
                      type="text"
                      bind:value={projectTitle}
                      class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                      placeholder="Enter project title"
                    />
                  </div>
                  <div>
                    <label for="projectUrl" class="block text-sm font-medium text-gray-700 mb-2">Project URL</label>
                    <input
                      id="projectUrl"
                      type="url"
                      bind:value={projectUrl}
                      class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                      placeholder="https://github.com/username/project"
                    />
                  </div>
                </div>
                <div>
                  <label for="projectDescription" class="block text-sm font-medium text-gray-700 mb-2">Project Description</label>
                  <textarea
                    id="projectDescription"
                    bind:value={projectDescription}
                    rows="3"
                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Describe your project"
                  ></textarea>
                </div>
                <div class="flex justify-end">
                  <button 
                    type="button"
                    on:click={handleProjectSubmit}
                    class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded transition-colors"
                  >
                    Add Project
                  </button>
                </div>
              </div>

              <!-- Projects List -->
              <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Your Projects</h3>
                {#if projects.length === 0}
                  <p class="text-gray-500 italic">No projects added yet</p>
                {:else}
                  <div class="space-y-4">
                    {#each projects as project (project.id)}
                      {#if editingProject === project.id}
                        <!-- Edit Project Form -->
                        <div class="bg-blue-50 rounded-lg p-4 space-y-4">
                          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                              <label class="block text-sm font-medium text-gray-700 mb-2">Project Title</label>
                              <input
                                type="text"
                                bind:value={editProjectTitle}
                                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm"
                              />
                            </div>
                            <div>
                              <label class="block text-sm font-medium text-gray-700 mb-2">Project URL</label>
                              <input
                                type="url"
                                bind:value={editProjectUrl}
                                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm"
                              />
                            </div>
                          </div>
                          <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Project Description</label>
                            <textarea
                              bind:value={editProjectDescription}
                              rows="3"
                              class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm"
                            ></textarea>
                          </div>
                          <div class="flex justify-end space-x-2">
                            <button 
                              type="button"
                              on:click={cancelEditing}
                              class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded"
                            >
                              Cancel
                            </button>
                            <button 
                              type="button"
                              on:click={() => handleEditProject(project.id)}
                              class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded"
                            >
                              Save Changes
                            </button>
                          </div>
                        </div>
                      {:else}
                        <!-- Project Display -->
                        <div class="bg-gray-50 rounded-lg p-4 flex justify-between items-start">
                          <div>
                            <h4 class="font-semibold text-gray-800">{project.title}</h4>
                            <p class="text-sm text-gray-600 mt-1">{project.description}</p>
                            {#if project.project_url}
                              <a 
                                href={project.project_url} 
                                target="_blank" 
                                class="text-blue-500 hover:text-blue-700 text-sm mt-1 inline-block"
                              >
                                View Project
                              </a>
                            {/if}
                          </div>
                          <div class="flex space-x-2">
                            <button 
                              type="button"
                              on:click={() => startEditing(project)}
                              class="text-blue-500 hover:text-blue-700"
                              title="Edit Project"
                            >
                              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                              </svg>
                            </button>
                            <button 
                              type="button"
                              on:click={() => deleteProject(project.id)}
                              class="text-red-500 hover:text-red-700"
                              title="Delete Project"
                            >
                              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                              </svg>
                            </button>
                          </div>
                        </div>
                      {/if}
                    {/each}
                  </div>
                {/if}
              </div>
            </div>
          </div>

          <!-- Right Column: Design & Template -->
          <div class="space-y-6">
            <!-- Design Settings Card -->
            <div class="bg-white rounded-lg shadow-md p-6">
              <h2 class="text-xl font-semibold text-gray-900 pb-4 border-b mb-4">Design Settings</h2>
              <div class="space-y-4">
                <div>
                  <label for="design_template" class="block text-sm font-medium text-gray-700 mb-2">Template Style</label>
                  <select
                    id="design_template"
                    bind:value={design_template}
                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
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

            <!-- Additional Information Cards -->
            <div class="bg-white rounded-lg shadow-md p-6">
              <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-3 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                Additional Information
              </h2>
              <div class="space-y-4">
                <div>
                  <label for="education" class="block text-sm font-medium text-gray-700 mb-2">Education</label>
                  <textarea
                    id="education"
                    bind:value={education}
                    rows="3"
                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Degrees, certifications, etc."
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
            Save All Changes
          </button>
        </div>
      </form>
    </div>
  </div>
{/if}
