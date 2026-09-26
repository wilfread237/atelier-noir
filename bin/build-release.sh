#!/usr/bin/env bash
# Builds dist/atelier-noir.zip (theme + bundled plugin) and dist/atelier-noir-core.zip.
# Unversioned names keep releases/latest/download/<file> links stable.
set -euo pipefail

root="$(cd "$(dirname "$0")/.." && pwd)"
version="$(sed -n 's/^Version:[[:space:]]*//p' "$root/style.css" | head -1 | tr -d '\r')"
dist="$root/dist"
work="$(mktemp -d)"
trap 'rm -rf "$work"' EXIT

rm -rf "$dist"
mkdir -p "$dist" "$work/atelier-noir/bundled"

(cd "$root/plugins" && zip -rq "$work/atelier-noir-core.zip" atelier-noir-core -x '*.DS_Store')

tar -C "$root" -cf - \
	--exclude './.git' --exclude './.github' --exclude './.gitignore' --exclude './.gitattributes' \
	--exclude './bin' --exclude './dist' --exclude './bundled' --exclude './plugins' --exclude './tests' \
	--exclude './vendor' --exclude './composer.json' --exclude './composer.lock' --exclude './phpunit.xml.dist' \
	--exclude './.phpunit.result.cache' --exclude './CONTRIBUTING.md' --exclude './CODE_OF_CONDUCT.md' \
	--exclude './SECURITY.md' --exclude '.DS_Store' . \
	| tar -C "$work/atelier-noir" -xf -
cp "$work/atelier-noir-core.zip" "$work/atelier-noir/bundled/atelier-noir-core.zip"

(cd "$work" && zip -rq "$dist/atelier-noir.zip" atelier-noir)
cp "$work/atelier-noir-core.zip" "$dist/atelier-noir-core.zip"
echo "Atelier Noir $version"
(cd "$dist" && sha256sum ./*.zip > SHA256SUMS.txt)

ls -lh "$dist"
