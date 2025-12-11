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

# Files and folders to exclude (exact folder names or file patterns)
$excludeFolders = @(
    "node_modules",
    "src-svelte",
    ".git",
    ".claude",
    "plugin"
)

$excludeFiles = @(
    "*.md",
    ".gitignore",
    "package.json",
    "package-lock.json",
    "vite.config.js",
    "tsconfig.json",
    "*.config.js",
    "*.config.ts",
    "create-plugin-zip.ps1"
)

# Load compression assembly
Add-Type -Assembly System.IO.Compression
Add-Type -Assembly System.IO.Compression.FileSystem

# Create ZIP file with forward slashes
$zipFile = [System.IO.Compression.ZipFile]::Open($zipPath, [System.IO.Compression.ZipArchiveMode]::Create)

try {
    # Get all files
    $files = Get-ChildItem -Path $pluginDir -Recurse -File

    foreach ($file in $files) {
        $relativePath = $file.FullName.Substring($pluginDir.Length + 1)
        $exclude = $false

        # Check if file is in an excluded folder
        foreach ($folder in $excludeFolders) {
            if ($relativePath -like "$folder\*" -or $relativePath -like "*\$folder\*") {
                $exclude = $true
                break
            }
        }

        # Check if file matches an excluded file pattern
        if (!$exclude) {
            foreach ($pattern in $excludeFiles) {
                if ($file.Name -like $pattern) {
                    $exclude = $true
                    break
                }
            }
        }

        if (!$exclude) {
            # Create entry path with forward slashes: pluginname/path/to/file
            $entryPath = "$pluginName/$($relativePath -replace '\\', '/')"

            # Add file to ZIP
            [System.IO.Compression.ZipFileExtensions]::CreateEntryFromFile(
                $zipFile,
                $file.FullName,
                $entryPath,
                [System.IO.Compression.CompressionLevel]::Optimal
            ) | Out-Null
        }
    }
}
finally {
    $zipFile.Dispose()
}

Write-Host "Created: $zipPath"
Write-Host "Plugin: $pluginName v$version"
