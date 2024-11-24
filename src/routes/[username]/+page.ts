import type { PageLoad } from './$types.js';

export const load: PageLoad = ({ params }) => {
  return {
    username: params.username
  };
};