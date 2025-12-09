document.addEventListener("DOMContentLoaded", function() {
    const lazyImages = document.querySelectorAll("img.lazy-img");

    if ("IntersectionObserver" in window) {
        const observer = new IntersectionObserver((entries, observer) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    const picture = img.closest("picture");

                    // Handle <source> tags for <picture>
                    if (picture) {
                        picture.querySelectorAll("source").forEach((source) => {
                            if (source.dataset.srcset) {
                                source.srcset = source.dataset.srcset;
                            }
                        });
                    }

                    img.src = img.dataset.src;
                    img.onload = () => img.classList.remove("lazy-img");
                    observer.unobserve(img);
                }
            });
        });

        lazyImages.forEach((img) => observer.observe(img));
    } else {
        // Fallback for browsers without IntersectionObserver
        lazyImages.forEach((img) => {
            img.src = img.dataset.src;
        });
    }
});