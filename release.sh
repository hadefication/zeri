#!/bin/bash

# Zeri Release Script
# Automates the complete release process including version updates, building, tagging, and GitHub release

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
PURPLE='\033[0;35m'
NC='\033[0m' # No Color

# Global dry-run flag
DRY_RUN=false

# Print colored output
print_info() {
    printf "${BLUE}ℹ️  %s${NC}\n" "$1"
}

print_success() {
    printf "${GREEN}✅ %s${NC}\n" "$1"
}

print_warning() {
    printf "${YELLOW}⚠️  %s${NC}\n" "$1"
}

print_error() {
    printf "${RED}❌ %s${NC}\n" "$1" >&2
}

print_dry_run() {
    printf "${PURPLE}🔍 [DRY RUN] %s${NC}\n" "$1"
}

# Check git status and commit any changes
check_and_commit_changes() {
    print_info "Checking git status..."
    
    if [ "$(git branch --show-current)" != "main" ]; then
        print_error "Not on main branch. Please switch to main branch."
        exit 1
    fi
    
    # Check if there are uncommitted changes
    if [ -n "$(git status --porcelain)" ]; then
        if [ "$DRY_RUN" = true ]; then
            print_dry_run "Would commit all changes:"
            git status --short | sed 's/^/    /'
            print_dry_run "Would run: git add . && git commit -m 'Pre-release commit - prepare for v$1'"
            return
        fi
        
        print_info "Found uncommitted changes. Committing them..."
        git status --short | sed 's/^/  /'
        
        # Add all changes and commit
        git add .
        git commit -m "Pre-release commit - prepare for v$1"
        print_success "Committed all changes"
    else
        print_success "Working directory is clean"
    fi
    
    print_success "Ready for release on main branch"
}

# Get current version from config/app.php
get_current_version() {
    grep "version.*=>" config/app.php | sed -E "s/.*'([^']+)'.*/\1/"
}

# Update version in config/app.php
update_version() {
    local new_version=$1
    if [ "$DRY_RUN" = true ]; then
        print_dry_run "Would update version to $new_version in config/app.php"
        return
    fi
    
    print_info "Updating version to $new_version in config/app.php..."
    
    sed -i.bak "s/'version' => '[^']*'/'version' => '$new_version'/" config/app.php
    rm config/app.php.bak
    
    print_success "Version updated to $new_version"
}

# Validate semantic version format
validate_version() {
    local version=$1
    if [[ ! $version =~ ^[0-9]+\.[0-9]+\.[0-9]+$ ]]; then
        print_error "Invalid version format. Use semantic versioning (e.g., 1.2.3)"
        exit 1
    fi
}

# Increment version based on type (patch, minor, major)
increment_version() {
    local version=$1
    local increment_type=${2:-patch}
    
    IFS='.' read -ra VERSION_PARTS <<< "$version"
    local major=${VERSION_PARTS[0]}
    local minor=${VERSION_PARTS[1]}
    local patch=${VERSION_PARTS[2]}
    
    case $increment_type in
        "patch")
            patch=$((patch + 1))
            ;;
        "minor")
            minor=$((minor + 1))
            patch=0
            ;;
        "major")
            major=$((major + 1))
            minor=0
            patch=0
            ;;
        *)
            print_error "Invalid increment type. Use: patch, minor, or major"
            exit 1
            ;;
    esac
    
    echo "$major.$minor.$patch"
}

# Show usage information
show_usage() {
    echo "Usage: $0 [VERSION|INCREMENT_TYPE] [OPTIONS]"
    echo ""
    echo "Arguments:"
    echo "  VERSION         Explicit version number (e.g., 1.8.0)"
    echo "  INCREMENT_TYPE  Auto-increment type: patch, minor, or major"
    echo ""
    echo "Options:"
    echo "  --dry-run       Show what would be done without making changes"
    echo "  -h, --help      Show this help message"
    echo ""
    echo "Examples:"
    echo "  $0                      # Interactive mode - choose increment type"
    echo "  $0 patch                # Auto-increment patch version (1.7.0 → 1.7.1)"
    echo "  $0 minor                # Auto-increment minor version (1.7.0 → 1.8.0)"
    echo "  $0 major                # Auto-increment major version (1.7.0 → 2.0.0)"
    echo "  $0 1.8.5                # Set specific version"
    echo "  $0 patch --dry-run      # Preview patch increment without changes"
    echo ""
    echo "Semantic Versioning Guide:"
    echo "  PATCH - Bug fixes and small improvements"
    echo "  MINOR - New features that don't break existing functionality"
    echo "  MAJOR - Breaking changes that affect backward compatibility"
}

