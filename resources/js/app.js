import Alpine from '@alpinejs/csp';
import focus from '@alpinejs/focus';
import theme from './components/theme';
import shell from './components/shell';
import scrollTop from './components/scroll-top';
import bankCard from './components/bank-card';
import damageMap from './components/damage-map';
import gallery from './components/gallery';
import permissionTree from './components/permission-tree';
import { registerCleanUrl } from './components/clean-url';
import { registerConfirmations } from './components/confirm';
import { registerFormGuard } from './components/form-guard';
import { registerScrollEffects } from './components/scroll-effects';
import { copyText, dropdown, imagePreview, localeTabs, methodFields, navGroup, passwordField, submitGuard, tabs, toast } from './components/ui';

Alpine.plugin(focus);

Alpine.data('theme', theme);
Alpine.data('shell', shell);
Alpine.data('scrollTop', scrollTop);
Alpine.data('bankCard', bankCard);
Alpine.data('damageMap', damageMap);
Alpine.data('gallery', gallery);
Alpine.data('permissionTree', permissionTree);
Alpine.data('dropdown', dropdown);
Alpine.data('passwordField', passwordField);
Alpine.data('submitGuard', submitGuard);
Alpine.data('toast', toast);
Alpine.data('copyText', copyText);
Alpine.data('imagePreview', imagePreview);
Alpine.data('localeTabs', localeTabs);
Alpine.data('methodFields', methodFields);
Alpine.data('navGroup', navGroup);
Alpine.data('tabs', tabs);

registerCleanUrl();
registerFormGuard();
registerConfirmations();
registerScrollEffects();

window.Alpine = Alpine;
Alpine.start();
