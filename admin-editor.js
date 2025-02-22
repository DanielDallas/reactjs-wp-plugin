document.addEventListener("DOMContentLoaded", () => {
  // Initialize CodeMirror editor
  if (document.getElementById("component_code")) {
    if (typeof wp !== "undefined" && wp.codeEditor) {
      wp.codeEditor.initialize(document.getElementById("component_code"), {
        mode: "javascript",
        lineNumbers: true,
        matchBrackets: true,
        autoCloseBrackets: true,
        theme: "default",
        indentUnit: 2,
        tabSize: 2,
        lineWrapping: true,
      });
    }
  }

  // Handle shortcode copying
  document.querySelectorAll(".copy-shortcode").forEach((button) => {
    button.addEventListener("click", function (e) {
      e.preventDefault();
      const shortcode = this.dataset.shortcode;

      navigator.clipboard
        .writeText(shortcode)
        .then(() => {
          const originalText = this.textContent;
          this.textContent = "Copied!";
          setTimeout(() => {
            this.textContent = originalText;
          }, 2000);
        })
        .catch((err) => {
          console.error("Failed to copy shortcode:", err);
        });
    });
  });
});
