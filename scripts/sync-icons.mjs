import { copyFileSync, existsSync, mkdirSync, readdirSync, rmSync } from 'node:fs';
import { dirname, join } from 'node:path';
import { fileURLToPath } from 'node:url';

const root = join(dirname(fileURLToPath(import.meta.url)), '..');
const source = join(root, 'node_modules', 'lucide-static', 'icons');
const target = join(root, 'resources', 'svg', 'icons');

const icons = [
    'archive', 'arrow-left', 'arrow-right', 'arrow-up', 'at-sign', 'award', 'ban', 'banknote', 'battery-charging', 'bell',
    'building-2', 'cable', 'calendar-days', 'chart-column', 'check', 'chevron-down', 'chevron-left', 'chevron-right',
    'circle-alert', 'circle-check', 'circle-help', 'clipboard-check', 'clock', 'copy', 'credit-card', 'crown', 'download', 'droplets', 'external-link',
    'eye', 'eye-off', 'factory', 'file-text', 'flag', 'globe', 'hand-coins', 'handshake', 'heart', 'heart-handshake',
    'heart-pulse', 'house', 'image', 'images', 'inbox', 'info', 'landmark', 'languages', 'layout-dashboard', 'lightbulb', 'link',
    'list', 'lock', 'log-in', 'log-out', 'mail', 'mail-open', 'map-pin', 'maximize-2', 'menu', 'message-circle', 'monitor', 'moon',
    'panel-right', 'pencil', 'phone', 'plus', 'quote', 'rotate-ccw', 'school', 'search', 'send', 'settings', 'shield-check',
    'smartphone', 'solar-panel', 'sparkles', 'sun', 'tags', 'target', 'tent', 'trash-2', 'triangle-alert', 'truck', 'undo-2', 'user-plus',
    'user-round', 'users', 'utility-pole', 'wallet', 'waves', 'wrench', 'x', 'zap', 'zap-off',
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
