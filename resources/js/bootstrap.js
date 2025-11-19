import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Optional: Realtime via Laravel Echo (Pusher-compatible servers like Laravel WebSockets/Soketi)
try {
	const key = import.meta?.env?.VITE_REVERB_APP_KEY ?? import.meta?.env?.VITE_PUSHER_APP_KEY;
	if (key) {
		const Pusher = (await import('pusher-js')).default;
		const Echo = (await import('laravel-echo')).default;
		window.Pusher = Pusher;
		window.Echo = new Echo({
			broadcaster: 'pusher',
			key,
			cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER ?? 'mt1',
			wsHost: import.meta.env.VITE_REVERB_HOST ?? import.meta.env.VITE_PUSHER_HOST ?? window.location.hostname,
			wsPort: Number(import.meta.env.VITE_REVERB_PORT ?? import.meta.env.VITE_PUSHER_PORT ?? 6001),
			wssPort: Number(import.meta.env.VITE_REVERB_PORT ?? import.meta.env.VITE_PUSHER_PORT ?? 6001),
			forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? import.meta.env.VITE_PUSHER_SCHEME ?? (location.protocol === 'https:' ? 'https' : 'http')) === 'https',
			enabledTransports: ['ws', 'wss'],
		});
	}
} catch (e) {
	// Echo setup is optional; ignore if not available
}
