@extends('layouts.admin')

@section('title', 'Dokumentasi Periode - ' . $group->name)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="{{ route('admin.groups.manage', $group->id) }}" class="btn btn-secondary shadow-sm btn-sm">
            <i class="fas fa-arrow-left me-1"></i>Kembali
        </a>
    </div>
    <div class="text-end">
        <h1 class="h3 mb-1 text-gray-800">
            <i class="fas fa-camera-retro me-2 text-primary"></i>Dokumentasi
        </h1>
        <p class="text-muted mb-0">{{ $group->name }}</p>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-white">Kelola Dokumentasi Per Periode</h6>
        <a href="{{ route('admin.groups.documentations.create', $group->id) }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus me-1"></i> Tambah Dokumentasi
        </a>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="accordion" id="documentationAccordion">
            @forelse($group->monthlyPeriods as $index => $period)
                <div class="accordion-item shadow-sm mb-3 border-0 rounded">
                    <h2 class="accordion-header" id="heading{{ $period->id }}">
                        <button class="accordion-button {{ $index == 0 ? '' : 'collapsed' }} rounded" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $period->id }}" aria-expanded="{{ $index == 0 ? 'true' : 'false' }}" aria-controls="collapse{{ $period->id }}">
                            <div class="d-flex align-items-center w-100">
                                <div class="me-3">
                                    <span class="badge bg-primary rounded-pill">{{ $index + 1 }}</span>
                                </div>
                                <div class="flex-grow-1">
                                    <span class="fw-bold text-dark">{{ $period->period_start->locale('id')->monthName }} {{ $period->period_start->year }}</span>
                                    <span class="ms-2 text-muted small">({{ $period->documentations->count() }} Dokumentasi)</span>
                                </div>
                                @if($period->status === 'finished')
                                    <span class="badge bg-success me-3">Selesai</span>
                                @elseif($period->status === 'bidding')
                                    <span class="badge bg-warning text-dark me-3">Sedang Berlangsung</span>
                                @endif
                            </div>
                        </button>
                    </h2>
                    <div id="collapse{{ $period->id }}" class="accordion-collapse collapse {{ $index == 0 ? 'show' : '' }}" aria-labelledby="heading{{ $period->id }}" data-bs-parent="#documentationAccordion">
                        <div class="accordion-body bg-light">
                            <div class="row g-3">
                                @forelse($period->documentations as $doc)
                                    <div class="col-md-4 col-sm-6">
                                        <div class="card h-100 shadow-sm border-0 position-relative">
                                            <div class="position-absolute top-0 end-0 p-2" style="z-index: 10;">
                                                <div class="dropdown">
                                                    <button class="btn btn-light btn-sm shadow-sm rounded-circle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="fas fa-ellipsis-v"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                                                        <li><a class="dropdown-item" href="{{ route('admin.documentations.edit', $doc->id) }}"><i class="fas fa-edit me-2 text-info"></i> Ubah</a></li>
                                                        <li>
                                                            <form action="{{ route('admin.documentations.destroy', $doc->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus dokumentasi ini?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="dropdown-item text-danger"><i class="fas fa-trash me-2"></i> Hapus</button>
                                                            </form>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>

                                            @if($doc->type === 'image')
                                                <img src="{{ asset('storage/' . $doc->content) }}" class="card-img-top object-fit-cover" style="height: 180px;" alt="{{ $doc->caption }}">
                                            @elseif($doc->type === 'video')
                                                <div class="ratio ratio-16x9">
                                                    <video controls>
                                                        <source src="{{ asset('storage/' . $doc->content) }}">
                                                        Your browser does not support the video tag.
                                                    </video>
                                                </div>
                                            @elseif($doc->type === 'text')
                                                <div class="card-body overflow-auto" style="height: 180px; background-color: #fcf8e3;">
                                                    <p class="card-text">{{ $doc->content }}</p>
                                                </div>
                                            @endif

                                            <div class="card-body py-2">
                                                <p class="card-text small text-dark fw-bold mb-0">
                                                    @if($doc->type === 'image') <i class="fas fa-image text-primary me-1"></i> Gambar
                                                    @elseif($doc->type === 'video') <i class="fas fa-video text-danger me-1"></i> Video
                                                    @else <i class="fas fa-align-left text-info me-1"></i> Teks
                                                    @endif
                                                </p>
                                                <p class="card-text small text-muted">{{ $doc->caption ?: 'Tidak ada keterangan' }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-12">
                                        <div class="text-center py-4 bg-white rounded">
                                            <i class="fas fa-info-circle text-muted mb-2"></i>
                                            <p class="text-muted mb-0">Belum ada dokumentasi untuk periode ini.</p>
                                        </div>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-5">
                    <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Belum ada periode yang dibuat.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<style>
    .accordion-button:not(.collapsed) {
        background-color: #f8f9fc;
        color: #4e73df;
        box-shadow: inset 0 -1px 0 rgba(0,0,0,.125);
    }
    .accordion-button:focus {
        box-shadow: none;
        border-color: rgba(0,0,0,.125);
    }
    .object-fit-cover {
        object-fit: cover;
    }
</style>
@endsection
