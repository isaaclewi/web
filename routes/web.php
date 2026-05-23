<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\ApprenantDashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BledController;
use App\Http\Controllers\DisciplinaireController;
use App\Http\Controllers\GradeConfigController;
use App\Http\Controllers\LibraryController;
use App\Http\Controllers\ParentController;
use App\Http\Controllers\PlanningController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StaffDashboardController;
use App\Http\Controllers\StaffTaskController;
use App\Http\Controllers\SupAdminDashboardController;
use App\Http\Controllers\TeacherDashboardController;
use App\Http\Controllers\TransferController;
use App\Http\Controllers\SujetController;
use App\Http\Controllers\BledPdfController;
use App\Http\Controllers\InstitutionPublicController;
use Illuminate\Support\Facades\Route;

/* ─────────────────────────────────────
 | AUTH
 ───────────────────────────────────── */
Route::get('/', fn () => view('welcome'))->name('home');
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/about', fn () => view('about'))->name('about');
Route::get('/mission', fn () => view('mission'))->name('mission');
Route::get('/institutions', [InstitutionPublicController::class, 'index'])->name('institutions.index');
Route::get('/institutions/{institution}/data', [InstitutionPublicController::class, 'show'])->name('institutions.show');

/* ─────────────────────────────────────
 | ADMIN (directeur)
 ───────────────────────────────────── */
