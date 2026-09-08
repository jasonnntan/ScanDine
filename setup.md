# Git Quick Reference Guide

### 1. Initialize Repository

git init

- What it does: Starts tracking your project locally by creating a hidden `.git` folder.

### 2. Stage Changes

git add .

- What it does: Gathers all new, modified, or deleted files and puts them in a temporary "holding area" (staging area).

### 3. Check Progress

git status

- What it does: Shows which files are staged (green), unstaged (red), or untracked. Safe to run anytime.

### 4. Save Locally

git commit -m "Your descriptive message here"

- What it does: Permanently saves a snapshot of your staged files to your local history.
- Note: Always use a description like "first commit" or "fixed login bug" inside the quotes.

### 5. Link GitHub (Run Once)

git remote add origin https://github.com

- What it does: Tells your local project exactly where your GitHub repository lives online.

### 6. Upload to GitHub

git push -u origin main

- What it does: Uploads your local saved snapshots up to the `main` branch on GitHub.
- Note: The `-u` remembers your settings so next time you only have to type `git push`.
