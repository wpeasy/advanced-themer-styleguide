/**
 * Bricks Style Guide Elements - JavaScript
 *
 * Bricks calls these functions via the $scripts property when elements render in the builder.
 * On the frontend, we auto-initialize on DOMContentLoaded.
 *
 * Performance optimized:
 * - Single resize listener with debounce/throttle
 * - IntersectionObserver for visibility-based updates
 * - Viewport width tracking to skip unnecessary updates
 * - Cached DOM references
 * - Static values computed once, not on resize
 */

(function() {
	'use strict';

	/**
	 * Debounce utility - limits function execution rate.
	 */
	function debounce(func, wait) {
		let timeout;
		return function executedFunction(...args) {
			const later = () => {
				clearTimeout(timeout);
				func(...args);
			};
			clearTimeout(timeout);
			timeout = setTimeout(later, wait);
		};
	}

	/**
	 * Throttle utility - ensures function executes at most once per interval.
	 */
	function throttle(func, limit) {
		let inThrottle;
		return function(...args) {
			if (!inThrottle) {
				func.apply(this, args);
				inThrottle = true;
				setTimeout(() => inThrottle = false, limit);
			}
		};
	}

	window.bsgDebounce = debounce;
	window.bsgThrottle = throttle;

	/**
	 * Round a CSS pixel value to 2 decimal places.
	 */
	function roundPxValue(value) {
		if (!value || typeof value !== 'string') return value;
		const pxMatch = value.match(/^([\d.]+)px$/);
		if (pxMatch) {
			const num = parseFloat(pxMatch[1]);
			return Math.round(num * 100) / 100 + 'px';
		}
		return value;
	}

	/**
	 * Round all px values within a complex CSS string.
	 */
	function roundAllPxValues(value) {
		if (!value || typeof value !== 'string') return value;
		return value.replace(/([\d.]+)px/g, (match, num) => {
			return Math.round(parseFloat(num) * 100) / 100 + 'px';
		});
	}

	window.roundPxValue = roundPxValue;
	window.roundAllPxValues = roundAllPxValues;

	/**
	 * Get the original CSS value for a property, preserving CSS variable references
	 * or original units (em, rem, %, unitless) instead of computed px values.
	 *
	 * Note: This is expensive (iterates stylesheets) so only call once per element,
	 * not on resize. Results are cached per element.
	 *
	 * @param {HTMLElement} element - The element to inspect.
	 * @param {string} property - The CSS property name (e.g., 'line-height').
	 * @returns {string|null} The original value with units, or null if not found.
	 */
	const cssValueCache = new WeakMap();

	function getOriginalCSSValue(element, property) {
		// Check cache first
		let elementCache = cssValueCache.get(element);
		if (elementCache && elementCache[property] !== undefined) {
			return elementCache[property];
		}

		// Check inline style first
		const inlineValue = element.style.getPropertyValue(property);
		if (inlineValue && inlineValue !== '') {
			if (!elementCache) {
				elementCache = {};
				cssValueCache.set(element, elementCache);
			}
			elementCache[property] = inlineValue;
			return inlineValue;
		}

		// Walk through stylesheets to find matching rules
		let matchedValue = null;
		try {
			const sheets = document.styleSheets;

			for (let i = 0; i < sheets.length; i++) {
				let rules;
				try {
					rules = sheets[i].cssRules || sheets[i].rules;
				} catch (e) {
					continue; // Cross-origin stylesheets
				}

				if (!rules) continue;

				for (let j = 0; j < rules.length; j++) {
					const rule = rules[j];
					if (rule.type !== CSSRule.STYLE_RULE) continue;

					try {
						if (element.matches(rule.selectorText)) {
							const value = rule.style.getPropertyValue(property);
							if (value && value !== '') {
								matchedValue = value;
							}
						}
					} catch (e) {
						continue;
					}
				}
			}

			// Resolve CSS variables if needed
			if (matchedValue && matchedValue.startsWith('var(')) {
				const varMatch = matchedValue.match(/var\(([^,)]+)/);
				if (varMatch) {
					const varName = varMatch[1].trim();
					const resolvedValue = getComputedStyle(document.documentElement).getPropertyValue(varName).trim();
					if (resolvedValue) {
						matchedValue = resolvedValue;
					}
				}
			}
		} catch (e) {
			// Fall through
		}

		// Cache result
		if (!elementCache) {
			elementCache = {};
			cssValueCache.set(element, elementCache);
		}
		elementCache[property] = matchedValue;

		return matchedValue;
	}

	window.getOriginalCSSValue = getOriginalCSSValue;

	/**
	 * Centralized resize handler with viewport tracking.
	 * Only triggers updates when viewport WIDTH changes (not height).
	 */
	const ResizeManager = {
		callbacks: new Map(), // Map of callback -> { fn, element, lastWidth }
		initialized: false,
		lastViewportWidth: 0,
		observer: null,
		visibleElements: new Set(),

		/**
		 * Add a resize callback for an element.
		 * @param {Function} callback - Update function
		 * @param {HTMLElement} element - The element to track visibility
		 */
		add(callback, element) {
			this.callbacks.set(callback, { fn: callback, element });
			this.init();
			this.observeElement(element);
		},

		/**
		 * Initialize resize listener and IntersectionObserver.
		 */
		init() {
			if (this.initialized) return;
			this.initialized = true;
			this.lastViewportWidth = window.innerWidth;

			// IntersectionObserver for visibility tracking
			this.observer = new IntersectionObserver((entries) => {
				entries.forEach(entry => {
					if (entry.isIntersecting) {
						this.visibleElements.add(entry.target);
					} else {
						this.visibleElements.delete(entry.target);
					}
				});
			}, { rootMargin: '50px' }); // 50px buffer

			// Debounced resize handler - only fires after resize stops
			const debouncedHandler = debounce(() => {
				const newWidth = window.innerWidth;
				// Only update if width actually changed
				if (newWidth !== this.lastViewportWidth) {
					this.lastViewportWidth = newWidth;
					this.executeCallbacks();
				}
			}, 150);

			window.addEventListener('resize', debouncedHandler, { passive: true });
		},

		/**
		 * Observe an element for visibility.
		 */
		observeElement(element) {
			if (element && this.observer) {
				this.observer.observe(element);
				// Assume visible initially for first render
				this.visibleElements.add(element);
			}
		},

		/**
		 * Execute callbacks only for visible elements.
		 */
		executeCallbacks() {
			requestAnimationFrame(() => {
				this.callbacks.forEach((data) => {
					// Only update if element is visible
					if (this.visibleElements.has(data.element)) {
						try {
							data.fn();
						} catch (e) {
							// Silent fail
						}
					}
				});
			});
		},

		/**
		 * Force update all callbacks (for initial render).
		 */
		updateAll() {
			requestAnimationFrame(() => {
				this.callbacks.forEach((data) => {
					try {
						data.fn();
					} catch (e) {
						// Silent fail
					}
				});
			});
		}
	};

	window.BSGResizeManager = ResizeManager;

	/**
	 * Initialize all elements.
	 */
	function initAllElements() {
		if (typeof bsgTypographyInit === 'function') bsgTypographyInit();
		if (typeof bsgTypographyItemInit === 'function') bsgTypographyItemInit();
		if (typeof bsgSpacingInit === 'function') bsgSpacingInit();
		if (typeof bsgSpacingItemInit === 'function') bsgSpacingItemInit();
		if (typeof bsgRadiiInit === 'function') bsgRadiiInit();
		if (typeof bsgRadiiItemInit === 'function') bsgRadiiItemInit();
		if (typeof bsgBoxShadowsInit === 'function') bsgBoxShadowsInit();
		if (typeof bsgBoxShadowsItemInit === 'function') bsgBoxShadowsItemInit();
		if (typeof bsgButtonsInit === 'function') bsgButtonsInit();
		if (typeof bsgButtonsItemInit === 'function') bsgButtonsItemInit();
		if (typeof bsgColorsInit === 'function') bsgColorsInit();
		if (typeof bsgColorsItemInit === 'function') bsgColorsItemInit();
		if (typeof bsgTypographySpreadInit === 'function') bsgTypographySpreadInit();
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', initAllElements);
	} else {
		initAllElements();
	}
})();

