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
			entries: ['*']
		}
	},
	preprocess: preprocess({
		postcss: true
	})
};

export default config;
