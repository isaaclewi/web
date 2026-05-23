{{--
    Partial réutilisable dans les modals d'ajout/édition de livres.
    Variables optionnelles : $isEdit (bool), $book (LibraryBook)
--}}
@php $isEdit = $isEdit ?? false; @endphp

<div style="display:grid;grid-template-columns:1fr 1fr;gap:.875rem;">
    <div style="grid-column:span 2;">
        <label class="f-label">Titre *</label>
        <input type="text" name="title" class="f-field" required placeholder="Titre du livre"
            value="{{ old('title', $book->title ?? '') }}">
    </div>
    <div>
        <label class="f-label">Auteur</label>
        <input type="text" name="author" class="f-field" placeholder="Nom de l'auteur"
            value="{{ old('author', $book->author ?? '') }}">
    </div>
    <div>
        <label class="f-label">ISBN</label>
        <input type="text" name="isbn" class="f-field" placeholder="ISBN"
            value="{{ old('isbn', $book->isbn ?? '') }}">
    </div>
    <div>
        <label class="f-label">Catégorie</label>
        <select name="category" class="f-field">
            <option value="">-- Choisir --</option>
            @foreach(\App\Models\LibraryBook::categories() as $cat)
                <option value="{{ $cat }}" @selected(old('category', $book->category ?? '') === $cat)>{{ $cat }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="f-label">Niveau</label>
        <input type="text" name="level" class="f-field" placeholder="Ex : Terminale, Licence 1"
            value="{{ old('level', $book->level ?? '') }}">
    </div>
    <div style="grid-column:span 2;">
        <label class="f-label">Description</label>
        <textarea name="description" class="f-field" rows="3" placeholder="Résumé ou description du livre">{{ old('description', $book->description ?? '') }}</textarea>
    </div>

    {{-- Upload fichier --}}
    <div style="grid-column:span 2;">
        <label class="f-label">{{ $isEdit ? 'Remplacer le fichier' : 'Fichier *' }} (PDF, DOCX, PPTX, XLSX, EPUB — max 50 Mo)</label>
        <label class="upload-zone" id="fileZone">
            <input type="file" name="file" accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.epub"
                onchange="showFileName(this, 'fileLabel')" {{ $isEdit ? '' : 'required' }}>
            <div style="font-size:2rem;">📁</div>
            <div id="fileLabel" style="font-size:.8rem;color:var(--c-muted,#64748b);margin-top:.3rem;">
                {{ $isEdit ? 'Cliquer pour remplacer le fichier actuel' : 'Cliquer pour sélectionner un fichier' }}
            </div>
        </label>
    </div>

    {{-- Couverture --}}
    <div style="grid-column:span 2;">
        <label class="f-label">Image de couverture (JPG, PNG, WEBP — max 2 Mo)</label>
        <label class="upload-zone">
            <input type="file" name="cover" accept=".jpg,.jpeg,.png,.webp"
                onchange="showFileName(this, 'coverLabel')">
            <div style="font-size:1.5rem;">🖼</div>
            <div id="coverLabel" style="font-size:.78rem;color:var(--c-muted,#64748b);margin-top:.25rem;">
                Cliquer pour ajouter une couverture
            </div>
        </label>
    </div>

    {{-- Options --}}
    <div style="display:flex;align-items:center;gap:.5rem;">
        <input type="hidden" name="allow_download" value="0">
        <input type="checkbox" name="allow_download" value="1" id="allow_dl_{{ $isEdit ? 'edit' : 'add' }}"
            {{ old('allow_download', ($book->allow_download ?? true) ? '1' : '0') == '1' ? 'checked' : '' }}
            style="accent-color:var(--c-accent,#00d4ff);width:16px;height:16px;">
        <label for="allow_dl_{{ $isEdit ? 'edit' : 'add' }}" style="font-size:.8rem;color:var(--c-text,#e2e8f0);cursor:pointer;">
            Autoriser le téléchargement
        </label>
    </div>
    <div style="display:flex;align-items:center;gap:.5rem;">
        <input type="hidden" name="is_published" value="0">
        <input type="checkbox" name="is_published" value="1" id="is_pub_{{ $isEdit ? 'edit' : 'add' }}"
            {{ old('is_published', ($book->is_published ?? true) ? '1' : '0') == '1' ? 'checked' : '' }}
            style="accent-color:var(--c-accent,#00d4ff);width:16px;height:16px;">
        <label for="is_pub_{{ $isEdit ? 'edit' : 'add' }}" style="font-size:.8rem;color:var(--c-text,#e2e8f0);cursor:pointer;">
            Publier immédiatement
        </label>
    </div>
</div>

@push('scripts')
<script>
function showFileName(input, labelId) {
    const label = document.getElementById(labelId);
    if (input.files && input.files[0]) {
        label.textContent = '✅ ' + input.files[0].name;
    }
}
</script>
@endpush
