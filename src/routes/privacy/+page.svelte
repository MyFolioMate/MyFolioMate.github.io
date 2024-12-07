<script lang="ts">
  import LegalHeader from '$lib/components/LegalHeader.svelte';
</script>

<div class="min-h-screen bg-gray-50">
  <!-- Hero Section -->
  <div class="bg-gradient-to-r from-blue-600 to-blue-800 text-white">
    <div class="container mx-auto px-4 py-16">
      <div class="max-w-3xl mx-auto text-center">
        <h1 class="text-4xl font-bold mb-4">Privacy Policy</h1>
        <p class="text-xl text-blue-100">
          How we collect, use, and protect your information
        </p>
      </div>
    </div>
  </div>

  <div class="container mx-auto px-4 py-12 max-w-4xl">
    <div class="bg-white rounded-lg shadow-lg p-8">
      <div class="prose max-w-none">
        <section class="mb-12">
          <h2 class="text-2xl font-semibold mb-6 text-blue-800">Information We Collect</h2>
          <p class="mb-4 text-gray-600">We collect information that you provide directly to us when you:</p>
          <ul class="list-disc pl-6 mb-4 text-gray-600 space-y-2">
            <li>Create an account</li>
            <li>Create or edit your portfolio</li>
            <li>Contact us</li>
            <li>Upload content</li>
          </ul>
        </section>

        <section class="mb-12">
          <h2 class="text-2xl font-semibold mb-6 text-blue-800">How We Use Your Information</h2>
          <p class="mb-4 text-gray-600">We use the information we collect to:</p>
          <ul class="list-disc pl-6 mb-4 text-gray-600 space-y-2">
            <li>Provide and maintain our services</li>
            <li>Process your requests and transactions</li>
            <li>Send you technical notices and support messages</li>
            <li>Improve our services</li>
          </ul>
        </section>

        <section class="mb-12">
          <h2 class="text-2xl font-semibold mb-6 text-blue-800">Information Security</h2>
          <p class="mb-4 text-gray-600">
            We implement appropriate security measures to protect your personal information. 
            However, no method of transmission over the Internet is 100% secure, and we cannot 
            guarantee absolute security.
          </p>
        </section>

        <section class="mb-12">
          <h2 class="text-2xl font-semibold mb-6 text-blue-800">Contact Us</h2>
          <p class="mb-4 text-gray-600">
            If you have any questions about this Privacy Policy, please contact us at:
            <a href="mailto:privacy@portfoliohub.com" class="text-blue-600 hover:text-blue-800">
              privacy@portfoliohub.com
            </a>
          </p>
        </section>
      </div>
    </div>
  </div>
</div> 