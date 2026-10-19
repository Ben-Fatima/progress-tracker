# Odin task ideas — next submissions

Three banked ideas, each a different **task type**, **app trio**, and **project**. Same format as the approved task 1 proposal. Recording window: **15–45 min** — each idea below has a **~25–30 min** and a **~20 min** variant side by side (the 20-min lever is always *smaller scope*, never faster narration — fast speech is what the reviewer flagged).

Approved-task comparison (for Loki "why different" notes):

| | Task 1 (approved) | Idea 1 | Idea 2 | Idea 3 |
|---|---|---|---|---|
| Project | StockTracker API | progress-tracker | StockTracker `cli/` | FileImporter |
| Task type | API feature | DevOps (containerize) | CLI feature | Debugging/bug-fix |
| Main apps | VS Code, Postman, DBeaver | VS Code, Docker Desktop, browser, DBeaver | VS Code, Postman, DBeaver | VS Code, browser, DBeaver |

---

## Idea 1 — Containerize a Laravel + Vite app with Docker Compose (progress-tracker)

**Domain / Role:** Backend Developer / DevOps

**Goal:** Containerize the "Progress Tracker" (a personal productivity dashboard built with Laravel, Livewire, and Vite) using Docker Compose. The goal is to move from a manual multi-terminal setup (running `php artisan serve` for the backend and `npm run dev` for the Vite/Livewire assets) to a single-command `docker compose up` environment.

**Starting materials:** Existing progress-tracker codebase on the local machine; Docker Desktop installed.

**Steps (with the apps for each):**
1. **Show task description:** Introduce the objective and apps (LibreOffice).
2. **Review existing environment:** Show the codebase and the current manual setup requirements—highlighting that both the PHP server and the Vite dev server for Livewire must run simultaneously (VS Code).
3. **Implement containerization:** Author a `Dockerfile` for the Laravel app and a `docker-compose.yml` defining an `app` service for the backend and a `node` service for the Vite/Livewire assets (VS Code).
4. **Build and launch:** Run `docker compose build` and `up` to start both services at once (VS Code integrated terminal).
5. **Verify infrastructure:** Inspect the running containers and service logs (Docker Desktop).
6. **Verify application:** Load the dashboard and verify frontend assets load correctly (Browser).
7. **Verify data persistence:** Confirm the container is correctly writing to the host-mounted SQLite database (DBeaver).
8. **Recap and close:** Show the final configuration files and stop the recording (VS Code).

**Deliverable:** video.mp4, deliverables.zip (Dockerfile, docker-compose.yml, app source unchanged).

> **20-min variant:** single-service compose — `app` only, serving pre-built Vite assets (`npm run build` before recording, PHP serves `public/build`). Skips the `node` service and shortens the Dockerfile.

**Why it fits Odin:** Genuinely different category from task 1 (devops, not feature code); 4 distinct apps; clear before/after (app runs on host → app runs in containers); expertise shown via port mapping, volumes, service naming — all narrated.

**Budget:** *~27 min:* intro 0.5 / show app 2 / Dockerfile 8 / compose 5 / build+up 5 / Docker Desktop 2 / browser 2 / DBeaver volume check 1.5 / close 1. *~21 min:* single service — intro 0.5 / show app 2 / Dockerfile 7 / compose 2.5 / build+up 4 / Docker Desktop + browser 2.5 / DBeaver check 1 / close 1.

**Pre-recording prep (critical):**
- Docker Desktop installed and "hello world" tested *before* recording.
- All `docker` commands run in **VS Code's integrated terminal** — Terminal.app is restricted and VS Code's terminal does not count as a separate app; narrate it as part of VS Code.
- `docker pull php:8.3-cli` and `node:22` **before** recording so `docker compose build` is a cached rebuild — a fresh pull is minutes of idle, an AutoQC fail.
- Do one full dry run first; record only the *clean* rebuild + up.

---

## Idea 2 — Add a `report` command to the StockTracker CLI client

**Domain / Role:** Backend Developer

**Goal:** Add a `stock report` command to the StockTracker CLI client. The command will fetch data from the existing API and display a formatted report of stock levels.

**Starting materials:** StockTracker API and CLI codebase; API server running locally.

