<script lang="ts">
  export let portfolio;
  export let projects;
</script>

<div class="min-h-screen bg-white">
  <div class="max-w-4xl mx-auto px-4 py-20">
    <h1 class="text-6xl font-light mb-16 tracking-tight" style="color: {portfolio.theme_color}">
      {portfolio.title || 'My Portfolio'}
    </h1>
    
    <div class="space-y-32">
      <!-- About -->
      <section>
        <p class="text-xl leading-relaxed text-gray-700 max-w-2xl">{portfolio.about}</p>
        {#if portfolio.social_links}
          <div class="flex space-x-6 mt-8">
            {#each portfolio.social_links.split(',') as link}
              <a 
                href={link.trim()} 
                target="_blank" 
                class="text-gray-400 hover:text-gray-800 transition-colors"
                style="color: {portfolio.theme_color}"
              >
                {link.includes('linkedin') ? 'LinkedIn' : 
                 link.includes('github') ? 'GitHub' : 
                 link.includes('twitter') ? 'Twitter' : 'Link'}
              </a>
            {/each}
          </div>
        {/if}
      </section>

      <!-- Education -->
      <section>
        <h2 class="text-2xl font-light mb-8" style="color: {portfolio.theme_color}">Education</h2>
        <div class="prose prose-gray max-w-2xl">
          {@html portfolio.education || 'No education listed yet.'}
        </div>
      </section>

      <!-- Skills -->
      <section>
        <h2 class="text-2xl font-light mb-8" style="color: {portfolio.theme_color}">Skills</h2>
        <div class="flex flex-wrap gap-4">
          {#if portfolio.skills}
            {#each portfolio.skills as skill}
              <span 
                class="px-4 py-2 border rounded-sm text-sm transition-colors"
                style="border-color: {portfolio.theme_color}; color: {portfolio.theme_color}"
              >
                {skill}
              </span>
            {/each}
          {/if}
        </div>
      </section>

      <!-- Achievements -->
      <section>
        <h2 class="text-2xl font-light mb-8" style="color: {portfolio.theme_color}">Achievements</h2>
        <div class="prose prose-gray max-w-2xl">
          {@html portfolio.achievements || 'No achievements listed yet.'}
        </div>
      </section>

      <!-- Projects -->
      <section>
        <h2 class="text-2xl font-light mb-12" style="color: {portfolio.theme_color}">Projects</h2>
        <div class="space-y-16">
          {#each projects as project}
            <div class="group">
              <h3 class="text-xl mb-3">{project.title}</h3>
              <p class="text-gray-600 mb-4 max-w-2xl">{project.description}</p>
              <a 
                href={project.project_url} 
                target="_blank" 
                class="text-sm transition-colors"
                style="color: {portfolio.theme_color}"
              >
                View Project →
              </a>
            </div>
          {/each}
        </div>
      </section>

      <!-- Contact -->
      <section>
        <h2 class="text-2xl font-light mb-8" style="color: {portfolio.theme_color}">Contact</h2>
        <div class="prose prose-gray max-w-2xl">
          {@html portfolio.contact_info}
        </div>
      </section>
    </div>
  </div>
</div> 