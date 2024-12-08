<script lang="ts">
  import { page } from '$app/stores';
  import Classic from '$lib/components/portfolio-templates/Classic.svelte';
  import Modern from '$lib/components/portfolio-templates/Modern.svelte';
  import Minimal from '$lib/components/portfolio-templates/Minimal.svelte';
  import Creative from '$lib/components/portfolio-templates/Creative.svelte';
  import Corporate from '$lib/components/portfolio-templates/Corporate.svelte';

  // Sample data for preview
  const previewData = {
    title: 'Sample Portfolio',
    about: 'This is a preview of how your portfolio could look with this template.',
    user: {
      full_name: 'John Doe',
      email: 'john@example.com'
    },
    skills: [
      { name: 'Web Development', description: 'Full-stack development with modern technologies' },
      { name: 'UI/UX Design', description: 'Creating user-friendly interfaces' },
      { name: 'Project Management', description: 'Agile methodologies' }
    ],
    projects: [
      {
        title: 'E-commerce Platform',
        description: 'A full-featured online shopping platform',
        project_url: '#'
      },
      {
        title: 'Portfolio Builder',
        description: 'A tool for creating professional portfolios',
        project_url: '#'
      }
    ],
    contact_info: 'Email: john@example.com\nLinkedIn: linkedin.com/in/johndoe',
    theme_color: '#3B82F6',
    design_template: $page.params.id,
    education: 'Bachelor in Computer Science\nMaster in Software Engineering',
    achievements: 'Best Developer Award 2023\nOpen Source Contributor',
    social_links: 'github.com/johndoe, linkedin.com/in/johndoe'
  };

  const templates: Record<string, any> = {
    classic: Classic,
    modern: Modern,
    minimal: Minimal,
    creative: Creative,
    corporate: Corporate
  };

  const templateId = $page.params.id.toLowerCase();
  const SelectedTemplate = templates[templateId as keyof typeof templates];
</script>

<div class="min-h-screen bg-gray-50">
  <!-- Preview Header -->
  <div class="bg-white border-b sticky top-0 z-50 shadow-sm">
    <div class="container mx-auto px-4">
      <div class="flex justify-between items-center h-16">
        <h1 class="text-xl font-semibold">Template Preview: {templateId}</h1>
      </div>
    </div>
  </div>

  <!-- Template Preview -->
  {#if SelectedTemplate}
    <svelte:component this={SelectedTemplate} portfolio={previewData} projects={previewData.projects} />
  {:else}
    <div class="container mx-auto px-4 py-16 text-center">
      <h1 class="text-2xl font-bold text-red-600">Template not found</h1>
      <a href="/templates" class="text-blue-600 hover:underline mt-4 inline-block">
        Back to templates
      </a>
    </div>
  {/if}
</div> 