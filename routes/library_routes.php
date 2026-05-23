<?php
// ═══════════════════════════════════════════════════════════
//  ROUTES BIBLIOTHÈQUE — à fusionner dans routes/web.php
//  Ajoute cet import en haut du fichier :
//  use App\Http\Controllers\LibraryController;
// ═══════════════════════════════════════════════════════════

/* ─────────────────────────────────────────────────────────
 |  ACTIONS COMMUNES (lecture + téléchargement)
 |  Accessible à tout utilisateur authentifié
 ───────────────────────────────────────────────────────── */
Route::middleware('auth')->group(function () {
    Route::get('/library/{book}/read',     [LibraryController::class, 'read'])    ->name('library.read');
    Route::get('/library/{book}/download', [LibraryController::class, 'download'])->name('library.download');
});

/* ─────────────────────────────────────────────────────────
 |  SUPERADMIN — bibliothèque globale
 |  Ajout dans le groupe existant : Route::prefix('superadmin')…
 ───────────────────────────────────────────────────────── */
// À placer DANS le groupe prefix('superadmin')->name('superadmin.')
Route::get('/library',              [LibraryController::class, 'superIndex'])        ->name('library');
Route::post('/library',             [LibraryController::class, 'superStore'])        ->name('library.store');
Route::put('/library/{book}',       [LibraryController::class, 'superUpdate'])       ->name('library.update');
Route::delete('/library/{book}',    [LibraryController::class, 'superDestroy'])      ->name('library.destroy');
Route::patch('/library/{book}/toggle', [LibraryController::class, 'superTogglePublish'])->name('library.toggle');

/* ─────────────────────────────────────────────────────────
 |  ADMIN (DIRECTEUR) — bibliothèque de son institution
 |  Ajout dans le groupe existant : Route::prefix('admin')…
 ───────────────────────────────────────────────────────── */
// À placer DANS le groupe prefix('admin')->name('admin.')
Route::get('/library',           [LibraryController::class, 'adminIndex'])   ->name('library');
Route::post('/library',          [LibraryController::class, 'adminStore'])   ->name('library.store');
Route::put('/library/{book}',    [LibraryController::class, 'adminUpdate'])  ->name('library.update');
Route::delete('/library/{book}', [LibraryController::class, 'adminDestroy'])->name('library.destroy');

/* ─────────────────────────────────────────────────────────
 |  ENSEIGNANT — lecture + ajout de cours
 |  Ajout dans le groupe existant : Route::prefix('teacher')…
 ───────────────────────────────────────────────────────── */
// À placer DANS le groupe prefix('teacher')->name('teacher.')
Route::get('/library',            [LibraryController::class, 'teacherIndex'])  ->name('library');
Route::post('/library',           [LibraryController::class, 'teacherStore'])  ->name('library.store');
Route::delete('/library/{book}',  [LibraryController::class, 'teacherDestroy'])->name('library.destroy');

/* ─────────────────────────────────────────────────────────
 |  ÉTUDIANT — lecture seule
 |  Ajout dans le groupe existant : Route::prefix('student')…
 ───────────────────────────────────────────────────────── */
// À placer DANS le groupe prefix('student')->name('student.')
Route::get('/library', [LibraryController::class, 'studentIndex'])->name('library');
