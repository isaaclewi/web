@extends('student.master')
@section('title', 'Mes Enseignants')
@section('page-title', 'Mes Enseignants')
@section('page-sub', 'Les professeurs de votre classe')

@section('content')
<style>
    /* ─────────────────────────────────────────────
   RESPONSIVE — PAGE ENSEIGNANTS
───────────────────────────────────────────── */

/* Tablettes */
@media (max-width: 1024px) {

    .three-col {
        grid-template-columns: repeat(2, 1fr) !important;
    }

    .card {
        padding: .75rem;
    }
}

/* Mobile */
@media (max-width: 768px) {

    /* Grid enseignants */
    .three-col {
        grid-template-columns: 1fr !important;
        gap: .75rem;
    }

    /* Cards enseignants */
    .card {
        border-radius: .75rem;
    }

    .card img {
        width: 60px !important;
        height: 60px !important;
    }

    .card div[style*="font-weight:700"] {
        font-size: .9rem !important;
    }

    /* Filter bar */
    .filter-bar {
        flex-wrap: wrap;
        gap: .5rem;
    }

    .filter-bar .f-input {
        width: 100% !important;
    }

    .filter-bar button,
    .filter-bar a {
        width: 100%;
        text-align: center;
    }

    /* Badges */
    .badge {
        font-size: .6rem;
        padding: .2rem .4rem;
    }

    /* Table */
    .t-table {
        font-size: .75rem;
    }

    .t-table th,
    .t-table td {
        padding: .4rem;
        white-space: nowrap;
    }

    /* Scroll table horizontal */
    .t-table {
        display: block;
        overflow-x: auto;
        white-space: nowrap;
    }
}

/* Petits mobiles */
@media (max-width: 480px) {

    .card img {
        width: 50px !important;
        height: 50px !important;
    }

    .card {
        padding: .65rem !important;
    }

    .badge {
        font-size: .55rem;
    }

    .mono {
        font-size: .65rem !important;
    }

    /* Modal */
    .modal {
        width: 95% !important;
        padding: 1rem !important;
    }

    .modal-title {
        font-size: 1rem;
    }

    #teacherModalContent {
        font-size: .8rem;
    }

    .info-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: .5rem;
    }
}

/* Très petits écrans */
@media (max-width: 360px) {

    .card div[style*="font-size:1.25rem"] {
        font-size: 1rem !important;
    }

    .t-table {
        font-size: .65rem;
    }

    .badge {
        font-size: .5rem;
    }
}
</style>
<nav class="bc">
    <a href="{{ route('student.dashboard') }}">Tableau de bord</a>
    <span class="bc-sep">›</span><span class="bc-cur">Mes Enseignants</span>
</nav>

{{-- FILTRE --}}
<form method="GET" action="{{ route('student.enseignants') }}">
<div class="filter-bar">
    <span class="filter-label">Rechercher</span>
    <div class="filter-sep"></div>
    <div style="position:relative;">
        <input type="text" name="search" value="{{ request('search') }}" class="f-input sm" style="width:240px;padding-left:1.75rem;" placeholder="Nom, spécialité…">
        <span style="position:absolute;left:.5rem;top:50%;transform:translateY(-50%);color:var(--muted);">🔍</span>
    </div>
    <div class="filter-spacer"></div>
    <button type="submit" class="btn btn-p btn-sm">Appliquer</button>
    @if(request('search'))
        <a href="{{ route('student.enseignants') }}" class="btn btn-o btn-sm">✕</a>
    @endif
</div>
</form>

@if($teachers->isEmpty())
    <div class="card"><div class="empty"><div class="empty-icon">👨‍🏫</div><div class="empty-text">Aucun enseignant trouvé</div></div></div>
@else

