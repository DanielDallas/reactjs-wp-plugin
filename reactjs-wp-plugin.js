function loadReactComponent() {
  let ReactComponent;
  try {
    eval(reactPluginData.code); // Dynamically execute the saved code
  } catch (e) {
    console.error("Error in React Component:", e);
    ReactComponent = function () {
      return React.createElement("h2", {}, "Error in React Code!");
    };
  }

  document.addEventListener("DOMContentLoaded", function () {
    const root = document.getElementById("react-plugin-root");
    if (root) {
      ReactDOM.createRoot(root).render(React.createElement(ReactComponent));
    }
  });
}

loadReactComponent();
