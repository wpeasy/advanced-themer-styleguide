<script lang="ts">
  // Svelte 5 component using runes
  let message = $state('Advanced Themer Style Guide');
</script>

<div class="at-style-guide">
  <p>{message}</p>
</div>

<style>
  .at-style-guide {
    padding: 1rem;
  }
</style>