{{-- GRILLE ENSEIGNANTS --}}
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1.25rem;margin-bottom:1.25rem;" class="three-col">
    @foreach($teachers as $teacher)
        <div class="card" style="cursor:pointer;transition:all .2s;" onmouseover="this.style.boxShadow='0 4px 20px rgba(0,0,0,.08)'" onmouseout="this.style.boxShadow=''" onclick="openTeacher({{ $teacher->id }})">
            <div style="padding:1.5rem;text-align:center;border-bottom:1px solid #f1f5f9;">
                <div style="position:relative;display:inline-block;margin-bottom:.875rem;">
                    <img src="https://i.pravatar.cc/72?u=t{{ $teacher->id }}"
                         style="width:72px;height:72px;border-radius:1rem;object-fit:cover;box-shadow:0 4px 12px rgba(0,0,0,.1);">
                    @if($teacher->status==='actif')
                        <div style="position:absolute;bottom:2px;right:2px;width:12px;height:12px;background:var(--success);border:2px solid white;border-radius:50%;"></div>
                    @endif
                </div>
                <div style="font-weight:700;color:var(--ink);font-size:.95rem;">{{ $teacher->prenom }} {{ $teacher->nom }}</div>
                <div style="font-size:.75rem;color:var(--muted);margin-top:.2rem;">{{ $teacher->specialite ?? 'Enseignant' }}</div>
                <div style="margin-top:.625rem;display:flex;justify-content:center;flex-wrap:wrap;gap:.3rem;">
                    @foreach($teacher->my_subjects as $sub)
                        <span class="badge b-indigo" style="font-size:.65rem;">{{ $sub->name }}</span>
                    @endforeach
                </div>
            </div>
            <div style="padding:1rem;display:grid;grid-template-columns:1fr 1fr;gap:.5rem;text-align:center;">
                <div>
                    @if($teacher->my_avg)
                        @php $pill = $teacher->my_avg>=14?'gp-A':($teacher->my_avg>=10?'gp-B':($teacher->my_avg>=8?'gp-C':'gp-D')); @endphp
                        <span class="gp {{ $pill }}" style="width:100%;display:flex;">{{ $teacher->my_avg }}</span>
                    @else
                        <span style="font-size:.8rem;color:var(--muted);">—</span>
                    @endif
                    <div style="font-size:.65rem;color:var(--muted);margin-top:.2rem;">Ma moy.</div>
                </div>
                <div>
                    <div class="mono" style="font-weight:700;color:var(--ink);">{{ $teacher->my_grade_cnt }}</div>
                    <div style="font-size:.65rem;color:var(--muted);margin-top:.2rem;">Notes</div>
                </div>
            </div>
            <div style="padding:.75rem 1rem;border-top:1px solid #f1f5f9;display:flex;gap:.5rem;font-size:.72rem;color:var(--muted);">
                @if($teacher->email)<span title="Email">✉️ {{ Str::limit($teacher->email, 22) }}</span>@endif
                @if($teacher->telephone)<span>📞 {{ $teacher->telephone }}</span>@endif
                @if(!$teacher->email && !$teacher->telephone)<span>Pas de contact disponible</span>@endif
            </div>
        </div>
    @endforeach
</div>

{{-- TABLE RÉCAP --}}
<div class="card">
    <div class="card-header">
        <div class="card-title">Récapitulatif</div>
        <span class="badge b-green">{{ $teachers->count() }} enseignant(s)</span>
    </div>
    <table class="t-table">
        <thead><tr><th>Enseignant</th><th>Spécialité</th><th>Matières</th><th>Coeff. total</th><th>Ma moyenne</th><th>Notes reçues</th><th>Contrat</th></tr></thead>
        <tbody>
            @foreach($teachers as $t)
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:.5rem;">
                            <img src="https://i.pravatar.cc/32?u=t{{ $t->id }}" style="width:30px;height:30px;border-radius:.375rem;object-fit:cover;">
                            <span style="font-weight:600;">{{ $t->prenom }} {{ $t->nom }}</span>
                        </div>
                    </td>
                    <td>{{ $t->specialite ?? '—' }}</td>
                    <td>
                        @foreach($t->my_subjects as $s)
                            <span class="badge b-indigo" style="margin:.1rem;font-size:.65rem;">{{ $s->name }}</span>
                        @endforeach
                    </td>
                    <td class="mono">{{ $t->my_subjects->sum('coefficient') }}</td>
                    <td>
                        @if($t->my_avg)
                            @php $p=$t->my_avg>=14?'gp-A':($t->my_avg>=10?'gp-B':($t->my_avg>=8?'gp-C':'gp-D')); @endphp
                            <span class="gp {{ $p }}">{{ $t->my_avg }}</span>
                        @else <span style="color:var(--muted);">—</span>@endif
                    </td>
                    <td class="mono">{{ $t->my_grade_cnt }}</td>
                    <td><span class="badge b-gray">{{ $t->type_contrat ?? '—' }}</span></td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

