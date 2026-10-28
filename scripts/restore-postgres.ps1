param(
    [Parameter(Mandatory = $true)][string]$BackupPath,
    [switch]$ConfirmRestore,
    [string]$DatabaseHost = "127.0.0.1",
    [int]$Port = 5432,
    [string]$Database = "buktravels",
    [string]$Username = "buktravels"
)

$ErrorActionPreference = "Stop"
if (-not $ConfirmRestore) { throw "Restore replaces database objects. Re-run with -ConfirmRestore after checking the target database." }
if (-not (Get-Command pg_restore -ErrorAction SilentlyContinue)) { throw "pg_restore was not found. Install PostgreSQL client tools and add them to PATH." }

$resolvedBackup = (Resolve-Path -LiteralPath $BackupPath).Path
if ([System.IO.Path]::GetExtension($resolvedBackup) -ne ".dump") { throw "Expected a .dump file created by backup-postgres.ps1." }

& pg_restore --host=$DatabaseHost --port=$Port --username=$Username --dbname=$Database --clean --if-exists --no-owner $resolvedBackup
if ($LASTEXITCODE -ne 0) { throw "pg_restore failed with exit code $LASTEXITCODE." }

Write-Output "Restore completed for database '$Database' from '$resolvedBackup'."
