document.addEventListener('DOMContentLoaded', () => {
  const observerOptions = {
    rootMargin: '0px 0px -50px 0px',
    threshold: 0.1
  };

  let visibleIndex = 0;
  const stagger = 150;

  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        const delay = visibleIndex * stagger;
        visibleIndex++;

        setTimeout(() => {
          entry.target.classList.add('is-visible');
        }, delay);

        observer.unobserve(entry.target);
      }
    });
  }, observerOptions);

  document.querySelectorAll('.is-animated').forEach(el => observer.observe(el));
});
