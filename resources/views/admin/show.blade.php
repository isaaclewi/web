@extends('admin.master')

@section('title', 'Demande de consultation #' . $transfer->id)

@push('styles')
<style>
    :root { --ink:#111827; --ink-mid:#374151; --muted:#6b7280; --border:#e5e7eb; --radius:.75rem; --accent:#1f2937; }
    .card { background:white; border:1px solid var(--border); border-radius:var(--radius); overflow:hidden; margin-bottom:1.25rem; }
    .card-head { padding:.875rem 1.375rem; border-bottom:1px solid #f3f4f6; display:flex; align-items:center; gap:.5rem; }
    .card-title { font-size:.875rem; font-weight:700; color:var(--ink); }
    .card-body { padding:1.25rem 1.375rem; }
    .info-row { display:flex; gap:.75rem; padding:.65rem 0; border-bottom:1px solid #f9fafb; align-items:flex-start; }
    .info-row:last-child { border-bottom:none; }
    .info-key { font-size:.65rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:#9ca3af; min-width:140px; flex-shrink:0; padding-top:.1rem; }
    .info-val { font-size:.8125rem; font-weight:500; color:var(--ink); }
    .badge { display:inline-block; padding:.25rem .75rem; border-radius:99px; font-size:.72rem; font-weight:700; }
    .badge-pending  { background:#fef3c7; color:#92400e; }
    .badge-approved { background:#d1fae5; color:#065f46; }
    .badge-rejected { background:#fee2e2; color:#991b1b; }
    .badge-completed{ background:#dbeafe; color:#1e40af; }
    .scope-tag { display:inline-block; background:#f3f4f6; color:#374151; font-size:.72rem; padding:.2rem .6rem; border-radius:4px; margin:.1rem; }
    .btn-back { display:inline-flex; align-items:center; gap:.375rem; padding:.45rem .875rem; background:#f3f4f6; border:1px solid var(--border); border-radius:.5rem; color:var(--muted); font-size:.8rem; font-weight:600; text-decoration:none; }
    .btn-back:hover { background:#e5e7eb; color:var(--ink); }
    .btn-green { background:#10b981; color:white; font-weight:700; border:none; padding:.575rem 1.25rem; border-radius:.5rem; cursor:pointer; font-size:.8rem; font-family:inherit; }
    .btn-red { background:#ef4444; color:white; font-weight:700; border:none; padding:.575rem 1.25rem; border-radius:.5rem; cursor:pointer; font-size:.8rem; font-family:inherit; }
    .btn-primary { background:var(--accent); color:white; font-weight:700; border:none; padding:.575rem 1.25rem; border-radius:.5rem; cursor:pointer; font-size:.8rem; font-family:inherit; text-decoration:none; display:inline-flex; align-items:center; gap:.375rem; }
    .flash-ok  { background:#d1fae5; border:1px solid #6ee7b7; color:#065f46; border-radius:.5rem; padding:.75rem 1rem; font-size:.8rem; margin-bottom:1.25rem; }
    .flash-err { background:#fee2e2; border:1px solid #fca5a5; color:#991b1b; border-radius:.5rem; padding:.75rem 1rem; font-size:.8rem; margin-bottom:1.25rem; }
    .f-field { background:#f9fafb; border:1px solid var(--border); border-radius:.5rem; padding:.575rem .875rem; font-size:.875rem; color:var(--ink-mid); width:100%; outline:none; font-family:inherit; }
    .f-field:focus { border-color:var(--accent); }
    .f-label { font-size:.68rem; font-weight:700; color:var(--muted); text-transform:uppercase; letter-spacing:.05em; display:block; margin-bottom:.3rem; }
</style>
@endpush

@section('content')

@if(session('success'))
    <div class="flash-ok">✓ {{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="flash-err">⚠️ {{ session('error') }}</div>
@endif

<div style="display:flex;align-items:center;gap:.75rem;margin-bottom:1.25rem;flex-wrap:wrap;">
    <a href="{{ route('admin.transfer.index') }}" class="btn-back">← Retour</a>
    <div>
        <h1 style="font-size:1rem;font-weight:700;color:#111827;">
            Demande de consultation #{{ $transfer->id }}
        </h1>
        <p style="font-size:.72rem;color:#6b7280;">
            <span class="badge badge-{{ $transfer->statut }}">{{ $transfer->statut_label }}</span>
        </p>
    </div>
    @if(in_array($transfer->statut, ['approved','completed']) && $transfer->institution_dest_id === $institution->id)
        <a href="{{ route('admin.transfer.dossier', $transfer) }}" class="btn-primary" style="margin-left:auto;">
            📂 Consulter le dossier
        </a>
    @endif
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;">

    {{-- Détails de la demande --}}
    <div>
        <div class="card">
            <div class="card-head">
                <span style="font-size:1rem;">📋</span>
                <span class="card-title">Détails de la demande</span>
            </div>
            <div class="card-body">
                <div class="info-row">
                    <span class="info-key">Apprenant</span>
                    <span class="info-val" style="font-weight:700;">
                        {{ $apprenant->prenom }} {{ $apprenant->nom }}
                        <span style="font-family:monospace;font-size:.75rem;color:#6b7280;margin-left:.5rem;">{{ $apprenant->matricule }}</span>
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-key">École source</span>
                    <span class="info-val">{{ $transfer->institutionSource?->name }}</span>
                </div>
                <div class="info-row">
                    <span class="info-key">École demandeuse</span>
                    <span class="info-val">{{ $transfer->institutionDest?->name }}</span>
                </div>
                <div class="info-row">
                    <span class="info-key">Demandé par</span>
                    <span class="info-val">{{ $transfer->requestedBy?->name }}</span>
                </div>
                <div class="info-row">
                    <span class="info-key">Date de demande</span>
                    <span class="info-val">{{ $transfer->created_at->format('d/m/Y à H:i') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-key">Motif</span>
                    <span class="info-val">{{ $transfer->motif }}</span>
                </div>
                <div class="info-row">
                    <span class="info-key">Données demandées</span>
                    <span class="info-val">
                        @foreach($transfer->scope ?? [] as $s)
                            <span class="scope-tag">{{ \App\Models\TransferRequest::scopeLabels()[$s] ?? $s }}</span>
                        @endforeach
                    </span>
                </div>
                @if($transfer->statut !== 'pending')
                <div class="info-row">
                    <span class="info-key">Traité par</span>
                    <span class="info-val">{{ $transfer->processedBy?->name ?? '—' }} {{ $transfer->processed_at ? 'le '.$transfer->processed_at->format('d/m/Y') : '' }}</span>
                </div>
                @endif
                @if($transfer->motif_refus)
                <div class="info-row">
                    <span class="info-key">Motif du refus</span>
                    <span class="info-val" style="color:#991b1b;">{{ $transfer->motif_refus }}</span>
                </div>
                @endif
                @if($transfer->token_expires_at)
                <div class="info-row">
                    <span class="info-key">Accès expire le</span>
                    <span class="info-val">{{ $transfer->token_expires_at->format('d/m/Y à H:i') }}</span>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Profil de l'apprenant --}}
    <div>
        <div class="card">
            <div class="card-head">
                <span style="font-size:1rem;">👤</span>
                <span class="card-title">Profil de l'apprenant</span>
            </div>
            <div class="card-body">
                <div class="info-row">
                    <span class="info-key">Nom complet</span>
                    <span class="info-val" style="font-weight:700;">{{ $apprenant->prenom }} {{ $apprenant->nom }}</span>
                </div>
                <div class="info-row">
                    <span class="info-key">Matricule</span>
                    <span class="info-val" style="font-family:monospace;">{{ $apprenant->matricule }}</span>
                </div>
                <div class="info-row">
                    <span class="info-key">Date de naissance</span>
                    <span class="info-val">{{ $apprenant->date_naissance ?? '—' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-key">Sexe</span>
                    <span class="info-val">{{ $apprenant->sexe === 'M' ? '👨 Masculin' : ($apprenant->sexe === 'F' ? '👩 Féminin' : '—') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-key">Établissement</span>
                    <span class="info-val">{{ $apprenant->institution?->name }}</span>
                </div>
                <div class="info-row">
                    <span class="info-key">Classe actuelle</span>
                    <span class="info-val">{{ $apprenant->classe?->name ?? '—' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-key">Niveau</span>
                    <span class="info-val">{{ $apprenant->niveau?->name ?? '—' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-key">Filière</span>
                    <span class="info-val">{{ $apprenant->filiere?->name ?? '—' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-key">Année académique</span>
                    <span class="info-val">{{ $apprenant->annee_academique ?? '—' }}</span>
                </div>
            </div>
        </div>

        {{-- Actions si je suis la source et demande en attente --}}
        @if($transfer->statut === 'pending' && $transfer->institution_source_id === $institution->id)
        <div class="card">
            <div class="card-head">
                <span style="font-size:1rem;">⚡</span>
                <span class="card-title">Traiter cette demande</span>
            </div>
            <div class="card-body">
                <p style="font-size:.8rem;color:#6b7280;margin-bottom:1rem;">
                    L'établissement <strong>{{ $transfer->institutionDest?->name }}</strong> souhaite consulter
                    le dossier de cet apprenant. En approuvant, vous lui accordez un accès de 72 heures.
                </p>
                <div style="display:flex;gap:.75rem;flex-wrap:wrap;">
                    <form method="POST" action="{{ route('admin.transfer.approve', $transfer) }}">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn-green"
                                onclick="return confirm('Approuver et accorder l\'accès pendant 72h ?')">
                            ✅ Approuver la demande
                        </button>
                    </form>
                    <button class="btn-red" onclick="document.getElementById('rejectForm').classList.toggle('hidden')">
                        ✕ Refuser
                    </button>
                </div>
                <div id="rejectForm" class="hidden" style="margin-top:1rem;padding-top:1rem;border-top:1px solid #f3f4f6;">
                    <form method="POST" action="{{ route('admin.transfer.reject', $transfer) }}">
                        @csrf @method('PATCH')
                        <label class="f-label">Motif du refus *</label>
                        <textarea name="motif_refus" class="f-field" rows="3" required
                                  placeholder="Expliquez votre refus..." style="margin-bottom:.75rem;"></textarea>
                        <button type="submit" class="btn-red">Confirmer le refus</button>
                    </form>
                </div>
            </div>
        </div>
        @endif
    </div>

</div>

@endsection

@push('scripts')
<script>
document.querySelectorAll('.hidden').forEach(el => el.style.display = 'none');
function toggleHidden(id) {
    const el = document.getElementById(id);
    el.style.display = el.style.display === 'none' ? 'block' : 'none';
}
// Remplace la classe .hidden par le toggle JS
document.getElementById('rejectForm') && (document.getElementById('rejectForm').style.display = 'none');
document.querySelector('.btn-red') && document.querySelector('.btn-red').addEventListener('click', function() {
    const f = document.getElementById('rejectForm');
    if (f) f.style.display = f.style.display === 'none' ? 'block' : 'none';
});
</script>
@endpush
