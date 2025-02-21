document.addEventListener("DOMContentLoaded", function () {
  wp.codeEditor.initialize(document.getElementById("react_component_code"), {
    mode: "javascript",
    lineNumbers: true,
    matchBrackets: true,
    autoCloseBrackets: true,
    theme: "monokai",
  });
});
