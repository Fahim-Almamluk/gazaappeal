import { copyFileSync, existsSync, mkdirSync, readdirSync, rmSync } from 'node:fs';
import { dirname, join } from 'node:path';
import { fileURLToPath } from 'node:url';

const root = join(dirname(fileURLToPath(import.meta.url)), '..');
const source = join(root, 'node_modules', 'lucide-static', 'icons');
const target = join(root, 'resources', 'svg', 'icons');

const icons = [
    'archive', 'arrow-left', 'arrow-right', 'arrow-up', 'at-sign', 'award', 'badge-check', 'ban', 'banknote', 'battery-charging',
    'bell', 'bitcoin', 'book-open', 'briefcase', 'building-2', 'cable', 'calendar-days', 'chart-column', 'check',
    'chevron-down', 'chevron-left', 'chevron-right', 'circle-alert', 'circle-check', 'circle-help', 'clipboard-check', 'clock', 'copy', 'credit-card',
    'droplets', 'external-link', 'eye', 'eye-off', 'factory', 'file-text', 'flag', 'gauge', 'globe', 'hand-coins',
    'hand-heart', 'handshake', 'heart', 'heart-handshake', 'heart-pulse', 'house', 'image', 'images', 'inbox', 'info',
    'landmark', 'languages', 'layout-dashboard', 'lightbulb', 'link', 'list', 'lock', 'log-in', 'log-out', 'mail', 'mail-open',
    'map-pin', 'maximize-2', 'menu', 'message-circle', 'monitor', 'moon', 'panel-right', 'pencil', 'phone', 'plug-zap', 'plus',
    'power-off', 'quote', 'rotate-ccw', 'school', 'search', 'send', 'server', 'settings', 'shield-check', 'smartphone',
    'solar-panel', 'sparkles', 'stethoscope', 'sun', 'target', 'tent', 'trash-2', 'trending-up', 'triangle-alert',
    'truck', 'user-check', 'user-plus', 'user-round', 'users', 'utility-pole', 'wallet', 'waves', 'wrench', 'x', 'zap',
    'zap-off',
];

rmSync(target, { recursive: true, force: true });
mkdirSync(target, { recursive: true });

const missing = icons.filter((name) => !existsSync(join(source, `${name}.svg`)));
if (missing.length) {
    console.error(`Missing icons: ${missing.join(', ')}`);
    process.exit(1);
}

icons.forEach((name) => copyFileSync(join(source, `${name}.svg`), join(target, `${name}.svg`)));
console.log(`Synced ${readdirSync(target).length} icons.`);
