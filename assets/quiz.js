// Shared multiple-choice quiz widget for lessons.
// Usage:
//   <div class="quiz" data-correct-feedback="..." data-incorrect-feedback="...">
//     <p class="quiz-question">Question text?</p>
//     <button class="quiz-option" data-correct="false">Option A</button>
//     <button class="quiz-option" data-correct="true">Option B</button>
//     <p class="quiz-feedback" aria-live="polite"></p>
//   </div>
document.querySelectorAll('.quiz').forEach((quiz) => {
  const feedback = quiz.querySelector('.quiz-feedback');
  const options = quiz.querySelectorAll('.quiz-option');

  options.forEach((btn) => {
    btn.addEventListener('click', () => {
      options.forEach((b) => (b.disabled = true));
      const correct = quiz.querySelector('.quiz-option[data-correct="true"]');

      if (btn.dataset.correct === 'true') {
        btn.classList.add('correct');
        feedback.textContent = quiz.dataset.correctFeedback || 'Correct.';
      } else {
        btn.classList.add('incorrect');
        correct.classList.add('correct');
        feedback.textContent =
          quiz.dataset.incorrectFeedback || 'Not quite — see the highlighted answer.';
      }
    });
  });
});
