# Deployment Guide

## Configure local repository

Disable file permission tracking (prevents false positives in git diff):

```bash
git config core.filemode false
```

## Change remote URL

Update the remote origin to point to the new repository:

```bash
git remote set-url origin git@github.com:user/new-repo.git
```

## Checkout master branch and pull

```bash
git checkout master
git pull
```
