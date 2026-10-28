param(
    [string]$DatabaseHost = "127.0.0.1",
    [int]$Port = 5432,
    [string]$Database = "buktravels",
    [string]$Username = "buktravels",
    [string]$OutputDirectory = (Join-Path $PSScriptRoot "..\backups")
)

$ErrorActionPreference = "Stop"
if (-not (Get-Command pg_dump -ErrorAction SilentlyContinue)) {
    throw "pg_dump was not found. Install the PostgreSQL client tools and add them to PATH."
}

$resolvedOutput = [System.IO.Path]::GetFullPath($OutputDirectory)
New-Item -ItemType Directory -Force -Path $resolvedOutput | Out-Null
$timestamp = Get-Date -Format "yyyyMMdd-HHmmss"
$backupPath = Join-Path $resolvedOutput "$Database-$timestamp.dump"

& pg_dump --host=$DatabaseHost --port=$Port --username=$Username --dbname=$Database --format=custom --file=$backupPath
if ($LASTEXITCODE -ne 0) { throw "pg_dump failed with exit code $LASTEXITCODE." }

Write-Output "Backup created: $backupPath"
