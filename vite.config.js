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

    css: {
        preprocessorOptions: {
            scss: {
                // Todo build cuspia 310+ avisos de depreciação do Dart Sass, e
                // nenhum vinha do nosso SCSS: a origem é `bootstrap/scss/*`, que
                // ainda usa `@import`, as funções globais (`red()`, `green()`,
                // `blue()`) e o `if()` antigo. Não temos como corrigir código de
                // dependência, e o ruído tinha o efeito oposto ao de um aviso —
                // enterrava qualquer depreciação nossa no meio de centenas de
                // linhas. Silenciamos as quatro categorias por nome, e não com
                // um `quietDeps` geral, para que uma categoria nova apareça.
                silenceDeprecations: [
                    'import',
                    'global-builtin',
                    'color-functions',
                    'if-function',
                ],
            },
        },
    },

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