/**
 * Typography Element (Nestable Container).
 */
function bsgTypographyInit() {
	const containers = document.querySelectorAll('.bsg-typography');
	containers.forEach(container => {
		const parentSampleText = container.dataset.sampleText;
		if (parentSampleText) {
			const inheritItems = container.querySelectorAll('.bsg-typography-item[data-inherit-sample="true"]');
			inheritItems.forEach(item => {
				const sample = item.querySelector('.bsg-typography-item__sample');
				if (sample) {
					sample.textContent = parentSampleText;
				}
			});
		}
	});
	bsgTypographyItemInit();
}

/**
 * Typography Item Element.
 *
 * OPTIMIZATION: Only font-size updates on resize (it's the only responsive value).
 * All other values (family, weight, line-height, etc.) are computed once.
 */
function bsgTypographyItemInit() {
	const items = document.querySelectorAll('.bsg-typography-item');

	items.forEach(item => {
		if (item.dataset.bsgInit) return;
		item.dataset.bsgInit = 'true';

		const sample = item.querySelector('.bsg-typography-item__sample');
		if (!sample) return;

		// Cache element references
		const refs = {
			family: item.querySelector('[data-computed="font-family"]'),
			size: item.querySelector('[data-computed="font-size"]'),
			lineHeight: item.querySelector('[data-computed="line-height"]'),
			weight: item.querySelector('[data-computed="font-weight"]'),
			spacing: item.querySelector('[data-computed="letter-spacing"]'),
			color: item.querySelector('[data-computed="color"]'),
			transform: item.querySelector('[data-computed="text-transform"]'),
			style: item.querySelector('[data-computed="font-style"]')
		};

		// STATIC VALUES - computed once, never on resize
		const computeStaticValues = () => {
			const computed = window.getComputedStyle(sample);

			if (refs.family) {
				refs.family.textContent = computed.fontFamily.split(',')[0].replace(/["']/g, '').trim();
			}

			if (refs.lineHeight) {
				// Get original CSS value (e.g., 1.5, 1.6em) instead of computed px
				const originalLineHeight = getOriginalCSSValue(sample, 'line-height');
				if (originalLineHeight) {
					refs.lineHeight.textContent = originalLineHeight;
				} else if (computed.lineHeight === 'normal') {
					refs.lineHeight.textContent = 'Browser Default';
				} else {
					refs.lineHeight.textContent = 'Browser Default';
				}
			}

			if (refs.weight) {
				refs.weight.textContent = computed.fontWeight;
			}

			if (refs.spacing) {
				const spacing = computed.letterSpacing;
				refs.spacing.textContent = spacing === 'normal' ? '0' : roundPxValue(spacing);
			}

			if (refs.color) {
				const rgbMatch = computed.color.match(/rgba?\((\d+),\s*(\d+),\s*(\d+)/);
				if (rgbMatch) {
					const r = parseInt(rgbMatch[1]).toString(16).padStart(2, '0');
					const g = parseInt(rgbMatch[2]).toString(16).padStart(2, '0');
					const b = parseInt(rgbMatch[3]).toString(16).padStart(2, '0');
					refs.color.textContent = `#${r}${g}${b}`.toUpperCase();
				} else {
					refs.color.textContent = computed.color;
				}
			}

			if (refs.transform) {
				refs.transform.textContent = computed.textTransform;
			}

			if (refs.style) {
				refs.style.textContent = computed.fontStyle;
			}
		};

		// DYNAMIC VALUE - only font-size responds to viewport (clamp)
		const updateFontSize = () => {
			if (refs.size) {
				refs.size.textContent = roundPxValue(window.getComputedStyle(sample).fontSize);
			}
		};

		// Initial render - all values
		computeStaticValues();
		updateFontSize();

		// Only register font-size for resize updates (if element has size display)
		if (refs.size) {
			window.BSGResizeManager.add(updateFontSize, item);
		}
	});
}

/**
 * Spacing Element (Nestable Container).
 */
function bsgSpacingInit() {
	bsgSpacingItemInit();
}

/**
 * Spacing Item Element.
 *
 * OPTIMIZATION: Uses cached references and visibility-based updates.
 */
function bsgSpacingItemInit() {
	const items = document.querySelectorAll('.bsg-spacing-item');

	items.forEach(item => {
		if (item.dataset.bsgInit) return;
		item.dataset.bsgInit = 'true';

		const bar = item.querySelector('.bsg-spacing-item__bar');
		const computedEl = item.querySelector('.bsg-spacing-item__computed');
		if (!bar || !computedEl) return;

		// Cache parent lookup
		const parent = item.closest('.bsg-spacing');
		const isVertical = parent && parent.classList.contains('bsg-spacing--vertical');

		const updateValue = () => {
			const computed = window.getComputedStyle(bar);
			computedEl.textContent = roundPxValue(isVertical ? computed.height : computed.width);
		};

		// Initial render
		updateValue();

		// Register for resize (spacing often uses clamp)
		window.BSGResizeManager.add(updateValue, item);
	});
}

/**
 * Radii Element (Nestable Container).
 */
function bsgRadiiInit() {
	bsgRadiiItemInit();
}

/**
 * Radii Item Element.
 *
 * NO RESIZE LISTENER - border-radius is static.
 */
function bsgRadiiItemInit() {
	const items = document.querySelectorAll('.bsg-radii-item');

	items.forEach(item => {
		if (item.dataset.bsgInit) return;
		item.dataset.bsgInit = 'true';

		const box = item.querySelector('.bsg-radii-item__box');
		const computedEl = item.querySelector('.bsg-radii-item__computed');
		if (!box || !computedEl) return;

		// One-time computation
		computedEl.textContent = roundPxValue(window.getComputedStyle(box).borderRadius);
	});
}

/**
 * Box Shadows Element (Nestable Container).
 */
function bsgBoxShadowsInit() {
	bsgBoxShadowsItemInit();
}

/**
 * Box Shadows Item Element.
 *
 * NO RESIZE LISTENER - box-shadow is static.
 * Reads the CSS variable value from the stylesheet, not the computed value.
 */
function bsgBoxShadowsItemInit() {
	const items = document.querySelectorAll('.bsg-shadows-item');

	items.forEach(item => {
		if (item.dataset.bsgInit) return;
		item.dataset.bsgInit = 'true';

		const computedEl = item.querySelector('.bsg-shadows-item__computed');
		const variableName = item.dataset.var;
		if (!computedEl || !variableName) return;

		// Read the CSS variable value from the stylesheet (not computed box-shadow)
		const variableValue = window.getComputedStyle(item).getPropertyValue(variableName).trim();
		computedEl.textContent = variableValue || 'none';
	});
}

/**
 * Buttons Element (Nestable Container).
 */
function bsgButtonsInit() {
	const containers = document.querySelectorAll('.bsg-buttons');
	containers.forEach(container => {
		const toggles = container.querySelectorAll('[data-toggle]');
		toggles.forEach(toggle => {
			if (toggle.dataset.initialized) return;
			toggle.dataset.initialized = 'true';

			toggle.addEventListener('change', () => {
				const toggleType = toggle.dataset.toggle;
				const buttons = container.querySelectorAll('.bsg-btn');

				buttons.forEach(btn => {
					const isAcss = btn.classList.contains('btn');
					const isBricks = btn.classList.contains('bricks-button');

					if (toggleType === 'outline') {
						btn.classList.toggle('btn--outline', toggle.checked && isAcss);
						btn.classList.toggle('outline', toggle.checked && isBricks);
						if (!toggle.checked) {
							btn.classList.remove('btn--outline', 'outline');
						}
					} else if (toggleType === 'rounded') {
						btn.classList.toggle('btn--rounded', toggle.checked && isAcss);
						btn.classList.toggle('circle', toggle.checked && isBricks);
						if (!toggle.checked) {
							btn.classList.remove('btn--rounded', 'circle');
						}
					}
				});
			});
		});
	});
	bsgButtonsItemInit();
}

/**
 * Buttons Item Element.
 */
function bsgButtonsItemInit() {
	// No computed values needed
}

/**
 * Colors Element (Nestable Container).
 */
function bsgColorsInit() {
	const containers = document.querySelectorAll('.bsg-colors');
	containers.forEach(container => {
		const toggle = container.querySelector('[data-toggle="a11y-badges"]');
		if (toggle && !toggle.dataset.initialized) {
			toggle.dataset.initialized = 'true';
			toggle.addEventListener('change', () => {
				if (toggle.checked) {
					container.setAttribute('data-show-a11y-badges', 'true');
				} else {
					container.removeAttribute('data-show-a11y-badges');
				}
			});
		}
	});
	bsgColorsItemInit();
}

/**
 * Colors Item Element.
 *
 * NO RESIZE LISTENER - colors are static.
 * Includes full keyboard accessibility and ARIA support.
 */
function bsgColorsItemInit() {
	const swatches = document.querySelectorAll('.bsg-colors-item__swatch, .bsg-colors-item__base');

	swatches.forEach(swatch => {
		if (swatch.dataset.initialized) return;
		swatch.dataset.initialized = 'true';

		const cssVar = swatch.dataset.var;
		const menu = swatch.querySelector('.bsg-colors-item__menu');
		if (!menu || !cssVar) return;

		// One-time color computation
		const bgColor = window.getComputedStyle(swatch).backgroundColor;
		const colorData = parseColor(bgColor);
		if (!colorData) return;

		// Store for copy buttons
		swatch._colorData = colorData;

		// ARIA: Make swatch accessible as a button
		swatch.setAttribute('role', 'button');
		swatch.setAttribute('tabindex', '0');
		swatch.setAttribute('aria-haspopup', 'dialog');
		swatch.setAttribute('aria-expanded', 'false');
		swatch.setAttribute('aria-label', `Color ${cssVar}, ${colorData.hex}. Press Enter to open color details.`);

		// ARIA: Configure menu as dialog
		const menuId = `color-menu-${Math.random().toString(36).substr(2, 9)}`;
		menu.setAttribute('id', menuId);
		menu.setAttribute('role', 'dialog');
		menu.setAttribute('aria-label', `Color details for ${cssVar}`);
		menu.setAttribute('aria-modal', 'false');
		swatch.setAttribute('aria-controls', menuId);

		// Update menu values
		const valueEl = menu.querySelector('.bsg-colors-item__menu-value');
		if (valueEl) {
			valueEl.textContent = colorData.hex;
		}

		// Update contrast values
		if (colorData.contrast && !colorData.hasAlpha) {
			const whiteValueEl = menu.querySelector('[data-contrast="white"]');
			const whiteBadgeEl = menu.querySelector('[data-contrast-badge="white"]');
			const blackValueEl = menu.querySelector('[data-contrast="black"]');
			const blackBadgeEl = menu.querySelector('[data-contrast-badge="black"]');

			if (whiteValueEl) whiteValueEl.textContent = colorData.contrast.white.ratioText;
			if (whiteBadgeEl) {
				whiteBadgeEl.textContent = colorData.contrast.white.wcag.label;
				whiteBadgeEl.dataset.level = colorData.contrast.white.wcag.level;
			}
			if (blackValueEl) blackValueEl.textContent = colorData.contrast.black.ratioText;
			if (blackBadgeEl) {
				blackBadgeEl.textContent = colorData.contrast.black.wcag.label;
				blackBadgeEl.dataset.level = colorData.contrast.black.wcag.level;
			}
		} else if (colorData.hasAlpha) {
			const contrastSection = menu.querySelector('.bsg-colors-item__menu-contrast');
			if (contrastSection) {
				contrastSection.innerHTML = '<div class="bsg-colors-item__menu-contrast-header">Contrast</div>' +
					'<div class="bsg-colors-item__menu-contrast-note">N/A for transparent colors</div>';
			}
		}

		// Add contrast badges
		const contrastBadges = document.createElement('div');
		contrastBadges.className = 'bsg-colors-item__contrast-badges';
		contrastBadges.setAttribute('aria-hidden', 'true'); // Decorative, info is in menu
		if (colorData.hasAlpha) {
			contrastBadges.style.display = 'none';
		} else {
			contrastBadges.innerHTML =
				`<span class="bsg-colors-item__contrast-badge bsg-colors-item__contrast-badge--white" data-level="${colorData.contrast.white.wcag.level}">W</span>` +
				`<span class="bsg-colors-item__contrast-badge bsg-colors-item__contrast-badge--black" data-level="${colorData.contrast.black.wcag.level}">B</span>`;
		}
		swatch.appendChild(contrastBadges);

		// Add click hint (hidden from screen readers)
		const hint = document.createElement('span');
		hint.className = 'bsg-colors-item__hint';
		hint.textContent = 'Click';
		hint.setAttribute('aria-hidden', 'true');
		swatch.appendChild(hint);

		// Menu toggle state
		let isMenuOpen = false;

		// Get all focusable elements in menu for focus trap
		const getFocusableElements = () => {
			return menu.querySelectorAll('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
		};

		const showMenu = () => {
			isMenuOpen = true;
			positionMenuAtSwatch(swatch, menu);
			menu.style.opacity = '1';
			menu.style.visibility = 'visible';
			menu.style.pointerEvents = 'auto';
			hint.style.display = 'none';
			swatch.setAttribute('aria-expanded', 'true');

			// Focus first focusable element in menu
			requestAnimationFrame(() => {
				const focusable = getFocusableElements();
				if (focusable.length > 0) {
					focusable[0].focus();
				}
			});

			// Announce to screen readers
			menu.setAttribute('aria-live', 'polite');
		};

		const closeMenu = (returnFocus = true) => {
			isMenuOpen = false;
			menu.style.opacity = '0';
			menu.style.visibility = 'hidden';
			menu.style.pointerEvents = 'none';
			hint.style.display = '';
			swatch.setAttribute('aria-expanded', 'false');
			menu.removeAttribute('aria-live');

			// Return focus to swatch
			if (returnFocus) {
				swatch.focus();
			}
		};

		const toggleMenu = () => {
			if (isMenuOpen) {
				closeMenu();
			} else {
				// Close other menus
				document.querySelectorAll('.bsg-colors-item__menu').forEach(m => {
					if (m !== menu) {
						m.style.opacity = '0';
						m.style.visibility = 'hidden';
						m.style.pointerEvents = 'none';
						// Update aria-expanded on other swatches
						const otherSwatch = m.closest('.bsg-colors-item__swatch, .bsg-colors-item__base');
						if (otherSwatch) {
							otherSwatch.setAttribute('aria-expanded', 'false');
						}
					}
				});
				showMenu();
			}
		};

		// Click handler
		swatch.addEventListener('click', (e) => {
			e.stopPropagation();
			toggleMenu();
		});

		// Keyboard handler for swatch
		swatch.addEventListener('keydown', (e) => {
			if (e.key === 'Enter' || e.key === ' ') {
				e.preventDefault();
				e.stopPropagation();
				toggleMenu();
			} else if (e.key === 'Escape' && isMenuOpen) {
				e.preventDefault();
				e.stopPropagation();
				closeMenu();
			}
		});

		// Focus trap and keyboard navigation within menu
		menu.addEventListener('keydown', (e) => {
			const focusable = getFocusableElements();
			const firstFocusable = focusable[0];
			const lastFocusable = focusable[focusable.length - 1];
			const currentIndex = Array.from(focusable).indexOf(document.activeElement);

			switch (e.key) {
				case 'Escape':
					e.preventDefault();
					e.stopPropagation();
					closeMenu();
					break;

				case 'ArrowRight':
				case 'ArrowDown':
					e.preventDefault();
					e.stopPropagation();
					if (currentIndex < focusable.length - 1) {
						focusable[currentIndex + 1].focus();
					} else {
						// At end, wrap to first or exit
						firstFocusable.focus();
					}
					break;

				case 'ArrowLeft':
				case 'ArrowUp':
					e.preventDefault();
					e.stopPropagation();
					if (currentIndex > 0) {
						focusable[currentIndex - 1].focus();
					} else {
						// At beginning, wrap to last or exit
						lastFocusable.focus();
					}
					break;

				case 'Tab':
					// Trap focus within menu
					if (e.shiftKey) {
						if (document.activeElement === firstFocusable) {
							e.preventDefault();
							lastFocusable.focus();
						}
					} else {
						if (document.activeElement === lastFocusable) {
							e.preventDefault();
							firstFocusable.focus();
						}
					}
					break;

				case 'Home':
					e.preventDefault();
					firstFocusable.focus();
					break;

				case 'End':
					e.preventDefault();
					lastFocusable.focus();
					break;
			}
		});

		menu.addEventListener('mouseleave', () => closeMenu(false));

		// Global click to close
		document.addEventListener('click', (e) => {
			if (isMenuOpen && !menu.contains(e.target) && !swatch.contains(e.target)) {
				closeMenu(false);
			}
		}, { passive: true });

		// Global escape to close any open menu
		document.addEventListener('keydown', (e) => {
			if (e.key === 'Escape' && isMenuOpen) {
				closeMenu();
			}
		});

		// Copy handlers with ARIA feedback
		const varBtn = menu.querySelector('.bsg-colors-item__menu-var');
		if (varBtn) {
			varBtn.setAttribute('aria-label', `Copy CSS variable ${cssVar}`);
			varBtn.addEventListener('click', (e) => {
				e.preventDefault();
				e.stopPropagation();
				if (varBtn.dataset.varValue) {
					copyToClipboard(varBtn.dataset.varValue, varBtn);
				}
			});
			varBtn.addEventListener('keydown', (e) => {
				if (e.key === 'Enter' || e.key === ' ') {
					e.preventDefault();
					e.stopPropagation();
					if (varBtn.dataset.varValue) {
						copyToClipboard(varBtn.dataset.varValue, varBtn);
					}
				}
			});
		}

		menu.querySelectorAll('.bsg-colors-item__menu-btn').forEach(btn => {
			// Add ARIA labels based on action
			const action = btn.dataset.action;
			const actionLabels = {
				'copy-hex': 'Copy hex value',
				'copy-rgb': 'Copy RGB value',
				'copy-hsl': 'Copy HSL value',
				'copy-oklch': 'Copy OKLCH value'
			};
			if (actionLabels[action]) {
				btn.setAttribute('aria-label', actionLabels[action]);
			}

			const handleCopy = () => {
				let valueToCopy = '';

				switch (action) {
					case 'copy-hex':
						valueToCopy = colorData.hasAlpha ? colorData.rgba : colorData.hex;
						break;
					case 'copy-rgb':
						valueToCopy = colorData.hasAlpha ? colorData.rgba : colorData.rgb;
						break;
					case 'copy-hsl':
						valueToCopy = colorData.hasAlpha ? colorData.hsla : colorData.hsl;
						break;
					case 'copy-oklch':
						valueToCopy = colorData.oklch;
						break;
				}

				if (valueToCopy) {
					copyToClipboard(valueToCopy, btn);
				}
			};

			btn.addEventListener('click', (e) => {
				e.preventDefault();
				e.stopPropagation();
				handleCopy();
			});

			btn.addEventListener('keydown', (e) => {
				if (e.key === 'Enter' || e.key === ' ') {
					e.preventDefault();
					e.stopPropagation();
					handleCopy();
				}
			});
		});
	});

	// Arrow key navigation between swatches in the same color item
	setupSwatchNavigation();
}

/**
 * Setup arrow key navigation between swatches within a color item.
 */
function setupSwatchNavigation() {
	document.querySelectorAll('.bsg-colors-item').forEach(colorItem => {
		if (colorItem.dataset.navInitialized) return;
		colorItem.dataset.navInitialized = 'true';

		const allSwatches = colorItem.querySelectorAll('.bsg-colors-item__swatch, .bsg-colors-item__base');
		if (allSwatches.length <= 1) return;

		allSwatches.forEach((swatch, index) => {
			swatch.addEventListener('keydown', (e) => {
				// Only handle navigation when menu is not open
				if (swatch.getAttribute('aria-expanded') === 'true') return;

				let targetIndex = -1;

				switch (e.key) {
					case 'ArrowRight':
					case 'ArrowDown':
						e.preventDefault();
						targetIndex = index < allSwatches.length - 1 ? index + 1 : 0;
						break;

					case 'ArrowLeft':
					case 'ArrowUp':
						e.preventDefault();
						targetIndex = index > 0 ? index - 1 : allSwatches.length - 1;
						break;

					case 'Home':
						e.preventDefault();
						targetIndex = 0;
						break;

					case 'End':
						e.preventDefault();
						targetIndex = allSwatches.length - 1;
						break;
				}

				if (targetIndex >= 0) {
					allSwatches[targetIndex].focus();
				}
			});
		});
	});
}

/**
 * Position menu relative to swatch, ensuring it stays within viewport.
 */
function positionMenuAtSwatch(swatch, menu) {
	const gap = 8;
	const margin = 10; // Minimum distance from viewport edges
	const rect = swatch.getBoundingClientRect();
	const vw = window.innerWidth;
	const vh = window.innerHeight;

	// Temporarily show menu to measure it
	menu.style.visibility = 'hidden';
	menu.style.display = 'block';
	const menuRect = menu.getBoundingClientRect();
	const menuWidth = menuRect.width;
	const menuHeight = menuRect.height;

	let left, top;

	// === HORIZONTAL POSITIONING ===
	// Try right side first
	if (rect.right + gap + menuWidth <= vw - margin) {
		left = rect.right + gap;
	}
	// Try left side
	else if (rect.left - gap - menuWidth >= margin) {
		left = rect.left - gap - menuWidth;
	}
	// Fallback: center horizontally or align with swatch
	else {
		left = rect.left;
	}

	// Check if menu would go beyond right edge
	if (left + menuWidth > vw - margin) {
		left = vw - menuWidth - margin;
	}

	// Check if menu would go beyond left edge
	if (left < margin) {
		left = margin;
	}

	// Final safety check: if menu is wider than viewport, position at left
	if (menuWidth > vw - (margin * 2)) {
		left = margin;
	}

	// === VERTICAL POSITIONING ===
	// Start aligned with swatch top
	top = rect.top;

	// Check if menu would go below viewport
	if (top + menuHeight > vh - margin) {
		// Try aligning menu bottom with swatch bottom
		top = rect.bottom - menuHeight;
	}

	// Check if menu would go above viewport
	if (top < margin) {
		// Position at top margin
		top = margin;
	}

	// Final safety check: if menu is taller than viewport, position at top
	if (menuHeight > vh - (margin * 2)) {
		top = margin;
	}

	// Ensure we don't exceed bottom even after adjustments
	if (top + menuHeight > vh - margin) {
		top = vh - menuHeight - margin;
	}

	// Final clamp to ensure top is never negative
	top = Math.max(margin, top);

	menu.style.position = 'fixed';
	menu.style.left = left + 'px';
	menu.style.top = top + 'px';
	menu.style.transform = 'none';
	menu.style.visibility = '';
}

/**
 * Parse color string to various formats.
 */
function parseColor(colorStr) {
	const rgbaMatch = colorStr.match(/rgba?\((\d+),\s*(\d+),\s*(\d+)(?:,\s*([\d.]+))?\)/);
	if (!rgbaMatch) return null;

	const r = parseInt(rgbaMatch[1]);
	const g = parseInt(rgbaMatch[2]);
	const b = parseInt(rgbaMatch[3]);
	const a = rgbaMatch[4] !== undefined ? parseFloat(rgbaMatch[4]) : 1;
	const hasAlpha = a < 1;

	const toHex = (n) => n.toString(16).padStart(2, '0');
	const hex = `#${toHex(r)}${toHex(g)}${toHex(b)}`;
	const hex8 = `#${toHex(r)}${toHex(g)}${toHex(b)}${toHex(Math.round(a * 255))}`;

	const rgb = `rgb(${r}, ${g}, ${b})`;
	const rgba = `rgba(${r}, ${g}, ${b}, ${a})`;

	const hsl = rgbToHsl(r, g, b);
	const hslStr = `hsl(${hsl.h}, ${hsl.s}%, ${hsl.l}%)`;
	const hslaStr = `hsla(${hsl.h}, ${hsl.s}%, ${hsl.l}%, ${a})`;

	const oklch = rgbToOklch(r, g, b);
	const oklchStr = `oklch(${oklch.l}% ${oklch.c} ${oklch.h}${hasAlpha ? ` / ${a}` : ''})`;

	const contrast = calculateContrastInfo(r, g, b);

	return {
		r, g, b, a, hasAlpha,
		hex: hasAlpha ? hex8 : hex,
		rgb, rgba,
		hsl: hslStr, hsla: hslaStr,
		oklch: oklchStr,
		contrast
	};
}

function rgbToHsl(r, g, b) {
	r /= 255; g /= 255; b /= 255;
	const max = Math.max(r, g, b);
	const min = Math.min(r, g, b);
	let h, s;
	const l = (max + min) / 2;

	if (max === min) {
		h = s = 0;
	} else {
		const d = max - min;
		s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
		switch (max) {
			case r: h = ((g - b) / d + (g < b ? 6 : 0)) / 6; break;
			case g: h = ((b - r) / d + 2) / 6; break;
			case b: h = ((r - g) / d + 4) / 6; break;
		}
	}

	return { h: Math.round(h * 360), s: Math.round(s * 100), l: Math.round(l * 100) };
}

function rgbToOklch(r, g, b) {
	const toLinear = (c) => {
		c = c / 255;
		return c <= 0.04045 ? c / 12.92 : Math.pow((c + 0.055) / 1.055, 2.4);
	};

	const lr = toLinear(r);
	const lg = toLinear(g);
	const lb = toLinear(b);

	const l_ = 0.4122214708 * lr + 0.5363325363 * lg + 0.0514459929 * lb;
	const m_ = 0.2119034982 * lr + 0.6806995451 * lg + 0.1073969566 * lb;
	const s_ = 0.0883024619 * lr + 0.2817188376 * lg + 0.6299787005 * lb;

	const l = Math.cbrt(l_);
	const m = Math.cbrt(m_);
	const s = Math.cbrt(s_);

	const L = 0.2104542553 * l + 0.7936177850 * m - 0.0040720468 * s;
	const A = 1.9779984951 * l - 2.4285922050 * m + 0.4505937099 * s;
	const B = 0.0259040371 * l + 0.7827717662 * m - 0.8086757660 * s;

	const C = Math.sqrt(A * A + B * B);
	let H = Math.atan2(B, A) * 180 / Math.PI;
	if (H < 0) H += 360;

	return { l: (L * 100).toFixed(1), c: C.toFixed(3), h: H.toFixed(1) };
}

function getLuminance(r, g, b) {
	const toLinear = (c) => {
		c = c / 255;
		return c <= 0.03928 ? c / 12.92 : Math.pow((c + 0.055) / 1.055, 2.4);
	};
	return 0.2126 * toLinear(r) + 0.7152 * toLinear(g) + 0.0722 * toLinear(b);
}

function getContrastRatio(l1, l2) {
	const lighter = Math.max(l1, l2);
	const darker = Math.min(l1, l2);
	return (lighter + 0.05) / (darker + 0.05);
}

function getWcagLevel(ratio) {
	if (ratio >= 7) return { level: 'AAA', label: 'AAA', pass: true };
	if (ratio >= 4.5) return { level: 'AA', label: 'AA', pass: true };
	if (ratio >= 3) return { level: 'AA-large', label: 'AA Large', pass: true };
	return { level: 'fail', label: 'Fail', pass: false };
}

function calculateContrastInfo(r, g, b) {
	const luminance = getLuminance(r, g, b);
	const whiteContrast = getContrastRatio(luminance, 1);
	const blackContrast = getContrastRatio(luminance, 0);

	return {
		luminance,
		white: { ratio: whiteContrast, ratioText: whiteContrast.toFixed(1) + ':1', wcag: getWcagLevel(whiteContrast) },
		black: { ratio: blackContrast, ratioText: blackContrast.toFixed(1) + ':1', wcag: getWcagLevel(blackContrast) },
		bestChoice: whiteContrast > blackContrast ? 'white' : 'black'
	};
}

/**
 * Copy text to clipboard with visual and ARIA feedback.
 */
function copyToClipboard(text, btn) {
	navigator.clipboard.writeText(text).then(() => {
		btn.classList.add('copied');
		const originalText = btn.innerHTML;

		if (btn.dataset.action === 'copy-hex') {
			btn.innerHTML = '<span class="bsg-colors-item__menu-btn-icon">✓</span> Copied!';
		} else {
			btn.textContent = '✓';
		}

		// Announce to screen readers
		announceToScreenReader(`Copied ${text} to clipboard`);

		setTimeout(() => {
			btn.classList.remove('copied');
			btn.innerHTML = originalText;
		}, 1500);
	}).catch((err) => {
		console.error('Failed to copy:', err);
		announceToScreenReader('Failed to copy to clipboard');
	});
}

/**
 * Announce message to screen readers via ARIA live region.
 */
function announceToScreenReader(message) {
	// Get or create the live region
	let liveRegion = document.getElementById('bsg-aria-live');
	if (!liveRegion) {
		liveRegion = document.createElement('div');
		liveRegion.id = 'bsg-aria-live';
		liveRegion.setAttribute('role', 'status');
		liveRegion.setAttribute('aria-live', 'polite');
		liveRegion.setAttribute('aria-atomic', 'true');
		// Visually hidden but accessible to screen readers
		liveRegion.style.cssText = 'position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0, 0, 0, 0); white-space: nowrap; border: 0;';
		document.body.appendChild(liveRegion);
	}

	// Clear and set message (clearing first ensures announcement even if same message)
	liveRegion.textContent = '';
	requestAnimationFrame(() => {
		liveRegion.textContent = message;
	});
}

/**
 * Typography Spread Element.
 *
 * Handles the expand/collapse functionality for the read more feature.
 */
function bsgTypographySpreadInit() {
	const spreads = document.querySelectorAll('.bsg-typography-spread--collapsible');

	spreads.forEach(spread => {
		if (spread.dataset.bsgInit) return;
		spread.dataset.bsgInit = 'true';

		const content = spread.querySelector('.bsg-typography-spread__content');
		const btn = spread.querySelector('.bsg-typography-spread__read-more-btn');
		const btnText = spread.querySelector('.bsg-typography-spread__btn-text');

		if (!content || !btn) return;

		const collapsedHeight = spread.dataset.collapsedHeight || '300px';
		const readMoreText = spread.dataset.readMoreText || 'Read More';
		const readLessText = spread.dataset.readLessText || 'Read Less';

		// Store the full height
		let fullHeight = 0;
		let isExpanded = false;

		// Get the actual content height
		const measureFullHeight = () => {
			// Temporarily remove max-height to measure
			content.style.maxHeight = 'none';
			fullHeight = content.scrollHeight;
			// Restore collapsed state
			if (!isExpanded) {
				content.style.maxHeight = collapsedHeight;
			}
		};

		// Initial setup
		const init = () => {
			measureFullHeight();

			// Check if content is shorter than collapsed height
			const collapsedPx = parseFloat(collapsedHeight);
			if (fullHeight <= collapsedPx) {
				// Content fits, hide the button and remove mask
				btn.parentElement.style.display = 'none';
				spread.classList.remove('bsg-typography-spread--collapsed');
				content.style.maxHeight = 'none';
				return;
			}

			// Set initial collapsed state
			content.style.maxHeight = collapsedHeight;
		};

		// Toggle expand/collapse
		const toggle = () => {
			if (isExpanded) {
				// Collapse
				isExpanded = false;
				content.style.maxHeight = collapsedHeight;
				spread.classList.remove('bsg-typography-spread--expanded');
				spread.classList.add('bsg-typography-spread--collapsed');
				if (btnText) btnText.textContent = readMoreText;
				btn.setAttribute('aria-expanded', 'false');
			} else {
				// Expand
				isExpanded = true;
				measureFullHeight();
				content.style.maxHeight = fullHeight + 'px';
				spread.classList.remove('bsg-typography-spread--collapsed');
				spread.classList.add('bsg-typography-spread--expanded');
				if (btnText) btnText.textContent = readLessText;
				btn.setAttribute('aria-expanded', 'true');
			}
		};

		// Set up button
		btn.setAttribute('aria-expanded', 'false');
		btn.setAttribute('aria-controls', content.id || 'typography-spread-content');
		btn.addEventListener('click', toggle);

		// Initialize
		init();

		// Re-measure on resize (content may reflow)
		window.BSGResizeManager.add(() => {
			if (isExpanded) {
				measureFullHeight();
				content.style.maxHeight = fullHeight + 'px';
			}
		}, spread);
	});
}
