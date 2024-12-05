import { encryptRequest, decryptResponse } from './crypto.js';

export async function fetchApi(url: string, options: RequestInit = {}) {
    try {
        const isPost = options.method === 'POST' || options.method === 'PUT';
        const isAuthEndpoint = url.includes('/login') || url.includes('/register');
        
        if (isPost && options.body && !isAuthEndpoint) {
            options.body = await encryptRequest(JSON.parse(options.body as string));
        }
        
        const headers = {
            ...options.headers,
            'Content-Type': isAuthEndpoint ? 'application/json' : 'application/x-encrypted-json'
        };
        
        const response = await fetch(url, { ...options, headers });
        
        if (!response.ok) {
            throw new Error('API request failed');
        }
        
        const responseData = await response.text();
        
        if (url.includes('/login')) {
            try {
                return await decryptResponse(responseData);
            } catch {
                return JSON.parse(responseData);
            }
        }
        
        return isAuthEndpoint ? JSON.parse(responseData) : await decryptResponse(responseData);
    } catch (error) {
        console.error('API Error:', error);
        throw error;
    }
}