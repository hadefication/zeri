#!/bin/bash

# Zeri Installation Script
# Supports: Ubuntu/Debian (apt), CentOS/RHEL/Fedora (yum/dnf), Arch (pacman), macOS (brew)

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

# Install PHP if not present
install_php() {
    if ! command -v php &> /dev/null; then
        log_info "PHP not found. Installing PHP..."
        case $OS in
            "debian")
                sudo apt update && sudo apt install -y php-cli php-mbstring php-xml
                ;;
            "redhat")
                if command -v dnf &> /dev/null; then
                    sudo dnf install -y php-cli php-mbstring php-xml
                else
                    sudo yum install -y php-cli php-mbstring php-xml
                fi
                ;;
            "arch")
                sudo pacman -S --noconfirm php
                ;;
            "macos")
                log_info "Please install PHP manually on macOS:"
                log_info "- Using Homebrew: brew install php"
                log_info "- Using MacPorts: sudo port install php82"
                exit 1
                ;;
            *)
                log_error "Unsupported OS. Please install PHP 8.2+ manually."
                exit 1
                ;;
        esac
    else
        log_info "PHP found: $(php --version | head -n1)"
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

# Install via Composer
install_composer() {
    if command -v composer &> /dev/null; then
        log_info "Installing via Composer..."
        composer global require $REPO
        
        # Check if composer global bin is in PATH
        if [[ ":$PATH:" != *":$HOME/.composer/vendor/bin:"* ]] && [[ ":$PATH:" != *":$HOME/.config/composer/vendor/bin:"* ]]; then
            log_warn "Composer global bin directory is not in PATH."
            log_warn "Add this to your shell profile:"
            log_warn "export PATH=\"\$HOME/.composer/vendor/bin:\$PATH\""
        fi
    else
        log_warn "Composer not found. Falling back to binary installation."
        return 1
    fi
}


# Main installation logic
main() {
    log_info "Starting Zeri installation..."
    
    detect_os
    log_info "Detected OS: $OS"
    
    install_php
    
    
    # Try Composer installation
    if install_composer; then
        log_info "✅ Zeri installed successfully via Composer!"
        exit 0
    fi
    
    # Fallback to binary installation
    log_info "Installing via direct binary download..."
    
    version=$(get_latest_release)
    if [[ -z "$version" ]]; then
        log_error "Failed to get latest release version"
        exit 1
    fi
    
    install_binary "$version"
    
    log_info "✅ Zeri $version installed successfully!"
    log_info "Run 'zeri --help' to get started."
}

# Check if running as root (for some package managers)
if [[ $EUID -eq 0 ]]; then
    log_warn "Running as root. Some package managers may not work correctly."
fi

main "$@"