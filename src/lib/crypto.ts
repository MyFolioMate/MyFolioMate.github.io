const ENCRYPTION_KEY = 'YourSecureKey123!@#$%^&*()+=-_0123456789abcdefghijk';

// Convert string to Uint8Array for crypto operations
function getKeyBytes() {
  const encoder = new TextEncoder();
  const keyBytes = encoder.encode(ENCRYPTION_KEY).slice(0, 32); // Ensure exactly 32 bytes
  return keyBytes;
}

export async function encryptRequest(data: any): Promise<string> {
  const jsonString = JSON.stringify(data);
  const encoder = new TextEncoder();
  const dataBuffer = encoder.encode(jsonString);
  
  const iv = crypto.getRandomValues(new Uint8Array(16));
  const key = await crypto.subtle.importKey(
    'raw',
    getKeyBytes(),
    { name: 'AES-CBC', length: 256 },
    false,
    ['encrypt']
  );
  
  const encryptedData = await crypto.subtle.encrypt(
    { name: 'AES-CBC', iv },
    key,
    dataBuffer
  );
  
  // Use a safer base64 encoding approach
  const encryptedArray = new Uint8Array(encryptedData);
  const encryptedBase64 = btoa(
    Array.from(encryptedArray)
      .map(byte => String.fromCharCode(byte))
      .join('')
  );
  
  const ivBase64 = btoa(
    Array.from(iv)
      .map(byte => String.fromCharCode(byte))
      .join('')
  );
  
  const payload = {
    data: encryptedBase64,
    iv: ivBase64
  };
  
  return btoa(encodeURIComponent(JSON.stringify(payload)));
}

export async function decryptResponse(encryptedPayload: string): Promise<any> {
  // Decode the outer payload first
  const payload = JSON.parse(atob(encryptedPayload));
  const { data, iv } = payload;
  
  // Convert base64 back to Uint8Array - remove the base64url handling since we're using standard base64
  const encryptedData = Uint8Array.from(atob(data), c => c.charCodeAt(0));
  const ivArray = Uint8Array.from(atob(iv), c => c.charCodeAt(0));
  
  const key = await crypto.subtle.importKey(
    'raw',
    getKeyBytes(),
    { name: 'AES-CBC', length: 256 },
    false,
    ['decrypt']
  );
  
  const decryptedData = await crypto.subtle.decrypt(
    { name: 'AES-CBC', iv: ivArray },
    key,
    encryptedData
  );
  
  return JSON.parse(new TextDecoder().decode(decryptedData));
}