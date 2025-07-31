#!/bin/bash

# Zeri Update Script
# Updates existing Zeri installation to the latest version from GitHub releases
# Supports: Linux and macOS

set -e

REPO="hadefication/zeri"
BINARY_NAME="zeri"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Helper functions
log_info() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

log_warn() {
    echo -e "${YELLOW}[WARN]${NC} $1"
}

log_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

log_update() {
    echo -e "${BLUE}[UPDATE]${NC} $1"
}

# Check system requirements
check_requirements() {
    # Check if we can download files
    if ! command -v curl &> /dev/null; then
        log_error "curl is required but not installed."
        log_error "Please install curl and try again."
        exit 1
    fi
}

# Check if Zeri is installed
check_zeri_installed() {
    if ! command -v zeri &> /dev/null; then
        log_error "Zeri is not installed on this system."
        log_error "Use the installation script instead:"
        log_error "curl -sSL https://raw.githubusercontent.com/$REPO/main/scripts/install.sh | bash"
        exit 1
    fi
    
    # Get current installation path
    CURRENT_PATH=$(which zeri)
    log_info "Found Zeri installation: $CURRENT_PATH"
    
    # Get current version
    CURRENT_VERSION=$(zeri --version 2>/dev/null | grep -o 'Zeri [0-9.]*' | cut -d' ' -f2 || echo 'unknown')
    log_info "Current version: $CURRENT_VERSION"
    
    # Check if we have write access to the installation directory
    INSTALL_DIR=$(dirname "$CURRENT_PATH")
    if [[ ! -w "$INSTALL_DIR" ]] && ! sudo -n true 2>/dev/null; then
        log_warn "Update requires sudo access to write to $INSTALL_DIR"
        log_warn "You may be prompted for your password."
    fi
}

# Get latest release info
get_latest_release() {
    local response=$(curl -s "https://api.github.com/repos/$REPO/releases/latest")
    
    # Check if the API call was successful
    if [[ -z "$response" ]]; then
        log_error "Failed to fetch release information from GitHub API"
        return 1
    fi
    
    # Check if we got an error response
    if echo "$response" | grep -q '"message".*"Not Found"'; then
        log_error "No releases found for $REPO"
        return 1
    fi
    
    # Extract tag name and remove 'v' prefix if present
    local tag_name=$(echo "$response" | grep '"tag_name":' | sed -E 's/.*"([^"]+)".*/\1/')
    local version=$(echo "$tag_name" | sed 's/^v//')
    
    if [[ -z "$version" ]]; then
        log_error "Could not parse release version from GitHub API response"
        return 1
    fi
    
    echo "$version"
}

# Compare versions (simple string comparison for semantic versions)
version_greater() {
    local version1="$1"
    local version2="$2"
    
    # Handle unknown version
    if [[ "$version1" == "unknown" ]]; then
        return 0  # Assume update is needed
    fi
    
    # Use sort -V for version comparison
    if printf '%s\n%s\n' "$version1" "$version2" | sort -V -C; then
        return 1  # version1 <= version2
    else
        return 0  # version1 > version2
    fi
}

# Create backup of current installation
create_backup() {
    local backup_path="${CURRENT_PATH}.backup.$(date +%Y%m%d_%H%M%S)"
    log_info "Creating backup: $backup_path"
    
    if [[ -w "$(dirname "$CURRENT_PATH")" ]]; then
        cp "$CURRENT_PATH" "$backup_path"
    else
        sudo cp "$CURRENT_PATH" "$backup_path"
    fi
    
    echo "$backup_path"
}

