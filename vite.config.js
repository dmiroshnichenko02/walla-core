import { resolve } from 'path'
import { defineConfig } from 'vite'

export default defineConfig({
	css: {
		postcss: './postcss.config.cjs'
	},
	build: {
		outDir: 'dist',
		emptyOutDir: false,
		minify: 'terser',
		terserOptions: {
			compress: {
				drop_console: false,
				drop_debugger: true
			},
			mangle: true
		},
		cssMinify: false,
		rollupOptions: {
			input: {
				'js/main': resolve(__dirname, 'src/js/main.js'),

				'css/index': resolve(__dirname, 'src/scss/index.scss')
			},
			output: {
				format: 'es',
				entryFileNames: '[name].js',
				chunkFileNames: 'js/chunks/[name]-[hash].js',
				assetFileNames: ({ name }) => {
					if (/\.(css)$/.test(name ?? '')) {
						return '[name][extname]'
					}
					return 'dist/[name][extname]'
				}
			}
		}
	}
})
