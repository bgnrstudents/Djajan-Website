        // ==================================================
        // UMKM
        // ==================================================

        // Data UMKM (bisa difilter ?status=pending|terverifikasi|ditolak)
        Route::get('/umkm', [UmkmController::class, 'index'])
            ->name('umkm.index');

        // Detail UMKM + tombol Setujui/Tolak
        Route::get('/umkm/{umkm}', [UmkmController::class, 'show'])
            ->name('umkm.show');

        // Setujui / tolak pendaftaran (dari tombol di halaman detail)
        Route::post('/umkm/{umkm}/approve', [UmkmController::class, 'approve'])
            ->name('umkm.approve');
        Route::post('/umkm/{umkm}/reject', [UmkmController::class, 'reject'])
            ->name('umkm.reject');

        // Form Edit UMKM (data dasar, bukan password)
        Route::get('/umkm/{umkm}/edit', [UmkmController::class, 'edit'])
            ->name('umkm.edit');

        // Update UMKM
        Route::put('/umkm/{umkm}', [UmkmController::class, 'update'])
            ->name('umkm.update');

        // Hapus UMKM
        Route::delete('/umkm/{umkm}', [UmkmController::class, 'destroy'])
            ->name('umkm.destroy');

        // CATATAN: route umkm.create & umkm.store SENGAJA DIHAPUS -- admin
        // tidak lagi membuat akun UMKM manual dari sini. Akun baru masuk
        // lewat pendaftaran di aplikasi mobile (status awal "pending"),
        // admin di sini hanya meninjau & menyetujui/menolak.
