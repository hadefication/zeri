#!/bin/bash

# Zeri Build Script
# This script builds the standalone application following Laravel Zero best practices

set -e

echo "🔨 Building Zeri CLI Application..."

# Clean up any existing builds
echo "🧹 Cleaning up existing builds..."
rm -rf builds/
mkdir -p builds/

# Optimize autoloader for production
echo "⚡ Optimizing autoloader..."
composer dump-autoload --classmap-authoritative --no-dev

# Build with Box
echo "📦 Building PHAR with Box..."
php vendor/laravel-zero/framework/bin/box compile

# Restore development autoloader
echo "🔄 Restoring development autoloader..."
composer dump-autoload

# Verify build
if [ -f "builds/zeri" ]; then
    echo "✅ Build successful!"
    echo "📄 Built: builds/zeri"
    
    # Test the executable
    echo "🧪 Testing executable..."
    if ./builds/zeri --version > /dev/null 2>&1; then
        echo "✅ Executable test passed!"
        echo ""
        echo "🎉 Zeri CLI built successfully!"
        echo "📍 Location: $(pwd)/builds/zeri"
        echo "📏 Size: $(du -h builds/zeri | cut -f1)"
        echo ""
        echo "To install globally:"
        echo "  sudo cp builds/zeri /usr/local/bin/zeri"
    else
        echo "❌ Executable test failed!"
        exit 1
    fi
else
    echo "❌ Build failed - executable not found!"
    exit 1
fi