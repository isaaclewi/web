@extends('staff.master')
@section('title', 'Notes & Bulletins')
@section('page-title', 'Notes & Bulletins')
@section('page-sub', 'Saisie · Évaluations · Bulletins')

@push('styles')
    <style>
        .fg2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: .875rem
        }

        .fg-group {
            display: flex;
            flex-direction: column;
            gap: .35rem
        }

        .n-tabs {
            display: flex;
            gap: .25rem;
            border-bottom: 2px solid var(--brd);
            margin-bottom: 1.5rem
        }

        .n-tab {
            padding: .6rem 1.25rem;
            font-size: .82rem;
            font-weight: 600;
            color: var(--mist);
            border: none;
            background: none;
            cursor: pointer;
            font-family: inherit;
            border-bottom: 2px solid transparent;
            margin-bottom: -2px;
            transition: all .15s
        }

        .n-tab.on {
            color: var(--night);
            border-bottom-color: var(--gold)
        }

        .n-tab:hover:not(.on) {
            color: #374151
        }

        .n-section {
            display: none
        }

        .n-section.on {
            display: block
        }

        /* Searchable select */
        .ss-wrap {
            position: relative
        }

        .ss-input {
            width: 100%;
            border: 1.5px solid var(--brd);
            border-radius: 9px;
            padding: .55rem .875rem;
            font-size: .84rem;
            font-family: inherit;
            color: var(--night);
            background: var(--white);
            outline: none;
            cursor: pointer;
            transition: border-color .18s
        }

        .ss-input:focus,
        .ss-input.open {
            border-color: var(--gold);
            box-shadow: 0 0 0 3px rgba(245, 158, 11, .12)
        }

        .ss-dropdown {
            display: none;
            position: absolute;
            top: calc(100% + 4px);
            left: 0;
            right: 0;
            background: #fff;
            border: 1.5px solid var(--gold);
            border-radius: 9px;
            z-index: 300;
            box-shadow: 0 8px 24px rgba(0, 0, 0, .12);
            max-height: 240px;
            overflow: hidden;
            flex-direction: column
        }

        .ss-dropdown.open {
            display: flex
        }

        .ss-search {
            border: none;
            border-bottom: 1px solid var(--brd);
            padding: .6rem .875rem;
            font-size: .83rem;
            font-family: inherit;
            outline: none;
            width: 100%;
            background: #fafafa
        }

        .ss-list {
            overflow-y: auto;
            flex: 1
        }

        .ss-opt {
            padding: .55rem .875rem;
            font-size: .82rem;
            color: #374151;
            cursor: pointer;
            transition: background .1s
        }

        .ss-opt:hover {
            background: var(--bg)
        }

        .ss-opt.sel {
            background: rgba(245, 158, 11, .1);
            color: #92400e;
            font-weight: 600
        }

        .ss-opt.disabled {
            color: var(--mist);
            font-style: italic;
            pointer-events: none;
            font-size: .75rem;
            padding: .3rem .875rem;
            background: #fafafa
        }

        .ss-empty {
            padding: 1rem;
            font-size: .8rem;
            color: var(--mist);
            text-align: center
        }

        /* Grade */
        .grade-input {
            border: 1.5px solid var(--brd);
            border-radius: 7px;
            padding: .4rem .6rem;
            font-size: .85rem;
            font-weight: 700;
            text-align: center;
            width: 80px;
            outline: none;
            transition: all .15s
        }

        .grade-input:focus {
            border-color: var(--gold);
            box-shadow: 0 0 0 3px rgba(245, 158, 11, .12)
        }

        .grade-input.valid {
            border-color: var(--ok);
            background: var(--ok-l)
        }

        .grade-input.over {
            border-color: var(--err);
            background: var(--err-l)
        }

        .grade-row {
            display: grid;
            grid-template-columns: 2fr 90px 70px;
            gap: .5rem;
            align-items: center;
            padding: .55rem .875rem;
            border-bottom: 1px solid var(--brd);
            transition: background .1s
        }

        .grade-row:last-child {
            border-bottom: none
        }

        .grade-row:hover {
            background: var(--bg)
        }

        .grade-row-hd {
            background: #fafbfd;
            padding: .4rem .875rem;
            display: grid;
            grid-template-columns: 2fr 90px 70px;
            gap: .5rem;
            border-bottom: 2px solid var(--brd)
        }

        .grade-row-hd span {
            font-size: .68rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .07em;
            color: var(--mist)
        }

        .score-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-family: 'Syne', sans-serif;
            font-weight: 800;
            font-size: .78rem;
            padding: .25rem .65rem;
            border-radius: 7px;
            min-width: 52px
        }

        .score-hi {
            background: var(--ok-l);
            color: #065f46
        }

        .score-mid {
            background: var(--warn-l);
            color: #92400e
        }

        .score-lo {
            background: var(--err-l);
            color: #7f1d1d
        }

        .rang-badge {
            width: 26px;
            height: 26px;
            border-radius: 50%;
            background: var(--bg);
            border: 1.5px solid var(--brd);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-family: 'Syne', sans-serif;
            font-size: .67rem;
            font-weight: 800;
            color: var(--night)
        }

        .rang-badge.r1 {
            background: var(--warn-l);
            border-color: var(--gold);
            color: #92400e
        }

        .rang-badge.r2 {
            background: #f1f5f9;
            border-color: #94a3b8;
            color: #475569
        }

        .rang-badge.r3 {
            background: var(--err-l);
            border-color: #fca5a5;
            color: #7f1d1d
        }

        .classe-card {
            background: var(--bg);
            border: 1px solid var(--brd);
            border-radius: 10px;
            padding: .875rem;
            transition: all .15s
        }

        .classe-card:hover {
            border-color: var(--brd-d);
            box-shadow: 0 2px 10px rgba(0, 0, 0, .06)
        }

        .n-modal {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 500;
            background: rgba(8, 12, 20, .6);
            backdrop-filter: blur(4px);
            align-items: flex-start;
            justify-content: center;
            padding-top: 3%
        }

        .n-modal.open {
            display: flex
        }

        .n-modal-box {
            background: var(--white);
            border-radius: 16px;
            width: 520px;
            max-width: 95%;
            max-height: 92vh;
            overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0, 0, 0, .2);
            animation: mIn .22s cubic-bezier(.4, 0, .2, 1) both
        }

        @keyframes mIn {
            from {
                transform: translateY(-14px);
                opacity: 0
            }

            to {
                transform: none;
                opacity: 1
            }
        }

        .n-modal-hd {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--brd);
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            background: var(--white);
            z-index: 1
        }

        .n-modal-hd h3 {
            font-family: 'Syne', sans-serif;
            font-size: 1rem;
            font-weight: 700
        }

        .n-modal-body {
            padding: 1.5rem
        }

        .n-modal-ft {
            padding: 1rem 1.5rem;
            border-top: 1px solid var(--brd);
            display: flex;
            gap: .75rem;
            justify-content: flex-end;
            position: sticky;
            bottom: 0;
            background: var(--white)
        }

        .prog-wrap {
            height: 4px;
            background: var(--bg);
            border-radius: 2px;
            overflow: hidden;
            margin-top: .3rem
        }

        .prog-bar {
            height: 100%;
            border-radius: 2px
        }

        @media(max-width:1024px) {
            .saisie-grid {
                grid-template-columns: 1fr !important
            }
        }

        @media(max-width:768px) {
            .fg2 {
                grid-template-columns: 1fr
            }

            .grade-row,
            .grade-row-hd {
                grid-template-columns: 1fr 80px 60px
            }
        }
    </style>
