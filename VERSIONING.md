# Versioning and Release Guide

This document defines how we version, commit, branch, review, and release changes for the One Unborn project.

## Prerequisites
- Git installed and configured
- PHP 8.2+, Composer
- Node.js LTS and npm

Run once after cloning:

```powershell
composer install
npm install
```

This installs Husky so commit messages are validated locally.

## Branching Model
- Default branch: `main`
- Create short-lived branches from `main`:
  - `feat/<topic>` for features
  - `fix/<topic>` for bug fixes
  - `chore/<topic>` for maintenance
  - `refactor/<topic>` for internal changes

Example:
```powershell
git checkout -b feat/po-pricing-validation
```

## Conventional Commits
All commits must follow Conventional Commits. Examples:
- `feat: add feasibility PO price validation`
- `fix: correct external vendor amount rule`
- `chore: update dependencies`
- `refactor: simplify privilege middleware`
- `test: add PO validation test`
- `ci: run tests on PR`

Rules:
- Type: `feat|fix|chore|refactor|ci|test|docs` (+ optional scope)
- Subject: lower case, imperative mood
- Breaking change: add `!` and a footer, e.g. `feat!: change PO validation` with `BREAKING CHANGE:` in body

Local enforcement:
- Husky runs `commitlint` on commit message. If it fails, edit the message and recommit.

## Pull Requests
- Open PRs from your branch into `main`.
- Title should also follow Conventional Commits.
- All checks must pass:
  - CI tests (`.github/workflows/ci.yml`)
- Use labels to categorize changes for release notes:
  - `feature`, `fix`, `chore`, `refactor`, `ci`, `test`, `breaking`

Run tests locally before pushing:
```powershell
composer test
```

## Changelog and Releases
- Release Drafter creates a draft release from merged PRs and labels.
- We use Semantic Versioning: `vMAJOR.MINOR.PATCH`
  - `MAJOR`: incompatible changes
  - `MINOR`: features
  - `PATCH`: fixes and maintenance

Tagging a release:
```powershell
# choose next version e.g., v0.1.1
git tag -a v0.1.1 -m "chore(release): v0.1.1"
git push origin v0.1.1
```

Publishing:
- Go to GitHub → Releases → Edit the draft → Publish.

## Do Not Commit
The `.gitignore` already protects these, but double-check:
- `.env` and any secrets
- `vendor/` and `node_modules/`
- Logs and caches

## Fork/Upstream Workflow (if needed)
If you work from a fork:
```powershell
# after forking to <you>/one-unborn
git remote rename origin upstream
git remote add origin https://github.com/<you>/one-unborn.git
# keep fork updated
git fetch upstream
git rebase upstream/main
```
Open PRs from your fork to the main repository.

## Quick Flow
1) Create branch → 2) Code → 3) `composer test` → 4) Commit with Conventional Commit → 5) Push and open PR → 6) Merge after CI passes → 7) Tag and publish release if applicable.