<div class="row mt-3">
  <livewire:comments :model="$task" newest-first />
</div>
@livewireScripts
<x-comments::scripts />
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
