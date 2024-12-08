import type { PageLoad } from './$types.js';

export const prerender = true;

export const load: PageLoad = ({ params }) => {
  return {
    username: params.username,
    id: params.id
  };
};