# Run tests
run_tests() {
    if [ "$DRY_RUN" = true ]; then
        print_dry_run "Would run tests with: php application test"
        return
    fi
    
    print_info "Running tests..."
    
    if ! php application test > /dev/null 2>&1; then
        print_error "Tests failed. Please fix tests before releasing."
        exit 1
    fi
    
    print_success "All tests passed"
}

# Run code formatting
run_formatting() {
    if [ "$DRY_RUN" = true ]; then
        print_dry_run "Would run code formatting with: ./vendor/bin/pint"
        print_dry_run "Would commit any formatting changes"
        return
    fi
    
    print_info "Running code formatting..."
    
    ./vendor/bin/pint
    
    # Check if pint made changes
    if [ -n "$(git status --porcelain)" ]; then
        print_warning "Code formatting made changes. Committing formatted code..."
        git add .
        git commit -m "Code formatting with Pint"
    fi
    
    print_success "Code formatting completed"
}

# Build the application
build_application() {
    if [ "$DRY_RUN" = true ]; then
        print_dry_run "Would build application with: ./build.sh"
        return
    fi
    
    print_info "Building application..."
    
    if ! ./build.sh; then
        print_error "Build failed. Please fix build issues before releasing."
        exit 1
    fi
    
    print_success "Application built successfully"
}

# Create git tag
create_git_tag() {
    local version=$1
    local tag="v$version"
    
    if [ "$DRY_RUN" = true ]; then
        print_dry_run "Would commit version change: git add config/app.php && git commit -m 'Bump version to $tag'"
        print_dry_run "Would create git tag: git tag -a '$tag' -m 'Release $tag'"
        return
    fi
    
    print_info "Creating git tag $tag..."
    
    # Commit version change
    git add config/app.php
    git commit -m "Bump version to $tag"
    
    # Create annotated tag
    git tag -a "$tag" -m "Release $tag"
    
    print_success "Created git tag $tag"
}

# Create GitHub release
create_github_release() {
    local version=$1
    local tag="v$version"
    
    if [ "$DRY_RUN" = true ]; then
        print_dry_run "Would create GitHub release for $tag"
        if command -v gh &> /dev/null; then
            print_dry_run "Would run: gh release create '$tag' --title 'Release $tag' --generate-notes builds/zeri"
        else
            print_dry_run "GitHub CLI (gh) not found - would skip release creation"
        fi
        return
    fi
    
    print_info "Creating GitHub release for $tag..."
    
    # Check if gh CLI is available
    if ! command -v gh &> /dev/null; then
        print_warning "GitHub CLI (gh) not found. Skipping GitHub release creation."
        print_info "Please create the release manually at: https://github.com/your-username/zeri/releases"
        return
    fi
    
    # Generate release notes from recent commits
    local release_notes
    release_notes=$(git log --oneline --pretty=format:"- %s" "$(git describe --tags --abbrev=0 2>/dev/null || echo 'HEAD~10')..HEAD" | head -10)
    
    if [ -z "$release_notes" ]; then
        release_notes="- Various improvements and bug fixes"
    fi
    
    # Create release with binary
    gh release create "$tag" \
        --title "Release $tag" \
        --notes "$release_notes" \
        --generate-notes \
        builds/zeri
    
    print_success "GitHub release created: $tag"
}

# Push changes to remote
push_changes() {
    local version=$1
    local tag="v$version"
    
    if [ "$DRY_RUN" = true ]; then
        print_dry_run "Would push changes: git push origin main"
        print_dry_run "Would push tag: git push origin '$tag'"
        return
    fi
    
    print_info "Pushing changes to remote..."
    
    git push origin main
    git push origin "$tag"
    
    print_success "Changes pushed to remote"
}

