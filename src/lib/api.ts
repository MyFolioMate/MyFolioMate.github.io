import { encryptRequest, decryptResponse } from './crypto.js';

interface FetchOptions extends RequestInit {
  encrypt?: boolean;
}

export async function fetchApi(url: string, options: FetchOptions = {}) {
    try {
        const isPost = options.method === 'POST' || options.method === 'PUT';
        const shouldEncrypt = options.encrypt || !url.includes('/session-verify');
        
        // Handle request body encryption
        if (isPost && options.body) {
            const bodyData = JSON.parse(options.body as string);
            options.body = shouldEncrypt 
                ? await encryptRequest(bodyData)
                : JSON.stringify(bodyData);
        }
        
        // Set appropriate headers
        const headers = {
            ...options.headers,
            'Content-Type': shouldEncrypt 
                ? 'application/x-encrypted-json'
                : 'application/json'
        };
        
        const response = await fetch(url, { ...options, headers });
        
        if (!response.ok) {
            throw new Error('API request failed');
        }
        
        const responseData = await response.text();
        
        // Handle response decryption
        try {
            return shouldEncrypt 
                ? await decryptResponse(responseData)
                : JSON.parse(responseData);
        } catch (e) {
            console.error('Response processing error:', e);
            return JSON.parse(responseData);
        }
    } catch (error) {
        console.error('API Error:', error);
        throw error;
    }
}