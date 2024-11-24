<script lang="ts">
  import { onMount } from 'svelte';
  import { goto } from '$app/navigation';
  
  interface PageData {
    username: string;
  }
  
  export let data: PageData;
  const { username } = data;
  
  onMount(async () => {
    try {
      const userResponse = await fetch(`/api/user/${username}`, {
        credentials: 'include'
      });
      
      if (!userResponse.ok) {
        throw new Error('User not found');
      }
      
      const userData = await userResponse.json();
      if (userData.error) {
        throw new Error(userData.error);
      }
      
      // Redirect to the correct URL structure with user ID
      goto(`/${username}/${userData.id}`);
    } catch (e) {
      console.error('Error:', e);
      goto('/404');
    }
  });
</script>

<div class="flex justify-center items-center min-h-screen">
  <div class="animate-spin rounded-full h-32 w-32 border-t-2 border-b-2 border-gray-900"></div>
</div>