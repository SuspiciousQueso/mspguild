# MSP Portal Quick Setup Script
# This script helps automate the setup process

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  MSP Customer Portal - Setup Wizard  " -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Check if PHP is installed
Write-Host "Checking for PHP..." -ForegroundColor Yellow
$phpVersion = php -v 2>$null
if ($LASTEXITCODE -eq 0) {
    Write-Host "✓ PHP is installed" -ForegroundColor Green
    Write-Host $phpVersion[0] -ForegroundColor Gray
} else {
    Write-Host "✗ PHP is not installed or not in PATH" -ForegroundColor Red
    Write-Host "Please install XAMPP or PHP 8+ to continue" -ForegroundColor Yellow
    exit 1
}

# Check if MySQL is accessible
Write-Host "`nChecking for MySQL..." -ForegroundColor Yellow
$mysqlCheck = mysql --version 2>$null
if ($LASTEXITCODE -eq 0) {
    Write-Host "✓ MySQL is installed" -ForegroundColor Green
} else {
    Write-Host "⚠ MySQL command not found in PATH" -ForegroundColor Yellow
    Write-Host "Make sure MySQL/MariaDB is running" -ForegroundColor Gray
}

Write-Host "`n========================================" -ForegroundColor Cyan
Write-Host "  Setup Options  " -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "1. Quick Start (PHP Built-in Server)" -ForegroundColor White
Write-Host "   - Best for quick testing" -ForegroundColor Gray
Write-Host "   - Requires MySQL running separately" -ForegroundColor Gray
Write-Host ""
Write-Host "2. View Setup Instructions" -ForegroundColor White
Write-Host "   - Full installation guide" -ForegroundColor Gray
Write-Host ""
Write-Host "3. Open Project Folder" -ForegroundColor White
Write-Host "   - Browse project files" -ForegroundColor Gray
Write-Host ""
Write-Host "4. Exit" -ForegroundColor White
Write-Host ""

$choice = Read-Host "Select an option (1-4)"

switch ($choice) {
    "1" {
        Write-Host "`nStarting PHP development server..." -ForegroundColor Yellow
        Write-Host "Server will run at: http://localhost:8000" -ForegroundColor Green
        Write-Host ""
        Write-Host "IMPORTANT: Make sure MySQL is running!" -ForegroundColor Yellow
        Write-Host "- XAMPP: Start MySQL from Control Panel" -ForegroundColor Gray
        Write-Host "- Import sql/schema.sql via phpMyAdmin" -ForegroundColor Gray
        Write-Host ""
        Write-Host "Press Ctrl+C to stop the server" -ForegroundColor Gray
        Write-Host ""
        
        Set-Location -Path "$PSScriptRoot\public"
        php -S localhost:8000
    }
    "2" {
        Write-Host "`nOpening README.md..." -ForegroundColor Yellow
        Start-Process "$PSScriptRoot\README.md"
    }
    "3" {
        Write-Host "`nOpening project folder..." -ForegroundColor Yellow
        Start-Process $PSScriptRoot
    }
    "4" {
        Write-Host "`nExiting..." -ForegroundColor Gray
        exit 0
    }
    default {
        Write-Host "`nInvalid option. Please run the script again." -ForegroundColor Red
        exit 1
    }
}