@endpush

@section('content')

    {{-- STATS --}}
    <div class="stat-grid" style="margin-bottom:1.5rem">
        @foreach ($periodes as $p)
            @php $s=$statsParPeriode[$p['key']]??['total'=>0,'publie'=>0,'calcule'=>0]; @endphp
            <div class="stat-card">
                <div class="stat-val">{{ $s['total'] }}</div>
                <div class="stat-lbl">{{ $p['label'] }}</div>
                <div style="margin-top:.5rem;font-size:.65rem;display:flex;flex-direction:column;gap:.15rem">
                    <span style="color:var(--ok);font-weight:600">✓ {{ $s['publie'] }} publiés</span>
                    <span style="color:var(--info);font-weight:600">⚙ {{ $s['calcule'] }} calculés</span>
                </div>
            </div>
        @endforeach
    </div>

    {{-- TABS --}}
    <div class="n-tabs">
        <button class="n-tab on" onclick="showTab('saisie',this)">📝 Saisie de notes</button>
        <button class="n-tab" onclick="showTab('evaluations',this)">📋 Évaluations</button>
        <button class="n-tab" onclick="showTab('bulletins',this)">📄 Bulletins</button>
    </div>

    {{-- ══ SAISIE ══ --}}
    <div class="n-section on" id="tab-saisie">
        <div class="saisie-grid" style="display:grid;grid-template-columns:320px 1fr;gap:1.25rem;align-items:start">

            {{-- PANNEAU GAUCHE --}}
            <div>
                {{-- Sélecteur évaluation --}}
                <div class="s-card" style="margin-bottom:1rem">
                    <div class="s-card-hd">
                        <h3>Sélectionner</h3>
                    </div>
                    <div class="s-card-body" style="display:flex;flex-direction:column;gap:.875rem">

                        {{-- Filtre niveau --}}
                        <div class="fg-group">
                            <label class="lbl">Niveau</label>
                            <div class="ss-wrap">
                                <input class="ss-input" id="fil-niveau-display" placeholder="Tous les niveaux" readonly
                                    onclick="toggleSS('fil-niveau')">
                                <div class="ss-dropdown" id="fil-niveau-dd">
                                    <input class="ss-search" placeholder="Rechercher un niveau…"
                                        oninput="filterSS('fil-niveau',this.value)">
                                    <div class="ss-list" id="fil-niveau-list">
                                        <div class="ss-opt sel" data-val=""
                                            onclick="selectSS('fil-niveau','','Tous les niveaux');filterClasses()">Tous les
                                            niveaux</div>
                                        @foreach ($classes->pluck('niveau')->filter()->unique('id') as $niv)
                                            <div class="ss-opt" data-val="{{ $niv->id }}"
                                                onclick="selectSS('fil-niveau','{{ $niv->id }}','{{ $niv->name }}');filterClasses()">
                                                {{ $niv->name }}</div>
                                        @endforeach
                                    </div>
                                </div>
                                <input type="hidden" id="fil-niveau-val" value="">
                            </div>
                        </div>

                        {{-- Filtre classe --}}
                        <div class="fg-group">
                            <label class="lbl">Classe <span style="color:var(--err)">*</span></label>
                            <div class="ss-wrap">
                                <input class="ss-input" id="fil-classe-display" placeholder="Sélectionner une classe…"
                                    readonly onclick="toggleSS('fil-classe')">
                                <div class="ss-dropdown" id="fil-classe-dd">
                                    <input class="ss-search" placeholder="Rechercher une classe…"
                                        oninput="filterSS('fil-classe',this.value)">
                                    <div class="ss-list" id="fil-classe-list">
                                        @foreach ($classes as $c)
                                            <div class="ss-opt" data-val="{{ $c->id }}"
                                                data-niveau="{{ $c->niveau_id }}"
                                                onclick="selectSS('fil-classe','{{ $c->id }}','{{ $c->name }}');filterEvals()">
                                                {{ $c->name }}@if ($c->niveau)
                                                    <span style="color:var(--mist);font-size:.7rem"> —
                                                        {{ $c->niveau->name }}</span>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <input type="hidden" id="fil-classe-val" value="">
                            </div>
                        </div>

                        {{-- Évaluation --}}
                        <div class="fg-group">
                            <label class="lbl">Évaluation <span style="color:var(--err)">*</span></label>
                            <div class="ss-wrap">
                                <input class="ss-input" id="fil-eval-display" placeholder="Choisir une évaluation…" readonly
                                    onclick="toggleSS('fil-eval')">
                                <div class="ss-dropdown" id="fil-eval-dd">
                                    <input class="ss-search" placeholder="Rechercher…"
                                        oninput="filterSS('fil-eval',this.value)">
                                    <div class="ss-list" id="fil-eval-list">
                                        <div class="ss-opt disabled">— Sélectionnez d'abord une classe —</div>
                                        @foreach ($evaluations as $e)
                                            <div class="ss-opt" data-val="{{ $e->id }}"
                                                data-classe="{{ $e->subject->class_id }}"
                                                onclick="goToEval({{ $e->id }})">
                                                {{ $e->title }} <span style="color:var(--mist);font-size:.72rem"> —
                                                    {{ $e->subject->name ?? '' }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- NOUVELLE ÉVALUATION --}}
                <div class="s-card" id="card-new-eval" style="display:none">
                    <div class="s-card-hd">
                        <h3>Nouvelle évaluation</h3>
                        <button class="btn btn-ot btn-sm"
                            onclick="document.getElementById('card-new-eval').style.display='none'">✕</button>
                    </div>
                    <div class="s-card-body">
                        {{-- ✅ FIX : action pointe vers staff.evaluations.store --}}
                        <form method="POST" action="{{ route('staff.evaluations.store') }}" id="form-new-eval">
                            @csrf
                            <div
                                style="background:var(--bg);border-radius:8px;padding:.625rem .75rem;margin-bottom:.875rem;font-size:.75rem;color:var(--mist);border-left:3px solid var(--gold)">
                                Sélectionnez le <strong style="color:var(--night)">niveau</strong> → <strong
                                    style="color:var(--night)">classe</strong> → <strong
                                    style="color:var(--night)">matière</strong>
                            </div>

                            {{-- Niveau --}}
                            <div class="fg-group" style="margin-bottom:.625rem">
                                <label class="lbl">Niveau</label>
                                <div class="ss-wrap">
                                    <input class="ss-input" id="ev-niveau-display" placeholder="Tous les niveaux"
                                        readonly onclick="toggleSS('ev-niveau')">
                                    <div class="ss-dropdown" id="ev-niveau-dd">
                                        <input class="ss-search" placeholder="Rechercher…"
                                            oninput="filterSS('ev-niveau',this.value)">
                                        <div class="ss-list" id="ev-niveau-list">
                                            <div class="ss-opt sel" data-val=""
                                                onclick="selectSS('ev-niveau','','Tous');filterEvClasses()">Tous les
                                                niveaux</div>
                                            @foreach ($classes->pluck('niveau')->filter()->unique('id') as $niv)
                                                <div class="ss-opt" data-val="{{ $niv->id }}"
                                                    onclick="selectSS('ev-niveau','{{ $niv->id }}','{{ $niv->name }}');filterEvClasses()">
                                                    {{ $niv->name }}</div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <input type="hidden" id="ev-niveau-val" value="">
                                </div>
                            </div>

                            {{-- Classe --}}
                            <div class="fg-group" style="margin-bottom:.625rem">
                                <label class="lbl">Classe <span style="color:var(--err)">*</span></label>
                                <div class="ss-wrap">
                                    <input class="ss-input" id="ev-classe-display" placeholder="Sélectionner une classe…"
                                        readonly onclick="toggleSS('ev-classe')">
                                    <div class="ss-dropdown" id="ev-classe-dd">
                                        <input class="ss-search" placeholder="Rechercher…"
                                            oninput="filterSS('ev-classe',this.value)">
                                        <div class="ss-list" id="ev-classe-list">
                                            @foreach ($classes as $c)
                                                <div class="ss-opt" data-val="{{ $c->id }}"
                                                    data-niveau="{{ $c->niveau_id }}"
                                                    onclick="selectSS('ev-classe','{{ $c->id }}','{{ $c->name }}');filterEvSubjects()">
                                                    {{ $c->name }}@if ($c->niveau)
                                                        <span style="color:var(--mist);font-size:.7rem"> —
                                                            {{ $c->niveau->name }}</span>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <input type="hidden" id="ev-classe-val" value="">
                                </div>
                            </div>

                            {{-- Matière filtrée --}}
                            <div class="fg-group" style="margin-bottom:.625rem">
                                <label class="lbl">Matière <span style="color:var(--err)">*</span></label>
                                <div class="ss-wrap">
                                    <input class="ss-input" id="ev-subject-display"
                                        placeholder="Sélectionner une matière…" readonly onclick="toggleSS('ev-subject')">
                                    <div class="ss-dropdown" id="ev-subject-dd">
                                        <input class="ss-search" placeholder="Rechercher…"
                                            oninput="filterSS('ev-subject',this.value)">
                                        <div class="ss-list" id="ev-subject-list">
                                            <div class="ss-opt disabled">— Sélectionnez d'abord une classe —</div>
                                            @foreach ($subjects as $s)
                                                <div class="ss-opt" data-val="{{ $s->id }}"
                                                    data-classe="{{ $s->class_id }}"
                                                    onclick="selectSS('ev-subject','{{ $s->id }}','{{ $s->name }}')">
                                                    {{ $s->name }}@if ($s->coefficient)
                                                        <span
                                                            style="color:var(--mist);font-size:.7rem">(coef.{{ $s->coefficient }})</span>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <input type="hidden" id="ev-subject-val" name="subject_id" value="" required>
                                </div>
                            </div>

                            {{-- Titre + Type --}}
                            <div class="fg2" style="margin-bottom:.625rem">
                                <div class="fg-group">
                                    <label class="lbl">Titre <span style="color:var(--err)">*</span></label>
                                    <input class="inp" name="title" id="ev-title" placeholder="Ex. Contrôle Ch.1"
                                        required>
                                </div>
                                <div class="fg-group">
                                    <label class="lbl">Type <span style="color:var(--err)">*</span></label>
                                    <select class="inp" name="type" required>
                                        <option value="controle">Contrôle</option>
                                        <option value="examen">Examen</option>
                                        <option value="tp">TP</option>
                                        <option value="projet">Projet</option>
                                    </select>
                                </div>
                            </div>

                            {{-- Note max + Période --}}
                            <div class="fg2" style="margin-bottom:.625rem">
                                <div class="fg-group">
                                    <label class="lbl">Note max <span style="color:var(--err)">*</span></label>
                                    <input class="inp" name="max_score" type="number" value="20" min="1"
                                        max="1000" required>
                                </div>
                                <div class="fg-group">
                                    <label class="lbl">Période</label>
                                    <select class="inp" name="periode">
                                        <option value="">—</option>
                                        @foreach ($periodes as $p)
                                            <option value="{{ $p['key'] }}">{{ $p['label'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="fg-group" style="margin-bottom:.875rem">
                                <label class="lbl">Date <span style="color:var(--err)">*</span></label>
                                <input class="inp" type="date" name="date" value="{{ date('Y-m-d') }}"
                                    required>
                            </div>
                            <button class="btn btn-gold" style="width:100%" type="submit">✓ Créer l'évaluation</button>
                        </form>
                    </div>
                </div>

                <button class="btn btn-gold" style="width:100%;margin-top:.5rem" onclick="toggleNewEval()">+ Nouvelle
                    évaluation</button>
            </div>

            {{-- PANNEAU DROIT --}}
            <div class="s-card">
                <div class="s-card-hd">
                    <h3>Saisie des notes</h3>
                    @if (isset($selectedEval) && $selectedEval)
                        <span style="font-size:.72rem;color:var(--mist)">{{ $selectedEval->title }} ·
                            /{{ $selectedEval->max_score }}</span>
                    @endif
                </div>

                @if (isset($selectedEval) && $selectedEval && isset($evalApprenants) && $evalApprenants->count())
                    <div
                        style="padding:.5rem 1.375rem;background:var(--bg);border-bottom:1px solid var(--brd);font-size:.78rem;color:#374151;display:flex;gap:1rem;flex-wrap:wrap;align-items:center">
                        <strong>{{ $selectedEval->title }}</strong>
                        <span>{{ $selectedEval->subject->name ?? '—' }}</span>
                        <span class="bdg bdg-b">{{ $selectedEval->type_evaluation ?? $selectedEval->type }}</span>
                        <span>{{ $selectedEval->max_score }} pts</span>
                        @if ($selectedEval->periode)
                            <span style="color:var(--mist)">{{ $selectedEval->periode }}</span>
                        @endif
                        <span style="margin-left:auto;color:var(--ok);font-weight:600">{{ $evalApprenants->count() }}
                            apprenants</span>
                    </div>

                    <div style="padding:.5rem 1.375rem;border-bottom:1px solid var(--brd)">
                        <input class="inp" id="search-ap" placeholder="🔍 Rechercher un apprenant…"
                            oninput="filterAp(this.value)" style="max-width:280px">
                    </div>

                    <form method="POST" action="{{ route('staff.grades.store') }}">
                        @csrf
                        <input type="hidden" name="evaluation_id" value="{{ $selectedEval->id }}">
                        <div class="grade-row-hd">
                            <span>Apprenant</span>
                            <span style="text-align:center">/ {{ $selectedEval->max_score }}</span>
                            <span style="text-align:center">/ 20</span>
                        </div>
                        @foreach ($evalApprenants as $ap)
                            @php $eg=$selectedEval->grades->firstWhere('apprenant_id',$ap->id); @endphp
                            <div class="grade-row ap-row" data-name="{{ strtolower($ap->prenom . ' ' . $ap->nom) }}">
                                <div>
                                    <div style="font-weight:600;font-size:.82rem;color:var(--night)">{{ $ap->prenom }}
                                        {{ $ap->nom }}</div>
                                    <div style="font-size:.67rem;color:var(--mist);font-family:monospace">
                                        {{ $ap->matricule }}</div>
                                </div>
                                <div style="text-align:center">
                                    <input type="number" name="grades[{{ $ap->id }}]" class="grade-input"
                                        min="0" max="{{ $selectedEval->max_score }}" step="0.25"
                                        value="{{ $eg ? $eg->score : '' }}" placeholder="—"
                                        data-max="{{ $selectedEval->max_score }}" oninput="liveGrade(this)">
                                </div>
                                <div class="preview-20"
                                    style="font-size:.8rem;font-weight:700;color:var(--mist);text-align:center">
                                    @if ($eg)
                                        {{ round(($eg->score / $selectedEval->max_score) * 20, 1) }}
                                    @else
                                        —
                                    @endif
                                </div>
                            </div>
                        @endforeach
                        <div
                            style="padding:.875rem 1.375rem;border-top:1px solid var(--brd);display:flex;gap:.5rem;justify-content:flex-end;flex-wrap:wrap">
                            <button type="button" class="btn btn-ot btn-sm" onclick="fillAll()">Remplir tout</button>
                            <button type="button" class="btn btn-ot btn-sm" onclick="clearAll()">Effacer</button>
                            <button type="submit" class="btn btn-gold">💾 Enregistrer
                                ({{ $evalApprenants->count() }})</button>
                        </div>
                    </form>
                @elseif(isset($selectedEval) && $selectedEval)
                    <div class="s-empty">
                        <div class="s-empty-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg></div>
                        <h4>Aucun apprenant</h4>
                        <p>La classe ne contient pas encore d'apprenants.</p>
                    </div>
                @else
                    <div class="s-empty">
                        <div class="s-empty-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg></div>
                        <h4>Sélectionnez une évaluation</h4>
                        <p>Filtrez par niveau et classe, puis choisissez une évaluation.<br>Ou créez une nouvelle
                            évaluation.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ══ ÉVALUATIONS ══ --}}
    <div class="n-section" id="tab-evaluations">
        <div style="display:flex;gap:.625rem;flex-wrap:wrap;margin-bottom:1rem;align-items:center">
            <div class="ss-wrap" style="max-width:220px">
                <input class="ss-input" id="fev-classe-display" placeholder="Toutes les classes" readonly
                    onclick="toggleSS('fev-classe')" style="height:36px;font-size:.8rem">
                <div class="ss-dropdown" id="fev-classe-dd">
                    <input class="ss-search" placeholder="Rechercher…" oninput="filterSS('fev-classe',this.value)">
                    <div class="ss-list" id="fev-classe-list">
                        <div class="ss-opt sel" data-val=""
                            onclick="selectSS('fev-classe','','Toutes les classes');filterEvTable()">Toutes les classes
                        </div>
                        @foreach ($classes as $c)
                            <div class="ss-opt" data-val="{{ $c->id }}"
                                onclick="selectSS('fev-classe','{{ $c->id }}','{{ $c->name }}');filterEvTable()">
                                {{ $c->name }}</div>
                        @endforeach
                    </div>
                </div>
                <input type="hidden" id="fev-classe-val" value="">
            </div>
            <select class="inp" style="max-width:160px;height:36px;font-size:.8rem"
                onchange="filterEvTableType(this.value)">
                <option value="">Tous types</option>
                <option value="controle">Contrôle</option>
                <option value="examen">Examen</option>
                <option value="tp">TP</option>
                <option value="composition">Composition</option>
            </select>
            <span id="ev-count" style="font-size:.78rem;color:var(--mist);margin-left:auto"></span>
        </div>
        <div class="s-card">
            <div class="s-card-hd">
                <h3>Évaluations de l'établissement</h3>
            </div>
            <table class="s-tbl" id="eval-table">
                <thead>
                    <tr>
                        <th>Évaluation</th>
                        <th>Matière</th>
                        <th>Classe</th>
                        <th>Niveau</th>
                        <th>Type</th>
                        <th>Période</th>
                        <th>Barème</th>
                        <th>Complétion</th>
                        <th style="width:100px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($evaluations as $eval)
                        @php
                            $nbG = $eval->grades->count();
                            $nbA = $eval->subject->classe?->apprenants?->count() ?? 0;
                            $pct = $nbA > 0 ? round(($nbG / $nbA) * 100) : 0;
                        @endphp
                        <tr data-classe="{{ $eval->subject->class_id }}" data-type="{{ $eval->type }}">
                            <td>
                                <div style="font-weight:600;font-size:.83rem">{{ $eval->title }}</div>
                                <div style="font-size:.68rem;color:var(--mist)">{{ $eval->date }}</div>
                            </td>
                            <td style="font-size:.8rem">{{ $eval->subject->name ?? '—' }}</td>
                            <td style="font-size:.8rem">{{ $eval->subject->classe->name ?? '—' }}</td>
                            <td style="font-size:.78rem;color:var(--mist)">
                                {{ $eval->subject->classe->niveau->name ?? '—' }}</td>
                            <td><span class="bdg bdg-b">{{ $eval->type }}</span></td>
                            <td style="font-size:.78rem;color:var(--mist)">{{ $eval->periode ?? '—' }}</td>
                            <td style="font-weight:700">{{ $eval->max_score }}</td>
                            <td>
                                <span style="font-size:.78rem">{{ $nbG }}/{{ $nbA }}</span>
                                <div class="prog-wrap" style="width:70px">
                                    <div class="prog-bar"
                                        style="width:{{ $pct }}%;background:{{ $pct >= 80 ? 'var(--ok)' : ($pct >= 50 ? 'var(--warn)' : 'var(--err)') }}">
                                    </div>
                                </div>
                            </td>
                            <td style="display:flex;gap:.3rem">
                                <a href="?evaluation_id={{ $eval->id }}&tab=saisie" class="btn btn-ot btn-sm">📝
                                    Saisir</a>
                                <form method="POST" action="{{ route('staff.evaluations.destroy', $eval) }}">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-err btn-sm" onclick="return confirm('Supprimer ?')">✕</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9">
                                <div class="s-empty">
                                    <h4>Aucune évaluation</h4>
                                    <p>Créez-en une dans l'onglet Saisie.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ══ BULLETINS ══ --}}
    <div class="n-section" id="tab-bulletins">
        <div class="s-card" style="margin-bottom:1.25rem">
            <div class="s-card-hd">
                <h3>Actions par classe</h3>
                <form method="POST" action="{{ route('staff.bulletins.calculer.tous') }}"
                    style="display:flex;gap:.5rem">
                    @csrf
                    <select name="periode" class="inp" style="max-width:180px">
                        @foreach ($periodes as $p)
                            <option value="{{ $p['key'] }}">{{ $p['label'] }}</option>
                        @endforeach
                    </select>
                    <button class="btn btn-dk btn-sm">⚙ Calculer toutes</button>
                </form>
            </div>
            <div class="s-card-body">
                <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:.875rem">
                    @foreach ($classes as $classe)
                        <div class="classe-card">
                            <div
                                style="font-family:'Syne',sans-serif;font-size:.83rem;font-weight:700;color:var(--night);margin-bottom:.2rem">
                                {{ $classe->name }}</div>
                            @if ($classe->niveau)
                                <div style="font-size:.7rem;color:var(--mist)">{{ $classe->niveau->name }}</div>
                            @endif
                            <div style="font-size:.7rem;color:var(--mist);margin-bottom:.75rem">
                                {{ $classe->apprenants_count ?? 0 }} apprenant(s)</div>
                            <div style="display:flex;gap:.35rem;flex-wrap:wrap">
                                <button class="btn btn-ot btn-sm"
                                    onclick="openM('calc','{{ $classe->id }}','{{ $classe->name }}')">⚙
                                    Calculer</button>
                                <button class="btn btn-ok btn-sm"
                                    onclick="openM('pub','{{ $classe->id }}','{{ $classe->name }}')">↑ Publier</button>
                                <button class="btn btn-err btn-sm"
                                    onclick="openM('dep','{{ $classe->id }}','{{ $classe->name }}')">↓
                                    Dépublier</button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div style="display:flex;gap:.625rem;flex-wrap:wrap;margin-bottom:1rem">
            <form method="GET" style="display:contents">
                <input type="hidden" name="tab" value="bulletins">
                <select class="inp" style="max-width:180px" name="periode" onchange="this.form.submit()">
                    @foreach ($periodes as $p)
                        <option value="{{ $p['key'] }}" @selected(request('periode') == $p['key'])>{{ $p['label'] }}</option>
                    @endforeach
                </select>
                <select class="inp" style="max-width:200px" name="classe_id" onchange="this.form.submit()">
                    <option value="">Toutes les classes</option>
                    @foreach ($classes as $c)
                        <option value="{{ $c->id }}" @selected(request('classe_id') == $c->id)>{{ $c->name }}@if ($c->niveau)
                                — {{ $c->niveau->name }}
                            @endif
                        </option>
                    @endforeach
                </select>
                <select class="inp" style="max-width:140px" name="publie" onchange="this.form.submit()">
                    <option value="">Tous</option>
                    <option value="1" @selected(request('publie') === '1')>Publiés</option>
                    <option value="0" @selected(request('publie') === '0')>Non publiés</option>
                </select>
            </form>
        </div>
        <div class="s-card">
            @if (isset($bulletins) && $bulletins->count())
                <table class="s-tbl">
                    <thead>
                        <tr>
                            <th>Apprenant</th>
                            <th>Classe</th>
                            <th>Moyenne</th>
                            <th>Rang</th>
                            <th>Mention</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($bulletins as $b)
                            @php
                                $pct = $config->note_max > 0 ? round(($b->moyenne_generale / $config->note_max) * 100) : 0;
                                $sc = $pct >= 70 ? 'score-hi' : ($pct >= 50 ? 'score-mid' : 'score-lo');
                                $rc = $b->rang == 1 ? 'r1' : ($b->rang == 2 ? 'r2' : ($b->rang == 3 ? 'r3' : ''));
                            @endphp
                            <tr>
                                <td>
                                    <div style="font-weight:600;font-size:.83rem">{{ $b->apprenant?->prenom }}
                                        {{ $b->apprenant?->nom }}</div>
                                    <div style="font-size:.68rem;color:var(--mist);font-family:monospace">
                                        {{ $b->apprenant?->matricule }}</div>
                                </td>
                                <td style="font-size:.8rem">{{ $b->classe?->name ?? '—' }}</td>
                                <td><span
                                        class="score-badge {{ $sc }}">{{ number_format($b->moyenne_generale, $config->decimales) }}/{{ $config->note_max }}</span>
                                </td>
                                <td><span class="rang-badge {{ $rc }}">{{ $b->rang ?? '—' }}</span></td>
                                <td style="font-size:.78rem">{{ $b->mention ?? '—' }}</td>
                                <td>
                                    @if ($b->publie)
                                        <span class="bdg bdg-g">Publié</span>
                                    @elseif($b->calcule_at)
                                    <span class="bdg bdg-b">Calculé</span>@else<span
                                            class="bdg bdg-n">Brouillon</span>
                                    @endif
                                </td>
                                <td style="display:flex;gap:.3rem;flex-wrap:wrap">
                                    <a href="{{ route('staff.bulletins.show', $b) }}" class="btn btn-ot btn-sm">Voir</a>
                                    @if (!$b->publie && $b->calcule_at)
                                        <form method="POST" action="{{ route('staff.bulletins.publier', $b) }}">
                                            @csrf<button class="btn btn-ok btn-sm">↑</button></form>
                                    @elseif($b->publie)
                                        <form method="POST" action="{{ route('staff.bulletins.depublier', $b) }}">
                                            @csrf<button class="btn btn-err btn-sm">↓</button></form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div style="padding:.875rem 1.375rem;border-top:1px solid var(--brd)">
                    {{ $bulletins->appends(request()->query())->links() }}</div>
            @else<div class="s-empty">
                    <div class="s-empty-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg></div>
                    <h4>Aucun bulletin</h4>
                    <p>Calculez les bulletins d'une classe.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- MODALS --}}
    @foreach ([['calc', route('staff.bulletins.calculer.classe'), 'calc_id', 'btn-dk', 'Calculer'], ['pub', route('staff.bulletins.publier.classe'), 'pub_id', 'btn-ok', 'Publier'], ['dep', route('staff.bulletins.depublier.classe'), 'dep_id', 'btn-err', 'Dépublier']] as [$key, $action, $hid, $bcls, $blbl])
        <div class="n-modal" id="modal-{{ $key }}">
            <div class="n-modal-box" style="max-width:420px">
                <div class="n-modal-hd">
                    <h3 id="modal-{{ $key }}-title">{{ $blbl }}</h3><button class="btn btn-ot btn-sm"
                        onclick="closeM('{{ $key }}')">✕</button>
                </div>
                <form method="POST" action="{{ $action }}">@csrf<input type="hidden" name="classe_id"
                        id="{{ $hid }}">
                    <div class="n-modal-body">
                        <div class="fg-group"><label class="lbl">Période *</label><select class="inp"
                                name="periode" required>
                                @foreach ($periodes as $p)
                                    <option value="{{ $p['key'] }}">{{ $p['label'] }}</option>
                                @endforeach
                            </select></div>
                    </div>
                    <div class="n-modal-ft"><button type="button" class="btn btn-ot"
                            onclick="closeM('{{ $key }}')">Annuler</button><button type="submit"
                            class="btn {{ $bcls }}">{{ $blbl }}</button></div>
                </form>
            </div>
        </div>
    @endforeach

@endsection

@push('scripts')
    @php
        // Subjects (gestion pagination au cas où)
        $subjectsData =
            $subjects instanceof \Illuminate\Pagination\LengthAwarePaginator
                ? $subjects->getCollection()->map(function ($s) {
                    return [
                        'id' => $s->id,
                        'name' => $s->name,
                        'class_id' => $s->class_id,
                        'coef' => $s->coefficient,
                        'teacher' => $s->teacher ? $s->teacher->prenom . ' ' . $s->teacher->nom : null,
                    ];
                })
                : $subjects->map(function ($s) {
                    return [
                        'id' => $s->id,
                        'name' => $s->name,
                        'class_id' => $s->class_id,
                        'coef' => $s->coefficient,
                        'teacher' => $s->teacher ? $s->teacher->prenom . ' ' . $s->teacher->nom : null,
                    ];
                });

        // Classes (idem)
        $classesData =
            $classes instanceof \Illuminate\Pagination\LengthAwarePaginator
                ? $classes->getCollection()->map(function ($c) {
                    return [
                        'id' => $c->id,
                        'name' => $c->name,
                        'niveau_id' => $c->niveau_id,
                        'niveau' => $c->niveau?->name,
                    ];
                })
                : $classes->map(function ($c) {
                    return [
                        'id' => $c->id,
                        'name' => $c->name,
                        'niveau_id' => $c->niveau_id,
                        'niveau' => $c->niveau?->name,
                    ];
                });

        // Evaluations (tu avais déjà bon 👍)
        $evalsData =
            $evaluations instanceof \Illuminate\Pagination\LengthAwarePaginator
                ? $evaluations->getCollection()->map(function ($e) {
                    return [
                        'id' => $e->id,
                        'title' => $e->title,
                        'class_id' => $e->subject?->class_id,
                        'subject' => $e->subject?->name,
                    ];
                })
                : $evaluations->map(function ($e) {
                    return [
                        'id' => $e->id,
                        'title' => $e->title,
                        'class_id' => $e->subject?->class_id,
                        'subject' => $e->subject?->name,
                    ];
                });
    @endphp

    <script>
        const SUBJECTS_DATA = @json($subjectsData);
        const CLASSES_DATA = @json($classesData);
        const EVALS_DATA = @json($evalsData);

        function showTab(id, btn) {
            document.querySelectorAll('.n-section').forEach(s => s.classList.remove('on'));
            document.querySelectorAll('.n-tab').forEach(b => b.classList.remove('on'));
            document.getElementById('tab-' + id).classList.add('on');
            btn.classList.add('on');
        }
        const urlTab = new URLSearchParams(location.search).get('tab');
        if (urlTab) {
            const m = {
                saisie: 0,
                evaluations: 1,
                bulletins: 2
            };
            const i = m[urlTab];
            if (i !== undefined) {
                const btns = document.querySelectorAll('.n-tab');
                showTab(urlTab, btns[i]);
            }
        }

        function openM(key, id, name) {
            document.getElementById(key + '_id').value = id;
            document.getElementById('modal-' + key + '-title').textContent = {
                calc: 'Calculer',
                pub: 'Publier',
                dep: 'Dépublier'
            } [key] + ' — ' + name;
            document.getElementById('modal-' + key).classList.add('open');
            document.body.style.overflow = 'hidden';
        }

        function closeM(key) {
            document.getElementById('modal-' + key).classList.remove('open');
            document.body.style.overflow = '';
        }
        document.querySelectorAll('.n-modal').forEach(m => m.addEventListener('click', e => {
            if (e.target === m) {
                m.classList.remove('open');
                document.body.style.overflow = '';
            }
        }));

        // ── Searchable Select ──
        function toggleSS(id) {
            const dd = document.getElementById(id + '-dd');
            const isOpen = dd.classList.contains('open');
            document.querySelectorAll('.ss-dropdown.open').forEach(d => d.classList.remove('open'));
            if (!isOpen) {
                dd.classList.add('open');
                const si = dd.querySelector('.ss-search');
                if (si) {
                    si.value = '';
                    si.focus();
                    filterSS(id, '');
                }
            }
        }
        document.addEventListener('click', e => {
            if (!e.target.closest('.ss-wrap')) document.querySelectorAll('.ss-dropdown.open').forEach(d => d
                .classList.remove('open'));
        });

        function filterSS(id, q) {
            const list = document.getElementById(id + '-list');
            const opts = list.querySelectorAll('.ss-opt:not(.disabled)');
            const lq = q.toLowerCase();
            let count = 0;
            opts.forEach(o => {
                const show = !lq || o.textContent.toLowerCase().includes(lq);
                o.style.display = show ? '' : 'none';
                if (show) count++;
            });
            let empty = list.querySelector('.ss-empty');
            if (count === 0 && !empty) {
                empty = document.createElement('div');
                empty.className = 'ss-empty';
                empty.textContent = 'Aucun résultat';
                list.appendChild(empty);
            } else if (count > 0 && empty) empty.remove();
        }

        function selectSS(id, val, label) {
            document.getElementById(id + '-display').value = label;
            const h = document.getElementById(id + '-val');
            if (h) h.value = val;
            document.getElementById(id + '-dd').classList.remove('open');
            document.querySelectorAll('#' + id + '-list .ss-opt').forEach(o => o.classList.toggle('sel', o.dataset.val ===
                val));
        }

        // Filtre classes du sélecteur éval par niveau
        function filterClasses() {
            const niv = document.getElementById('fil-niveau-val').value;
            const list = document.getElementById('fil-classe-list');
            list.innerHTML = '';
            const filtered = CLASSES_DATA.filter(c => !niv || String(c.niveau_id) === String(niv));
            if (filtered.length === 0) {
                list.innerHTML = '<div class="ss-opt disabled">Aucune classe</div>';
                return;
            }
            filtered.forEach(c => {
                const d = document.createElement('div');
                d.className = 'ss-opt';
                d.dataset.val = c.id;
                d.dataset.niveau = c.niveau_id || '';
                d.innerHTML = c.name + (c.niveau ?
                    ` <span style="color:var(--mist);font-size:.7rem"> — ${c.niveau}</span>` : '');
                d.onclick = () => {
                    selectSS('fil-classe', String(c.id), c.name);
                    filterEvals();
                };
                list.appendChild(d);
            });
            selectSS('fil-classe', '', 'Sélectionner une classe…');
            document.getElementById('fil-classe-display').value = '';
            filterEvals();
        }

        function filterEvals() {
            const cId = document.getElementById('fil-classe-val').value;
            const list = document.getElementById('fil-eval-list');
            list.innerHTML = '';
            if (!cId) {
                list.innerHTML = '<div class="ss-opt disabled">— Sélectionnez d\'abord une classe —</div>';
                return;
            }
            const filtered = EVALS_DATA.filter(e => String(e.class_id) === String(cId));
            if (filtered.length === 0) {
                list.innerHTML = '<div class="ss-opt disabled">Aucune évaluation</div>';
                return;
            }
            filtered.forEach(e => {
                const d = document.createElement('div');
                d.className = 'ss-opt';
                d.dataset.val = e.id;
                d.innerHTML = e.title + (e.subject ?
                    ` <span style="color:var(--mist);font-size:.72rem"> — ${e.subject}</span>` : '');
                d.onclick = () => goToEval(e.id);
                list.appendChild(d);
            });
        }

        function goToEval(id) {
            const u = new URL(location.href);
            u.searchParams.set('evaluation_id', id);
            u.searchParams.set('tab', 'saisie');
            location.href = u.toString();
        }

        // Filtre classes + matières du formulaire création
        function filterEvClasses() {
            const niv = document.getElementById('ev-niveau-val').value;
            const list = document.getElementById('ev-classe-list');
            list.innerHTML = '';
            const filtered = CLASSES_DATA.filter(c => !niv || String(c.niveau_id) === String(niv));
            if (filtered.length === 0) {
                list.innerHTML = '<div class="ss-opt disabled">Aucune classe</div>';
                return;
            }
            filtered.forEach(c => {
                const d = document.createElement('div');
                d.className = 'ss-opt';
                d.dataset.val = c.id;
                d.dataset.niveau = c.niveau_id || '';
                d.innerHTML = c.name + (c.niveau ?
                    ` <span style="color:var(--mist);font-size:.7rem"> — ${c.niveau}</span>` : '');
                d.onclick = () => {
                    selectSS('ev-classe', String(c.id), c.name);
                    filterEvSubjects();
                };
                list.appendChild(d);
            });
            selectSS('ev-classe', '', 'Sélectionner une classe…');
            document.getElementById('ev-classe-display').value = '';
            filterEvSubjects();
        }

        function filterEvSubjects() {
            const cId = document.getElementById('ev-classe-val').value;
            const list = document.getElementById('ev-subject-list');
            list.innerHTML = '';
            if (!cId) {
                list.innerHTML = '<div class="ss-opt disabled">— Sélectionnez d\'abord une classe —</div>';
                return;
            }
            const filtered = SUBJECTS_DATA.filter(s => String(s.class_id) === String(cId));
            if (filtered.length === 0) {
                list.innerHTML = '<div class="ss-opt disabled">Aucune matière</div>';
                return;
            }
            filtered.forEach(s => {
                const d = document.createElement('div');
                d.className = 'ss-opt';
                d.dataset.val = s.id;
                let label = s.name;
                if (s.coef) label += ` <span style="color:var(--mist);font-size:.7rem">(coef.${s.coef})</span>`;
                d.innerHTML = label;
                d.onclick = () => {
                    selectSS('ev-subject', String(s.id), s.name);
                };
                list.appendChild(d);
            });
            selectSS('ev-subject', '', 'Sélectionner une matière…');
            document.getElementById('ev-subject-display').value = '';
            document.getElementById('ev-subject-val').value = '';
        }

        // Validation
        document.getElementById('form-new-eval')?.addEventListener('submit', function(e) {
            const s = document.getElementById('ev-subject-val').value;
            const t = document.getElementById('ev-title').value.trim();
            if (!s) {
                e.preventDefault();
                alert('Veuillez sélectionner une matière.');
                return;
            }
            if (!t) {
                e.preventDefault();
                alert('Veuillez saisir un titre.');
                return;
            }
        });

        function toggleNewEval() {
            const c = document.getElementById('card-new-eval');
            c.style.display = c.style.display === 'none' ? 'block' : 'none';
        }

        // Notes
        function liveGrade(inp) {
            const max = parseFloat(inp.dataset.max) || 20;
            const val = parseFloat(inp.value);
            inp.classList.remove('valid', 'over');
            const p = inp.closest('.grade-row')?.querySelector('.preview-20');
            if (!isNaN(val)) {
                inp.classList.add(val > max ? 'over' : 'valid');
                if (p) p.textContent = val > max ? '⚠' : (val / max * 20).toFixed(1);
            } else if (p) p.textContent = '—';
        }

        function fillAll() {
            const maxEl = document.querySelector('.grade-input');
            if (!maxEl) return;
            const max = parseFloat(maxEl.dataset.max) || 20;
            const v = prompt('Note pour tous (sur ' + max + ') :');
            if (v === null) return;
            document.querySelectorAll('.grade-input').forEach(inp => {
                inp.value = Math.min(Math.max(0, parseFloat(v) || 0), max);
                liveGrade(inp);
            });
        }

        function clearAll() {
            document.querySelectorAll('.grade-input').forEach(inp => {
                inp.value = '';
                liveGrade(inp);
            });
        }

        function filterAp(q) {
            const lq = q.toLowerCase();
            document.querySelectorAll('.ap-row').forEach(r => {
                r.style.display = (!lq || r.dataset.name.includes(lq)) ? '' : 'none';
            });
        }

        // Table évaluations
        function filterEvTable() {
            const cId = document.getElementById('fev-classe-val').value;
            let count = 0;
            document.querySelectorAll('#eval-table tbody tr[data-classe]').forEach(r => {
                const show = !cId || r.dataset.classe == cId;
                r.style.display = show ? '' : 'none';
                if (show) count++;
            });
            const el = document.getElementById('ev-count');
            if (el) el.textContent = count + ' évaluation(s)';
        }

        function filterEvTableType(t) {
            document.querySelectorAll('#eval-table tbody tr[data-type]').forEach(r => {
                r.style.display = (!t || r.dataset.type == t) ? '' : 'none';
            });
        }
        setTimeout(() => {
            const rows = document.querySelectorAll('#eval-table tbody tr[data-classe]');
            const el = document.getElementById('ev-count');
            if (el) el.textContent = rows.length + ' évaluation(s)';
        }, 100);

        // Pré-remplir si eval_id dans URL
        const evalIdInUrl = new URLSearchParams(location.search).get('evaluation_id');
        if (evalIdInUrl) {
            const ev = EVALS_DATA.find(e => String(e.id) === evalIdInUrl);
            if (ev) {
                document.getElementById('fil-eval-display').value = ev.title;
                const cl = CLASSES_DATA.find(c => String(c.id) === String(ev.class_id));
                if (cl) selectSS('fil-classe', String(cl.id), cl.name);
            }
        }
    </script>
@endpush
