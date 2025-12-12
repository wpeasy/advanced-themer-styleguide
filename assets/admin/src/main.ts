import { mount } from 'svelte';
import App from './App.svelte';

// Mount Svelte app when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
  const targets = document.querySelectorAll('[data-at-style-guide-instructions]');

  targets.forEach((target) => {
    mount(App, {
      target,
      props: {}
    });
  });
});

// Expose for WordPress
declare global {
  interface Window {
    atStyleGuideAdminData: {
      ajaxUrl: string;
      nonce: string;
      pluginUrl: string;
    };
  }
}
