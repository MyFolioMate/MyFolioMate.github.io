import { redirect } from '@sveltejs/kit';
import type { PageServerLoad } from './$types';

export const load: PageServerLoad = async ({ params }) => {
  // Redirect to the proper portfolio URL structure
  throw redirect(301, `/portfolio/${params.username}/${params.id}`);
}; 