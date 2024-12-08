import { sveltekit } from '@sveltejs/kit/vite';
import { defineConfig } from 'vite';

export default defineConfig(({ mode }) => ({
	plugins: [sveltekit()],
	server: {
		proxy: mode === 'development' ? {
			'/api': {
				target: 'https://formalytics.me/api-myfolio/',
				changeOrigin: true,
				rewrite: (path) => path.replace(/^\/api/, ''),
				secure: true
			}
		} : undefined
	}
}));
