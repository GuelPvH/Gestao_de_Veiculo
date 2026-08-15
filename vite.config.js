import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

const vitePort = Number(process.env.VITE_PORT ?? 5175);

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.scss', 'resources/js/app.js'],
            refresh: true,
        }),
    ],

    server: {
        // Escuta em todas as interfaces do container — sozinho isso NÃO basta.
        host: '0.0.0.0',
        port: vitePort,
        strictPort: true,

        // URL que o NAVEGADOR DO HOST consegue alcançar. Sem isto o browser
        // tenta se conectar ao hostname interno do container e o HMR morre
        // em silêncio: a página não recarrega e nada aparece no console.
        hmr: {
            host: 'localhost',
            protocol: 'ws',
            clientPort: vitePort,
        },

        // inotify não propaga através de bind mount em macOS/Windows/WSL2:
        // sem polling, salvar um arquivo não recarrega nada. Em Linux nativo
        // o polling pode ser desligado (custa CPU).
        watch: {
            usePolling: true,
            interval: 300,
            ignored: ['**/storage/framework/views/**', '**/vendor/**'],
        },
    },
});
