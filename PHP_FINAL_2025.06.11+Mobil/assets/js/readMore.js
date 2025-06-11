document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll(".read-more").forEach(button => {
        button.addEventListener("click", function () {
            const descriptionElement = this.previousElementSibling;
            const fullText = descriptionElement.getAttribute("data-full-text");

            if (descriptionElement.classList.contains("expanded")) {
                descriptionElement.classList.remove("expanded");
                descriptionElement.textContent = fullText.slice(0, 100) + "...";
                this.textContent = "Read more...";
            } else {
                descriptionElement.classList.add("expanded");
                descriptionElement.textContent = fullText;
                this.textContent = "Show less";
            }
        });
    });
});
