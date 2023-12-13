#!/bin/sh

# Function to check if the given string is a valid Git commit hash
function is_commit_hash() {
    local hash=$1
    if [ "$hash" == "$(git rev-parse --verify "$hash")" ]; then
        return 0
    else
        return 1
    fi
}

# Function to check if the given string is a valid branch name in the current Git repository
function is_branch_name() {
    local branch=$1
    if [ "$branch" == "$(git rev-parse --verify "$branch")" ]; then
        return 0
    else
        return 1
    fi
}

# Function to check if the given string is a valid Git reference
function is_git_ref() {
    local ref=$1
    if [ "$ref" == "$(git rev-parse --verify "$ref")" ]; then
        return 0
    else
        return 1
    fi
}

# Check if the input is a valid Git reference (commit hash, branch name, or tag)
if is_git_ref "$1"; then
    echo "Input is a valid Git reference: $1"

    # If the input is a commit hash, use it as-is
    if is_commit_hash "$1"; then
        echo "Checking commit hash: $1"
    else
        # If the input is a branch name, get the commit hash of the latest commit on that branch
        if is_branch_name "$1"; then
            commit_hash=$(git rev-parse "$1")
            echo "Checking branch name '$1' (commit hash: $commit_hash)"
        else
            # If the input is a tag, get the commit hash of the tag
            commit_hash=$(git rev-parse "$1^{commit}")
            echo "Checking tag '$1' (commit hash: $commit_hash)"
        fi
    fi

    # Run psalm and PHP_CodeSniffer checks on the given commit hash
    echo "Running psalm and PHP_CodeSniffer checks..."
    git checkout "$commit_hash"
    vendor/bin/psalm
    vendor/bin/phpcs
    git checkout -
else
    echo "Input is not a valid Git reference: $1"
fi