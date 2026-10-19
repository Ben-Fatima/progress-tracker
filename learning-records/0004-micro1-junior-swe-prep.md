# 0004 – micro1 Junior SWE interview prep

- **Date:** 2026-10-15
- **Related goal:** "AI Platforms — Get Hired" track; micro1 Junior Software Engineer contract
- **Related lesson:** `lessons/0004-micro1-junior-swe-interview.html`

## Context

Sourced (not applied) by micro1's AI recruiter for a **Junior Software Engineer** contractor role: **$150–200/hr** — the highest rate on this track. Work: evaluate AI-generated code, author problems + reference solutions + test cases, review model reasoning on architecture/debugging/system design.

Caveats noted: listing prefers "based in an English-speaking country" and "1+ year at a top technology company." Applying anyway — let their filter decide. Pipeline: apply → AI interview (micro1's interviewer is typically Zara) → client matching.

Also received a micro1 Video Data Annotation offer ($8–10/hr) same day — deprioritized; bottom-tier rate, filler work only if engineering queues run dry.

## Planned learning outcomes for Lesson 0004

After completing the lesson, I should be able to:

1. Detect **plausible-but-wrong** AI code — clean, idiomatic, fails on ordering/edge cases.
2. Author a problem + test suite where at least one test kills plausible-but-wrong solutions.
3. Flag conclusion-right/reasoning-wrong model output.
4. Answer design questions aloud in requirements → components → tradeoffs → decision shape.
5. Deliver the background intro re-angled toward code evaluation (reuses Lesson 0003 arc).

## Early design decisions

- **Combined lesson** — this role needs Lesson 0002's review skill AND Lesson 0003's spoken format, plus two new skills (plausible-but-wrong detection, problem authoring). Built as one lesson referencing both rather than duplicating.
- **Exercise code:** counter-based bracket matcher — the canonical clean-looking AI solution that's wrong on `([)]`. Teaches the exact failure mode the job exists to catch.
- **STAR story:** stock tracker event sourcing — real architectural decision with a real tradeoff; better than generic "challenging bug" stories.

## Open questions / to validate after the interview

- Does micro1's AI interview include a live code review task, or only spoken questions?
- How strictly is the "English-speaking country" preference enforced — did Morocco get filtered or ignored?
- Actual question set → record here to improve the shared question bank (Mercor/micro1/Alignerr formats overlap).

## Next steps

1. Apply through the micro1 link; run the mock out loud once; take the AI interview.
2. Log actual interview questions in this record.
3. If matched: first tasks are an audition — quality drives future invites.
