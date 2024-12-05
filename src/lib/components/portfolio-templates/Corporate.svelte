<script lang="ts">
  export let portfolio;
  export let projects;
</script>

<style>
  @keyframes float {
    0% { transform: translateY(0px); }
    50% { transform: translateY(-20px); }
    100% { transform: translateY(0px); }
  }
</style>

<main class="min-h-screen bg-gray-50">
  <header class="bg-white shadow-sm sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 py-4">
      <div class="flex justify-between items-center">
        <h2 class="text-xl font-semibold flex items-center">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="color: {portfolio.theme_color}">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
          </svg>
          <span style="color: {portfolio.theme_color}">{portfolio.user?.full_name || 'Portfolio'}</span>
        </h2>
        <nav class="flex space-x-8">
          <button 
            on:click={() => document.getElementById('about')?.scrollIntoView({ behavior: 'smooth' })}
            class="text-gray-600 hover:text-gray-900 flex items-center">About</button>
          <button 
            on:click={() => document.getElementById('skills')?.scrollIntoView({ behavior: 'smooth' })}
            class="text-gray-600 hover:text-gray-900 flex items-center">Skills</button>
          <button 
            on:click={() => document.getElementById('projects')?.scrollIntoView({ behavior: 'smooth' })}
            class="text-gray-600 hover:text-gray-900 flex items-center">Projects</button>
          <button 
            on:click={() => document.getElementById('contact')?.scrollIntoView({ behavior: 'smooth' })}
            class="text-gray-600 hover:text-gray-900 flex items-center">Contact</button>
        </nav>
      </div>
    </div>
  </header>
  <section id="about" class="bg-white shadow-sm">
    <div class="max-w-7xl mx-auto px-4 py-6">
      <div class="px-8 py-16 border-b relative" style="background-color: {portfolio.theme_color}05">
        <div class="max-w-3xl">
          <h1 class="text-4xl font-semibold mb-6" style="color: {portfolio.theme_color}">
            {portfolio.title || 'My Portfolio'}
          </h1>
          <p class="text-lg text-gray-600 mb-8">{portfolio.about}</p>
          {#if portfolio.social_links}
            <div class="flex space-x-4">
              {#each portfolio.social_links.split(',') as link}
                <a 
                  href={link.trim()} 
                  target="_blank" 
                  class="text-gray-600 hover:text-gray-900 transition-colors"
                  style="color: {portfolio.theme_color}"
                >
                  {link.includes('linkedin') ? 'LinkedIn' : 
                   link.includes('github') ? 'GitHub' : 
                   link.includes('twitter') ? 'Twitter' : 'Link'}
                </a>
              {/each}
            </div>
          {/if}
        </div>
      </div>
    </div>
  </section>

  <section id="skills" class="bg-gray-50 p-8 rounded-lg">
    <div class="max-w-7xl mx-auto px-4 py-6">
      <div class="px-8 py-12">
        <!-- Education & Skills -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
          <section class="bg-gray-50 p-8 rounded-lg">
            <h2 class="text-2xl font-semibold mb-6" style="color: {portfolio.theme_color}">
              Education & Certifications
            </h2>
            <div class="prose">
              {@html portfolio.education || 'No education listed yet.'}
            </div>
          </section>

          <section class="bg-gray-50 p-8 rounded-lg">
            <h2 class="text-2xl font-semibold mb-6" style="color: {portfolio.theme_color}">
              Core Competencies
            </h2>
            <div class="flex flex-wrap gap-3">
              {#if portfolio.skills}
                {#each portfolio.skills as skill}
                  <div class="group relative">
                    <span 
                      class="px-4 py-2 rounded-md text-white transition-colors"
                      style="background-color: {portfolio.theme_color}"
                    >
                      {skill.name}
                    </span>
                    {#if skill.description}
                      <div class="absolute z-10 bottom-full mb-2 left-0 hidden group-hover:block bg-white p-4 rounded-lg shadow-lg text-sm min-w-[200px] border border-gray-100">
                        <p class="text-gray-700">{skill.description}</p>
                        <div class="absolute bottom-[-6px] left-4 w-3 h-3 bg-white border-r border-b border-gray-100 transform rotate-45"></div>
                      </div>
                    {/if}
                  </div>
                {/each}
              {/if}
            </div>
          </section>
        </div>
      </div>
    </div>
  </section>

  <section id="projects" class="mb-12">
    <div class="max-w-7xl mx-auto px-4 py-6">
      <div class="px-8 py-12">
        <!-- Projects -->
        <section class="mb-12">
          <h2 class="text-2xl font-semibold mb-8" style="color: {portfolio.theme_color}">Featured Projects</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {#each projects as project}
              <div class="bg-gray-50 rounded-lg p-6 hover:shadow-lg transition-all duration-300">
                <h3 class="text-xl font-semibold mb-3">{project.title}</h3>
                <p class="text-gray-600 mb-4">{project.description}</p>
                <a 
                  href={project.project_url} 
                  target="_blank" 
                  class="inline-flex items-center transition-colors"
                  style="color: {portfolio.theme_color}"
                >
                  View Details
                  <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                  </svg>
                </a>
              </div>
            {/each}
          </div>
        </section>

        <!-- Achievements -->
        <section class="mb-12">
          <h2 class="text-2xl font-semibold mb-6" style="color: {portfolio.theme_color}">Notable Achievements</h2>
          <div class="prose max-w-none">
            {@html portfolio.achievements || 'No achievements listed yet.'}
          </div>
        </section>
      </div>
    </div>
  </section>

  <section id="contact" class="bg-gray-50 p-8 rounded-lg">
    <div class="max-w-7xl mx-auto px-4 py-6">
      <div class="px-8 py-12">
        <!-- Contact -->
        <section class="bg-gray-50 p-8 rounded-lg">
          <h2 class="text-2xl font-semibold mb-6" style="color: {portfolio.theme_color}">Contact Information</h2>
          <div class="prose text-gray-700">
            {@html portfolio.contact_info}
          </div>
        </section>
      </div>
    </div>
  </section>
</main> 