Route::prefix('admin')->name('admin.')->middleware(['auth', 'institution.active'])->group(function () {

    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/administrative', [AdminDashboardController::class, 'administrative'])->name('administrative');
    Route::get('/results', [AdminDashboardController::class, 'results'])->name('results');
    Route::get('/report-cards', [AdminDashboardController::class, 'reportCards'])->name('report_cards');

    // ── Établissement ──
    Route::get('/mon-etablissement', [AdminDashboardController::class, 'institutionSettings'])->name('institution.settings');
    Route::patch('/mon-etablissement', [AdminDashboardController::class, 'institutionUpdate'])->name('institution.update');

    // ── Utilisateurs ──
    Route::get('/users', [AdminDashboardController::class, 'userManagement'])->name('users');
    Route::post('/users', [AdminDashboardController::class, 'userStore'])->name('users.store');
    Route::put('/users/{user}', [AdminDashboardController::class, 'userUpdate'])->name('users.update');
    Route::put('/users/{user}/reset-password', [AdminDashboardController::class, 'userResetPassword'])->name('users.reset_password');
    Route::patch('/users/{user}/status', [AdminDashboardController::class, 'userToggleStatus'])->name('users.status');
    Route::delete('/users/{user}', [AdminDashboardController::class, 'userDestroy'])->name('users.destroy');

    // ── Apprenants ──
    Route::get('/apprenants', [AdminDashboardController::class, 'apprenants'])->name('apprenants');
    Route::post('/apprenants', [AdminDashboardController::class, 'apprenantStore'])->name('apprenants.store');
    Route::delete('/apprenants', [AdminDashboardController::class, 'apprenantBulkDestroy'])->name('apprenants.bulkDestroy');
    Route::get('/apprenants/export', [AdminDashboardController::class, 'apprenantExport'])->name('apprenants.export');
    Route::post('/apprenants/import', [AdminDashboardController::class, 'apprenantImport'])->name('apprenants.import');
    Route::put('/apprenants/{apprenant}', [AdminDashboardController::class, 'apprenantUpdate'])->name('apprenants.update');
    Route::put('/apprenants/{apprenant}/reset-password', [AdminDashboardController::class, 'apprenantResetPassword'])->name('apprenants.reset_password');
    Route::delete('/apprenants/{apprenant}', [AdminDashboardController::class, 'apprenantDestroy'])->name('apprenants.destroy');

    // ── Staff ──
    Route::get('/staff', [AdminDashboardController::class, 'staff'])->name('staff');
    Route::post('/staff', [AdminDashboardController::class, 'staffStore'])->name('staff.store');
    Route::put('/staff/{staff}', [AdminDashboardController::class, 'staffUpdate'])->name('staff.update');
    Route::put('/staff/{staff}/reset-password', [AdminDashboardController::class, 'staffResetPassword'])->name('staff.reset_password');
    Route::patch('/staff/{staff}/status', [AdminDashboardController::class, 'staffToggleStatus'])->name('staff.status');
    Route::delete('/staff/{staff}', [AdminDashboardController::class, 'staffDestroy'])->name('staff.destroy');

    // ── Enseignants ──
    Route::get('/teachers', [AdminDashboardController::class, 'teachers'])->name('teachers');
    Route::post('/teachers', [AdminDashboardController::class, 'teacherStore'])->name('teachers.store');
    Route::put('/teachers/{teacher}', [AdminDashboardController::class, 'teacherUpdate'])->name('teachers.update');
    Route::put('/teachers/{teacher}/reset-password', [AdminDashboardController::class, 'teacherResetPassword'])->name('teachers.reset_password');
    Route::patch('/teachers/{teacher}/status', [AdminDashboardController::class, 'teacherToggleStatus'])->name('teachers.status');
    Route::delete('/teachers/{teacher}', [AdminDashboardController::class, 'teacherDestroy'])->name('teachers.destroy');

    // ── Académique ──
    Route::get('/academic', [AdminDashboardController::class, 'academic'])->name('academic');

    Route::post('/academic/classes', [AdminDashboardController::class, 'classeStore'])->name('academic.classes.store');
    Route::put('/academic/classes/{classe}', [AdminDashboardController::class, 'classeUpdate'])->name('academic.classes.update');
    Route::delete('/academic/classes/{classe}', [AdminDashboardController::class, 'classeDestroy'])->name('academic.classes.destroy');

    Route::post('/academic/niveaux', [AdminDashboardController::class, 'niveauStore'])->name('academic.niveaux.store');
    Route::put('/academic/niveaux/{niveau}', [AdminDashboardController::class, 'niveauUpdate'])->name('academic.niveaux.update');
    Route::delete('/academic/niveaux/{niveau}', [AdminDashboardController::class, 'niveauDestroy'])->name('academic.niveaux.destroy');

    Route::post('/academic/filieres', [AdminDashboardController::class, 'filiereStore'])->name('academic.filieres.store');
    Route::put('/academic/filieres/{filiere}', [AdminDashboardController::class, 'filiereUpdate'])->name('academic.filieres.update');
    Route::delete('/academic/filieres/{filiere}', [AdminDashboardController::class, 'filiereDestroy'])->name('academic.filieres.destroy');

    Route::post('/academic/matieres', [AdminDashboardController::class, 'matiereStore'])->name('academic.matieres.store');
    Route::put('/academic/matieres/{subject}', [AdminDashboardController::class, 'matiereUpdate'])->name('academic.matieres.update');
    Route::delete('/academic/matieres/{subject}', [AdminDashboardController::class, 'matiereDestroy'])->name('academic.matieres.destroy');

    Route::post('/academic/affectations/teacher-classe',
        [AdminDashboardController::class, 'affectationTeacherClasse'])
        ->name('academic.affectations.teacher-classe');
    Route::delete('/academic/affectations/teacher-classe/{teacher}/{classe}',
        [AdminDashboardController::class, 'affectationTeacherClasseDestroy'])
        ->name('academic.affectations.teacher-classe-destroy');

    Route::post('/academic/affectations/teacher-niveau',
        [AdminDashboardController::class, 'affectationTeacherNiveau'])
        ->name('academic.affectations.teacher-niveau');
    Route::delete('/academic/affectations/teacher-niveau/{teacher}/{niveau}',
        [AdminDashboardController::class, 'affectationTeacherNiveauDestroy'])
        ->name('academic.affectations.teacher-niveau-destroy');

    Route::post('/academic/affectations/eleve-classe',
        [AdminDashboardController::class, 'affectationEleveClasse'])
        ->name('academic.affectations.eleve-classe');
    Route::delete('/academic/affectations/eleve-classe/{apprenant}',
        [AdminDashboardController::class, 'affectationEleveClasseDestroy'])
        ->name('academic.affectations.eleve-classe-destroy');

    // ── Finances ──────────────────────────────────────────────────────
    Route::get('/financial', [AdminDashboardController::class, 'financial'])->name('financial');
    Route::get('/financial/export', [AdminDashboardController::class, 'financialExport'])->name('financial.export');
    Route::get('/financial/apprenant/{apprenant}', [AdminDashboardController::class, 'financialApprenant'])->name('financial.apprenant');
    Route::post('/financial/store', [AdminDashboardController::class, 'financialStore'])->name('financial.store');
    Route::patch('/financial/validate/{record}', [AdminDashboardController::class, 'financialValidate'])->name('financial.validate');
    Route::delete('/financial/{record}', [AdminDashboardController::class, 'financialDestroy'])->name('financial.destroy');

    // Liste et création
    Route::get('/parents', [ParentController::class, 'index'])->name('parents');
    Route::post('/parents', [ParentController::class, 'store'])->name('parents.store');

    // IMPORTANT : les routes POST sans paramètre doivent être AVANT les routes avec {parent}
    Route::post('/parents/affect', [ParentController::class, 'affect'])->name('parents.affect');
    Route::post('/parents/detach', [ParentController::class, 'detach'])->name('parents.detach');
    Route::get('/parents/search-apprenants', [ParentController::class, 'searchApprenants'])->name('parents.search-apprenants');

    // CRUD sur un parent spécifique
    Route::get('/parents/{parent}', [ParentController::class, 'show'])->name('parents.show');
    Route::put('/parents/{parent}', [ParentController::class, 'update'])->name('parents.update');
    Route::delete('/parents/{parent}', [ParentController::class, 'destroy'])->name('parents.destroy');
    Route::put('/parents/{parent}/reset-password', [ParentController::class, 'resetPassword'])->name('parents.reset_password');

    // ── Recherche AJAX (pour les selects dynamiques) ──────────────
    Route::get('/academic/search/apprenants', [AdminDashboardController::class, 'searchApprenants'])->name('academic.search.apprenants');
    Route::get('/academic/search/teachers', [AdminDashboardController::class, 'searchTeachers'])->name('academic.search.teachers');
    Route::get('/academic/search/classes', [AdminDashboardController::class, 'searchClasses'])->name('academic.search.classes');

    // Dans routes/web.php, groupe admin :
    Route::get('/rapports', [AdminDashboardController::class, 'rapports'])->name('rapports');

    // Liste globale des incidents
    Route::get('/disciplinaire', [DisciplinaireController::class, 'index'])->name('disciplinaire');

    // Créer un incident
    Route::post('/disciplinaire', [DisciplinaireController::class, 'store'])->name('disciplinaire.store');

    // Export CSV
    Route::get('/disciplinaire/export', [DisciplinaireController::class, 'export'])->name('disciplinaire.export');

    // Fiche apprenant (doit être AVANT {disciplinaire})
    Route::get('/disciplinaire/apprenant/{apprenant}', [DisciplinaireController::class, 'apprenant'])->name('disciplinaire.apprenant');

    // Modifier / Supprimer un incident
    Route::put('/disciplinaire/{disciplinaire}', [DisciplinaireController::class, 'update'])->name('disciplinaire.update');
    Route::delete('/disciplinaire/{disciplinaire}', [DisciplinaireController::class, 'destroy'])->name('disciplinaire.destroy');

    // Page principale
    Route::get('/planning', [PlanningController::class, 'index'])->name('planning');

    // Emploi du temps CRUD
    Route::post('/planning/edt', [PlanningController::class, 'edtStore'])->name('planning.edt.store');
    Route::put('/planning/edt/{emploiDuTemps}', [PlanningController::class, 'edtUpdate'])->name('planning.edt.update');
    Route::delete('/planning/edt/{emploiDuTemps}', [PlanningController::class, 'edtDestroy'])->name('planning.edt.destroy');

    // Séances CRUD
    Route::post('/planning/seance', [PlanningController::class, 'seanceStore'])->name('planning.seance.store');
    Route::put('/planning/seance/{seanceCours}', [PlanningController::class, 'seanceUpdate'])->name('planning.seance.update');
    Route::delete('/planning/seance/{seanceCours}', [PlanningController::class, 'seanceDestroy'])->name('planning.seance.destroy');

    // Programmes paiement CRUD
    Route::post('/planning/paiement', [PlanningController::class, 'paiementStore'])->name('planning.paiement.store');
    Route::put('/planning/paiement/{programmePaiement}', [PlanningController::class, 'paiementUpdate'])->name('planning.paiement.update');
    Route::delete('/planning/paiement/{programmePaiement}', [PlanningController::class, 'paiementDestroy'])->name('planning.paiement.destroy');

    Route::get('/library', [LibraryController::class, 'adminIndex'])->name('library');
    Route::post('/library', [LibraryController::class, 'adminStore'])->name('library.store');
    Route::put('/library/{book}', [LibraryController::class, 'adminUpdate'])->name('library.update');
    Route::delete('/library/{book}', [LibraryController::class, 'adminDestroy'])->name('library.destroy');

    Route::get('/profil', [ProfileController::class, 'show'])->name('profil');
    Route::patch('/profil/infos', [ProfileController::class, 'updateInfos'])->name('profil.infos');
    Route::patch('/profil/password', [ProfileController::class, 'updatePassword'])->name('profil.password');
    Route::post('/profil/avatar', [ProfileController::class, 'updateAvatar'])->name('profil.avatar');

    Route::get('/transfer', [TransferController::class, 'index'])->name('transfer.index');
    Route::get('/transfer/search', [TransferController::class, 'search'])->name('transfer.search');
    Route::post('/transfer/request', [TransferController::class, 'store'])->name('transfer.store');
    Route::get('/transfer/{transfer}/show', [TransferController::class, 'show'])->name('transfer.show');
    Route::get('/transfer/{transfer}/dossier', [TransferController::class, 'dossier'])->name('transfer.dossier');
    Route::patch('/transfer/{transfer}/approve', [TransferController::class, 'approve'])->name('transfer.approve');
    Route::patch('/transfer/{transfer}/reject', [TransferController::class, 'reject'])->name('transfer.reject');
    Route::delete('/transfer/{transfer}', [TransferController::class, 'destroy'])->name('transfer.destroy');

    // ── Configuration notation & Bulletins ──
    Route::get('/grade-config', [GradeConfigController::class, 'index'])->name('grade_config');
    Route::patch('/grade-config', [GradeConfigController::class, 'updateConfig'])->name('grade_config.update');

    // Calcul
    Route::post('/bulletins/calcul/apprenant', [GradeConfigController::class, 'calculerBulletinApprenant'])->name('bulletins.calcul.apprenant');
    Route::post('/bulletins/calcul/classe', [GradeConfigController::class, 'calculerBulletinsClasse'])->name('bulletins.calcul.classe');
    Route::post('/bulletins/calcul/tous', [GradeConfigController::class, 'calculerTousLesBulletins'])->name('bulletins.calcul.tous');

    // Publication
    Route::post('/bulletins/publier/classe', [GradeConfigController::class, 'publierBulletinsClasse'])->name('bulletins.publier.classe');
    Route::post('/bulletins/depublier/classe', [GradeConfigController::class, 'depublierBulletinsClasse'])->name('bulletins.depublier.classe');
    Route::patch('/bulletins/{bulletin}/publier', [GradeConfigController::class, 'publierBulletin'])->name('bulletins.publier');
    Route::patch('/bulletins/{bulletin}/depublier', [GradeConfigController::class, 'depublierBulletin'])->name('bulletins.depublier');
    Route::patch('/bulletins/{bulletin}/appreciation', [GradeConfigController::class, 'bulletinUpdateAppreciation'])->name('bulletins.appreciation');

    // Listes & détail admin
    Route::get('/bulletins', [GradeConfigController::class, 'bulletinsIndex'])->name('bulletins.index');
    Route::get('/bulletins/{bulletin}', [GradeConfigController::class, 'bulletinShow'])->name('bulletins.show');

    // ── Notes saisies par enseignants (vue admin)
    Route::get('/teacher-notes-overview', [GradeConfigController::class, 'teacherNotesOverview'])->name('teacher.notes.overview');

    // ── Gestion tâches staff ──
    Route::get('/staff-tasks', [StaffTaskController::class, 'index'])->name('staff_tasks.index');
    Route::get('/staff-tasks/{staff}', [StaffTaskController::class, 'show'])->name('staff_tasks.show');
    Route::post('/staff-tasks/{staff}/assign', [StaffTaskController::class, 'assign'])->name('staff_tasks.assign');
    Route::post('/staff-tasks/{staff}/bulk-assign', [StaffTaskController::class, 'bulkAssign'])->name('staff_tasks.bulk_assign');
    Route::patch('/staff-tasks/assignment/{assignment}/toggle', [StaffTaskController::class, 'toggle'])->name('staff_tasks.toggle');
    Route::patch('/staff-tasks/assignment/{assignment}/notes', [StaffTaskController::class, 'updateNotes'])->name('staff_tasks.notes');
    Route::delete('/staff-tasks/assignment/{assignment}', [StaffTaskController::class, 'remove'])->name('staff_tasks.remove');

    // ── Saisie de notes par l'admin ──
    // Notes (admin)
    Route::post('/grades', [GradeConfigController::class, 'gradesStore'])->name('grades.store');
    Route::patch('/grades/{grade}', [GradeConfigController::class, 'gradeUpdate'])->name('grades.update');
    Route::delete('/grades/{grade}', [GradeConfigController::class, 'gradeDestroy'])->name('grades.destroy');

    // ── Gestion évaluations par l'admin ──
    Route::post('/evaluations', [GradeConfigController::class, 'evaluationStore'])->name('evaluations.store');
    Route::delete('/evaluations/{evaluation}', [GradeConfigController::class, 'evaluationDestroy'])->name('evaluations.destroy');

    // Page principale BLED
    Route::get('/', [BledController::class, 'index'])->name('bled.index');          // → admin.bled                                                                            // court-circuit

    // Créer et stocker une archive
    Route::post('/', [BledController::class, 'store'])->name('bled.store');

    // Télécharger une archive stockée
    Route::get('/{archive}/download', [BledController::class, 'download'])->name('bled.download');

    // Aperçu rapide (50 premières lignes)
    Route::get('/{archive}/preview', [BledController::class, 'preview'])->name('bled.preview');

    // Export à la volée (sans stocker)
    Route::get('/export', [BledController::class, 'export'])->name('bled.export');

    // Export global ZIP (toutes catégories)
    Route::get('/export/global', [BledController::class, 'exportGlobal'])->name('bled.export.global');

    // Supprimer une archive
    Route::delete('/{archive}', [BledController::class, 'destroy'])->name('bled.destroy');

    Route::get('/sujets', [SujetController::class, 'adminIndex'])->name('sujets.index');
 Route::patch('/sujets/{sujet}/statut', [SujetController::class, 'adminStatut'])->name('sujets.statut');
 Route::get('/sujets/fichier/{fichier}/download', [SujetController::class, 'adminDownload'])->name('sujets.download');

}); // fin groupe admin
Route::get('/pdf/filtres',  [App\Http\Controllers\BledPdfController::class, 'filtres'])->name('admin.bled.pdf.filtres');
    Route::get('/pdf/apercu',   [App\Http\Controllers\BledPdfController::class, 'apercu'])->name('admin.bled.pdf.apercu');
    Route::get('/pdf/export',   [App\Http\Controllers\BledPdfController::class, 'export'])->name('admin.bled.pdf.export');

