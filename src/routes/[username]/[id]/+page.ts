import type { PageLoad } from './$types.js';

export const prerender = false;

export const load: PageLoad = ({ params }) => {
  return {
    username: params.username,
    id: params.id
  };
};