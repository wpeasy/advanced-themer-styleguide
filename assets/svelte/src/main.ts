import { mount } from 'svelte';
import App from './App.svelte';

// Mount Svelte app when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
  const targets = document.querySelectorAll('[data-at-style-guide-app]');

  targets.forEach((target) => {
    const app = mount(App, {
      target,
      props: {
        // Props can be passed from WordPress via data attributes
      }
    });
  });
});

// Expose for WordPress
declare global {
  interface Window {
    atStyleGuideData: {
      ajaxUrl: string;
      nonce: string;
    };
  }
}
