<script lang="ts">
  export let portfolio;
  export let projects;
</script>

<div class="min-h-screen" style="background-color: {portfolio.theme_color}05">
  <div class="max-w-6xl mx-auto px-4 py-16">


    <div class="mb-24">
      <h1 class="text-8xl font-bold mb-8" style="color: {portfolio.theme_color}">
        {portfolio.title || 'My Portfolio'}
      </h1>
      <p class="text-2xl leading-relaxed max-w-3xl">{portfolio.about}</p>
      {#if portfolio.social_links}
        <div class="flex space-x-6 mt-8">
          {#each portfolio.social_links.split(',') as link}
            <a href={link.trim()} target="_blank" class="text-black hover:text-gray-600 transition-colors">
              <!-- Add social icons -->
            </a>
          {/each}
        </div>
      {/if}
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-24 mb-24">
      <section>
        <h2 class="text-4xl font-bold mb-8" style="color: {portfolio.theme_color}">Experience</h2>
        <div class="prose prose-lg text-gray-800">
          {@html portfolio.experience || 'No experience listed yet.'}
        </div>
      </section>

      <section>
        <h2 class="text-4xl font-bold mb-8" style="color: {portfolio.theme_color}">Education</h2>
        <div class="prose prose-lg text-gray-800">
          {@html portfolio.education || 'No education listed yet.'}
        </div>
      </section>
    </div>

    <section class="mb-24">
      <h2 class="text-4xl font-bold mb-8" style="color: {portfolio.theme_color}">Skills</h2>
      <div class="flex flex-wrap gap-4">
        {#if portfolio.skills}
          {#each portfolio.skills.split(',') as skill}
            <span class="px-6 py-3 bg-black text-white text-lg transform hover:rotate-2 transition-transform">
              {skill}
            </span>
          {/each}
        {/if}
      </div>
    </section>

    <section class="mb-24">
      <h2 class="text-4xl font-bold mb-12" style="color: {portfolio.theme_color}">Projects</h2>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
        {#each projects as project}
          <div class="group relative p-8 border-2 border-black hover:bg-black transition-colors">
            <h3 class="text-2xl font-bold mb-4 group-hover:text-white">{project.title}</h3>
            <p class="text-gray-600 mb-6 group-hover:text-gray-300">{project.description}</p>
            <a 
              href={project.project_url} 
              target="_blank" 
              class="inline-block text-lg group-hover:text-white"
            >
              View Project →
            </a>
          </div>
        {/each}
      </div>
    </section>

    <section class="mb-24">
      <h2 class="text-4xl font-bold mb-8" style="color: {portfolio.theme_color}">Achievements</h2>
      <div class="prose prose-lg text-gray-800 max-w-none">
        {@html portfolio.achievements || 'No achievements listed yet.'}
      </div>
    </section>

    <section>
      <h2 class="text-4xl font-bold mb-8" style="color: {portfolio.theme_color}">Contact</h2>
      <div class="text-xl text-gray-800">{portfolio.contact_info}</div>
    </section>
  </div>
</div> 