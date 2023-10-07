# Author: Moritz Dahlke
# Date: 2023-08-18

# Specify 7-Zip path
$SEVENZIP_PATH = "E:\Programme\7-Zip\7z.exe"

# Check if 7-Zip exists
if (-not (Test-Path -Path $SEVENZIP_PATH -PathType Leaf)) {
    Write-Host "7-Zip not found. Aborting..." -ForegroundColor DarkRed -BackgroundColor Red
    PAUSE
    exit
}

# Set 7-Zip alias
Set-Alias SEVENZIP $SEVENZIP_PATH

# Save current location
$MAIN_DIR = Get-Location

# get all subfolder in the current directory except the .github folder
$SUBFOLDERS = Get-ChildItem -Directory | Where-Object { $_.Name -ne ".github" }

# Get all subfolders in the current directory
$SUBFOLDERS | ForEach-Object {
    # Check if the subfolder contains a package.xml file
    if (-not(Test-Path "$($_.FullName)\package.xml")) {
        continue
    }

    # Set the package directory and name
    $PACKAGE_DIR = $_.FullName
    $PACKAGE_NAME = $_.Name

    # Change the current directory to the package directory
    Set-Location $PACKAGE_DIR

    # Extract the version number from the package.xml file
    $PACKAGE_VERSION = (Get-Content package.xml | Select-String -Pattern "<version>(.*)</version>" | ForEach-Object { $_.Matches.Groups[1].Value })

    # Trim the version number and replace spaces with underscores
    $PACKAGE_VERSION = $PACKAGE_VERSION.Trim().Replace(" ", "_")

    # Replace uppercase letters with lowercase letters
    $PACKAGE_VERSION = $PACKAGE_VERSION.ToLower()

    # Log package build to console
    Write-Host "Building package $PACKAGE_NAME v$PACKAGE_VERSION" -ForegroundColor DarkYellow -BackgroundColor Yellow

    # Set the build name
    $BUILD_NAME = $PACKAGE_NAME + "_v" + $PACKAGE_VERSION

    # Delete files from previous builds if they exist
    if (Test-Path "$PACKAGE_DIR\files.tar") {
        Remove-Item -Path "$PACKAGE_DIR\files.tar" -Force
    }

    if (Test-Path "$PACKAGE_DIR\templates.tar") {
        Remove-Item -Path "$PACKAGE_DIR\templates.tar" -Force
    }

    if (Test-Path "$PACKAGE_DIR\acptemplates.tar") {
        Remove-Item -Path "$PACKAGE_DIR\acptemplates.tar" -Force
    }

    if (Test-Path "$PACKAGE_DIR\$PACKAGE_NAME*.tar") {
        Remove-Item -Path "$PACKAGE_DIR\$PACKAGE_NAME*.tar" -Force
    }

    if (Test-Path "$PACKAGE_DIR\$PACKAGE_NAME*.tar.gz") {
        Remove-Item -Path "$PACKAGE_DIR\$PACKAGE_NAME*.tar.gz" -Force
    }

    # Create a tar file from the files directory
    if (Test-Path "$PACKAGE_DIR\files") {
        SEVENZIP a -ttar -mx=9 files.tar .\files\*
        Move-Item "files.tar" "$PACKAGE_DIR"
        Set-Location $PACKAGE_DIR
    }

    # Create a tar file from the templates directory
    if (Test-Path "$PACKAGE_DIR\templates") {
        SEVENZIP a -ttar -mx=9 templates.tar .\templates\*
        Move-Item "templates.tar" "$PACKAGE_DIR"
        Set-Location $PACKAGE_DIR
    }

    # Create a tar file from the templates directory
    if (Test-Path "$PACKAGE_DIR\acptemplates") {
        SEVENZIP a -ttar -mx=9 acptemplates.tar .\acptemplates\*
        Move-Item "acptemplates.tar" "$PACKAGE_DIR"
        Set-Location $PACKAGE_DIR
    }

    # Create the package file
    SEVENZIP a -ttar -mx=9 "$BUILD_NAME.tar" .\* -x!files -x!templates -x!acptemplates
    SEVENZIP a -tgzip "$BUILD_NAME.tar.gz" "$BUILD_NAME.tar"

    # Delete the tar files
    if (Test-Path "$PACKAGE_DIR\files.tar") {
        Remove-Item -Path "$PACKAGE_DIR\files.tar" -Force
    }

    if (Test-Path "$PACKAGE_DIR\templates.tar") {
        Remove-Item -Path "$PACKAGE_DIR\templates.tar" -Force
    }

    if (Test-Path "$PACKAGE_DIR\acptemplates.tar") {
        Remove-Item -Path "$PACKAGE_DIR\acptemplates.tar" -Force
    }

    if (Test-Path "$PACKAGE_DIR\$BUILD_NAME.tar") {
        Remove-Item -Path "$PACKAGE_DIR\$BUILD_NAME.tar" -Force
    }

    # Delete the previous build from main directory if it exists
    if (Test-Path "$MAIN_DIR\$PACKAGE_NAME*.tar.gz") {
        Remove-Item -Path "$MAIN_DIR\$PACKAGE_NAME*.tar.gz" -Force
    }

    # Move the package tar file to the main directory
    Move-Item "$BUILD_NAME.tar.gz" $MAIN_DIR

    # Log package build to console
    Write-Host "Package $PACKAGE_NAME v$PACKAGE_VERSION built successfully" -ForegroundColor DarkGreen -BackgroundColor Green
}

# Change the current directory back to the main directory
Set-Location $MAIN_DIR

# Print a completion message to the console
Write-Host "All builds complete!" -ForegroundColor DarkGreen -BackgroundColor Green

PAUSE