/* ─────────────────────────────────────
 | TEACHER / PARENT / APPRENANT
 ───────────────────────────────────── */
/* ─────────────────────────────────────────────────────────────
 | TEACHER — toutes les routes sont protégées par auth + role
 | Ajout ->middleware(['auth', 'role:teacher']) selon ton setup
 ───────────────────────────────────────────────────────────── */

/*
 |──────────────────────────────────────────────────────────────
 |  TEACHER — groupe de routes
 |  Ajoute ->middleware(['auth', 'role:teacher']) selon ton setup
 |──────────────────────────────────────────────────────────────
 */

Route::prefix('teacher')->name('teacher.')->group(function () {

    // ── 1. DASHBOARD ─────────────────────────────────────────
    Route::get('/dashboard', [TeacherDashboardController::class, 'index'])
        ->name('dashboard');

    // ── 2. CLASSES ───────────────────────────────────────────
    Route::get('/classes', [TeacherDashboardController::class, 'classes'])
        ->name('classes.index');

    // ── 3. APPRENANTS ────────────────────────────────────────
    Route::get('/apprenants', [TeacherDashboardController::class, 'apprenants'])
        ->name('apprenants.index');

    // Profil JSON d'un apprenant (fetch/AJAX)
    Route::get('/apprenants/{apprenant}', [TeacherDashboardController::class, 'apprenantShow'])
        ->name('apprenants.show');

    // ── 4. ÉVALUATIONS ───────────────────────────────────────
    Route::get('/evaluations', [TeacherDashboardController::class, 'evaluations'])
        ->name('evaluations.index');

    Route::post('/evaluations', [TeacherDashboardController::class, 'evaluationStore'])
        ->name('evaluations.store');

    Route::delete('/evaluations/{evaluation}', [TeacherDashboardController::class, 'evaluationDestroy'])
        ->name('evaluations.destroy');

    Route::get('/evaluations/{evaluation}/export', [TeacherDashboardController::class, 'exportGrades'])
        ->name('evaluations.export');

    // ── 5. NOTES ─────────────────────────────────────────────
    Route::get('/notes', [TeacherDashboardController::class, 'notes'])
        ->name('notes.index');

    Route::post('/grades', [TeacherDashboardController::class, 'gradesStore'])
        ->name('grades.store');

    Route::patch('/grades/{grade}', [TeacherDashboardController::class, 'gradeUpdate'])
        ->name('grades.update');

    // ── 6. PROFIL ────────────────────────────────────────────
    Route::get('/profil', [TeacherDashboardController::class, 'profil'])
        ->name('profil');

    Route::get('/planning', [PlanningController::class, 'teacherPlanning'])
        ->name('planning');

    Route::get('/library', [LibraryController::class, 'teacherIndex'])->name('library');
    Route::post('/library', [LibraryController::class, 'teacherStore'])->name('library.store');
    Route::delete('/library/{book}', [LibraryController::class, 'teacherDestroy'])->name('library.destroy');

    Route::get('/notes-overview', [GradeConfigController::class, 'teacherNotesOverview'])->name('notes.overview');

    Route::get('/enfant/{apprenant}/bulletins', [GradeConfigController::class, 'parentBulletins'])
        ->name('enfant.bulletins');

    Route::get('/sujets', [SujetController::class, 'teacherIndex'])->name('sujets.index');
    Route::post('/sujets', [SujetController::class, 'teacherStore'])->name('sujets.store');
    Route::delete('/sujets/{sujet}', [SujetController::class, 'teacherDestroy'])->name('sujets.destroy');
    Route::get('/sujets/fichier/{fichier}/download', [SujetController::class, 'teacherDownload'])->name('sujets.download');
    
    Route::post('/profil/avatar', [ProfileController::class, 'updateTeacherAvatar'])
    ->name('profil.avatar');
Route::patch('/profil/infos', [ProfileController::class, 'updateTeacherInfos'])
    ->name('profil.infos');
Route::patch('/profil/password', [ProfileController::class, 'updateTeacherPassword'])
    ->name('profil.password');
});

