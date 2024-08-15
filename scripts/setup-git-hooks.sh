#!/bin/bash

echo "Setting up Git hooks..."

# Create symbolic links for all hooks in the .git-hooks directory
for hook in $(ls -1 .git-hooks/); do
    ln -sf ../../.git-hooks/$hook .git/hooks/$hook
done

echo "Git hooks are set up."
