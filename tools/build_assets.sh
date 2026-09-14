#!/usr/bin/env sh
# Build locally; Hostinger only needs the generated files. No Node runtime required.
set -eu
cd "$(dirname "$0")/.."
./node_modules/.bin/esbuild public/assets/css/landing.css --bundle --minify --outfile=public/assets/css/landing.min.css
./node_modules/.bin/esbuild public/assets/css/platforms.css --minify --outfile=public/assets/css/platforms.min.css
./node_modules/.bin/esbuild public/assets/js/main.js --minify --outfile=public/assets/js/main.min.js