/*
 |──────────────────────────────────────────────────────────────
 |  ROUTES PARENT — à intégrer dans routes/web.php
 |  Remplacent les routes parent existantes
 |──────────────────────────────────────────────────────────────
 */
use App\Http\Controllers\ParentDashboardController;

Route::prefix('parent')->name('parent.')->middleware(['auth'])->group(function () {

    // ── Dashboard principal
    Route::get('/dashboard', [ParentDashboardController::class, 'index'])
        ->name('dashboard');

    // ── Suivi par enfant
    Route::get('/enfant/{apprenant}/notes', [ParentDashboardController::class, 'notes'])
        ->name('enfant.notes');

    Route::get('/enfant/{apprenant}/finances', [ParentDashboardController::class, 'finances'])
        ->name('enfant.finances');

    // ── Discipline (géré par DisciplinaireController::parentView)
    Route::get('/disciplinaire/{apprenant}', [DisciplinaireController::class, 'parentView'])
        ->name('disciplinaire');

    Route::get('/planning', [PlanningController::class, 'parentPlanning'])
        ->name('planning');

    Route::get('/enfant/{apprenant}/bulletins', [GradeConfigController::class, 'parentBulletins'])->name('enfant.bulletins');
    
    Route::get('/profil', [ProfileController::class, 'parentShow'])
    ->name('profil');
Route::post('/profil/avatar', [ProfileController::class, 'updateParentAvatar'])
    ->name('profil.avatar');
Route::patch('/profil/infos', [ProfileController::class, 'updateParentInfos'])
    ->name('profil.infos');
Route::patch('/profil/password', [ProfileController::class, 'updateParentPassword'])
    ->name('profil.password');
});
// Route::get('/disciplinaire/{apprenant}', [DisciplinaireController::class, 'parentView'])->name('disciplinaire');

