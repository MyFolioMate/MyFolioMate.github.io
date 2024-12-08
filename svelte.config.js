import adapter from '@sveltejs/adapter-auto';
import { preprocessMeltUI } from '@melt-ui/pp';
import sequence from 'svelte-sequential-preprocessor';
import preprocess from 'svelte-preprocess';

/** @type {import('@sveltejs/kit').Config} */
const config = {
	kit: {
		adapter: adapter(),
		prerender: {
			handleMissingId: 'ignore'
		}
	},
	preprocess: sequence([
		preprocess(),
		preprocessMeltUI()
	])
};

export default config;
