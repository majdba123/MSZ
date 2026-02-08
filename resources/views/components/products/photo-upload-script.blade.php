@props(['color' => 'brand', 'alertId' => 'create-alert'])

<script>
(function() {
    const photoInput = document.getElementById('photo-input');
    const preview = document.getElementById('photo-preview');
    const dropZone = document.getElementById('drop-zone');
    const countEl = document.getElementById('photo-count');
    let selectedFiles = [];

    if (!photoInput || !preview || !dropZone) return;

    // Click to browse
    dropZone.addEventListener('click', () => photoInput.click());

    // Drag & drop
    const hoverBorder = '{{ $color }}' === 'brand' ? 'border-brand-400' : 'border-emerald-400';
    const hoverBg = '{{ $color }}' === 'brand' ? 'bg-brand-50/40' : 'bg-emerald-50/40';
    ['dragenter', 'dragover'].forEach(e => dropZone.addEventListener(e, ev => {
        ev.preventDefault();
        dropZone.classList.add(hoverBorder, hoverBg);
    }));
    ['dragleave', 'drop'].forEach(e => dropZone.addEventListener(e, ev => {
        ev.preventDefault();
        dropZone.classList.remove(hoverBorder, hoverBg);
    }));
    dropZone.addEventListener('drop', ev => {
        const files = Array.from(ev.dataTransfer.files).filter(f => f.type.startsWith('image/'));
        addFiles(files);
    });

    photoInput.addEventListener('change', function () {
        addFiles(Array.from(this.files));
        this.value = '';
    });

    function addFiles(files) {
        if (selectedFiles.length + files.length > 10) {
            showAlert('{{ $alertId }}', 'Maximum 10 photos allowed.');
            return;
        }
        selectedFiles = selectedFiles.concat(files);
        renderPreviews();
    }

    function renderPreviews() {
        if (selectedFiles.length === 0) {
            preview.innerHTML = '';
            countEl.classList.add('hidden');
            return;
        }
        countEl.textContent = selectedFiles.length + ' of 10 photos selected';
        countEl.classList.remove('hidden');

        preview.innerHTML = selectedFiles.map((f, i) => {
            const url = URL.createObjectURL(f);
            const sizeMB = (f.size / 1048576).toFixed(1);
            return `<div class="group relative overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm transition-shadow hover:shadow-md">
                <div class="aspect-square overflow-hidden">
                    <img src="${url}" class="h-full w-full object-cover transition-transform duration-200 group-hover:scale-105" alt="">
                </div>
                <div class="px-2.5 py-2">
                    <p class="truncate text-xs font-medium text-gray-700">${esc(f.name)}</p>
                    <p class="text-[10px] text-gray-400">${sizeMB} MB</p>
                </div>
                <button type="button" onclick="removePreview(${i})" class="absolute right-1.5 top-1.5 flex h-6 w-6 items-center justify-center rounded-full bg-red-500 text-white shadow-lg hover:bg-red-600" title="Remove photo">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>`;
        }).join('');
    }

    window.removePreview = function (i) {
        selectedFiles.splice(i, 1);
        renderPreviews();
    };

    // Expose selectedFiles for form submission
    window.getSelectedPhotos = function() {
        return selectedFiles;
    };

    function esc(t) {
        if (!t) return '';
        const d = document.createElement('div');
        d.textContent = t;
        return d.innerHTML;
    }

    function showAlert(id, msg) {
        const el = document.getElementById(id);
        if (el) {
            const msgEl = document.getElementById(id + '-message');
            if (msgEl) msgEl.textContent = msg;
            el.classList.remove('hidden');
        }
    }
})();
</script>