**Steps (with the apps for each):**
1. **Show task description:** Introduce the goal (LibreOffice).
2. **Review existing CLI:** Show the current commands and the shared HTTP client structure (VS Code).
3. **Implement command:** Create `ReportCommand.php` to handle the API call and table formatting (VS Code).
4. **Register command:** Wire the new command into the CLI entry point and argument parser (VS Code).
5. **Execute CLI:** Run the new report command and verify the output (VS Code integrated terminal).
6. **Verify against API:** Compare the CLI output with a raw JSON response from the same endpoint (Postman).
7. **Verify against DB:** Show the movement records in the database that make up the report totals (DBeaver).
8. **Recap and close:** Review the code and stop the recording (VS Code).

**Deliverable:** video.mp4, deliverables.zip (New Command file, updated CLI entry point, app source).

**Why it fits Odin:** New task type (CLI client work, not an API endpoint), and the "CLI output must match the API and the database" cross-check uses two apps for real verification.

**Budget:** *~26 min:* intro 0.5 / show CLI structure 3 / write command 10 / wire + parse 4 / run in VS Code terminal 2 / Postman cross-check 3 / DBeaver rows 2 / close 1. *~21 min:* simpler output (fewer columns), wire into `ArgParser` as a single edit — intro 0.5 / show structure 2 / write command 7 / wire 2 / terminal 2 / Postman 3 / DBeaver 2 / close 1.

**Pre-recording prep:** API server running; a few products/locations/movements seeded so `report` returns real numbers.

---

## Idea 3 — Debug a broken CSV import in FileImporter

**Domain / Role:** Backend Developer / QA

**Goal:** Diagnose and fix a row-counting bug in "FileImporter" (a PHP application that uploads CSV files in chunks and imports the rows into a SQLite database). Reproduce the discrepancy between CSV rows and database records, trace the logic error in the import service, and verify the fix.

**Starting materials:** FileImporter codebase; a sample CSV file with a known row count; a seeded bug in the import logic.

**Steps (with the apps for each):**
1. **Show task description:** Introduce the debugging objective (LibreOffice).
2. **Reproduce error:** Perform an upload and show that the imported count is incorrect (Browser).
3. **Evidence collection:** Query the database to prove the physical row count doesn't match the CSV (DBeaver).
4. **Code diagnosis:** Trace the chunking and counting logic in the import service to find the bug (VS Code).
5. **Apply fix:** Correct the logic and re-run the import process (VS Code).
6. **Final verification:** Verify the count now matches the ground truth in both the UI and the database (Browser / DBeaver).
7. **Recap and close:** Summarize the fix and stop the recording (VS Code).

**Deliverable:** video.mp4, deliverables.zip (Patched Service file, app source).

**Why it fits Odin:** Fourth category — *diagnosis*, not construction. Reviewer sees reproduce → verify against ground truth → trace → fix → re-verify, which is a harder skill than writing a feature and reads as senior.

**Budget:** *~23 min:* intro 0.5 / reproduce 4 / DBeaver evidence 3 / trace `Import.php` 6 / fix + re-import 5 / recount 3 / close 1. *~20 min:* smaller CSV (8 rows → faster upload/import + recount) — intro 0.5 / reproduce 3 / evidence 2.5 / trace 5 / fix + re-import 4 / recount 2 / close 1.

**Pre-recording prep (critical):**
- **Seed the bug yourself first** — pick the exact spot in `Import.php`, confirm it produces a clean, obvious wrong count (not a crash — a crash is a bad demo; a *wrong number* is a good one).
- Have a clean CSV ready (e.g., 12 rows) and know the expected count cold.
- Confirm the *fix* is a 1–3 line change; a debugging task that needs a rewrite blows the time budget.

---

## Submission order & notes

1. **Idea 1 (Docker)** first — biggest visual difference from the approved task while the reviewer is fresh; Docker Desktop prep is the only gate.
2. **Idea 2 (CLI)** second — zero new environment, uses StockTracker which is already trusted/set up on the Mac.
3. **Idea 3 (Debug)** third — needs a seeded bug + FileImporter on the Mac; save for when the first two are submitted.

Shared rules carried from task 1 (apply to all): narrate every click what/where/why, line numbers not highlighting, move mouse during typing (<45 s idle), ~220 wpm max, pause after clicks, Dock visible at start/end, Accessibility Inspector running hidden, TaskRecorder only at start/stop, metadata.rtf not on camera, intro <20 s.
