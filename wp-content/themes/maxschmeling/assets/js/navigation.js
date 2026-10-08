(() => {
  document.documentElement.classList.add("js");

  const button = document.querySelector(".menu-toggle");
  const navigation = document.querySelector(".primary-navigation");

  if (!button || !navigation) {
    return;
  }

  button.addEventListener("click", () => {
    const isOpen = navigation.classList.toggle("is-open");
    button.setAttribute("aria-expanded", String(isOpen));
  });

  navigation.addEventListener("click", (event) => {
    if (event.target instanceof HTMLAnchorElement) {
      navigation.classList.remove("is-open");
      button.setAttribute("aria-expanded", "false");
    }
  });

  document.addEventListener("keydown", (event) => {
    if (event.key === "Escape") {
      navigation.classList.remove("is-open");
      button.setAttribute("aria-expanded", "false");
      button.focus();
    }
  });
})();
