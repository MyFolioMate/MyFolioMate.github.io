<script>
    import { page } from '$app/stores';
    
    // Show main layout only for edit page
    const isEditPage = $page.url.pathname.endsWith('/edit');
</script>

{#if isEditPage}
    <div class="min-h-screen flex flex-col">
        <header class="bg-white shadow-sm">
            <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16">
                <div class="flex justify-between items-center h-full">
                    <a href="/" class="text-xl font-bold text-gray-900">Portfolio Hub</a>
                    <div class="flex items-center space-x-4">
                        <a href="/profile" class="text-gray-600 hover:text-gray-900">Back to Profile</a>
                        <a href="/{$page.params.username}/{$page.params.id}" target="_blank" class="text-blue-600 hover:text-blue-800">View Portfolio</a>
                    </div>
                </div>
            </nav>
        </header>

        <main class="flex-grow bg-gray-50">
            <slot />
        </main>
    </div>
{:else}
    <slot />
{/if}