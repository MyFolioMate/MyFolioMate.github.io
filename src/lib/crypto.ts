let ENCRYPTION_KEY: string | null = null;

async function getEncryptionKey() {
    if (!ENCRYPTION_KEY) {
        try {
            const response = await fetch('/api/session-verify', {
                credentials: 'include',
                headers: {
                    'Accept': 'application/json',
                    'X-Request-ID': crypto.randomUUID()
                }
            });
            
            if (!response.ok) {
                throw new Error('Failed to get encryption key');
            }

            const buffer = await response.arrayBuffer();
            const bytes = new Uint8Array(buffer);
            
            const deobfuscated = deobfuscateResponse(bytes);
            const data = JSON.parse(new TextDecoder().decode(deobfuscated));
            
            if (!data.success || !data.key || !data.token || !data.iv) {
                throw new Error('Invalid key response');
            }

            const encryptedBytes = Uint8Array.from(atob(data.key), c => c.charCodeAt(0));
            const keyBytes = Uint8Array.from(atob(data.token), c => c.charCodeAt(0));
            const ivBytes = Uint8Array.from(atob(data.iv), c => c.charCodeAt(0));

            const cryptoKey = await crypto.subtle.importKey(
                'raw',
                keyBytes,
                { name: 'AES-CBC', length: 256 },
                false,
                ['decrypt']
            );

            const decryptedBuffer = await crypto.subtle.decrypt(
                {
                    name: 'AES-CBC',
                    iv: ivBytes
                },
                cryptoKey,
                encryptedBytes
            );

            ENCRYPTION_KEY = new TextDecoder().decode(decryptedBuffer);
        } catch (error) {
            console.error('Error getting encryption key:', error);
            throw error;
        }
    }
    return ENCRYPTION_KEY;
}

function deobfuscateResponse(bytes: Uint8Array): Uint8Array {
    const mask = new Uint8Array([0x5A, 0xF3, 0xE2, 0x1D]);
    const result = new Uint8Array(bytes.length);
    
    for (let i = 0; i < bytes.length; i++) {
        result[i] = bytes[i] ^ mask[i % mask.length];
    }
    
    return result;
}

// Convert string to Uint8Array for crypto operations
async function getKeyBytes() {
    const key = await getEncryptionKey();
    if (!key) throw new Error('Encryption key is required');
    const encoder = new TextEncoder();
    const keyBytes = encoder.encode(key).slice(0, 32);
    return keyBytes;
}

export async function encryptRequest(data: any): Promise<string> {
    const jsonString = JSON.stringify(data);
    const encoder = new TextEncoder();
    const dataBuffer = encoder.encode(jsonString);
    
    const iv = crypto.getRandomValues(new Uint8Array(16));
    const key = await crypto.subtle.importKey(
        'raw',
        await getKeyBytes(),
        { name: 'AES-CBC', length: 256 },
        false,
        ['encrypt']
    );
    
    const encryptedData = await crypto.subtle.encrypt(
        { name: 'AES-CBC', iv },
        key,
        dataBuffer
    );
    
    const encryptedArray = new Uint8Array(encryptedData);
    const encryptedBase64 = btoa(String.fromCharCode.apply(null, [...encryptedArray]));
    const ivBase64 = btoa(String.fromCharCode.apply(null, [...iv]));
    
    return btoa(JSON.stringify({
        data: encryptedBase64,
        iv: ivBase64
    }));
}

export async function decryptResponse(encryptedPayload: string): Promise<any> {
    try {
        const key = await crypto.subtle.importKey(
            'raw',
            await getKeyBytes(),
            { name: 'AES-CBC', length: 256 },
            false,
            ['decrypt']
        );
        
        const payload = JSON.parse(atob(encryptedPayload));
        const { data, iv } = payload;
        
        const encryptedData = Uint8Array.from(atob(data), c => c.charCodeAt(0));
        const ivArray = Uint8Array.from(atob(iv), c => c.charCodeAt(0));
        
        const decryptedData = await crypto.subtle.decrypt(
            { name: 'AES-CBC', iv: ivArray },
            key,
            encryptedData
        );
        
        return JSON.parse(new TextDecoder().decode(decryptedData));
    } catch (error) {
        console.error('Decryption error:', error);
        throw error;
    }
}