/*
 |──────────────────────────────────────────────────────────────
 |  APPRENANT / STUDENT — routes
 |  Protège avec ->middleware(['auth', 'role:apprenant'])
 |──────────────────────────────────────────────────────────────
 */

Route::prefix('student')->name('student.')->group(function () {

    // ── 1. DASHBOARD ────────────────────────────────────────
    Route::get('/dashboard', [ApprenantDashboardController::class, 'index'])
        ->name('dashboard');

    // ── 2. NOTES & ÉVALUATIONS ──────────────────────────────
    Route::get('/notes', [ApprenantDashboardController::class, 'notes'])
        ->name('notes');

    // ── 3. CLASSES & MATIÈRES ───────────────────────────────
    Route::get('/classes', [ApprenantDashboardController::class, 'classes'])
        ->name('classes');

    // ── 4. ENSEIGNANTS ──────────────────────────────────────
    Route::get('/enseignants', [ApprenantDashboardController::class, 'enseignants'])
        ->name('enseignants');

    // ── 5. BULLETINS ────────────────────────────────────────
    Route::get('/bulletins', [ApprenantDashboardController::class, 'bulletins'])
        ->name('bulletins');

    // ── 6. PROFIL & PARENTS & FINANCES ──────────────────────
    Route::get('/profil', [ApprenantDashboardController::class, 'profil'])
        ->name('profil');

    Route::get('/disciplinaire', [\App\Http\Controllers\ApprenantDashboardController::class, 'disciplinaire'])
        ->name('disciplinaire');

    // ── STUDENT / PARENT — emploi du temps lecture seule ──
    Route::get('/planning/classe/{classe}', [PlanningController::class, 'classeEdt'])
        ->middleware('auth')
        ->name('planning.classe');

    // ── PLANNING ÉTUDIANT ──
    Route::get('/planning', [PlanningController::class, 'studentPlanning'])
        ->middleware('auth')
        ->name('planning');

    Route::get('/library', [LibraryController::class, 'studentIndex'])->name('library');

    Route::get('/bulletins', [GradeConfigController::class, 'studentBulletins'])->name('bulletins');
    Route::get('/bulletins/{bulletin}', [GradeConfigController::class, 'studentBulletinShow'])->name('bulletins.show');

    Route::post('/student/ai-chat', [ApprenantDashboardController::class, 'aiChat'])
    ->name('student.ai-chat')->middleware('auth');

        Route::get('/ai-coach', [\App\Http\Controllers\ApprenantDashboardController::class, 'aiCoach'])
        ->middleware('auth')
        ->name('ai-coach');
 
    // ── 8. PROXY ANTHROPIC (appels depuis le JS du Coach IA) ────
    Route::post('/ai-chat', [\App\Http\Controllers\ApprenantDashboardController::class, 'aiChat'])
        ->middleware('auth')
        ->name('ai-chat');
    
    Route::post('/profil/avatar', [ProfileController::class, 'updateApprenantAvatar'])
    ->name('profil.avatar');
Route::patch('/profil/infos', [ProfileController::class, 'updateApprenantInfos'])
    ->name('profil.infos');
 
});