# Main release process
main() {
    # Parse arguments for flags
    local args=()
    while [[ $# -gt 0 ]]; do
        case $1 in
            -h|--help)
                show_usage
                exit 0
                ;;
            --dry-run)
                DRY_RUN=true
                shift
                ;;
            -*)
                print_error "Unknown option: $1"
                show_usage
                exit 1
                ;;
            *)
                args+=("$1")
                shift
                ;;
        esac
    done
    
    # Restore positional arguments
    set -- "${args[@]}"
    
    if [ "$DRY_RUN" = true ]; then
        echo "🚀 Zeri Release Script (DRY RUN MODE)"
        echo "====================================="
        echo "🔍 This is a dry run - no changes will be made"
    else
        echo "🚀 Zeri Release Script"
        echo "======================="
    fi
    echo ""
    
    # Get version input
    local current_version
    current_version=$(get_current_version)
    
    echo "Current version: $current_version"
    echo ""
    
    local new_version
    local increment_type
    
    if [ $# -eq 0 ]; then
        # No arguments provided - prompt for increment type
        echo "No version specified. Choose increment type:"
        echo "  1) patch (${current_version} → $(increment_version "$current_version" "patch")) - bug fixes"
        echo "  2) minor (${current_version} → $(increment_version "$current_version" "minor")) - new features"
        echo "  3) major (${current_version} → $(increment_version "$current_version" "major")) - breaking changes"
        echo "  4) custom - specify exact version"
        echo ""
        
        while true; do
            read -p "Select option [1-4]: " -r choice
            case $choice in
                1)
                    increment_type="patch"
                    new_version=$(increment_version "$current_version" "patch")
                    break
                    ;;
                2)
                    increment_type="minor"
                    new_version=$(increment_version "$current_version" "minor")
                    break
                    ;;
                3)
                    increment_type="major"
                    new_version=$(increment_version "$current_version" "major")
                    break
                    ;;
                4)
                    read -p "Enter custom version (e.g., 2.0.0): " -r custom_version
                    validate_version "$custom_version"
                    new_version="$custom_version"
                    increment_type="custom"
                    break
                    ;;
                *)
                    echo "Invalid option. Please choose 1-4."
                    ;;
            esac
        done
    elif [[ $1 =~ ^(patch|minor|major)$ ]]; then
        # First argument is increment type
        increment_type=$1
        new_version=$(increment_version "$current_version" "$increment_type")
    else
        # First argument is explicit version
        new_version=$1
        validate_version "$new_version"
        increment_type="custom"
    fi
    
    echo "New version: $new_version"
    if [ "$increment_type" != "custom" ]; then
        echo "Increment type: $increment_type"
    fi
    echo ""
    
    # Confirm release
    if [ "$DRY_RUN" = true ]; then
        echo "This would:"
    else
        echo "This will:"
    fi
    echo "  1. Commit any uncommitted changes"
    echo "  2. Run tests and code formatting"
    echo "  3. Update version in config/app.php ($current_version → $new_version)"
    echo "  4. Build the application"
    echo "  5. Commit version change and create git tag v$new_version"
    echo "  6. Create GitHub release with binary"
    echo "  7. Push all changes and tags to remote"
    echo ""
    
    if [ "$DRY_RUN" = false ]; then
        read -p "Continue with release $new_version? [y/N]: " -r
        if [[ ! $REPLY =~ ^[Yy]$ ]]; then
            print_info "Release cancelled."
            exit 0
        fi
    else
        print_info "Dry run mode - showing what would happen..."
    fi
    
    echo ""
    print_info "Starting release process for version $new_version..."
    echo ""
    
    # Execute release steps
    check_and_commit_changes "$new_version"
    run_tests
    run_formatting
    update_version "$new_version"
    build_application
    create_git_tag "$new_version"
    create_github_release "$new_version"
    push_changes "$new_version"
    
    echo ""
    if [ "$DRY_RUN" = true ]; then
        print_success "🔍 Dry run completed for release $new_version"
        echo ""
        echo "Would create release:"
        echo "  • Version: $current_version → v$new_version"
        if [ "$increment_type" != "custom" ]; then
            echo "  • Type: $increment_type increment"
        fi
        echo "  • Binary: builds/zeri"
        echo "  • GitHub: https://github.com/$(git remote get-url origin | sed -E 's|.*github\.com[:/]([^/]+/[^/]+)\.git?|\1|')/releases/tag/v$new_version"
        echo ""
        echo "To actually perform the release, run:"
        echo "  $0 $new_version"
    else
        print_success "🎉 Release $new_version completed successfully!"
        echo ""
        echo "Release details:"
        echo "  • Version: $current_version → v$new_version"
        if [ "$increment_type" != "custom" ]; then
            echo "  • Type: $increment_type increment"
        fi
        echo "  • Binary: builds/zeri"
        echo "  • Size: $(du -h builds/zeri | cut -f1)"
        echo "  • GitHub: https://github.com/$(git remote get-url origin | sed -E 's|.*github\.com[:/]([^/]+/[^/]+)\.git?|\1|')/releases/tag/v$new_version"
        echo ""
        echo "To install globally:"
        echo "  sudo cp builds/zeri /usr/local/bin/zeri"
        echo ""
        echo "Next steps:"
        echo "  • Test the release: ./builds/zeri --version"
        echo "  • Download from GitHub: gh release download v$new_version"
    fi
}

# Run main function with all arguments
main "$@"