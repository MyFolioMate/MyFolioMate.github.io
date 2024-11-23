<script lang="ts">
  export let portfolio;
  export let projects;
</script>

<div class="min-h-screen bg-white">
  <div class="max-w-4xl mx-auto px-4 py-20">


    <h1 class="text-6xl font-light mb-16 tracking-tight">{portfolio.title || 'My Portfolio'}</h1>
    
    <div class="space-y-32">
      <!-- About -->
      <section>
        <p class="text-xl leading-relaxed text-gray-700 max-w-2xl">{portfolio.about}</p>
        {#if portfolio.social_links}
          <div class="flex space-x-6 mt-8">
            {#each portfolio.social_links.split(',') as link}
              <a href={link.trim()} target="_blank" class="text-gray-400 hover:text-gray-800 transition-colors">
                <!-- Add social icons -->
              </a>
            {/each}
          </div>
        {/if}
      </section>

      <!-- Experience -->
      <section>
        <h2 class="text-2xl font-light mb-8">Experience</h2>
        <div class="prose prose-gray max-w-2xl">
          {@html portfolio.experience || 'No experience listed yet.'}
        </div>
      </section>

      <!-- Education -->
      <section>
        <h2 class="text-2xl font-light mb-8">Education</h2>
        <div class="prose prose-gray max-w-2xl">
          {@html portfolio.education || 'No education listed yet.'}
        </div>
      </section>

      <!-- Skills -->
      <section>
        <h2 class="text-2xl font-light mb-8">Skills</h2>
        <div class="flex flex-wrap gap-4">
          {#if portfolio.skills}
            {#each portfolio.skills.split(',') as skill}
              <span class="px-4 py-2 border border-gray-200 rounded-sm text-sm">
                {skill}
              </span>
            {/each}
          {/if}
        </div>
      </section>

      <!-- Achievements -->
      <section>
        <h2 class="text-2xl font-light mb-8">Achievements</h2>
        <div class="prose prose-gray max-w-2xl">
          {@html portfolio.achievements || 'No achievements listed yet.'}
        </div>
      </section>

      <!-- Projects -->
      <section>
        <h2 class="text-2xl font-light mb-12">Projects</h2>
        <div class="space-y-16">
          {#each projects as project}
            <div class="group">
              <h3 class="text-xl mb-3">{project.title}</h3>
              <p class="text-gray-600 mb-4 max-w-2xl">{project.description}</p>
              <a 
                href={project.project_url} 
                target="_blank" 
                class="text-sm text-gray-500 hover:text-black transition-colors"
              >
                View Project →
              </a>
            </div>
          {/each}
        </div>
      </section>

      <!-- Contact -->
      <section>
        <h2 class="text-2xl font-light mb-8">Contact</h2>
        <div class="text-gray-600 max-w-2xl">{portfolio.contact_info}</div>
      </section>
    </div>
  </div>
</div> 