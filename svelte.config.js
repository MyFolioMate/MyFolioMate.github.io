import adapter from '@sveltejs/adapter-static';
import { vitePreprocess } from '@sveltejs/vite-plugin-svelte';

/** @type {import('@sveltejs/kit').Config} */
const config = {
	kit: {
		adapter: adapter({
			pages: 'build',
			assets: 'build',
			fallback: '404.html',
			strict: false
		}),
		paths: {
			base: process.env.NODE_ENV === 'production' ? '/MyFolioMate.github.io' : ''
		}
	},
	preprocess: vitePreprocess()
};

export default config;
