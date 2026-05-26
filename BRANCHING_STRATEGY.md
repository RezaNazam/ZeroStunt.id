# Git Branching Strategy

## Branch Types

### 1. main (Production)

- Status: LOCKED (hanya saat submit final)
- Siapa yang bisa merge: Project Lead
- Dari: develop (setelah final testing)

### 2. develop (Integration)

- Status: Protected
- Siapa yang bisa merge: Anyone (tapi melalui PR)
- Dari: feature/\* branches (via Pull Request)

### 3. feature/\* (Work in Progress)

- Status: Open
- Siapa yang bisa merge: Pembuat branch
- Format: `feature/person-[name]-[task]`

## Naming Convention

- feature/person-1-database
- feature/person-2-navbar
- feature/person-3-auth-system
- feature/person-4-test-docs

## Merge Flow

feature/task-1 feature/task-2 feature/task-3
↓ (via PR) ↓ (via PR) ↓ (via PR)
└─────────────────────→ develop ←──────────────┘
↓ (setelah testing done)
main (hanya saat submit)

## DO's & DON'Ts

✅ DO:

- Create PR untuk setiap feature
- Request review sebelum merge
- Pull develop sebelum kerja
- Delete branch setelah merge

❌ DON'T:

- Push langsung ke main
- Push langsung ke develop
- Merge PR sendiri (get approval first)
- Force push (git push -f)

## BRANCHES YANG PERLU DIKETAHUI

PRODUCTION BRANCHES:
├─ main (hanya saat submit akhir)
└─ develop (integration branch)

PERSONAL BRANCHES (per orang):
├─ feature/person-1-database
├─ feature/person-2-ui
├─ feature/person-3-backend
└─ feature/person-4-testing

OPTIONAL BRANCHES (jika ada hotfix):
├─ hotfix/emergency-fix (if critical bug)
└─ staging/pre-testing (if you want intermediate testing)
