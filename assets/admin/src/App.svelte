<script lang="ts">
  import Tabs from '$lib/Tabs.svelte';
  import Card from '$lib/Card.svelte';
  import Alert from '$lib/Alert.svelte';

  // Tab content snippets must be defined inline
  const tabs = [
    { id: 'requirements', label: 'Requirements', content: requirementsContent },
    { id: 'general', label: 'General', content: generalContent },
    { id: 'colors', label: 'Colors', content: colorsContent },
    { id: 'typography', label: 'Typography', content: typographyContent },
    { id: 'spacing', label: 'Spacing', content: spacingContent },
    { id: 'radii', label: 'Radii', content: radiiContent },
    { id: 'box-shadows', label: 'Box Shadows', content: boxShadowsContent },
    { id: 'buttons', label: 'Buttons', content: buttonsContent }
  ];
</script>

{#snippet requirementsContent()}
  <div class="atsg-instructions__content">
    <Card title="Requirements">
      <Alert variant="warning">
        <strong>Required Plugins:</strong> This plugin requires both <strong>Bricks Builder</strong> and <strong>Advanced Themer for Bricks</strong> to be installed and activated.
      </Alert>

      <div class="atsg-instructions__section">
        <h4>Advanced Themer Variables</h4>
        <p>The Style Guide elements read CSS variables defined by Advanced Themer. Ensure you have configured:</p>
        <ul>
          <li><code>--at-primary</code>, <code>--at-secondary</code>, <code>--at-neutral</code> - Core color palette</li>
          <li><code>--at-text--*</code> - Typography scale variables</li>
          <li><code>--at-space--*</code> - Spacing scale variables</li>
          <li><code>--at-radius--*</code> - Border radius variables</li>
          <li><code>--at-shadow--*</code> - Box shadow variables</li>
        </ul>
      </div>

      <div class="atsg-instructions__section">
        <h4>Bricks Theme Styles</h4>
        <p>The Button element uses Bricks' built-in button classes:</p>
        <ul>
          <li><code>.bricks-button</code> - Base button class</li>
          <li><code>.bricks-color-*</code> - Color variants (primary, secondary, dark, light, etc.)</li>
          <li><code>.sm</code>, <code>.md</code>, <code>.lg</code>, <code>.xl</code> - Size classes</li>
          <li><code>.outline</code> - Outline variant</li>
          <li><code>.round</code>, <code>.square</code>, <code>.circle</code> - Shape variants</li>
        </ul>
      </div>
    </Card>
  </div>
{/snippet}

{#snippet generalContent()}
  <div class="atsg-instructions__content">
    <Card title="General Usage">
      <div class="atsg-instructions__section">
        <h4>Nestable Elements with Default Children</h4>
        <p>All Style Guide elements use Bricks' <strong>Nestable Elements</strong> pattern. When you add a parent element (e.g., Colors, Typography, Spacing), it automatically creates a set of default child items based on your Advanced Themer configuration.</p>
        <ul>
          <li><strong>Colors</strong> - Creates Color Items for each root color in your AT palette</li>
          <li><strong>Typography</strong> - Creates Typography Items for common heading and text styles</li>
          <li><strong>Spacing</strong> - Creates Spacing Items for your spacing scale tokens</li>
          <li><strong>Radii</strong> - Creates Radii Items for your border radius tokens</li>
          <li><strong>Box Shadows</strong> - Creates Box Shadow Items for your shadow tokens</li>
          <li><strong>Buttons</strong> - Creates Button Items showing color and size combinations</li>
        </ul>
        <p>You can add, remove, or reorder child items as needed. The defaults are just a starting point.</p>
      </div>

      <div class="atsg-instructions__section">
        <h4>Using Child Items Independently</h4>
        <p>While child items are designed to work within their parent containers, they can also be used independently:</p>
        <ul>
          <li>Add a single <strong>Color Item</strong> to showcase a specific color anywhere on your page</li>
          <li>Use <strong>Typography Items</strong> individually to demonstrate specific text styles</li>
          <li>Place <strong>Button Items</strong> as standalone examples in documentation</li>
        </ul>
        <p>Independent child items inherit default styling but won't have access to parent-level override controls or toolbar features.</p>
      </div>
    </Card>

    <Card title="CSS Architecture">
      <div class="atsg-instructions__section">
        <h4>BEM Naming Convention</h4>
        <p>All Style Guide elements follow the <strong>BEM (Block Element Modifier)</strong> naming methodology for predictable, maintainable CSS:</p>
        <ul>
          <li><strong>Block:</strong> <code>.atsg-colors</code>, <code>.atsg-typography</code>, <code>.atsg-spacing</code></li>
          <li><strong>Element:</strong> <code>.atsg-colors__toolbar</code>, <code>.atsg-colors-item__swatch</code></li>
          <li><strong>Modifier:</strong> <code>.atsg-colors--compact</code>, <code>.atsg-buttons--grid</code></li>
        </ul>
        <p>The <code>atsg-</code> prefix (AT Style Guide) prevents conflicts with other plugins and themes.</p>
      </div>

      <div class="atsg-instructions__section">
        <h4>CSS @layer for Easy Overrides</h4>
        <p>Decorative styles are wrapped in <code>@layer atsg</code>, making them easy to override without specificity battles:</p>
        <div class="atsg-instructions__code-block">
          <code>/* Plugin styles use @layer */<br>
@layer atsg &#123;<br>
&nbsp;&nbsp;.atsg-colors-item__label &#123;<br>
&nbsp;&nbsp;&nbsp;&nbsp;font-weight: 600;<br>
&nbsp;&nbsp;&#125;<br>
&#125;<br><br>
/* Your overrides win automatically */<br>
.atsg-colors-item__label &#123;<br>
&nbsp;&nbsp;font-weight: 400;<br>
&nbsp;&nbsp;text-transform: uppercase;<br>
&#125;</code>
        </div>
        <p>Critical layout styles (display, flex, grid) remain outside the layer to ensure proper rendering.</p>
      </div>

      <div class="atsg-instructions__section">
        <h4>CSS Variables</h4>
        <p>Elements use AT CSS variables with fallback values, ensuring graceful degradation:</p>
        <ul>
          <li><code>var(--at-space--m, 1rem)</code> - Spacing with fallback</li>
          <li><code>var(--at-radius--m, 8px)</code> - Radius with fallback</li>
          <li><code>var(--at-neutral-d-2, #6b7280)</code> - Colors with fallback</li>
        </ul>
      </div>
    </Card>

    <Card title="Important Notes">
      <div class="atsg-instructions__section">
        <h4>Base Font Size Control</h4>
        <p>Each parent element includes a <strong>Base Font Size</strong> setting. Child elements use <code>em</code> units relative to this base, allowing you to scale the entire style guide proportionally.</p>
      </div>

      <div class="atsg-instructions__section">
        <h4>Responsive Behavior</h4>
        <p>Style Guide elements are responsive by default:</p>
        <ul>
          <li>Grid layouts adjust columns on smaller screens</li>
          <li>Spacing and typography values update when using <code>clamp()</code> expressions</li>
          <li>Mobile breakpoints switch to stacked layouts where appropriate</li>
        </ul>
      </div>

      <div class="atsg-instructions__section">
        <h4>Builder vs Frontend</h4>
        <p>Elements behave identically in the Bricks Builder and on the frontend. JavaScript features (like click-to-copy) work in both contexts, allowing you to test functionality while editing.</p>
      </div>

      <div class="atsg-instructions__section">
        <h4>Performance</h4>
        <p>CSS is output inline with each element and deduplicated automatically. If the same element appears multiple times on a page, styles are only loaded once.</p>
      </div>
    </Card>
  </div>
{/snippet}

{#snippet colorsContent()}
  <div class="atsg-instructions__content">
    <Card title="Colors Element">
      <p>The <strong>Colors (Nestable)</strong> element displays your color palette with light, dark, and transparent variations, featuring accessibility testing and click-to-copy functionality.</p>

      <div class="atsg-instructions__section">
        <h4>Structure</h4>
        <ul>
          <li><strong>Colors</strong> - Parent container element with toolbar and glossary</li>
          <li><strong>Colors Item</strong> - Individual color row showing base color + variations</li>
        </ul>
      </div>

      <div class="atsg-instructions__section">
        <h4>A11Y Badges Toggle</h4>
        <p>The parent Colors element includes an <strong>A11Y Badges</strong> toggle switch in the toolbar:</p>
        <ul>
          <li>When enabled, displays WCAG contrast badges on each color swatch</li>
          <li>Badges show <strong>W</strong> (white text contrast) and <strong>B</strong> (black text contrast)</li>
          <li>Color-coded: <span style="color: var(--wpea-color--success);">Green</span> = AAA/AA pass, <span style="color: var(--wpea-color--warning, #f59e0b);">Orange</span> = AA Large only, <span style="color: var(--wpea-color--danger);">Red</span> = Fail</li>
        </ul>
      </div>

      <div class="atsg-instructions__section">
        <h4>A11Y Glossary</h4>
        <p>When A11Y Badges are enabled and "Show A11Y Glossary" is checked in settings, an expandable glossary appears explaining:</p>
        <ul>
          <li><strong>WCAG Contrast Standards</strong> - Overview of accessibility guidelines</li>
          <li><strong>Contrast Ratios</strong> - AAA (7:1+), AA (4.5:1+), AA Large (3:1+) thresholds</li>
          <li><strong>Badge Legend</strong> - Visual guide to badge colors and meanings</li>
          <li><strong>W/B Explanation</strong> - What the white and black text badges represent</li>
        </ul>
      </div>

      <div class="atsg-instructions__section">
        <h4>Click-to-Copy Context Menu</h4>
        <p>Clicking any color swatch opens a context menu with:</p>
        <ul>
          <li><strong>CSS Variable</strong> - Click to copy <code>var(--at-primary)</code> format</li>
          <li><strong>Copy Hex</strong> - Copy the computed hex value (e.g., <code>#3b82f6</code>)</li>
          <li><strong>Copy RGB</strong> - Copy RGB format (e.g., <code>rgb(59, 130, 246)</code>)</li>
          <li><strong>Copy HSL</strong> - Copy HSL format (e.g., <code>hsl(217, 91%, 60%)</code>)</li>
          <li><strong>Copy OKLCH</strong> - Copy OKLCH format for modern color spaces</li>
          <li><strong>Contrast Checker</strong> - Shows exact contrast ratios against white and black text</li>
        </ul>
        <p>A visual "Copied!" confirmation appears after copying any value.</p>
      </div>

      <div class="atsg-instructions__section">
        <h4>Layout Modes</h4>
        <p>The parent Colors element supports different layout modes:</p>
        <ul>
          <li><strong>Default (Grid)</strong> - Base color on left, variations in columns to the right</li>
          <li><strong>Stacked (Vertical)</strong> - Base color on top, variations in horizontal rows below</li>
          <li><strong>Compact</strong> - Smaller swatches with labels appearing on hover</li>
          <li><strong>Compact Vertical</strong> - Combines compact sizing with vertical stacking</li>
        </ul>
      </div>

      <div class="atsg-instructions__section">
        <h4>Colors Item Settings</h4>
        <ul>
          <li><strong>Select Color</strong> - Choose from Advanced Themer Color Manager palette</li>
          <li><strong>Variation Count</strong> - Number of variations per column (1-6)</li>
          <li><strong>Hide Dark/Light/Transparency Variants</strong> - Toggle individual variant columns</li>
          <li><strong>Hide Color Name</strong> - Hide the color label above the swatches</li>
          <li><strong>Swatch Style</strong> - Customize swatch size, base width, border radius, and gap</li>
        </ul>
      </div>

      <div class="atsg-instructions__section">
        <h4>Parent Override Controls</h4>
        <p>The parent Colors element can override settings for all child items:</p>
        <ul>
          <li><strong>Display Override</strong> - Hide color name, CSS variable, hex value, or shade labels globally</li>
          <li><strong>Variations Override</strong> - Hide all variations or specific shade columns (light/dark/transparency)</li>
          <li><strong>Style Override</strong> - Set swatch size, base width, gap, and border radius for all items</li>
        </ul>
      </div>
    </Card>
  </div>
{/snippet}

{#snippet typographyContent()}
  <div class="atsg-instructions__content">
    <Card title="Typography Element">
      <p>The <strong>Typography (Nestable)</strong> element displays your typography scale with live computed values.</p>

      <div class="atsg-instructions__section">
        <h4>Structure</h4>
        <ul>
          <li><strong>Typography</strong> - Parent container element</li>
          <li><strong>Typography Item</strong> - Individual typography sample</li>
        </ul>
      </div>

      <div class="atsg-instructions__section">
        <h4>Typography Item Settings</h4>
        <ul>
          <li><strong>Label</strong> - Display name (e.g., "Heading 1", "Body Text")</li>
          <li><strong>Sample Text</strong> - Preview text to display</li>
          <li><strong>CSS Variable</strong> - The AT font-size variable (e.g., <code>--at-text--3xl</code>)</li>
          <li><strong>HTML Element</strong> - The semantic element to use (h1-h6, p, span)</li>
        </ul>
      </div>

      <div class="atsg-instructions__section">
        <h4>Live Updates</h4>
        <p>The element automatically calculates and displays:</p>
        <ul>
          <li>Computed font size in pixels</li>
          <li>Font family name</li>
          <li>Line height (if specified)</li>
        </ul>
        <p>Values update on window resize for responsive typography scales.</p>
      </div>
    </Card>
  </div>
{/snippet}

{#snippet spacingContent()}
  <div class="atsg-instructions__content">
    <Card title="Spacing Element">
      <p>The <strong>Spacing (Nestable)</strong> element visualizes your spacing scale with responsive bars.</p>

      <div class="atsg-instructions__section">
        <h4>Structure</h4>
        <ul>
          <li><strong>Spacing</strong> - Parent container element</li>
          <li><strong>Spacing Item</strong> - Individual spacing token visualization</li>
        </ul>
      </div>

      <div class="atsg-instructions__section">
        <h4>Spacing Item Settings</h4>
        <ul>
          <li><strong>Label</strong> - Display name (e.g., "Small", "Medium", "Large")</li>
          <li><strong>CSS Variable</strong> - The AT spacing variable (e.g., <code>--at-space--m</code>)</li>
          <li><strong>Bar Color</strong> - Color for the visual bar</li>
        </ul>
      </div>

      <div class="atsg-instructions__section">
        <h4>Live Updates</h4>
        <p>The element calculates the current pixel value from clamp() expressions and updates on window resize.</p>
      </div>
    </Card>
  </div>
{/snippet}

{#snippet radiiContent()}
  <div class="atsg-instructions__content">
    <Card title="Radii Element">
      <p>The <strong>Radii (Nestable)</strong> element displays your border radius tokens as visual boxes.</p>

      <div class="atsg-instructions__section">
        <h4>Structure</h4>
        <ul>
          <li><strong>Radii</strong> - Parent container with grid layout</li>
          <li><strong>Radii Item</strong> - Individual radius preview box</li>
        </ul>
      </div>

      <div class="atsg-instructions__section">
        <h4>Radii Item Settings</h4>
        <ul>
          <li><strong>Label</strong> - Display name (e.g., "Small", "Large", "Pill")</li>
          <li><strong>CSS Variable</strong> - The AT radius variable (e.g., <code>--at-radius--m</code>)</li>
          <li><strong>Show Variable Name</strong> - Display the CSS variable name</li>
          <li><strong>Show Computed Value</strong> - Display the pixel value</li>
        </ul>
      </div>
    </Card>
  </div>
{/snippet}

{#snippet boxShadowsContent()}
  <div class="atsg-instructions__content">
    <Card title="Box Shadows Element">
      <p>The <strong>Box Shadows (Nestable)</strong> element showcases your shadow tokens on preview cards.</p>

      <div class="atsg-instructions__section">
        <h4>Structure</h4>
        <ul>
          <li><strong>Box Shadows</strong> - Parent container with grid layout</li>
          <li><strong>Box Shadow Item</strong> - Individual shadow preview card</li>
        </ul>
      </div>

      <div class="atsg-instructions__section">
        <h4>Box Shadow Item Settings</h4>
        <ul>
          <li><strong>Label</strong> - Display name (e.g., "Soft", "Medium", "Heavy")</li>
          <li><strong>CSS Variable</strong> - The AT shadow variable (e.g., <code>--at-shadow--m</code>)</li>
          <li><strong>Show Variable Name</strong> - Display the CSS variable name</li>
          <li><strong>Show Shadow Definition</strong> - Display the raw shadow value</li>
        </ul>
      </div>
    </Card>
  </div>
{/snippet}

{#snippet buttonsContent()}
  <div class="atsg-instructions__content">
    <Card title="Buttons Element">
      <p>The <strong>Buttons (Nestable)</strong> element displays button variants using Bricks' native button styles.</p>

      <div class="atsg-instructions__section">
        <h4>Structure</h4>
        <ul>
          <li><strong>Buttons</strong> - Parent container with toolbar toggles</li>
          <li><strong>Button Item</strong> - Individual button sample</li>
        </ul>
      </div>

      <div class="atsg-instructions__section">
        <h4>Button Item Settings</h4>
        <ul>
          <li><strong>Label</strong> - Button text</li>
          <li><strong>Description</strong> - Optional description (auto-generated if empty)</li>
          <li><strong>Variant</strong> - Solid or Outline</li>
          <li><strong>Color</strong> - Primary, Secondary, Dark, Light, Info, Success, Warning, Danger</li>
          <li><strong>Size</strong> - Small, Medium, Large, Extra Large</li>
          <li><strong>Shape</strong> - Default, Square, Round, Circle</li>
        </ul>
      </div>

      <div class="atsg-instructions__section">
        <h4>Interactive Toolbar</h4>
        <p>The parent element includes toggle switches to:</p>
        <ul>
          <li><strong>Rounded</strong> - Apply rounded corners to all buttons</li>
          <li><strong>Outline</strong> - Switch all buttons to outline variant</li>
        </ul>
      </div>

      <div class="atsg-instructions__section">
        <h4>Layout Modes</h4>
        <ul>
          <li><strong>Grid</strong> - Buttons arranged in a configurable grid</li>
          <li><strong>Rows</strong> - Buttons grouped by color in horizontal rows</li>
        </ul>
      </div>
    </Card>
  </div>
{/snippet}

<div class="atsg-instructions">
  <header class="atsg-instructions__header">
    <h1>AT Style Guide</h1>
    <p class="atsg-instructions__subtitle">Bricks Builder Elements for Advanced Themer Style Guides</p>
  </header>

  <Tabs {tabs} />
</div>

<style>
  .atsg-instructions {
    max-width: 960px;
    margin: 0 auto;
    padding: var(--wpea-space--lg) var(--wpea-space--md);
  }

  .atsg-instructions__header {
    text-align: center;
    margin-bottom: var(--wpea-space--xl);
  }

  .atsg-instructions__header h1 {
    font-size: var(--wpea-text--3xl);
    font-weight: 700;
    margin: 0 0 var(--wpea-space--xs) 0;
    color: var(--wpea-surface--text);
  }

  .atsg-instructions__subtitle {
    font-size: var(--wpea-text--md);
    color: var(--wpea-surface--text-muted);
    margin: 0;
  }

  .atsg-instructions__content {
    padding: var(--wpea-space--md) 0;
    display: flex;
    flex-direction: column;
    gap: var(--wpea-space--lg);
  }

  .atsg-instructions__section {
    margin-top: var(--wpea-space--lg);
  }

  .atsg-instructions__section h4 {
    font-size: var(--wpea-text--md);
    font-weight: 600;
    margin: 0 0 var(--wpea-space--sm) 0;
    color: var(--wpea-surface--text);
  }

  .atsg-instructions__section p {
    margin: 0 0 var(--wpea-space--sm) 0;
    color: var(--wpea-surface--text-muted);
  }

  .atsg-instructions__section ul {
    margin: 0;
    padding-left: var(--wpea-space--lg);
    color: var(--wpea-surface--text-muted);
  }

  .atsg-instructions__section li {
    margin-bottom: var(--wpea-space--xs);
  }

  .atsg-instructions__section code {
    background: var(--wpea-surface--muted);
    padding: 0.125em 0.375em;
    border-radius: var(--wpea-radius--xs);
    font-size: 0.875em;
    color: var(--wpea-color--primary);
  }

  .atsg-instructions__code-block {
    background: var(--wpea-surface--muted);
    border-radius: var(--wpea-radius--sm);
    padding: var(--wpea-space--md);
    margin: var(--wpea-space--sm) 0;
    overflow-x: auto;
  }

  .atsg-instructions__code-block code {
    background: transparent;
    padding: 0;
    font-size: 0.8125em;
    line-height: 1.6;
    color: var(--wpea-surface--text);
    display: block;
    white-space: pre-wrap;
  }

  :global(.wpea-alert) {
    margin-bottom: var(--wpea-space--md);
  }
</style>
