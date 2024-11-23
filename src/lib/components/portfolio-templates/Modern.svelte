<script lang="ts">
  export let portfolio;
  export let projects;
</script>

<div class="min-h-screen" style="background-color: {portfolio.theme_color}10">
  <div class="max-w-5xl mx-auto px-4 py-16">


    <!-- Hero Section -->
    <div class="bg-white rounded-2xl shadow-xl p-12 mb-12">
      <h1 class="text-5xl font-bold mb-6" style="color: {portfolio.theme_color}">
        {portfolio.title || 'My Portfolio'}
      </h1>
      <p class="text-xl text-gray-600 mb-8">{portfolio.about}</p>
      {#if portfolio.social_links}
        <div class="flex space-x-4">
          {#each portfolio.social_links.split(',') as link}
            <a href={link.trim()} target="_blank" class="text-gray-500 hover:text-gray-900">
              <!-- Add social icons -->
            </a>
          {/each}
        </div>
      {/if}
    </div>

    <!-- Skills Section -->
    <div class="bg-white rounded-2xl shadow-xl p-8 mb-12">
      <h2 class="text-3xl font-bold mb-6" style="color: {portfolio.theme_color}">Skills</h2>
      <div class="flex flex-wrap gap-4">
        {#if portfolio.skills}
          {#each portfolio.skills.split(',') as skill}
            <span 
              class="px-4 py-2 rounded-full text-white transition-opacity hover:opacity-90"
              style="background-color: {portfolio.theme_color}"
            >
              {skill}
            </span>
          {/each}
        {/if}
      </div>
    </div>

    <!-- Projects Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
      {#each projects as project}
        <div class="bg-white rounded-2xl shadow-xl p-8 hover:shadow-2xl transition-shadow">
          <h3 class="text-2xl font-bold mb-4" style="color: {portfolio.theme_color}">{project.title}</h3>
          <p class="text-gray-600 mb-6">{project.description}</p>
          <a 
            href={project.project_url} 
            target="_blank" 
            class="inline-flex items-center px-4 py-2 rounded-full text-white transition-opacity hover:opacity-90"
            style="background-color: {portfolio.theme_color}"
          >
            View Project
            <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
            </svg>
          </a>
        </div>
      {/each}
    </div>

    <!-- Experience & Education -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
      <div class="bg-white rounded-2xl shadow-xl p-8">
        <h2 class="text-3xl font-bold mb-6" style="color: {portfolio.theme_color}">Experience</h2>
        <div class="prose text-gray-600">
          {@html portfolio.experience || 'No experience listed yet.'}
        </div>
      </div>

      <div class="bg-white rounded-2xl shadow-xl p-8">
        <h2 class="text-3xl font-bold mb-6" style="color: {portfolio.theme_color}">Education</h2>
        <div class="prose text-gray-600">
          {@html portfolio.education || 'No education listed yet.'}
        </div>
      </div>
    </div>

    <!-- Achievements -->
    <div class="bg-white rounded-2xl shadow-xl p-8 mb-12">
      <h2 class="text-3xl font-bold mb-6" style="color: {portfolio.theme_color}">Achievements</h2>
      <div class="prose text-gray-600">
        {@html portfolio.achievements || 'No achievements listed yet.'}
      </div>
    </div>

    <!-- Contact -->
    <div class="bg-white rounded-2xl shadow-xl p-8">
      <h2 class="text-3xl font-bold mb-6" style="color: {portfolio.theme_color}">Contact</h2>
      <div class="text-gray-600">
        {@html portfolio.contact_info}
      </div>
    </div>
  </div>
</div> 