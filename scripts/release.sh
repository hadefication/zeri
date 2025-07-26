#!/bin/bash

# GitHub Release Script for Zeri
# Usage: ./scripts/release.sh [version]
# Example: ./scripts/release.sh v1.0.1

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

log_info() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

log_warn() {
    echo -e "${YELLOW}[WARN]${NC} $1"
}

log_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Get version from argument or prompt
VERSION=${1:-}
if [[ -z "$VERSION" ]]; then
    echo -n "Enter version (e.g., v1.0.1): "
    read VERSION
fi

# Validate version format
if [[ ! "$VERSION" =~ ^v[0-9]+\.[0-9]+\.[0-9]+$ ]]; then
    log_warn "Version should follow format v1.0.1"
    echo -n "Continue anyway? [y/N]: "
    read -n 1 -r
    echo
    if [[ ! $REPLY =~ ^[Yy]$ ]]; then
        log_info "Release cancelled."
        exit 0
    fi
fi

log_info "Creating release $VERSION..."

# Check if we're in a git repository
if ! git rev-parse --git-dir > /dev/null 2>&1; then
    log_error "Not in a git repository"
    exit 1
fi

# Check if gh CLI is installed and authenticated
if ! command -v gh &> /dev/null; then
    log_error "GitHub CLI (gh) is required but not installed"
    log_error "Install with: brew install gh"
    exit 1
fi

if ! gh auth status &> /dev/null; then
    log_error "GitHub CLI not authenticated"
    log_error "Run: gh auth login"
    exit 1
fi

# Check if binary exists
BINARY_PATH="builds/zeri"
if [[ ! -f "$BINARY_PATH" ]]; then
    log_error "Binary not found at $BINARY_PATH"
    log_error "Run ./build.sh first"
    exit 1
fi

# Check if tag already exists
if git tag -l | grep -q "^$VERSION$"; then
    log_warn "Tag $VERSION already exists"
    echo -n "Delete and recreate? [y/N]: "
    read -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        git tag -d "$VERSION"
        git push origin --delete "$VERSION" 2>/dev/null || true
    else
        log_info "Release cancelled."
        exit 0
    fi
fi

# Update version in config files
log_info "Updating version in config files..."
sed -i '' "s/'version' => '.*'/'version' => '${VERSION#v}'/" config/app.php

# Commit version update
git add config/app.php
git commit -m "Bump version to $VERSION" || log_warn "No changes to commit"

# Create and push tag
log_info "Creating git tag $VERSION..."
git tag "$VERSION"
git push origin main
git push origin "$VERSION"

# Create GitHub release with binary
log_info "Creating GitHub release..."
gh release create "$VERSION" \
    --title "Zeri $VERSION" \
    --notes "Release $VERSION" \
    "$BINARY_PATH#zeri"

log_info "✅ Release $VERSION created successfully!"
log_info "📍 Release URL: $(gh release view "$VERSION" --web)"
log_info "🔧 Install command: curl -fsSL https://raw.githubusercontent.com/hadefication/zeri/main/scripts/install.sh | bash"

# Test the installation
echo
echo -n "Test installation script? [y/N]: "
read -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    log_info "Testing installation script..."
    sleep 5  # Wait for GitHub to propagate
    bash -c "curl -fsSL https://raw.githubusercontent.com/hadefication/zeri/main/scripts/install.sh | bash"
fi