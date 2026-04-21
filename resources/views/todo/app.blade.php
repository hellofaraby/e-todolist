<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Tugas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --page-bg: linear-gradient(135deg, #f4f7fb 0%, #eef3ff 100%);
            --surface-bg: rgba(255, 255, 255, 0.92);
            --surface-border: rgba(148, 163, 184, 0.18);
            --text-main: #132238;
            --text-muted: #5c6b80;
            --shadow-soft: 0 18px 45px rgba(15, 23, 42, 0.08);
        }

        body {
            min-height: 100vh;
            background: var(--page-bg);
            color: var(--text-main);
            font-size: 0.94rem;
            transition: background .25s ease, color .25s ease;
        }

        body.dark-mode {
            --page-bg: linear-gradient(135deg, #0f172a 0%, #111827 100%);
            --surface-bg: rgba(15, 23, 42, 0.92);
            --surface-border: rgba(148, 163, 184, 0.15);
            --text-main: #e5eefc;
            --text-muted: #9db0cb;
            --shadow-soft: 0 18px 45px rgba(2, 6, 23, 0.45);
        }

        .page-shell {
            max-width: 100%;
        }

        .glass-card {
            background: var(--surface-bg);
            border: 1px solid var(--surface-border);
            box-shadow: var(--shadow-soft);
            backdrop-filter: blur(10px);
        }

        .muted-text {
            color: var(--text-muted);
        }

        .hero-title {
            font-size: clamp(1.6rem, 2.6vw, 2.35rem);
            font-weight: 800;
            letter-spacing: -0.04em;
        }

        .hero-subtitle {
            max-width: 880px;
            font-size: 0.92rem;
        }

        .stat-card {
            min-height: 112px;
        }

        .stat-icon {
            width: 40px;
            height: 40px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 14px;
            font-size: 1.05rem;
        }

        .task-item {
            border: 1px solid var(--surface-border);
            transition: transform .18s ease, border-color .18s ease, background-color .18s ease;
        }

        .task-item:hover {
            transform: translateY(-2px);
            border-color: rgba(13, 110, 253, 0.28);
        }

        .task-item.overdue {
            border-left: 5px solid #dc3545;
        }

        .task-item.soon {
            border-left: 5px solid #ffc107;
        }

        .task-item.upcoming {
            border-left: 5px solid #0dcaf0;
        }

        .badge-soft {
            border: 1px solid rgba(255, 255, 255, 0.18);
            font-weight: 600;
        }

        .deadline-text {
            font-size: 0.84rem;
        }

        .tag-pill {
            border-radius: 999px;
            padding: 0.35rem 0.6rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            font-size: 0.82rem;
        }

        .filter-chip {
            border-radius: 999px;
        }

        .form-control,
        .form-select {
            border-radius: 1rem;
            padding-top: .62rem;
            padding-bottom: .62rem;
            font-size: 0.92rem;
        }

        .btn {
            border-radius: 1rem;
            font-size: 0.9rem;
        }

        .empty-state {
            border: 1px dashed var(--surface-border);
        }

        .form-text,
        .small,
        .badge {
            font-size: 0.8rem;
        }

        h2.h4 {
            font-size: 1.1rem;
        }

        h3.h5 {
            font-size: 1rem;
        }
    </style>
</head>

<body>
    <div class="container-fluid py-3 py-lg-4 px-3 px-lg-4 page-shell">
        <div class="glass-card rounded-4 p-3 p-lg-4 mb-3">
            <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 align-items-lg-center">
                <div>
                    <span class="badge rounded-pill text-bg-primary mb-2">Sistem Manajemen Tugas</span>
                    <h1 class="hero-title mb-2">Kelola tugas, label, tenggat waktu, dan prioritas secara terstruktur.</h1>
                    <p class="hero-subtitle muted-text mb-0">
                        Sistem akan memproses input seperti
                        <code>Bayar listrik besok jam 9 #rumah !high</code>
                        dan secara otomatis menyimpan tugas, label, prioritas, serta tenggat waktu yang relevan.
                    </p>
                </div>
                <div class="d-flex flex-column gap-2">
                    <button id="theme-toggle" class="btn btn-outline-secondary px-3">
                        <i class="bi bi-moon-stars me-2"></i>
                        Mode Gelap
                    </button>
                    <a href="{{ route('todo', ['view' => $isTrashView ? null : 'trash']) }}"
                        class="btn {{ $isTrashView ? 'btn-primary' : 'btn-outline-dark' }} px-3">
                        <i class="bi {{ $isTrashView ? 'bi-arrow-counterclockwise' : 'bi-trash3' }} me-2"></i>
                        {{ $isTrashView ? 'Kembali ke Daftar Aktif' : 'Buka Arsip (' . $trashCount . ')' }}
                    </a>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-6 col-xl-3">
                <div class="glass-card rounded-4 p-3 stat-card">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="muted-text mb-1">Total Tugas</p>
                            <h2 class="mb-0 fw-bold fs-4">{{ $stats['total'] }}</h2>
                        </div>
                        <span class="stat-icon bg-primary-subtle text-primary"><i class="bi bi-list-check"></i></span>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="glass-card rounded-4 p-3 stat-card">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="muted-text mb-1">Tugas Selesai</p>
                            <h2 class="mb-0 fw-bold fs-4">{{ $stats['done'] }}</h2>
                        </div>
                        <span class="stat-icon bg-success-subtle text-success"><i class="bi bi-check2-circle"></i></span>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="glass-card rounded-4 p-3 stat-card">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="muted-text mb-1">Tugas Berjalan</p>
                            <h2 class="mb-0 fw-bold fs-4">{{ $stats['pending'] }}</h2>
                        </div>
                        <span class="stat-icon bg-warning-subtle text-warning"><i class="bi bi-hourglass-split"></i></span>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="glass-card rounded-4 p-3 stat-card">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="muted-text mb-1">Persentase Progres</p>
                            <h2 class="mb-2 fw-bold fs-4">{{ $stats['progress'] }}%</h2>
                            <div class="progress" role="progressbar" aria-valuemin="0" aria-valuemax="100"
                                aria-valuenow="{{ $stats['progress'] }}">
                                <div class="progress-bar" style="width: {{ $stats['progress'] }}%"></div>
                            </div>
                        </div>
                        <span class="stat-icon bg-info-subtle text-info"><i class="bi bi-graph-up-arrow"></i></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-xl-5">
                <div class="glass-card rounded-4 p-3 p-lg-4 mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <h2 class="h4 fw-bold mb-1">Tambah Tugas</h2>
                            <p class="muted-text mb-0">Input akan diproses untuk mengenali label, prioritas, dan tenggat waktu.</p>
                        </div>
                        <span class="badge rounded-pill text-bg-dark">Input Otomatis</span>
                    </div>

                    @if (session('success'))
                        <div class="alert alert-success rounded-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger rounded-4">
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                    <form action="{{ route('todo.post') }}" method="POST" class="d-grid gap-2">
                        @csrf
                        <div>
                            <label for="task" class="form-label fw-semibold">Deskripsi Tugas</label>
                            <textarea class="form-control" id="task" name="task" rows="3"
                                placeholder="Contoh: Rapat klien besok jam 09.00 #operasional !high" required>{{ old('task') }}</textarea>
                        </div>

                        <div class="row g-2">
                            <div class="col-md-6">
                                <label for="priority" class="form-label fw-semibold">Prioritas Manual</label>
                                <select class="form-select" id="priority" name="priority">
                                    <option value="medium" @selected(old('priority', 'medium') === 'medium')>Menengah</option>
                                    <option value="high" @selected(old('priority') === 'high')>High</option>
                                    <option value="low" @selected(old('priority') === 'low')>Low</option>
                                </select>
                                <div class="form-text">Digunakan apabila input tidak mengandung <code>!high</code>, <code>!medium</code>, atau <code>!low</code>.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="due_date" class="form-label fw-semibold">Tenggat Waktu Manual</label>
                                <input type="datetime-local" class="form-control" id="due_date" name="due_date"
                                    value="{{ old('due_date') }}">
                                <div class="form-text">Digunakan apabila input tidak mengandung informasi waktu.</div>
                            </div>
                        </div>

                        <button class="btn btn-primary">
                            <i class="bi bi-plus-circle me-2"></i>
                            Simpan Tugas
                        </button>
                    </form>

                    <div class="mt-3">
                        <p class="fw-semibold mb-2">Contoh format input:</p>
                        <div class="d-grid gap-1">
                            @foreach ($parserExamples as $example)
                                <span class="small muted-text"><i class="bi bi-lightning-charge me-2"></i>{{ $example }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="glass-card rounded-4 p-3 p-lg-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <h2 class="h4 fw-bold mb-1">Filter Label</h2>
                            <p class="muted-text mb-0">Pilih label untuk menampilkan tugas terkait.</p>
                        </div>
                        <span class="badge rounded-pill text-bg-secondary">{{ $tags->count() }} label</span>
                    </div>

                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('todo', ['view' => $isTrashView ? 'trash' : null]) }}"
                            class="btn btn-sm {{ empty($searchFilters['tags']) && ! request('search') ? 'btn-primary' : 'btn-outline-secondary' }} filter-chip">
                            Semua
                        </a>
                        @forelse ($tags as $tag)
                            <a href="{{ route('todo', ['search' => '#' . $tag->name, 'view' => $isTrashView ? 'trash' : null]) }}"
                                class="tag-pill text-white"
                                style="background-color: {{ $tag->color }}">
                                <i class="bi bi-hash"></i>
                                {{ $tag->name }}
                                <span class="badge rounded-pill text-bg-light text-dark">{{ $tag->todos_count }}</span>
                            </a>
                        @empty
                            <div class="empty-state rounded-4 p-3 w-100 text-center muted-text">
                                Label akan tersedia secara otomatis setelah tugas dengan format <code>#label</code> disimpan.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="col-xl-7">
                <div class="glass-card rounded-4 p-3 p-lg-4">
                    <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 mb-3">
                        <div>
                            <h2 class="h4 fw-bold mb-1">
                                {{ $isTrashView ? 'Arsip Tugas' : 'Daftar Tugas' }}
                            </h2>
                            <p class="muted-text mb-0">
                                Pencarian mendukung kata kunci, <code>#label</code>, <code>is:done</code>, dan <code>priority:high</code>.
                            </p>
                        </div>
                        <form action="{{ route('todo') }}" method="GET" class="w-100" style="max-width: 420px;">
                            @if ($isTrashView)
                                <input type="hidden" name="view" value="trash">
                            @endif
                            <div class="input-group">
                                <span class="input-group-text rounded-start-4 border-end-0 bg-transparent">
                                    <i class="bi bi-search"></i>
                                </span>
                                <input type="text" class="form-control border-start-0 border-end-0" name="search"
                                    value="{{ request('search') }}"
                                    placeholder="#operasional is:pending priority:high">
                                <button class="btn btn-outline-primary rounded-end-4" type="submit">
                                    Cari
                                </button>
                            </div>
                        </form>
                    </div>

                    @if (request('search'))
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            @if ($searchFilters['keyword'] !== '')
                                <span class="badge text-bg-light">Kata Kunci: {{ $searchFilters['keyword'] }}</span>
                            @endif
                            @foreach ($searchFilters['tags'] as $tag)
                                <span class="badge text-bg-info">#{{ $tag }}</span>
                            @endforeach
                            @if ($searchFilters['status'])
                                <span class="badge text-bg-secondary">Status: {{ $searchFilters['status'] }}</span>
                            @endif
                            @if ($searchFilters['priority'])
                                <span class="badge text-bg-warning text-dark">Prioritas: {{ $searchFilters['priority'] }}</span>
                            @endif
                        </div>
                    @endif

                    <div class="d-grid gap-2">
                        @forelse ($data as $item)
                            <div class="task-item rounded-4 p-3 {{ $item->deadline_state }}">
                                <div class="d-flex flex-column flex-lg-row justify-content-between gap-3">
                                    <div class="flex-grow-1">
                                        <div class="d-flex flex-wrap gap-2 mb-2">
                                            <span class="badge rounded-pill text-bg-{{ $item->status_badge_class }}">
                                                {{ $item->status_label }}
                                            </span>
                                            <span class="badge rounded-pill bg-{{ $item->priority_badge_class }}">
                                                Prioritas: {{ ucfirst($item->priority) }}
                                            </span>
                                            @if ($item->deadline_label)
                                                <span class="badge rounded-pill bg-{{ $item->deadline_badge_class }}">
                                                    {{ $item->deadline_label }}
                                                </span>
                                            @endif
                                        </div>

                                        <h3 class="h5 mb-2">
                                            @if ($item->is_done)
                                                <del>{{ $item->task }}</del>
                                            @else
                                                {{ $item->task }}
                                            @endif
                                        </h3>

                                        <div class="d-flex flex-wrap gap-2 mb-3">
                                            @forelse ($item->tags as $tag)
                                                <a href="{{ route('todo', ['search' => '#' . $tag->name, 'view' => $isTrashView ? 'trash' : null]) }}"
                                                    class="tag-pill text-white"
                                                    style="background-color: {{ $tag->color }}">
                                                    <i class="bi bi-tag"></i>
                                                    {{ $tag->name }}
                                                </a>
                                            @empty
                                                <span class="badge rounded-pill text-bg-light">Tanpa label</span>
                                            @endforelse
                                        </div>

                                        <div class="d-flex flex-wrap gap-3 deadline-text muted-text">
                                            <span><i class="bi bi-calendar-event me-2"></i>{{ $item->formatted_due_date ?? 'Tenggat waktu belum ditetapkan' }}</span>
                                            <span><i class="bi bi-clock-history me-2"></i>Dibuat {{ $item->created_at->diffForHumans() }}</span>
                                            @if ($item->deleted_at)
                                                <span><i class="bi bi-trash me-2"></i>Diarsipkan {{ $item->deleted_at->diffForHumans() }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="d-flex flex-column gap-2" style="min-width: 170px;">
                                        @if (! $isTrashView)
                                            <button class="btn btn-outline-primary"
                                                data-bs-toggle="collapse"
                                                data-bs-target="#collapse-{{ $item->id }}">
                                                <i class="bi bi-pencil-square me-2"></i>
                                                Ubah Tugas
                                            </button>
                                            <form action="{{ route('todo.delete', ['id' => $item->id]) }}" method="POST"
                                                onsubmit="return confirm('Arsipkan tugas ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-outline-danger w-100">
                                                    <i class="bi bi-trash3 me-2"></i>
                                                    Arsipkan
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('todo.restore', ['id' => $item->id]) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <button class="btn btn-outline-success w-100">
                                                    <i class="bi bi-arrow-counterclockwise me-2"></i>
                                                    Pulihkan
                                                </button>
                                            </form>
                                            <form action="{{ route('todo.force-delete', ['id' => $item->id]) }}" method="POST"
                                                onsubmit="return confirm('Hapus tugas ini secara permanen?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-outline-danger w-100">
                                                    <i class="bi bi-x-octagon me-2"></i>
                                                    Hapus Permanen
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>

                                @if (! $isTrashView)
                                    <div class="collapse mt-3" id="collapse-{{ $item->id }}">
                                        <div class="border-top pt-3">
                                            <form action="{{ route('todo.update', ['id' => $item->id]) }}" method="POST"
                                                class="d-grid gap-2">
                                                @csrf
                                                @method('PUT')
                                                <div>
                                                    <label class="form-label fw-semibold">Deskripsi Tugas</label>
                                                    <textarea class="form-control" name="task" rows="3" required>{{ $item->task }}</textarea>
                                                </div>
                                                <div class="row g-2">
                                                    <div class="col-md-4">
                                                        <label class="form-label fw-semibold">Prioritas</label>
                                                        <select class="form-select" name="priority">
                                                            <option value="high" @selected($item->priority === 'high')>High</option>
                                                            <option value="medium" @selected($item->priority === 'medium')>Menengah</option>
                                                            <option value="low" @selected($item->priority === 'low')>Low</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label fw-semibold">Tenggat Waktu</label>
                                                        <input type="datetime-local" class="form-control" name="due_date"
                                                            value="{{ optional($item->due_date)->format('Y-m-d\TH:i') }}">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label fw-semibold">Status</label>
                                                        <select class="form-select" name="is_done">
                                                            <option value="0" @selected(! $item->is_done)>Berjalan</option>
                                                            <option value="1" @selected($item->is_done)>Selesai</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <button class="btn btn-primary">
                                                    <i class="bi bi-floppy me-2"></i>
                                                    Simpan Perubahan
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="empty-state rounded-4 p-4 text-center">
                                <div class="display-6 mb-2"><i class="bi bi-inboxes"></i></div>
                                <h3 class="h5 fw-bold mb-2">Tidak ada data untuk ditampilkan</h3>
                                <p class="muted-text mb-0">
                                    {{ $isTrashView ? 'Arsip tugas masih kosong.' : 'Silakan tambahkan tugas baru atau sesuaikan kriteria pencarian.' }}
                                </p>
                            </div>
                        @endforelse
                    </div>

                    <div class="mt-3">
                        {{ $data->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const storageKey = 'todo-theme';
        const root = document.body;
        const toggleButton = document.getElementById('theme-toggle');

        const applyTheme = (theme) => {
            root.classList.toggle('dark-mode', theme === 'dark');
            toggleButton.innerHTML = theme === 'dark'
                ? '<i class="bi bi-sun me-2"></i>Mode Terang'
                : '<i class="bi bi-moon-stars me-2"></i>Mode Gelap';
        };

        const savedTheme = localStorage.getItem(storageKey) || 'light';
        applyTheme(savedTheme);

        toggleButton.addEventListener('click', () => {
            const nextTheme = root.classList.contains('dark-mode') ? 'light' : 'dark';
            localStorage.setItem(storageKey, nextTheme);
            applyTheme(nextTheme);
        });
    </script>
</body>

</html>
