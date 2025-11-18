export const JOB_DEV_API_URL = 'http://localhost:3002';
export const JOB_API_URL = 'https://king-prawn-app-kplso.ondigitalocean.app';
export const DEV_API_URL = 'http://localhost/trussmate/backend-php';
export const API_URL = 'https://api.trussmate.co.za';
export const MODE = process.env.NODE_ENV || 'development';
export const BASE_URL = MODE === 'development' ? DEV_API_URL : API_URL;
export const JOB_BASE_URL = MODE === 'development' ? JOB_DEV_API_URL : JOB_API_URL;
