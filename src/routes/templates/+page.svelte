<script lang="ts">
  const templates = [
    {
      id: 'classic',
      name: 'Classic',
      description: 'A timeless design that works for any profession',
      category: 'Professional'
    },
    {
      id: 'modern',
      name: 'Modern',
      description: 'Contemporary layout with clean aesthetics',
      category: 'Creative'
    },
    {
      id: 'minimal',
      name: 'Minimal',
      description: 'Clean and minimalistic design',
      category: 'Creative'
    },
    {
      id: 'creative',
      name: 'Creative',
      description: 'Bold design for creative professionals',
      category: 'Creative'
    },
    {
      id: 'corporate',
      name: 'Corporate',
      description: 'Professional design for business profiles',
      category: 'Business'
    }
  ];

  let selectedCategory = 'All';
  const categories = ['All', 'Professional', 'Creative', 'Business'];
</script>

<div class="container mx-auto px-4 py-8">
  <div class="max-w-7xl mx-auto">
    <h1 class="text-3xl font-bold mb-8">Portfolio Templates</h1>
    
    <!-- Category Filter -->
    <div class="flex gap-4 mb-8">
      {#each categories as category}
        <button
          class="px-4 py-2 rounded-md {selectedCategory === category ? 'bg-blue-500 text-white' : 'bg-gray-100 hover:bg-gray-200'}"
          on:click={() => selectedCategory = category}
        >
          {category}
        </button>
      {/each}
    </div>

    <!-- Templates Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      {#each templates.filter(t => selectedCategory === 'All' || t.category === selectedCategory) as template}
        <div class="bg-white rounded-lg shadow-sm overflow-hidden border hover:shadow-md transition">
          <div class="p-6">
            <h3 class="text-xl font-semibold mb-2">{template.name}</h3>
            <p class="text-gray-600 mb-4">{template.description}</p>
            <div class="flex justify-between items-center">
              <span class="text-sm text-blue-500">{template.category}</span>
              <a
                href={`/templates/${template.id}`}
                class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition"
              >
                Preview
              </a>
            </div>
          </div>
        </div>
      {/each}
    </div>
  </div>
</div> 