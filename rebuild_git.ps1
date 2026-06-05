# rebuild_git.ps1
# Script to rebuild Git history in a logical, step-by-step sequence of descriptive commits.

Write-Host "Saving remote URL..." -ForegroundColor Green
$remoteUrl = "https://github.com/azilputra8825-glitch/turnamen"

# 1. Remove old Git configuration
if (Test-Path -Path ".git") {
    Write-Host "Removing existing .git folder..." -ForegroundColor Yellow
    Remove-Item -Path ".git" -Recurse -Force
}

# 2. Re-initialize Git
Write-Host "Initializing new Git repository..." -ForegroundColor Green
git init

# Configure git user if not configured locally (to prevent commit failures)
git config user.name "Azil Putra"
git config user.email "azilputra8825@gmail.com"

# 3. Commit initial project configuration
Write-Host "Staging initial Laravel structure..." -ForegroundColor Cyan
git add package.json composer.json vite.config.js tailwind.config.js postcss.config.js .gitignore .gitattributes .editorconfig artisan config/ public/ bootstrap/
git commit -m "feat: inisialisasi project Laravel 11 dan konfigurasi awal"

# 4. Commit Database migrations & seeders
Write-Host "Staging migrations..." -ForegroundColor Cyan
git add database/
git commit -m "feat: menambahkan migration untuk tabel turnamen, peserta, dan pertandingan"

# 5. Commit Models
Write-Host "Staging Models..." -ForegroundColor Cyan
git add app/Models/
git commit -m "feat: membuat Eloquent model dan relasi database"

# 6. Commit Breeze Authentication
Write-Host "Staging Authentication templates..." -ForegroundColor Cyan
if (Test-Path -Path "app/Http/Controllers/Auth") { git add app/Http/Controllers/Auth/ }
if (Test-Path -Path "app/Http/Requests/Auth") { git add app/Http/Requests/Auth/ }
if (Test-Path -Path "resources/views/auth") { git add resources/views/auth/ }
if (Test-Path -Path "resources/views/profile") { git add resources/views/profile/ }
git add routes/auth.php
git commit -m "feat: integrasi Laravel Breeze untuk autentikasi"

# 7. Commit Admin Middleware
Write-Host "Staging Admin Middleware..." -ForegroundColor Cyan
if (Test-Path -Path "app/Http/Middleware") { git add app/Http/Middleware/ }
git add bootstrap/app.php
git commit -m "feat: proteksi halaman admin menggunakan middleware EnsureUserIsAdmin"

# 8. Commit main controllers
Write-Host "Staging Controllers..." -ForegroundColor Cyan
git add app/Http/Controllers/ParticipantController.php
git add app/Http/Controllers/TournamentController.php
git add app/Http/Controllers/MatchController.php
git add app/Http/Controllers/RankingController.php
git commit -m "feat: membuat controllers untuk manajemen turnamen, pertandingan, dan peserta"

# 9. Commit standard views and routes
Write-Host "Staging views and web routes..." -ForegroundColor Cyan
git add resources/views/layouts/
git add resources/views/layout/
git add resources/views/participants/
git add resources/views/tournaments/
git add resources/views/matches/
git add resources/views/rankings/
git add resources/views/welcome.blade.php
git add routes/web.php
git commit -m "feat: implementasi views dan layout responsif Bootstrap 5"

# 10. Commit dashboard & analytics (Chart.js)
Write-Host "Staging dashboard..." -ForegroundColor Cyan
git add resources/views/dashboard.blade.php
git commit -m "feat: membuat dashboard analytics dengan grafik Chart.js"

# 11. Commit final AJAX live search & CSV export updates
Write-Host "Staging remaining updates (AJAX & export)..." -ForegroundColor Cyan
git add -A
git commit -m "feat: implementasi AJAX live search dan export data peserta ke CSV"

# 12. Restore GitHub remote URL
Write-Host "Restoring remote origin..." -ForegroundColor Green
git remote add origin $remoteUrl

Write-Host "Git history successfully rebuilt! Run 'git log --oneline' to verify." -ForegroundColor Green
Write-Host "To push this clean history to GitHub, execute:" -ForegroundColor Yellow
Write-Host "  git push -u origin main --force" -ForegroundColor Yellow
