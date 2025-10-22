document.addEventListener("DOMContentLoaded", function () {
	// Observador para animação fade-in
	const observerOptions = {
		threshold: 0.1,
		rootMargin: "0px 0px -50px 0px",
	};

	const observer = new IntersectionObserver(function (entries) {
		entries.forEach((entry) => {
			if (entry.isIntersecting) {
				entry.target.classList.add("visible");
			}
		});
	}, observerOptions);

	// Elementos para animar
	const elementsToAnimate = document.querySelectorAll(
		".discover-content, .history-content, .values-grid, .environment-content"
	);
	elementsToAnimate.forEach((element) => {
		element.classList.add("fade-in");
		observer.observe(element);
	});

	// Efeitos hover nos botões
	const buttons = document.querySelectorAll("button");
	buttons.forEach((button) => {
		button.addEventListener("mouseenter", function () {
			this.style.transform = "translateY(-2px)";
		});

		button.addEventListener("mouseleave", function () {
			this.style.transform = "translateY(0)";
		});
	});

	// Animação suave dos cards de valores
	const valueCards = document.querySelectorAll(".value-card");
	valueCards.forEach((card) => {
		card.addEventListener("mouseenter", function () {
			this.style.transform = "translateY(-10px) scale(1.02)";
		});

		card.addEventListener("mouseleave", function () {
			this.style.transform = "translateY(0) scale(1)";
		});
	});
});

// Scroll suave entre seções
function scrollToSection(sectionId) {
	const section = document.getElementById(sectionId);
	if (section) {
		section.scrollIntoView({
			behavior: "smooth",
			block: "start",
		});
	}
}

// Efeito parallax suave no hero section
window.addEventListener("scroll", function () {
	const scrolled = window.pageYOffset;
	const heroSection = document.querySelector(".hero-section");
	if (heroSection) {
		heroSection.style.backgroundPositionY = -(scrolled * 0.5) + "px";
	}
});

// Loading animation
window.addEventListener("load", function () {
	document.body.classList.add("loaded");
});
