import { encryptRequest, decryptResponse } from './crypto.js';

export async function fetchApi(url: string, options: RequestInit = {}) {
  const isPost = options.method === 'POST' || options.method === 'PUT';
  
  if (isPost && options.body) {
    options.body = await encryptRequest(JSON.parse(options.body as string));
  }
  
  const response = await fetch(url, {
    ...options,
    headers: {
      ...options.headers,
      'Content-Type': 'application/x-encrypted-json'
    }
  });
  
  const encryptedData = await response.text();
  return await decryptResponse(encryptedData);
}