$ErrorActionPreference = "Stop"

Write-Host "=========================================="
Write-Host " Habilitando SQLite en PHP"
Write-Host "=========================================="

$phpIni = "C:\php\php.ini"

if (Test-Path $phpIni) {
    (Get-Content $phpIni) -replace '^;extension=pdo_sqlite', 'extension=pdo_sqlite' -replace '^;extension=sqlite3', 'extension=sqlite3' | Set-Content $phpIni -Encoding UTF8
    Write-Host "Extensiones habilitadas en $phpIni"
    Start-Sleep -Seconds 3
} else {
    Write-Host "No se encontró $phpIni"
    Start-Sleep -Seconds 5
}