/* ─────────────────────────────────────
 | SUPERADMIN
 ───────────────────────────────────── */
Route::prefix('superadmin')->name('superadmin.')->group(function () {

    Route::get('/dashboard', [SupAdminDashboardController::class, 'index'])->name('dashboard');

    Route::get('/users', [SupAdminDashboardController::class, 'users'])->name('users');
    Route::post('/users', [SupAdminDashboardController::class, 'store'])->name('users.store');
    Route::delete('/users/{user}', [SupAdminDashboardController::class, 'destroy'])->name('users.destroy');

    Route::get('/institutions', [SupAdminDashboardController::class, 'institutions'])->name('institutions');
    Route::post('/institutions', [SupAdminDashboardController::class, 'InstitutionStore'])->name('institutions.store');
    Route::patch('/institutions/{institution}', [SupAdminDashboardController::class, 'updateInstitution'])->name('institutions.update');
    Route::delete('/institutions/{institution}', [SupAdminDashboardController::class, 'destroyInstitution'])->name('institutions.destroy');
    Route::patch('/institutions/{institution}/toggle-status', [SupAdminDashboardController::class, 'toggleStatus'])->name('institutions.toggleStatus');
    Route::get('/library', [LibraryController::class, 'superIndex'])->name('library');
    Route::post('/library', [LibraryController::class, 'superStore'])->name('library.store');
    Route::put('/library/{book}', [LibraryController::class, 'superUpdate'])->name('library.update');
    Route::delete('/library/{book}', [LibraryController::class, 'superDestroy'])->name('library.destroy');
    Route::patch('/library/{book}/toggle', [LibraryController::class, 'superTogglePublish'])->name('library.toggle');
}); // fin groupe superadmin

Route::middleware('auth')->group(function () {
    Route::get('/library/{book}/read', [LibraryController::class, 'read'])->name('library.read');
    Route::get('/library/{book}/download', [LibraryController::class, 'download'])->name('library.download');
});

