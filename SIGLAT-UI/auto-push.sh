#!/bin/bash

# Auto-push script for SIGLAT UI
echo "Building SIGLAT UI..."

# Run the build
npm run build

if [ $? -eq 0 ]; then
    echo "Build successful! Committing and pushing changes..."
    
    # Add changes to git
    git add .
    
    # Create commit with timestamp
    git commit -m "Auto-push: UI build completed at $(date)"
    
    # Push to remote (you'll need to set up a remote first)
    # git push origin main
    
    echo "Changes committed successfully!"
else
    echo "Build failed! Not pushing changes."
    exit 1
fi