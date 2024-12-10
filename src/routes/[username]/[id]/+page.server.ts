// import type { PageServerLoad } from './$types';
// import { fetchApi } from '$lib/api';

// export const load: PageServerLoad = async ({ params }) => {
//   const { username, id } = params;
  
//   try {
//     // Fetch portfolio data
//     const portfolioData = await fetchApi(`/api/portfolio/${username}/${id}`);
//     if (!portfolioData.success) {
//       throw new Error(portfolioData.error);
//     }

//     // Fetch projects data
//     const projectsData = await fetchApi(`/api/projects/${username}/${id}`);
//     if (!projectsData.success) {
//       throw new Error(projectsData.error);
//     }

//     return {
//       portfolio: portfolioData.data,
//       projects: projectsData.data
//     };
//   } catch (error) {
//     console.error('Error loading portfolio:', error);
//     throw error;
//   }
// }; 