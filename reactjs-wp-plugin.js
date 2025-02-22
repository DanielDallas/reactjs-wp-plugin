import React from "react";
document.addEventListener("DOMContentLoaded", () => {
  // Find all React component containers
  const componentContainers = document.querySelectorAll(".react-component");

  if (componentContainers.length > 0) {
    componentContainers.forEach((container) => {
      const shortcode = container.dataset.shortcode;
      const component = reactPluginData.components.find(
        (c) => c.shortcode === shortcode
      );

      if (component) {
        try {
          // Create a new scope to evaluate the code
          const Component = ((React) => {
            const evalScope = {};
            const funcBody = `
                            ${component.code}
                            return ReactComponent;
                        `;

            const createComponentFunction = new Function("React", funcBody);
            return createComponentFunction(React);
          })(React);

          // Render the component
          ReactDOM.render(React.createElement(Component, null), container);
          console.log(
            `React component "${component.title}" successfully rendered`
          );
        } catch (error) {
          console.error(
            `Error rendering React component "${component.title}":`,
            error
          );
          container.innerHTML =
            '<p style="color: red;">Error loading React component. Check console for details.</p>';
        }
      } else {
        console.error(`Component with shortcode "${shortcode}" not found`);
        container.innerHTML = '<p style="color: red;">Component not found</p>';
      }
    });
  }
});

// Declare reactPluginData and ReactDOM.  These would ideally be provided by the WordPress plugin.
// This is a placeholder to prevent errors.  The actual implementation will depend on how
// the data and libraries are exposed by the WordPress plugin.
var reactPluginData = reactPluginData || {};
var ReactDOM = ReactDOM || {};
