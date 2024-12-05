<script lang="ts">
  export let portfolio;
  export let projects;
</script>

<header class="fixed top-0 left-0 right-0 bg-white/80 backdrop-blur-sm z-50">
  <div class="max-w-4xl mx-auto px-4 py-4">
    <div class="flex justify-between items-center">
      <h2 class="text-lg font-light" style="color: {portfolio.theme_color}">
        {portfolio.user?.full_name || 'Portfolio'}
      </h2>
      <nav class="flex space-x-8">
        <button 
          on:click={() => document.getElementById('about')?.scrollIntoView({ behavior: 'smooth' })}
          class="text-gray-400 hover:text-gray-800 transition-colors text-sm">About</button>
        <button 
          on:click={() => document.getElementById('skills')?.scrollIntoView({ behavior: 'smooth' })}
          class="text-gray-400 hover:text-gray-800 transition-colors text-sm">Skills</button>
        <button 
          on:click={() => document.getElementById('projects')?.scrollIntoView({ behavior: 'smooth' })}
          class="text-gray-400 hover:text-gray-800 transition-colors text-sm">Projects</button>
        <button 
          on:click={() => document.getElementById('contact')?.scrollIntoView({ behavior: 'smooth' })}
          class="text-gray-400 hover:text-gray-800 transition-colors text-sm">Contact</button>
      </nav>
    </div>
  </div>
</header>

<div class="min-h-screen bg-white pt-20">
  <div class="max-w-4xl mx-auto px-4 py-20">
    <h1 class="text-6xl font-light mb-16 tracking-tight" style="color: {portfolio.theme_color}">
      {portfolio.title || 'My Portfolio'}
    </h1>
    
    <div class="space-y-32">
      <!-- About -->
      <section id="about">
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
      <section id="skills">
        <h2 class="text-2xl font-light mb-8" style="color: {portfolio.theme_color}">Skills</h2>
        <div class="flex flex-wrap gap-4">
          {#if portfolio.skills}
            {#each portfolio.skills as skill}
              <div class="group relative">
                <span 
                  class="px-4 py-2 border rounded-sm text-sm transition-colors"
                  style="border-color: {portfolio.theme_color}; color: {portfolio.theme_color}"
                >
                  {skill.name}
                </span>
                {#if skill.description}
                  <div class="absolute z-10 bottom-full mb-2 left-0 hidden group-hover:block bg-white border border-gray-100 p-3 text-sm min-w-[200px]">
                    <p class="text-gray-600">{skill.description}</p>
                  </div>
                {/if}
              </div>
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
      <section id="projects">
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
      <section id="contact">
        <h2 class="text-2xl font-light mb-8" style="color: {portfolio.theme_color}">Contact</h2>
        <div class="prose prose-gray max-w-2xl">
          {@html portfolio.contact_info}
        </div>
      </section>
    </div>
  </div>
</div> 