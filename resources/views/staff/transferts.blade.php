@extends('staff.master')

@section('title', 'Transferts')
@section('page-title', 'Transferts & Mobilité')
@section('page-sub', 'Demandes de consultation inter-établissements')

@push('styles')
<style>
.fg2{display:grid;grid-template-columns:1fr 1fr;gap:.875rem}.fg-group{display:flex;flex-direction:column;gap:.35rem}
.tr-tabs{display:flex;border-bottom:2px solid var(--brd);margin-bottom:1.5rem;gap:.25rem}.tr-tab{padding:.625rem 1.25rem;font-size:.82rem;font-weight:600;color:var(--mist);border:none;background:none;cursor:pointer;font-family:inherit;border-bottom:2px solid transparent;margin-bottom:-2px;transition:all .15s}.tr-tab.on{color:var(--night);border-bottom-color:var(--gold)}.tr-tab:hover:not(.on){color:#374151}
.tr-badge{display:inline-flex;align-items:center;justify-content:center;min-width:18px;height:18px;border-radius:20px;font-size:.6rem;font-weight:700;margin-left:.35rem;padding:0 .3rem}
.tr-badge.warn{background:var(--err-l);color:var(--err)}.tr-badge.info{background:var(--info-l);color:var(--info)}
.tr-section{display:none}.tr-section.on{display:block}
.search-panel{background:linear-gradient(135deg,var(--night),var(--night-3));border-radius:16px;padding:1.5rem 1.75rem;margin-bottom:1.25rem;position:relative;overflow:hidden}
.search-panel::after{content:'';position:absolute;right:-40px;top:-40px;width:160px;height:160px;border-radius:50%;background:rgba(245,158,11,.07);pointer-events:none}
.sp-title{font-family:'Syne',sans-serif;font-size:1rem;font-weight:700;color:var(--white);margin-bottom:.2rem}
.sp-sub{font-size:.75rem;color:var(--mist);margin-bottom:1.125rem}
.search-bar{display:flex;gap:.625rem;flex-wrap:wrap}
.search-bar .inp{background:rgba(255,255,255,.08);border-color:rgba(255,255,255,.12);color:var(--white)}
.search-bar .inp::placeholder{color:rgba(255,255,255,.35)}
.search-bar .inp:focus{border-color:var(--gold);background:rgba(255,255,255,.12)}
.tr-card{background:var(--white);border:1px solid var(--brd);border-radius:12px;padding:.875rem 1.125rem;margin-bottom:.625rem;display:flex;align-items:center;gap:.875rem;transition:box-shadow .15s;position:relative;overflow:hidden}
.tr-card::before{content:'';position:absolute;left:0;top:0;bottom:0;width:3px}
.tr-card.pending::before{background:var(--warn)}.tr-card.approved::before,.tr-card.completed::before{background:var(--ok)}.tr-card.rejected::before{background:var(--err)}
.tr-card:hover{box-shadow:0 2px 12px rgba(0,0,0,.07)}
.tr-av{width:40px;height:40px;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0}
.tr-av svg{width:18px;height:18px}
.tr-info{flex:1;min-width:0}
.tr-name{font-weight:600;font-size:.85rem;color:var(--night)}
.tr-meta{font-size:.72rem;color:var(--mist);margin-top:.1rem}
.st-pending{background:#fef9c3;color:#713f12}.st-approved{background:var(--ok-l);color:#065f46}.st-rejected{background:var(--err-l);color:#7f1d1d}.st-completed{background:#f1f5f9;color:#64748b}
.scope-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:.5rem}
.scope-item{display:flex;align-items:center;gap:.5rem;background:var(--bg);border:1px solid var(--brd);border-radius:8px;padding:.5rem .75rem;cursor:pointer;font-size:.78rem;transition:all .12s}
.scope-item:hover{border-color:var(--brd-d)}.scope-item input{accent-color:var(--gold)}
.ap-result{background:var(--bg);border:1px solid var(--brd);border-radius:10px;padding:.875rem 1.125rem;margin-bottom:.5rem;display:flex;align-items:center;gap:.875rem}
.ap-av{width:38px;height:38px;border-radius:9px;background:var(--night);display:flex;align-items:center;justify-content:center;font-family:'Syne',sans-serif;font-size:.75rem;font-weight:700;color:var(--gold);flex-shrink:0}
.tr-modal{display:none;position:fixed;inset:0;z-index:500;background:rgba(8,12,20,.6);backdrop-filter:blur(4px);align-items:center;justify-content:center}
.tr-modal.open{display:flex}
.tr-modal-box{background:var(--white);border-radius:16px;width:440px;max-width:95%;box-shadow:0 20px 60px rgba(0,0,0,.2);animation:modalIn .25s cubic-bezier(.4,0,.2,1) both}
@keyframes modalIn{from{transform:translateY(-16px);opacity:0}to{transform:none;opacity:1}}
.tr-modal-hd{padding:1.25rem 1.5rem;border-bottom:1px solid var(--brd);display:flex;align-items:center;justify-content:space-between}
.tr-modal-hd h3{font-family:'Syne',sans-serif;font-size:1rem;font-weight:700}
.tr-modal-body{padding:1.5rem}
.tr-modal-ft{padding:1rem 1.5rem;border-top:1px solid var(--brd);display:flex;gap:.75rem;justify-content:flex-end}
.dossier-blk{background:var(--white);border:1px solid var(--brd);border-radius:14px;overflow:hidden;margin-bottom:1rem}
.dossier-blk-hd{padding:.875rem 1.375rem;border-bottom:1px solid var(--brd);background:#fafbfd;display:flex;align-items:center;gap:.5rem}
.dossier-blk-hd h4{font-family:'Syne',sans-serif;font-size:.85rem;font-weight:700}
@media(max-width:768px){.fg2,.scope-grid{grid-template-columns:1fr}}
</style>
@endpush

@section('content')

<div class="stat-grid" style="margin-bottom:1.5rem">
    <div class="stat-card">
        <div class="stat-val" style="color:var(--warn)">{{ $stats['envoyees_pending'] }}</div>
        <div class="stat-lbl">Demandes envoyées en attente</div>
    </div>
    <div class="stat-card">
        <div class="stat-val" style="color:var(--info)">{{ $stats['recues_pending'] }}</div>
        <div class="stat-lbl">Demandes reçues à traiter</div>
    </div>
    <div class="stat-card">
        <div class="stat-val" style="color:var(--ok)">{{ $stats['approuvees'] }}</div>
        <div class="stat-lbl">Demandes approuvées</div>
    </div>
</div>

<div class="tr-tabs">
    <button class="tr-tab on" onclick="showTab('recherche',this)">🔍 Nouvelle demande</button>
    <button class="tr-tab" onclick="showTab('envoyees',this)">
        📤 Envoyées
        @if($stats['envoyees_pending']>0)<span class="tr-badge warn">{{ $stats['envoyees_pending'] }}</span>@endif
    </button>
    <button class="tr-tab" onclick="showTab('recues',this)">
        📥 Reçues
        @if($stats['recues_pending']>0)<span class="tr-badge info">{{ $stats['recues_pending'] }}</span>@endif
    </button>
    @if(isset($dossier))
    <button class="tr-tab" onclick="showTab('dossier',this)" id="tab-dossier-btn">📋 Dossier</button>
    @endif
</div>

{{-- NOUVELLE DEMANDE --}}
<div class="tr-section on" id="tab-recherche">
    <div class="search-panel">
        <div class="sp-title">Rechercher un apprenant</div>
        <div class="sp-sub">Entrez le matricule de l'apprenant dans l'établissement source</div>
        <div class="search-bar">
            <input class="inp" id="search-matricule" type="text" placeholder="Matricule (ex: APP-00123)" style="flex:1;min-width:200px">
            <select class="inp" id="search-inst" style="min-width:180px">
                <option value="">Toutes les écoles</option>
                @foreach($institutions as $inst)
                    <option value="{{ $inst->id }}">{{ $inst->name }}</option>
                @endforeach
            </select>
            <button class="btn btn-gold" id="search-btn" onclick="searchApprenant()">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:14px;height:14px"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                Rechercher
            </button>
        </div>
    </div>

    <div id="search-results" style="display:none;margin-bottom:1.25rem">
        <div class="s-card">
            <div class="s-card-hd"><h3 id="results-title">Résultats</h3></div>
            <div class="s-card-body" id="results-body" style="padding:.75rem 1.375rem"></div>
        </div>
    </div>

    <div id="request-form" style="display:none">
        <div class="s-card">
            <div class="s-card-hd"><h3>Créer une demande</h3></div>
            <form method="POST" action="{{ route('staff.transfer.request') }}">
                @csrf
                <div class="s-card-body">
                    <input type="hidden" name="apprenant_id" id="req_ap_id">
                    <div id="req_ap_info" style="background:var(--bg);border:1px solid var(--brd);border-radius:10px;padding:.875rem;margin-bottom:1rem;font-size:.82rem"></div>
                    <div class="fg-group" style="margin-bottom:.875rem">
                        <label class="lbl">Motif *</label>
                        <textarea class="inp" name="motif" rows="3" placeholder="Raison de la consultation…" required></textarea>
                    </div>
                    <div class="fg-group" style="margin-bottom:1rem">
                        <label class="lbl">Informations demandées *</label>
                        <div class="scope-grid">
                            @foreach(['identity'=>'🪪 Identité','notes'=>'📊 Notes','bulletins'=>'📄 Bulletins','discipline'=>'⚠️ Disciplinaire','finances'=>'💳 Finances','classes'=>'🏫 Historique'] as $k=>$v)
                            <label class="scope-item">
                                <input type="checkbox" name="scope[]" value="{{ $k }}" {{ $k==='identity'?'checked':'' }}>
                                <span>{{ $v }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div style="padding:.875rem 1.375rem;border-top:1px solid var(--brd);display:flex;gap:.5rem;justify-content:flex-end">
                    <button type="button" class="btn btn-ot" onclick="document.getElementById('request-form').style.display='none'">Annuler</button>
                    <button type="submit" class="btn btn-gold">Envoyer la demande</button>
                </div>
            </form>
        </div>
    </div>

    <div style="background:var(--info-l);border:1px solid rgba(59,130,246,.2);border-radius:12px;padding:1rem 1.25rem;font-size:.8rem;color:#1e3a8a;line-height:1.6">
        <strong>Comment ça marche :</strong> Recherchez l'apprenant par matricule → Sélectionnez les informations souhaitées → Envoyez la demande → L'établissement source approuve → Accès au dossier pendant <strong>72h</strong>.
    </div>
</div>

{{-- DEMANDES ENVOYÉES --}}
<div class="tr-section" id="tab-envoyees">
    @forelse($demandesEnvoyees as $d)
    <div class="tr-card {{ $d->statut }}">
        <div class="tr-av" style="background:{{ match($d->statut){'pending'=>'var(--warn-l)','approved','completed'=>'var(--ok-l)','rejected'=>'var(--err-l)',default=>'var(--bg)'} }}">
            @if($d->statut==='pending')
                <svg fill="none" stroke="var(--warn)" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            @elseif(in_array($d->statut,['approved','completed']))
                <svg fill="none" stroke="var(--ok)" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            @else
                <svg fill="none" stroke="var(--err)" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            @endif
        </div>
        <div class="tr-info">
            <div class="tr-name">{{ $d->apprenant?->prenom }} {{ $d->apprenant?->nom }}</div>
            <div class="tr-meta">
                <span style="font-family:monospace">{{ $d->apprenant?->matricule }}</span>
                · {{ $d->created_at->format('d/m/Y') }}
            </div>
            <div style="font-size:.72rem;font-weight:500;color:#374151">📤 Vers : {{ $d->institutionSource?->name ?? '—' }}</div>
        </div>
        <div style="display:flex;flex-direction:column;align-items:flex-end;gap:.5rem;flex-shrink:0">
            <span class="bdg st-{{ $d->statut }}">
                {{ ['pending'=>'En attente','approved'=>'Approuvée','rejected'=>'Refusée','completed'=>'Consultée'][$d->statut]??$d->statut }}
            </span>
            <div style="display:flex;gap:.35rem">
                @if(in_array($d->statut,['approved','completed']))
                <a href="{{ route('staff.transfer.dossier',$d) }}" class="btn btn-ok btn-sm">📋 Dossier</a>
                @endif
                @if($d->statut==='pending')
                <form method="POST" action="{{ route('staff.transfer.destroy',$d) }}">
                    @csrf @method('DELETE')
                    <button class="btn btn-err btn-sm" onclick="return confirm('Annuler ?')">Annuler</button>
                </form>
                @endif
            </div>
            @if($d->statut==='rejected' && $d->motif_refus)
            <div style="font-size:.68rem;color:var(--err);max-width:180px;text-align:right">{{ $d->motif_refus }}</div>
            @endif
        </div>
    </div>
    @empty
    <div class="s-empty">
        <div class="s-empty-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg></div>
        <h4>Aucune demande envoyée</h4>
        <p>Utilisez l'onglet "Nouvelle demande" pour commencer.</p>
    </div>
    @endforelse
    @if($demandesEnvoyees->hasPages())
    <div style="margin-top:1rem">{{ $demandesEnvoyees->links() }}</div>
    @endif
</div>

{{-- DEMANDES REÇUES --}}
<div class="tr-section" id="tab-recues">
    @forelse($demandesRecues as $d)
    <div class="tr-card {{ $d->statut }}">
        <div class="tr-av" style="background:{{ $d->statut==='pending'?'var(--warn-l)':'var(--bg)' }}">
            <svg fill="none" stroke="{{ $d->statut==='pending'?'var(--warn)':'var(--mist)' }}" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
        </div>
        <div class="tr-info">
            <div class="tr-name">{{ $d->apprenant?->prenom }} {{ $d->apprenant?->nom }}</div>
            <div class="tr-meta">
                <span style="font-family:monospace">{{ $d->apprenant?->matricule }}</span>
                · {{ $d->created_at->format('d/m/Y') }}
                · {{ Str::limit($d->motif,60) }}
            </div>
            <div style="font-size:.72rem;font-weight:500;color:#374151">📥 Depuis : {{ $d->institutionDest?->name ?? '—' }}</div>
            @if($d->scope)
            <div style="display:flex;gap:.35rem;flex-wrap:wrap;margin-top:.35rem">
                @foreach($d->scope as $sc)<span class="bdg bdg-b" style="font-size:.6rem">{{ $sc }}</span>@endforeach
            </div>
            @endif
        </div>
        <div style="display:flex;flex-direction:column;align-items:flex-end;gap:.5rem;flex-shrink:0">
            <span class="bdg st-{{ $d->statut }}">
                {{ ['pending'=>'À traiter','approved'=>'Approuvée','rejected'=>'Refusée','completed'=>'Terminée'][$d->statut]??$d->statut }}
            </span>
            @if($d->statut==='pending')
            <div style="display:flex;gap:.35rem">
                <form method="POST" action="{{ route('staff.transfer.approve',$d) }}">
                    @csrf @method('PATCH')
                    <button class="btn btn-ok btn-sm">✓ Approuver</button>
                </form>
                <button class="btn btn-err btn-sm" onclick="openReject({{ $d->id }})">✕ Refuser</button>
            </div>
            @endif
        </div>
    </div>
    @empty
    <div class="s-empty">
        <div class="s-empty-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg></div>
        <h4>Aucune demande reçue</h4>
        <p>Les demandes des autres établissements apparaîtront ici.</p>
    </div>
    @endforelse
    @if($demandesRecues->hasPages())
    <div style="margin-top:1rem">{{ $demandesRecues->links() }}</div>
    @endif
</div>

{{-- DOSSIER --}}
@if(isset($dossier))
<div class="tr-section" id="tab-dossier">
    @if(isset($apprenant))
    <div style="background:var(--night);border-radius:16px;padding:1.5rem;margin-bottom:1.25rem;display:flex;align-items:center;gap:1rem">
        <div style="width:52px;height:52px;border-radius:12px;background:var(--gold-dim);border:2px solid var(--gold);display:flex;align-items:center;justify-content:center;font-family:'Syne',sans-serif;font-size:1.1rem;font-weight:800;color:var(--gold);flex-shrink:0">
            {{ mb_substr($apprenant->prenom,0,1) }}{{ mb_substr($apprenant->nom,0,1) }}
        </div>
        <div style="flex:1">
            <div style="font-family:'Syne',sans-serif;font-size:1rem;font-weight:700;color:var(--white)">{{ $apprenant->prenom }} {{ $apprenant->nom }}</div>
            <div style="font-size:.75rem;color:var(--mist)">{{ $apprenant->matricule }} · {{ $apprenant->institution?->name }} · {{ $apprenant->classe?->name ?? '—' }}</div>
        </div>
    </div>
    @endif

    @if(isset($dossier['apprenant']))
    <div class="dossier-blk">
        <div class="dossier-blk-hd"><span>🪪</span><h4>Identité</h4></div>
        <div style="padding:1.125rem;display:grid;grid-template-columns:repeat(auto-fill,minmax(170px,1fr));gap:.625rem">
            @foreach([
                ['Prénom',$dossier['apprenant']->prenom],
                ['Nom',$dossier['apprenant']->nom],
                ['Matricule',$dossier['apprenant']->matricule],
                ['Naissance',$dossier['apprenant']->date_naissance?\Carbon\Carbon::parse($dossier['apprenant']->date_naissance)->format('d/m/Y'):'—'],
                ['Sexe',$dossier['apprenant']->sexe??'—'],
                ['Classe',$dossier['apprenant']->classe?->name??'—'],
            ] as [$l,$v])
            <div style="background:var(--bg);border-radius:8px;padding:.625rem .875rem">
                <div style="font-size:.63rem;color:var(--mist);font-weight:600;text-transform:uppercase;letter-spacing:.06em">{{ $l }}</div>
                <div style="font-size:.85rem;font-weight:600;color:var(--night);margin-top:.15rem">{{ $v }}</div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    @if(isset($dossier['moyennes']) && $dossier['moyennes']->count())
    <div class="dossier-blk">
        <div class="dossier-blk-hd">
            <span>📊</span><h4>Notes</h4>
            @if(isset($dossier['moyenne_generale']))
            <span style="margin-left:auto;font-family:'Syne',sans-serif;font-weight:800">Moy. gén. : {{ $dossier['moyenne_generale'] }}</span>
            @endif
        </div>
        <table class="s-tbl">
            <thead><tr><th>Matière</th><th>Coeff.</th><th>Moyenne</th><th>Notes</th><th>Min</th><th>Max</th></tr></thead>
            <tbody>
                @foreach($dossier['moyennes'] as $m)
                <tr>
                    <td style="font-weight:600;font-size:.82rem">{{ $m['matiere'] }}</td>
                    <td>{{ $m['coefficient'] }}</td>
                    <td><span style="font-family:'Syne',sans-serif;font-weight:700">{{ $m['moyenne'] ?? '—' }}</span></td>
                    <td>{{ $m['nb_notes'] }}</td>
                    <td style="color:var(--err)">{{ $m['min']??'—' }}</td>
                    <td style="color:var(--ok)">{{ $m['max']??'—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if(isset($dossier['incidents']) && $dossier['incidents']->count())
    <div class="dossier-blk">
        <div class="dossier-blk-hd"><span>⚠️</span><h4>Disciplinaire</h4><span class="bdg bdg-a" style="margin-left:.5rem">{{ $dossier['incidents']->count() }}</span></div>
        <table class="s-tbl">
            <thead><tr><th>Date</th><th>Type</th><th>Gravité</th><th>Sanction</th><th>Statut</th></tr></thead>
            <tbody>
                @foreach($dossier['incidents'] as $inc)
                <tr>
                    <td style="font-size:.78rem">{{ $inc->date_incident?->format('d/m/Y') }}</td>
                    <td><span class="bdg bdg-n">{{ $inc->type }}</span></td>
                    <td><span class="bdg {{ ['1'=>'bdg-a','2'=>'bdg-a','3'=>'bdg-r'][$inc->gravite]??'bdg-n' }}">{{ $inc->gravite }}</span></td>
                    <td style="font-size:.78rem">{{ $inc->sanction??'—' }}</td>
                    <td><span class="bdg {{ $inc->statut==='clos'?'bdg-g':'bdg-a' }}">{{ $inc->statut }}</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if(isset($dossier['finances_totaux']))
    <div class="dossier-blk">
        <div class="dossier-blk-hd"><span>💳</span><h4>Finances</h4></div>
        <div style="padding:1.125rem;display:grid;grid-template-columns:repeat(3,1fr);gap:.75rem">
            @foreach([['Total dû',$dossier['finances_totaux']['total_du'],'var(--night)'],['Payé',$dossier['finances_totaux']['total_paye'],'var(--ok)'],['Reste',$dossier['finances_totaux']['total_reste'],'var(--err)']] as [$l,$v,$c])
            <div style="text-align:center;padding:.75rem;background:var(--bg);border-radius:10px">
                <div style="font-family:'Syne',sans-serif;font-size:1rem;font-weight:800;color:{{ $c }}">{{ number_format($v,0,' ',' ') }}</div>
                <div style="font-size:.7rem;color:var(--mist)">{{ $l }}</div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endif

{{-- MODAL REFUS --}}
<div class="tr-modal" id="modal-reject">
    <div class="tr-modal-box">
        <div class="tr-modal-hd">
            <h3>Motif de refus</h3>
            <button class="btn btn-ot btn-sm" onclick="closeReject()">✕</button>
        </div>
        <form method="POST" id="reject-form">
            @csrf @method('PATCH')
            <div class="tr-modal-body">
                <div class="fg-group">
                    <label class="lbl">Motif *</label>
                    <textarea class="inp" name="motif_refus" rows="4" placeholder="Expliquez la raison du refus…" required></textarea>
                </div>
            </div>
            <div class="tr-modal-ft">
                <button type="button" class="btn btn-ot" onclick="closeReject()">Annuler</button>
                <button type="submit" class="btn btn-err">Refuser</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
function showTab(id,btn){document.querySelectorAll('.tr-section').forEach(s=>s.classList.remove('on'));document.querySelectorAll('.tr-tab').forEach(b=>b.classList.remove('on'));document.getElementById('tab-'+id).classList.add('on');btn.classList.add('on');}
@if(isset($dossier))document.addEventListener('DOMContentLoaded',function(){const b=document.getElementById('tab-dossier-btn');if(b)showTab('dossier',b);});@endif

async function searchApprenant(){
    const mat=document.getElementById('search-matricule').value.trim();
    const inst=document.getElementById('search-inst').value;
    if(!mat){alert('Entrez un matricule.');return;}
    const p=new URLSearchParams({matricule:mat});if(inst)p.append('institution_source',inst);
    const btn=document.getElementById('search-btn');btn.textContent='Recherche…';btn.disabled=true;
    try{
        const res=await fetch('{{ route("staff.transfer.search") }}?'+p.toString(),{headers:{'X-Requested-With':'XMLHttpRequest'}});
        const data=await res.json();
        const rd=document.getElementById('search-results');rd.style.display='block';
        document.getElementById('results-title').textContent=data.found+' résultat(s)';
        const body=document.getElementById('results-body');
        if(data.found===0){body.innerHTML='<div style="padding:1.5rem;text-align:center;color:var(--mist);font-size:.82rem">Aucun apprenant trouvé.</div>';document.getElementById('request-form').style.display='none';return;}
        body.innerHTML=data.apprenants.map(a=>`<div class="ap-result"><div class="ap-av">${(a.prenom||'?')[0]}${(a.nom||'')[0]}</div><div style="flex:1"><div style="font-weight:600;font-size:.85rem">${a.prenom} ${a.nom}</div><div style="font-size:.72rem;color:var(--mist)">${a.matricule} · ${a.institution||'—'} · ${a.classe||'—'}</div></div><button class="btn btn-gold btn-sm" onclick='selectAp(${JSON.stringify(a)})'>Sélectionner</button></div>`).join('');
    }catch(e){alert('Erreur. Réessayez.');}
    finally{btn.innerHTML='<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:14px;height:14px"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg> Rechercher';btn.disabled=false;}
}

function selectAp(a){
    document.getElementById('req_ap_id').value=a.id;
    document.getElementById('req_ap_info').innerHTML=`<strong>${a.prenom} ${a.nom}</strong> — ${a.institution} — Classe : ${a.classe||'—'}<br><small style="font-family:monospace;color:var(--mist)">${a.matricule}</small>`;
    document.getElementById('request-form').style.display='block';
    document.getElementById('request-form').scrollIntoView({behavior:'smooth',block:'start'});
}

function openReject(id){document.getElementById('reject-form').action=`/staff/transferts/${id}/reject`;document.getElementById('modal-reject').classList.add('open');document.body.style.overflow='hidden';}
function closeReject(){document.getElementById('modal-reject').classList.remove('open');document.body.style.overflow='';}
document.getElementById('modal-reject').addEventListener('click',function(e){if(e.target===this)closeReject();});
</script>
@endpush
