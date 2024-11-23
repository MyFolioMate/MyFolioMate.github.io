<script lang="ts">
  export let portfolio;
  export let projects;
</script>

<div class="min-h-screen bg-gray-50">
  <div class="bg-white shadow-sm">
    <div class="max-w-7xl mx-auto px-4 py-6">
    </div>
  </div>

  <div class="max-w-7xl mx-auto px-4 py-12">
    <div class="bg-white shadow-xl rounded-lg overflow-hidden">
      <!-- Hero Section -->
      <div class="px-8 py-16 border-b relative" style="background-color: {portfolio.theme_color}05">
        <div class="max-w-3xl">
          <h1 class="text-4xl font-semibold mb-6" style="color: {portfolio.theme_color}">
            {portfolio.title || 'My Portfolio'}
          </h1>
          <p class="text-lg text-gray-600 mb-8">{portfolio.about}</p>
          {#if portfolio.social_links}
            <div class="flex space-x-4">
              {#each portfolio.social_links.split(',') as link}
                <a href={link.trim()} target="_blank" class="text-gray-600 hover:text-gray-900">
                  <!-- Add social icons -->
                </a>
              {/each}
            </div>
          {/if}
        </div>
      </div>

      <div class="px-8 py-12">
        <!-- Experience & Education -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
          <section class="bg-gray-50 p-8 rounded-lg">
            <h2 class="text-2xl font-semibold mb-6" style="color: {portfolio.theme_color}">
              Professional Experience
            </h2>
            <div class="prose">
              {@html portfolio.experience || 'No experience listed yet.'}
            </div>
          </section>

          <section class="bg-gray-50 p-8 rounded-lg">
            <h2 class="text-2xl font-semibold mb-6" style="color: {portfolio.theme_color}">
              Education & Certifications
            </h2>
            <div class="prose">
              {@html portfolio.education || 'No education listed yet.'}
            </div>
          </section>
        </div>

        <!-- Skills -->
        <section class="mb-12">
          <h2 class="text-2xl font-semibold mb-6" style="color: {portfolio.theme_color}">Core Competencies</h2>
          <div class="flex flex-wrap gap-3">
            {#if portfolio.skills}
              {#each portfolio.skills.split(',') as skill}
                <span class="px-4 py-2 rounded-md text-gray-700 bg-gray-100 border border-gray-200">
                  {skill}
                </span>
              {/each}
            {/if}
          </div>
        </section>

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
                  class="inline-flex items-center text-gray-700 hover:text-gray-900"
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

        <!-- Contact -->
        <section class="bg-gray-50 p-8 rounded-lg">
          <h2 class="text-2xl font-semibold mb-6" style="color: {portfolio.theme_color}">Contact Information</h2>
          <div class="prose text-gray-700">
            {@html portfolio.contact_info}
          </div>
        </section>
      </div>
    </div>
  </div>
</div> 