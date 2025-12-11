# Create WordPress Plugin ZIP with proper UNIX/Linux directory structure
# This script creates a ZIP file with forward-slash paths for cross-platform compatibility

$pluginDir = Split-Path -Parent $MyInvocation.MyCommand.Path
$pluginName = Split-Path -Leaf $pluginDir

# Get version from main plugin file
$mainFile = Get-Content "$pluginDir\$pluginName.php" -Raw
if ($mainFile -match "Version:\s*([0-9a-zA-Z.-]+)") {
    $version = $matches[1]
} else {
    $version = "1.0.0"
}

$zipName = "$pluginName-$version.zip"
$outputDir = "$pluginDir\plugin"
$zipPath = "$outputDir\$zipName"

# Create output directory if needed
if (!(Test-Path $outputDir)) {
    New-Item -ItemType Directory -Path $outputDir | Out-Null
}

# Remove old ZIP files
Get-ChildItem "$outputDir\*.zip" | Remove-Item -Force

# Files and folders to exclude
$excludePatterns = @(
    "*.md",
    "node_modules",
    "src-svelte",
    ".git",
    ".gitignore",
    "package.json",
    "package-lock.json",
    "vite.config.js",
    "tsconfig.json",
    "*.config.js",
    "*.config.ts",
    "plugin",
    "create-plugin-zip.ps1",
    ".claude"
)

# Create temporary directory for ZIP contents
$tempDir = "$env:TEMP\$pluginName-zip-temp"
if (Test-Path $tempDir) {
    Remove-Item -Recurse -Force $tempDir
}
New-Item -ItemType Directory -Path "$tempDir\$pluginName" | Out-Null

# Copy files excluding patterns
$files = Get-ChildItem -Path $pluginDir -Recurse -File
foreach ($file in $files) {
    $relativePath = $file.FullName.Substring($pluginDir.Length + 1)
    $exclude = $false

    foreach ($pattern in $excludePatterns) {
        if ($relativePath -like "*$pattern*" -or $file.Name -like $pattern) {
            $exclude = $true
            break
        }
    }

    if (!$exclude) {
        $destPath = "$tempDir\$pluginName\$relativePath"
        $destDir = Split-Path -Parent $destPath
        if (!(Test-Path $destDir)) {
            New-Item -ItemType Directory -Path $destDir -Force | Out-Null
        }
        Copy-Item $file.FullName -Destination $destPath
    }
}

# Create ZIP using .NET with forward slashes
Add-Type -Assembly System.IO.Compression.FileSystem
$compressionLevel = [System.IO.Compression.CompressionLevel]::Optimal

if (Test-Path $zipPath) {
    Remove-Item $zipPath -Force
}

[System.IO.Compression.ZipFile]::CreateFromDirectory($tempDir, $zipPath, $compressionLevel, $false)

# Clean up temp directory
Remove-Item -Recurse -Force $tempDir

Write-Host "Created: $zipPath"
Write-Host "Plugin: $pluginName v$version"
