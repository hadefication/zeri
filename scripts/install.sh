#!/bin/bash

# Zeri Installation Script
# Downloads and installs the latest Zeri binary from GitHub releases
# Supports: Linux and macOS

set -e

REPO="hadefication/zeri"
BINARY_NAME="zeri"
INSTALL_DIR="/usr/local/bin"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
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

# Detect OS and package manager
detect_os() {
    if [[ "$OSTYPE" == "darwin"* ]]; then
        OS="macos"
    elif [[ -f /etc/debian_version ]]; then
        OS="debian"
    elif [[ -f /etc/redhat-release ]]; then
        OS="redhat"
    elif [[ -f /etc/arch-release ]]; then
        OS="arch"
    else
        OS="unknown"
    fi
}

# Check system requirements
check_requirements() {
    # Check if we can download files
    if ! command -v curl &> /dev/null; then
        log_error "curl is required but not installed."
        log_error "Please install curl and try again."
        exit 1
    fi
    
    # Check if we have sudo access for installation
    if [[ ! -w "$INSTALL_DIR" ]] && ! sudo -n true 2>/dev/null; then
        log_warn "Installation requires sudo access to write to $INSTALL_DIR"
        log_warn "You may be prompted for your password."
    fi
}

# Get latest release info
get_latest_release() {
    curl -s "https://api.github.com/repos/$REPO/releases/latest" | grep '"tag_name":' | sed -E 's/.*"([^"]+)".*/\1/'
}

# Download and install binary
install_binary() {
    local version="$1"
    local temp_dir=$(mktemp -d)
    
    log_info "Downloading Zeri $version..."
    
    curl -L "https://github.com/$REPO/releases/download/$version/zeri" -o "$temp_dir/$BINARY_NAME"
    
    if [[ ! -f "$temp_dir/$BINARY_NAME" ]]; then
        log_error "Failed to download binary"
        exit 1
    fi
    
    chmod +x "$temp_dir/$BINARY_NAME"
    
    log_info "Installing to $INSTALL_DIR..."
    sudo mv "$temp_dir/$BINARY_NAME" "$INSTALL_DIR/"
    
    rm -rf "$temp_dir"
}

# Check if Zeri is already installed
check_existing() {
    if command -v zeri &> /dev/null; then
        log_warn "Zeri is already installed: $(which zeri)"
        log_warn "Current version: $(zeri --version 2>/dev/null || echo 'unknown')"
        
        if [[ "$1" == "--force" ]]; then
            log_info "Force flag provided, continuing with installation..."
            return 1
        else
            read -p "Do you want to reinstall? [y/N]: " -n 1 -r
            echo
            if [[ ! $REPLY =~ ^[Yy]$ ]]; then
                log_info "Installation cancelled."
                exit 0
            fi
        fi
    fi
    return 1
}


# Main installation logic
main() {
    log_info "Starting Zeri installation..."
    
    detect_os
    log_info "Detected OS: $OS"
    
    # Check system requirements
    check_requirements
    
    # Check if already installed
    check_existing "$1"
    
    # Get latest release version
    log_info "Fetching latest release information..."
    version=$(get_latest_release)
    if [[ -z "$version" ]]; then
        log_error "Failed to get latest release version"
        log_error "Please check your internet connection or install manually:"
        log_error "https://github.com/$REPO/releases/latest"
        exit 1
    fi
    
    log_info "Latest version: $version"
    
    # Install binary directly
    install_binary "$version"
    
    # Verify installation
    if command -v zeri &> /dev/null; then
        log_info "✅ Zeri $version installed successfully!"
        log_info "Location: $(which zeri)"
        log_info "Run 'zeri --help' to get started."
    else
        log_error "Installation completed but 'zeri' command not found in PATH"
        log_error "You may need to restart your terminal or add $INSTALL_DIR to your PATH"
        exit 1
    fi
}

# Check if running as root (for some package managers)
if [[ $EUID -eq 0 ]]; then
    log_warn "Running as root. Some package managers may not work correctly."
fi

main "$@"