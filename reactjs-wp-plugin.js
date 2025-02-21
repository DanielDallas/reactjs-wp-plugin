function ReactComponent() {
  return React.createElement("h2", {}, "Hello from React Plugin!");
}

document.addEventListener("DOMContentLoaded", function () {
  const root = document.getElementById("react-plugin-root");
  if (root) {
    ReactDOM.createRoot(root).render(React.createElement(ReactComponent));
  }
});
