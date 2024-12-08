import { defineConfig } from 'vite';
import { sveltekit } from '@sveltejs/kit/vite';

export default defineConfig({
	plugins: [sveltekit()],
	server: {
		proxy: {
			'/api': {
				target: 'https://formalytics.me/api-myfolio/',
				changeOrigin: true,
				rewrite: (path) => path.replace(/^\/api/, ''),
				secure: true
			}
		}
	}
});