Route::prefix('staff')->name('staff.')->middleware(['auth'])->group(function () {

    Route::get('/dashboard', [StaffDashboardController::class, 'index'])->name('dashboard');
    Route::get('/profil', [StaffDashboardController::class, 'profil'])->name('profil');

    // 🔹 APPRENANTS
    Route::get('/apprenants', [StaffDashboardController::class, 'apprenants'])->name('apprenants');

    Route::post('/apprenants/store', [StaffDashboardController::class, 'apprenantStore'])->name('apprenants.store');

    Route::put('/apprenants/{apprenant}', [StaffDashboardController::class, 'apprenantUpdate'])->name('apprenants.update');

    Route::delete('/apprenants/{apprenant}', [StaffDashboardController::class, 'apprenantDestroy'])->name('apprenants.destroy');

    Route::post('/apprenants/bulk-delete', [StaffDashboardController::class, 'apprenantBulkDestroy'])->name('apprenants.bulkDelete');

    Route::post('/apprenants/reset-password/{apprenant}', [StaffDashboardController::class, 'apprenantResetPassword'])->name('apprenants.resetPassword');

    Route::get('/apprenants/export', [StaffDashboardController::class, 'apprenantExport'])->name('apprenants.export');

    Route::post('/apprenants/import', [StaffDashboardController::class, 'apprenantImport'])->name('apprenants.import');

    Route::get('/finances', [StaffDashboardController::class, 'financial'])->name('finances');
    Route::get('/finances/{apprenant}', [StaffDashboardController::class, 'financialApprenant'])->name('finances.show');
    Route::post('/paiements', [StaffDashboardController::class, 'financialStore'])->name('paiements.store');
    Route::get('/finances/export', [StaffDashboardController::class, 'financialExport'])->name('finances.export');

    Route::get('/parents', [StaffDashboardController::class, 'parents'])->name('parents');

    Route::get('/inscriptions', [StaffDashboardController::class, 'staff'])
        ->name('inscriptions');

    // CREATE
    Route::post('/inscriptions', [StaffDashboardController::class, 'staffStore'])
        ->name('inscriptions.store');

    // UPDATE
    Route::put('/inscriptions/{staff}', [StaffDashboardController::class, 'staffUpdate'])
        ->name('inscriptions.update');

    // DELETE
    Route::delete('/inscriptions/{staff}', [StaffDashboardController::class, 'staffDestroy'])
        ->name('inscriptions.destroy');

    // PASSWORD RESET
    Route::post('/inscriptions/{staff}/reset-password', [StaffDashboardController::class, 'staffResetPassword'])
        ->name('inscriptions.resetPassword');

    // STATUS TOGGLE
    Route::post('/inscriptions/{staff}/toggle-status', [StaffDashboardController::class, 'staffToggleStatus'])
        ->name('inscriptions.toggleStatus');

    // Route::get('/planning', [StaffDashboardController::class, 'planning'])->name('planning');

    // 🔹 DISCIPLINAIRE
    Route::get('/disciplinaire', [StaffDashboardController::class, 'DisciplinaireIndex'])->name('disciplinaire');

    Route::get('/disciplinaire/apprenant/{apprenant}', [StaffDashboardController::class, 'DisciplinaireApprenant'])
        ->name('disciplinaire.apprenant');

    Route::post('/disciplinaire', [StaffDashboardController::class, 'DisciplinaireStore'])
        ->name('disciplinaire.store');

    Route::put('/disciplinaire/{disciplinaire}', [StaffDashboardController::class, 'DisciplinaireUpdate'])
        ->name('disciplinaire.update');

    Route::delete('/disciplinaire/{disciplinaire}', [StaffDashboardController::class, 'DisciplinaireDestroy'])
        ->name('disciplinaire.destroy');

    Route::get('/disciplinaire/export', [StaffDashboardController::class, 'DisciplinaireExport'])
        ->name('disciplinaire.export');

    Route::get('/rapports', [StaffDashboardController::class, 'rapports'])->name('rapports');

    Route::get('/library', [StaffDashboardController::class, 'adminIndex'])->name('library');

    Route::post('/library', [StaffDashboardController::class, 'adminStore'])->name('library.store');

    Route::put('/library/{book}', [StaffDashboardController::class, 'adminUpdate'])->name('library.update');

    Route::delete('/library/{book}', [StaffDashboardController::class, 'adminDestroy'])->name('library.destroy');

    // 🔹 ENSEIGNANTS
    Route::get('/enseignants', [StaffDashboardController::class, 'teachers'])->name('enseignants');

    Route::post('/enseignants/store', [StaffDashboardController::class, 'teacherStore'])->name('enseignants.store');

    Route::put('/enseignants/{teacher}', [StaffDashboardController::class, 'teacherUpdate'])->name('enseignants.update');

    Route::delete('/enseignants/{teacher}', [StaffDashboardController::class, 'teacherDestroy'])->name('enseignants.destroy');

    Route::post('/enseignants/reset-password/{teacher}', [StaffDashboardController::class, 'teacherResetPassword'])->name('enseignants.resetPassword');

    Route::post('/enseignants/status/{teacher}', [StaffDashboardController::class, 'teacherToggleStatus'])->name('enseignants.toggleStatus');

    // ═══════════════════════════════════════
    // 🔹 BULLETINS & CONFIG NOTATION
    // ═══════════════════════════════════════

    // Page principale (stats + notes + bulletins)
    Route::get('/bulletins', [StaffDashboardController::class, 'BulletinIndex'])->name('bulletins');

    // Config notation
    Route::post('/bulletins/config', [StaffDashboardController::class, 'updateConfig'])
        ->name('bulletins.config.update');

    // Calculs
    Route::post('/bulletins/calculer/apprenant', [StaffDashboardController::class, 'calculerBulletinApprenant'])
        ->name('bulletins.calculer.apprenant');

    Route::post('/bulletins/calculer/classe', [StaffDashboardController::class, 'calculerBulletinsClasse'])
        ->name('bulletins.calculer.classe');

    Route::post('/bulletins/calculer/tous', [StaffDashboardController::class, 'calculerTousLesBulletins'])
        ->name('bulletins.calculer.tous');

    // Publication (classe)
    Route::post('/bulletins/publier/classe', [StaffDashboardController::class, 'publierBulletinsClasse'])
        ->name('bulletins.publier.classe');

    Route::post('/bulletins/depublier/classe', [StaffDashboardController::class, 'depublierBulletinsClasse'])
        ->name('bulletins.depublier.classe');

    // Publication individuelle
    Route::post('/bulletins/{bulletin}/publier', [StaffDashboardController::class, 'publierBulletin'])
        ->name('bulletins.publier');

    Route::post('/bulletins/{bulletin}/depublier', [StaffDashboardController::class, 'depublierBulletin'])
        ->name('bulletins.depublier');

    // Détail bulletin
    Route::get('/bulletins/{bulletin}', [StaffDashboardController::class, 'bulletinShow'])
        ->name('bulletins.show');

    // Appréciations
    Route::put('/bulletins/{bulletin}/appreciation', [StaffDashboardController::class, 'bulletinUpdateAppreciation'])
        ->name('bulletins.appreciation.update');

    Route::get('/transferts', [StaffDashboardController::class, 'TransfertIndex'])->name('transferts');

    // 🔹 TRANSFERTS (cohérent avec Blade + Controller)

    // Recherche AJAX
    Route::get('/transferts/search', [StaffDashboardController::class, 'search'])
        ->name('transfer.search');

    // Créer une demande
    Route::post('/transferts/request', [StaffDashboardController::class, 'store'])
        ->name('transfer.request');

    // Voir dossier
    Route::get('/transferts/{transfer}/dossier', [StaffDashboardController::class, 'dossier'])
        ->name('transfer.dossier');

    // Voir détail
    Route::get('/transferts/{transfer}/show', [StaffDashboardController::class, 'show'])
        ->name('transfer.show');

    // Approuver
    Route::patch('/transferts/{transfer}/approve', [StaffDashboardController::class, 'approve'])
        ->name('transfer.approve');

    // Refuser
    Route::patch('/transferts/{transfer}/reject', [StaffDashboardController::class, 'reject'])
        ->name('transfer.reject');

    // Supprimer / annuler
    Route::delete('/transferts/{transfer}', [StaffDashboardController::class, 'destroy'])
        ->name('transfer.destroy');

    // Parents
    Route::get('/parents', [StaffDashboardController::class, 'ParentIndex'])->name('parents');
    Route::post('/parents', [StaffDashboardController::class, 'ParentStore'])->name('parents.store');
    Route::get('/parents/{parent}', [StaffDashboardController::class, 'ParentShow'])->name('parents.show');
    Route::put('/parents/{parent}', [StaffDashboardController::class, 'ParentUpdate'])->name('parents.update');
    Route::delete('/parents/{parent}', [StaffDashboardController::class, 'ParentDestroy'])->name('parents.destroy');

    // Affectation
    Route::post('/parents/affect', [StaffDashboardController::class, 'affect'])->name('parents.affect');
    Route::post('/parents/detach', [StaffDashboardController::class, 'detach'])->name('parents.detach');

    // Reset password
    Route::post('/parents/{parent}/reset-password', [StaffDashboardController::class, 'ParentResetPassword'])
        ->name('parents.reset');

    // Recherche AJAX
    Route::get('/apprenants/search', [StaffDashboardController::class, 'searchApprenants'])
        ->name('apprenants.search');

    Route::get('/planning', [StaffDashboardController::class, 'PlanningIndex'])
        ->name('planning');

    // ═══════════════════════════════════════
    // 🔹 PLANNING (EDT, Séances, Paiements)
    // ═══════════════════════════════════════

    // ─── EMPLOI DU TEMPS ───
    Route::post('/edt', [StaffDashboardController::class, 'edtStore'])
        ->name('edt.store');

    Route::put('/edt/{emploiDuTemps}', [StaffDashboardController::class, 'edtUpdate'])
        ->name('edt.update');

    Route::delete('/edt/{emploiDuTemps}', [StaffDashboardController::class, 'edtDestroy'])
        ->name('edt.destroy');

    // ─── SÉANCES ───
    Route::post('/seances', [StaffDashboardController::class, 'seanceStore'])
        ->name('seance.store');

    Route::put('/seances/{seanceCours}', [StaffDashboardController::class, 'seanceUpdate'])
        ->name('seance.update');

    Route::delete('/seances/{seanceCours}', [StaffDashboardController::class, 'seanceDestroy'])
        ->name('seance.destroy');

    // ─── PROGRAMMES DE PAIEMENT ───
    Route::post('/paiements/programmes', [StaffDashboardController::class, 'paiementStore'])
        ->name('paiement.store');

    Route::put('/paiements/programmes/{programmePaiement}', [StaffDashboardController::class, 'paiementUpdate'])
        ->name('paiement.update');

    Route::delete('/paiements/programmes/{programmePaiement}', [StaffDashboardController::class, 'paiementDestroy'])
        ->name('paiement.destroy');

    // ── Saisie de notes par le staff ──
    Route::post('/grades', [StaffDashboardController::class, 'gradesStore'])->name('grades.store');
    Route::patch('/grades/{grade}', [StaffDashboardController::class, 'gradeUpdate'])->name('grades.update');
    Route::delete('/grades/{grade}', [StaffDashboardController::class, 'gradeDestroy'])->name('grades.destroy');

    // ── Gestion évaluations par le staff ──
    Route::post('/evaluations', [StaffDashboardController::class, 'evaluationStore'])->name('evaluations.store');
    Route::delete('/evaluations/{evaluation}', [StaffDashboardController::class, 'evaluationDestroy'])->name('evaluations.destroy');

    Route::post('/staff/bulletins/{bulletin}/appreciation',
        [StaffDashboardController::class, 'bulletinUpdateAppreciation']
    )->name('staff.bulletins.appreciation.update');

    // Academic
    Route::get('/academic', [StaffDashboardController::class, 'academic'])->name('academic');

    // Classes
    Route::post('/classes', [StaffDashboardController::class, 'classeStore'])->name('classes.store');
    Route::put('/classes/{classe}', [StaffDashboardController::class, 'classeUpdate'])->name('classes.update');
    Route::delete('/classes/{classe}', [StaffDashboardController::class, 'classeDestroy'])->name('classes.destroy');

    // Niveaux
    Route::post('/niveaux', [StaffDashboardController::class, 'niveauStore'])->name('niveaux.store');
    Route::put('/niveaux/{niveau}', [StaffDashboardController::class, 'niveauUpdate'])->name('niveaux.update');
    Route::delete('/niveaux/{niveau}', [StaffDashboardController::class, 'niveauDestroy'])->name('niveaux.destroy');

    // Filières
    Route::post('/filieres', [StaffDashboardController::class, 'filiereStore'])->name('filieres.store');
    Route::put('/filieres/{filiere}', [StaffDashboardController::class, 'filiereUpdate'])->name('filieres.update');
    Route::delete('/filieres/{filiere}', [StaffDashboardController::class, 'filiereDestroy'])->name('filieres.destroy');

    // Matières
    Route::post('/matieres', [StaffDashboardController::class, 'matiereStore'])->name('matieres.store');
    Route::put('/matieres/{subject}', [StaffDashboardController::class, 'matiereUpdate'])->name('matieres.update');
    Route::delete('/matieres/{subject}', [StaffDashboardController::class, 'matiereDestroy'])->name('matieres.destroy');

    // Affectations
    Route::post('/affectations/teacher-classe', [StaffDashboardController::class, 'affectationTeacherClasse'])->name('affect.teacher.classe');
    Route::delete('/affectations/teacher-classe/{teacher}/{classe}', [StaffDashboardController::class, 'affect.teacher.classe.destroy']);

    Route::post('/affectations/teacher-niveau', [StaffDashboardController::class, 'affectationTeacherNiveau'])->name('affect.teacher.niveau');
    Route::delete('/affectations/teacher-niveau/{teacher}/{niveau}', [StaffDashboardController::class, 'affect.teacher.niveau.destroy']);

    Route::post('/affectations/eleve-classe', [StaffDashboardController::class, 'affectationEleveClasse'])->name('affect.eleve.classe');
    Route::delete('/affectations/eleve-classe/{apprenant}', [StaffDashboardController::class, 'affectationEleveClasseDestroy']);

    // AJAX
    Route::get('/search/apprenants', [StaffDashboardController::class, 'searchsApprenants']);
    Route::get('/search/teachers', [StaffDashboardController::class, 'searchTeachers']);
    Route::get('/search/classes', [StaffDashboardController::class, 'searchClasses']);
}); // fin groupe staff