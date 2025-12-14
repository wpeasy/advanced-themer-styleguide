import { mount } from 'svelte';
import App from './App.svelte';

// Mount Svelte app when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
  const targets = document.querySelectorAll('[data-bricks-style-guide-instructions]');

  targets.forEach((target) => {
    // Get framework info from localized data
    const adminData = window.bricksStyleGuideAdminData || {};

    mount(App, {
      target,
      props: {
        activeFramework: adminData.activeFramework || 'at',
        frameworkName: adminData.frameworkName || 'Advanced Themer'
      }
    });
  });
});

// Expose for WordPress
declare global {
  interface Window {
    bricksStyleGuideAdminData: {
      ajaxUrl: string;
      nonce: string;
      pluginUrl: string;
      activeFramework: 'at' | 'acss';
      frameworkName: string;
    };
  }
}
