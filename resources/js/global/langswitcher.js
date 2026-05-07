document.addEventListener('DOMContentLoaded', () => {
  const switchers = document.querySelectorAll('.langswitcher');

  if (switchers.length === 0) return;
  let initializeLangSwitcher = (switcher) => {
    const trigger = switcher.querySelector('.langswitcher-trigger');
    const optionsList = switcher.querySelector('.langswitcher-options');
    const options = switcher.querySelectorAll('.custom-option');

    // 1. Gérer l'ouverture/fermeture du menu au clic sur le bouton
    trigger.addEventListener('click', (e) => {
      e.preventDefault();
      const isExpanded = trigger.getAttribute('aria-expanded') === 'true' || false;

      // Basculer l'état
      trigger.setAttribute('aria-expanded', !isExpanded);
      optionsList.dataset.hidden = isExpanded;
    });

    // Fonction pour ouvrir/fermer le menu
    const setExpanded = (isExpanded) => {
      trigger.setAttribute('aria-expanded', isExpanded.toString());
      optionsList.dataset.hidden = !isExpanded;
    }

    // 1. Ouvrir au survol de l'ensemble du conteneur (switcher)
    switcher.addEventListener('mouseenter', () => {
      setExpanded(true);
    });

    // 2. Fermer lorsque la souris quitte l'ensemble du conteneur
    switcher.addEventListener('mouseleave', () => {
      setExpanded(false);
    });

    // 2. Gérer la sélection d'une option
    options.forEach(option => {
      // Redirection au clic
      option.addEventListener('click', () => {
        const url = option.getAttribute('data-url');
        if (url) {
          window.location.href = url;
        }
      });

      // Redirection avec la touche Entrée (pour l'accessibilité)
      option.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' || e.key === ' ') {
          e.preventDefault();
          const url = option.getAttribute('data-url');
          if (url) {
            window.location.href = url;
          }
        }
      });
    });

    // 3. Fermer le menu si l'utilisateur clique à l'extérieur
    document.addEventListener('click', (e) => {
      if (!switcher.contains(e.target)) {
        trigger.setAttribute('aria-expanded', 'false');
        optionsList.dataset.hidden = true;
      }
    });
  }
  switchers.forEach(initializeLangSwitcher);
});
