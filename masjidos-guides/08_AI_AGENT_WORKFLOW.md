# AI Agent Workflow Guide

Dokumen ini mengatur cara kerja Codex/Hermes/Kilo saat mengembangkan project.

## Mandatory Workflow

### Step 1 — Read Guides

Agent harus membaca semua guide `.md` yang relevan sebelum coding.

Minimal baca:

- Product spec
- Module scope
- Architecture guide
- Database guide
- Role permission guide
- UI/UX guide
- Development rules
- Sprint roadmap
- Kanban board

### Step 2 — Audit Current Codebase

Sebelum coding, audit:

- Framework backend
- Framework frontend
- Package/dependency files
- Routes
- Auth
- Role/permission
- Database migrations
- Models/entities
- Controllers/services
- Frontend layout/components
- Existing dashboard
- Build/test/lint config

Output audit:

1. Current architecture summary
2. Existing features
3. Gaps vs guide
4. Risks
5. Recommended next step

### Step 3 — Pick Current Sprint

Agent harus melihat `09_SPRINT_ROADMAP.md` dan `10_KANBAN_BOARD.md`.

Pilih task dari sprint aktif.

Jangan lompat ke sprint later jika sprint sebelumnya belum selesai, kecuali user approve.

### Step 4 — Plan Small Changes

Sebelum coding, agent harus menyebutkan:

- Files likely created/modified
- Migration impact
- Route impact
- UI impact
- Permission impact
- Test/manual check plan

### Step 5 — Implement Incrementally

Implementasi per task kecil.

Contoh jangan sekaligus:

- finance + donation + dashboard + public portal

Pecah menjadi:

1. finance migration/model
2. finance CRUD
3. finance approval
4. finance dashboard summary
5. finance report

### Step 6 — Verify

Setelah coding:

- Run available tests/build/lint.
- Jika tidak bisa run, jelaskan kenapa.
- Berikan manual test checklist.

### Step 7 — Update Kanban

Setelah task selesai:

- Pindahkan status task di `10_KANBAN_BOARD.md`.
- Tambahkan notes jika ada blocker.
- Jangan hapus history penting.

### Step 8 — Report Summary

Output wajib:

1. What was implemented
2. Files created
3. Files modified
4. Database migrations added
5. Commands to run
6. Manual checks
7. Risks/known issues
8. Next recommended task

## Agent Behavior Rules

- Jangan sok yakin jika belum audit.
- Jangan implement fitur dengan asumsi berbahaya.
- Jika menemukan conflict, laporkan dulu.
- Jika ada existing pattern, ikuti pattern itu.
- Jika ada fitur terlalu besar, pecah ke sprint/task.
- Jika task butuh dependency baru, jelaskan alasan dan alternatif.

## Stop Conditions

Agent harus berhenti dan minta approval jika:

- Perlu drop/rename database column existing.
- Perlu rewrite auth.
- Perlu rewrite frontend structure besar.
- Perlu dependency besar baru.
- Perlu menghapus fitur existing.
- Ada risiko data loss.

## Preferred Output Format

```md
## Summary

...

## Files Created

- ...

## Files Modified

- ...

## Migrations

- ...

## Commands to Run

```bash
...
```

## Manual Test Checklist

- [ ] ...

## Risks / Notes

- ...

## Next Step

...
```
