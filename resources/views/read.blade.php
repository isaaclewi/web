{{--
    Vue : library/read.blade.php
    Lecteur universel — supporte PDF (iframe), DOCX/autres (téléchargement forcé),
    avec miniature de couverture et métadonnées.
    Cette vue n'étend pas de layout spécifique pour laisser le plein écran au viewer.
--}}
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $book->title }} — Bibliothèque</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *,*::before,*::after { box-sizing:border-box; margin:0; padding:0; font-family:'Inter',sans-serif; }
        :root {
            --ink:#1f2937; --muted:#6b7280; --border:#e5e7eb;
            --primary:#6366f1; --bg:#f8f9fa; --header-h:52px;
        }
        body { background:var(--bg); display:flex; flex-direction:column; height:100vh; overflow:hidden; }

        /* ─── Topbar ─── */
        .topbar {
            height:var(--header-h); background:white; border-bottom:1px solid var(--border);
            display:flex; align-items:center; padding:0 1rem; gap:.75rem; flex-shrink:0; z-index:10;
        }
        .back-btn {
            display:flex; align-items:center; gap:.35rem; padding:.35rem .75rem;
            border:1px solid var(--border); border-radius:.375rem; font-size:.8rem;
            color:var(--muted); text-decoration:none; transition:all .15s; flex-shrink:0;
        }
        .back-btn:hover { background:#f3f4f6; color:var(--ink); }
        .book-info { flex:1; min-width:0; display:flex; align-items:center; gap:.625rem; }
        .book-thumb {
            width:32px; height:40px; border-radius:4px; object-fit:cover;
            border:1px solid var(--border); background:#f3f4f6;
            display:flex; align-items:center; justify-content:center; font-size:1.25rem;
            flex-shrink:0; overflow:hidden;
        }
        .book-thumb img { width:100%; height:100%; object-fit:cover; }
        .book-title-bar { font-size:.8rem; font-weight:600; color:var(--ink); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
        .book-meta-bar  { font-size:.68rem; color:var(--muted); }
        .topbar-actions { display:flex; align-items:center; gap:.5rem; flex-shrink:0; }
        .action-btn {
            display:flex; align-items:center; gap:.3rem; padding:.35rem .75rem;
            border-radius:.375rem; font-size:.75rem; font-weight:600;
            border:none; cursor:pointer; text-decoration:none; transition:all .15s;
        }
        .action-dl { background:#dcfce7; color:#14532d; }
        .action-dl:hover { background:#bbf7d0; }
        .action-dl.disabled { background:#f3f4f6; color:#9ca3af; cursor:not-allowed; pointer-events:none; }
        .file-badge { background:var(--primary); color:white; font-size:.65rem; font-weight:700; padding:.2rem .5rem; border-radius:4px; }

        /* ─── Viewer area ─── */
        .viewer { flex:1; display:flex; overflow:hidden; }

        .iframe-viewer { flex:1; border:none; }

        /* Non-PDF fallback */
        .fallback-viewer {
            flex:1; display:flex; align-items:center; justify-content:center;
            flex-direction:column; gap:1rem; padding:2rem; text-align:center;
        }
        .fallback-cover { width:120px; height:155px; border-radius:.625rem; object-fit:cover; border:1px solid var(--border); background:#f3f4f6; display:flex; align-items:center; justify-content:center; font-size:4rem; overflow:hidden; }
        .fallback-cover img { width:100%; height:100%; object-fit:cover; }
        .fallback-title { font-size:1.1rem; font-weight:700; color:var(--ink); }
        .fallback-desc  { font-size:.875rem; color:var(--muted); max-width:420px; line-height:1.6; }
        .dl-big {
            display:flex; align-items:center; gap:.5rem; padding:.7rem 1.5rem;
            background:var(--primary); color:white; border-radius:.5rem;
            font-weight:700; font-size:.875rem; text-decoration:none; transition:all .2s;
        }
        .dl-big:hover { background:#4f46e5; transform:translateY(-1px); box-shadow:0 4px 16px rgba(99,102,241,.3); }
        .info-chips { display:flex; gap:.5rem; flex-wrap:wrap; justify-content:center; }
        .chip { background:white; border:1px solid var(--border); border-radius:99px; font-size:.72rem; color:var(--muted); padding:.2rem .65rem; }

        @media(max-width:640px) {
            .book-meta-bar, .file-badge { display:none; }
            .book-title-bar { font-size:.75rem; }
        }
    </style>
</head>
<body>

    {{-- Topbar --}}
    <div class="topbar">
        <a href="javascript:history.back()" class="back-btn">
            <svg style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Retour
        </a>

        <div class="book-info">
            <div class="book-thumb">
                @if($book->cover_path)
                    <img src="{{ $book->cover_url }}" alt="">
                @else
                    {{ $book->file_icon }}
                @endif
            </div>
            <div style="min-width:0;">
                <div class="book-title-bar">{{ $book->title }}</div>
                <div class="book-meta-bar">
                    @if($book->author) {{ $book->author }} · @endif
                    {{ $book->file_size_human }}
                    @if($book->category) · {{ $book->category }} @endif
                </div>
            </div>
            <span class="file-badge">{{ strtoupper($book->file_type) }}</span>
        </div>

        <div class="topbar-actions">
            @if($book->allow_download)
                <a href="{{ route('library.download', $book) }}" class="action-btn action-dl">
                    <svg style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Télécharger
                </a>
            @else
                <span class="action-btn action-dl disabled">🔒 Lecture seule</span>
            @endif
        </div>
    </div>

    {{-- Viewer --}}
    <div class="viewer">
        @if($book->file_type === 'pdf')
            {{-- Lecteur PDF natif --}}
            <iframe
                class="iframe-viewer"
                src="{{ $book->file_url }}#toolbar=1&navpanes=0&scrollbar=1&view=FitH"
                title="{{ $book->title }}"
                allowfullscreen>
            </iframe>
        @elseif($book->file_type === 'epub')
            {{-- Pour les EPUB : on utilise Google Docs Viewer --}}
            <iframe
                class="iframe-viewer"
                src="https://docs.google.com/viewer?url={{ urlencode($book->file_url) }}&embedded=true"
                title="{{ $book->title }}">
            </iframe>
        @elseif(in_array($book->file_type, ['docx','pptx','xlsx']))
            {{-- Microsoft Office Online Viewer --}}
            <iframe
                class="iframe-viewer"
                src="https://view.officeapps.live.com/op/embed.aspx?src={{ urlencode($book->file_url) }}"
                title="{{ $book->title }}">
            </iframe>
        @else
            {{-- Fallback générique --}}
            <div class="fallback-viewer">
                <div class="fallback-cover">
                    @if($book->cover_path)
                        <img src="{{ $book->cover_url }}" alt="{{ $book->title }}">
                    @else
                        {{ $book->file_icon }}
                    @endif
                </div>
                <div class="fallback-title">{{ $book->title }}</div>
                @if($book->description)
                    <p class="fallback-desc">{{ $book->description }}</p>
                @endif
                <div class="info-chips">
                    @if($book->author) <span class="chip">✍️ {{ $book->author }}</span> @endif
                    @if($book->category) <span class="chip">🏷 {{ $book->category }}</span> @endif
                    <span class="chip">📦 {{ $book->file_size_human }}</span>
                    <span class="chip">{{ strtoupper($book->file_type) }}</span>
                </div>
                <p class="fallback-desc" style="font-size:.8rem;">
                    Ce format ne peut pas être prévisualisé directement.
                    @if($book->allow_download)
                        Téléchargez le fichier pour le consulter.
                    @else
                        La prévisualisation n'est pas disponible pour ce type de fichier.
                    @endif
                </p>
                @if($book->allow_download)
                    <a href="{{ route('library.download', $book) }}" class="dl-big">
                        <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Télécharger ({{ $book->file_size_human }})
                    </a>
                @endif
            </div>
        @endif
    </div>

</body>
</html>
