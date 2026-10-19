# 0002 – Alignerr assessment prep: structured code review

- **Date:** 2026-09-30
- **Related goal:** "AI Platforms — Get Hired" track; Alignerr application submitted 2026-10-01 (morning)
- **Related lesson:** `lessons/0002-structured-code-review-ai-evaluators.html`
- **Related reference:** `reference/evaluator-code-review-checklist.html`

## Context

The workspace's original mission (`MISSION.md`) covers CapCut Pro reels. A second learning track started today: **passing AI-platform assessments** (Alignerr, Mercor) to earn remote USD income — part of the relocation plan. Mission file may need updating to reflect both tracks.

Roles applied to:

- Software Engineer (AI Training), $20–100/hr — code review + written feedback
- Software Engineer Task Author, $80–120/hr — designing tasks + checkable rubrics

Also this week: applied to a Mercor project ($20/task, MacBook required — using sister's M-series MacBook).

## Planned learning outcomes for Lesson 0002

After completing the lesson, I should be able to:

1. Review any code snippet systematically through the **four lenses** (correctness → security → performance → readability).
2. Write findings in **Finding → Evidence → Fix** format — claim, mechanism, correction.
3. Convert a defect into a **checkable rubric criterion** (Task Author skill).

## Early design decisions

- **Feedback format standard:** "Line N: [what] — [mechanism/consequence]. Fix: [correction]." Half the assessed skill is the writing, not the bug-finding.
- **Severity ordering in reviews:** correctness > security > performance > style. Never lead with style.
- **Rubric test:** could a plausible-but-wrong solution still pass the criterion? If yes, rewrite it.
- **Primary source chosen:** Google Engineering Practices code-review guide — canonical and directly relevant.

## Open questions / to validate after practice

- Is the Alignerr assessment human-graded, AI-conducted, or async task-based? (Adjust prep accordingly once known.)
- Did the review exercise feel too easy or too hard? Generate a second buggy snippet if more drilling is needed.
- Does the Task Author application actually include rubric-writing tasks?

## Next steps

1. Complete Lesson 0002 exercises tonight; re-read the reference checklist tomorrow morning before applying.
2. After the assessment, record what format it actually took (here) so prep can be tuned for Mercor/future platforms.
3. Possible Lesson 0003: a second code-review drill with a different bug profile (async/concurrency, or a PHP/Laravel snippet matching my real stack).
