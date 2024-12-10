import adapter from '@sveltejs/adapter-static';
import preprocess from 'svelte-preprocess';

/** @type {import('@sveltejs/kit').Config} */
const config = {
	kit: {
		adapter: adapter({
			pages: "build",
			assets: "build",
			fallback: "index.html",
			precompress: false,
			strict: false
		}),
		prerender: {
			handleMissingId: 'ignore',
			entries: [
				'/',
				'/templates',
				'/templates/classic',
				'/templates/modern',
				'/templates/minimal',
				'/templates/creative',
				'/templates/corporate',
				'/explore',
				'/login',
				'/register',
				'/privacy',
				'/terms'
			],
			handleHttpError: ({ path, referrer, message }) => {
				// Ignore 404s
				if (message.includes('Not found')) return;
				throw new Error(message);
			}
		}
	},
	preprocess: preprocess({
		postcss: true
	})
};

export default config;