{{-- MODAL PROFIL ENSEIGNANT --}}
<div class="modal-bg" id="teacherModal">
    <div class="modal modal-lg">
        <button class="modal-close" onclick="document.getElementById('teacherModal').classList.remove('open')">✕</button>
        <div class="modal-title">Fiche enseignant</div>
        <div id="teacherModalContent" style="color:var(--muted);">Chargement…</div>
    </div>
</div>

@push('scripts')
<style>
.three-col { grid-template-columns: repeat(3,1fr); }
@media(max-width:900px){ .three-col { grid-template-columns: repeat(2,1fr); } }
@media(max-width:600px){ .three-col { grid-template-columns: 1fr; } }
</style>
<script>
function openTeacher(id) {
    const modal = document.getElementById('teacherModal');
    const content = document.getElementById('teacherModalContent');
    modal.classList.add('open');

    // Données embarquées (pas de route JSON dédié pour les teachers coté étudiant)
    const teachers = {!! $teachersJson !!};

    const t = teachers.find(t => t.id === id);
    if (!t) { content.innerHTML = '<p style="color:red;">Enseignant introuvable</p>'; return; }

    const subs = t.my_subjects.map(s=>`<span class="badge b-indigo">${s.name} ×${s.coefficient}</span>`).join(' ');
    const niveaux = t.niveaux?.map(n=>`<span class="badge b-purple">${n}</span>`).join(' ') || '—';

    content.innerHTML = `
        <div style="display:flex;align-items:center;gap:1.25rem;margin-bottom:1.5rem;padding-bottom:1.25rem;border-bottom:1px solid var(--border);">
            <img src="https://i.pravatar.cc/72?u=t${t.id}" style="width:72px;height:72px;border-radius:1rem;object-fit:cover;box-shadow:0 4px 12px rgba(0,0,0,.1);">
            <div>
                <div style="font-size:1.25rem;font-weight:700;color:var(--ink);">${t.prenom} ${t.nom}</div>
                <div style="font-size:.8rem;color:var(--muted);margin-top:.2rem;">${t.specialite ?? 'Enseignant'} · ${t.matricule ?? ''}</div>
                <div style="margin-top:.5rem;">${subs}</div>
            </div>
            <div style="margin-left:auto;text-align:center;background:var(--primary-light);border-radius:.875rem;padding:1rem 1.5rem;">
                <div class="mono" style="font-size:1.75rem;font-weight:800;color:var(--primary);">${t.my_avg ?? '—'}</div>
                <div style="font-size:.7rem;color:var(--muted);">Ma moyenne</div>
            </div>
        </div>
        <div class="info-grid">
            <div class="info-item"><div class="info-key">Email</div><div class="info-val" style="font-size:.82rem;">${t.email ?? '—'}</div></div>
            <div class="info-item"><div class="info-key">Téléphone</div><div class="info-val">${t.telephone ?? '—'}</div></div>
            <div class="info-item"><div class="info-key">Type contrat</div><div class="info-val">${t.type_contrat ?? '—'}</div></div>
            <div class="info-item"><div class="info-key">Notes reçues</div><div class="info-val mono">${t.my_grade_cnt}</div></div>
            <div class="info-item full"><div class="info-key">Niveaux enseignés</div><div class="info-val" style="margin-top:.4rem;">${niveaux}</div></div>
        </div>
    `;
}
</script>
@endpush
@endsection