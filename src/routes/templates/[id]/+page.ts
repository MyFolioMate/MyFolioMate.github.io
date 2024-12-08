import type { PageLoad } from './$types.js';

// Define valid template IDs
const validTemplates = ['classic', 'modern', 'minimal', 'creative', 'corporate'] as const;
type TemplateId = typeof validTemplates[number];

export const prerender = true;

export const load = (({ params }) => {
  const templateId = params.id.toLowerCase();
  
  if (!validTemplates.includes(templateId as TemplateId)) {
    throw new Error('Invalid template ID');
  }

  return {
    templateId
  };
}) satisfies PageLoad; 