# Download and install new version
update_binary() {
    local version="$1"
    local backup_path="$2"
    local temp_dir=$(mktemp -d)
    local download_url="https://github.com/$REPO/releases/download/v$version/zeri"
    
    log_update "Downloading Zeri v$version..."
    
    # Download with error handling
    if ! curl -L "$download_url" -o "$temp_dir/$BINARY_NAME" --fail --silent --show-error; then
        log_error "Failed to download binary from GitHub releases"
        log_error "Download URL: $download_url"
        rm -rf "$temp_dir"
        exit 1
    fi
    
    # Verify the download
    if [[ ! -f "$temp_dir/$BINARY_NAME" ]] || [[ ! -s "$temp_dir/$BINARY_NAME" ]]; then
        log_error "Downloaded file is missing or empty"
        rm -rf "$temp_dir"
        exit 1
    fi
    
    # Check if it's a valid binary
    if ! file "$temp_dir/$BINARY_NAME" | grep -q "executable"; then
        log_error "Downloaded file does not appear to be an executable"
        log_error "File type: $(file "$temp_dir/$BINARY_NAME")"
        rm -rf "$temp_dir"
        exit 1
    fi
    
    chmod +x "$temp_dir/$BINARY_NAME"
    
    log_update "Installing new version..."
    
    # Replace the binary
    if [[ -w "$(dirname "$CURRENT_PATH")" ]]; then
        mv "$temp_dir/$BINARY_NAME" "$CURRENT_PATH"
    else
        sudo mv "$temp_dir/$BINARY_NAME" "$CURRENT_PATH"
    fi
    
    rm -rf "$temp_dir"
    
    # Verify the update
    local new_version=$(zeri --version 2>/dev/null | grep -o 'Zeri [0-9.]*' | cut -d' ' -f2 || echo 'unknown')
    if [[ "$new_version" == "$version" ]]; then
        log_info "✅ Successfully updated to Zeri v$version!"
        log_info "Location: $CURRENT_PATH"
        
        # Check if self-update is now available
        if zeri list 2>/dev/null | grep -q "self-update"; then
            log_info "🔄 Self-update is now available! Use 'zeri self-update' for future updates."
        fi
        
        # Clean up backup on successful update
        if [[ -f "$backup_path" ]]; then
            if [[ -w "$(dirname "$backup_path")" ]]; then
                rm "$backup_path"
            else
                sudo rm "$backup_path"
            fi
            log_info "Backup removed: $backup_path"
        fi
    else
        log_error "Update verification failed. Version mismatch: expected $version, got $new_version"
        
        # Restore backup
        if [[ -f "$backup_path" ]]; then
            log_warn "Restoring backup..."
            if [[ -w "$(dirname "$CURRENT_PATH")" ]]; then
                mv "$backup_path" "$CURRENT_PATH"
            else
                sudo mv "$backup_path" "$CURRENT_PATH"
            fi
            log_info "Backup restored successfully"
        fi
        exit 1
    fi
}

# Main update logic
main() {
    local force_update=false
    
    # Parse command line arguments
    while [[ $# -gt 0 ]]; do
        case $1 in
            --force)
                force_update=true
                shift
                ;;
            --help|-h)
                echo "Zeri Update Script"
                echo ""
                echo "Usage: $0 [options]"
                echo ""
                echo "Options:"
                echo "  --force    Force update even if already on latest version"
                echo "  --help     Show this help message"
                echo ""
                echo "This script updates an existing Zeri installation to the latest version."
                echo "If Zeri is not installed, use the install script instead:"
                echo "curl -sSL https://raw.githubusercontent.com/$REPO/main/scripts/install.sh | bash"
                exit 0
                ;;
            *)
                log_error "Unknown option: $1"
                log_error "Use --help for usage information"
                exit 1
                ;;
        esac
    done
    
    log_info "🚀 Starting Zeri update process..."
    echo
    
    # Check system requirements
    check_requirements
    
    # Check if Zeri is installed and get current info
    check_zeri_installed
    
    # Get latest release version
    log_info "🔍 Checking for latest version..."
    if ! latest_version=$(get_latest_release); then
        log_error "Cannot fetch latest version information"
        exit 1
    fi
    
    log_info "Latest version: $latest_version"
    echo
    
    # Compare versions
    if ! $force_update && ! version_greater "$latest_version" "$CURRENT_VERSION"; then
        log_info "✅ You already have the latest version ($CURRENT_VERSION)"
        log_info "Use --force to reinstall the same version"
        exit 0
    fi
    
    if $force_update; then
        log_warn "🔄 Force update requested"
    else
        log_update "📦 Update available: $CURRENT_VERSION → $latest_version"
    fi
    
    # Confirm update
    if [[ -t 0 ]] && ! $force_update; then  # Only prompt if running interactively
        read -p "Do you want to proceed with the update? [Y/n]: " -n 1 -r
        echo
        if [[ $REPLY =~ ^[Nn]$ ]]; then
            log_info "Update cancelled by user"
            exit 0
        fi
    fi
    
    # Create backup
    backup_path=$(create_backup)
    
    # Perform update
    update_binary "$latest_version" "$backup_path"
    
    echo
    log_info "🎉 Update completed successfully!"
    log_info "Run 'zeri --help' to see available commands"
}

# Handle script interruption
trap 'log_error "Update interrupted"; exit 1' INT TERM

main "$@"