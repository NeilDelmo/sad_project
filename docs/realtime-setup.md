# Realtime Notifications (Laravel Reverb)

This project is configured to use Laravel Reverb for broadcasting realtime events (notifications, toasts) with Laravel Echo in the browser.

## 1) .env

Copy from `.env.example` or add these:

```
BROADCAST_CONNECTION=reverb
REVERB_APP_ID=local
REVERB_APP_KEY=local
REVERB_APP_SECRET=local
REVERB_HOST=127.0.0.1
REVERB_PORT=6001
REVERB_SCHEME=http

VITE_REVERB_APP_KEY=${REVERB_APP_KEY}
VITE_REVERB_HOST=${REVERB_HOST}
VITE_REVERB_PORT=${REVERB_PORT}
VITE_REVERB_SCHEME=${REVERB_SCHEME}
```

## 2) Local development

- Start Reverb:
```
./vendor/bin/sail artisan reverb:start --port=6001 --hostname=0.0.0.0
```
- Start Vite (if port 5173 is busy, choose another):
```
./vendor/bin/sail npm run dev
# or
VITE_PORT=5174 ./vendor/bin/sail npm run dev
```

## 3) Production (Nginx proxy on 443)

Terminate WebSockets over TLS on Nginx and proxy to Reverb on 127.0.0.1:6001.

Example (inside your SSL server block):

```
server {
    listen 443 ssl http2;
    server_name wss.example.com;

    ssl_certificate     /etc/letsencrypt/live/wss.example.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/wss.example.com/privkey.pem;

    # Proxy WebSocket upgrades
    location /app {
        proxy_pass http://127.0.0.1:6001;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection "upgrade";
        proxy_set_header Host $host;
        proxy_read_timeout 60s;
    }
}
```

Then set in your production `.env`:

```
REVERB_HOST=wss.example.com
REVERB_SCHEME=https
REVERB_PORT=443

VITE_REVERB_HOST=${REVERB_HOST}
VITE_REVERB_SCHEME=${REVERB_SCHEME}
VITE_REVERB_PORT=${REVERB_PORT}
```

## 4) Troubleshooting
- If the browser can’t connect, check the console for the Echo/Pusher URL it’s trying to reach and confirm host/port/scheme match your `.env`.
- Ensure only one Vite dev server is running to avoid port conflicts.
- Firewalls: allow 443 (TLS) or 6001 (plain WS) depending on your setup.
