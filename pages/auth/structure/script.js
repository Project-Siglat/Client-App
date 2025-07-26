// Auto-redirect if role exists in sessionStorage
if (sessionStorage.getItem("userRole") && sessionStorage.getItem("authToken")) {
  const storedRole = sessionStorage.getItem("userRole");
  const roleMap = {
    User: "/client",
    user: "/client",
    Ambulance: "/ambulance",
    Admin: "/siglat",
    BFP: "/bfp",
    PNP: "/pnp",
  };

  const redirectPath = roleMap[storedRole];
  if (redirectPath) {
    window.location.href = redirectPath;
  }
}

document.addEventListener("DOMContentLoaded", function () {
  // Tab switching functionality
  const loginTab = document.getElementById("loginTab");
  const registerTab = document.getElementById("registerTab");
  const loginPanel = document.getElementById("loginPanel");
  const registerPanel = document.getElementById("registerPanel");
  const tabIndicator = document.querySelector(".nord-tab-indicator");

  function switchTab(tab) {
    if (tab === "login") {
      loginTab.setAttribute("aria-selected", "true");
      registerTab.setAttribute("aria-selected", "false");
      loginPanel.style.display = "block";
      registerPanel.style.display = "none";
      tabIndicator.classList.remove("register");
      sessionStorage.setItem("activeTab", "login");
    } else {
      registerTab.setAttribute("aria-selected", "true");
      loginTab.setAttribute("aria-selected", "false");
      registerPanel.style.display = "block";
      loginPanel.style.display = "none";
      tabIndicator.classList.add("register");
      sessionStorage.setItem("activeTab", "register");
    }
  }

  // Check sessionStorage for active tab
  const activeTab = sessionStorage.getItem("activeTab") || "login";
  switchTab(activeTab);

  loginTab.addEventListener("click", () => switchTab("login"));
  registerTab.addEventListener("click", () => switchTab("register"));

  // Toast notification function
  function showToast(message, type = "success") {
    const toastContainer = document.getElementById("toastContainer");
    const toast = document.createElement("div");
    toast.className = `nord-toast ${type}`;
    toast.textContent = message;
    toastContainer.appendChild(toast);

    setTimeout(() => {
      toast.remove();
    }, 3000);
  }

  // Function to redirect based on role
  function redirectBasedOnRole(role) {
    const roleMap = {
      User: "/client",
      user: "/client",
      Ambulance: "/ambulance",
      Admin: "/siglat",
      BFP: "/bfp",
      PNP: "/pnp",
    };

    const redirectPath = roleMap[role];
    if (redirectPath) {
      window.location.href = redirectPath;
    } else {
      showToast("Unknown role. Please contact support.", "error");
    }
  }

  // Login form submission
  document
    .getElementById("loginForm")
    .addEventListener("submit", async function (e) {
      e.preventDefault();

      const loginData = {
        email: document.getElementById("loginEmail").value,
        password: document.getElementById("loginPassword").value,
      };

      try {
        // Store login attempt in sessionStorage
        sessionStorage.setItem("lastLoginEmail", loginData.email);

        // Make API call to login endpoint
        const response = await fetch(`${API()}/api/v1/Auth/login`, {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify(loginData),
        });

        if (response.ok) {
          // Handle both JSON and non-JSON responses
          const contentType = response.headers.get("content-type");
          let result;

          try {
            if (contentType && contentType.includes("application/json")) {
              result = await response.json();
            } else {
              const textResult = await response.text();
              console.log("Non-JSON response received:", textResult);

              // Try to parse as JSON in case content-type header is wrong
              try {
                result = JSON.parse(textResult);
              } catch (parseError) {
                console.log(
                  "Failed to parse as JSON, treating as text:",
                  parseError,
                );
                result = { message: textResult };
              }
            }
          } catch (parseError) {
            console.error("Error parsing response:", parseError);
            showToast("Invalid response from server.", "error");
            return;
          }

          console.log("Login response:", result);

          // Check if result has the expected properties
          if (
            result &&
            typeof result === "object" &&
            result.role &&
            result.token
          ) {
            // Store role and token in sessionStorage
            sessionStorage.setItem("userRole", result.role);
            sessionStorage.setItem("authToken", result.token);

            showToast("Login successful!", "success");

            // Redirect based on role
            setTimeout(() => {
              redirectBasedOnRole(result.role);
            }, 1000);
          } else {
            console.error("Invalid response structure:", result);
            showToast(
              "Invalid response from server. Missing role or token.",
              "error",
            );
          }
        } else {
          const errorData = await response.text();
          console.error("Login failed:", errorData);
          showToast("Login failed. Please check your credentials.", "error");
        }
      } catch (error) {
        console.error("Login error:", error);
        showToast("Login failed. Please try again.", "error");
      }
    });

  // Register form submission
  document
    .getElementById("registerForm")
    .addEventListener("submit", async function (e) {
      e.preventDefault();

      const formData = new FormData(e.target);
      const registerData = {
        id: crypto.randomUUID(), // Generate UUID
        firstName: formData.get("firstName"),
        middleName: formData.get("middleName") || "",
        lastName: formData.get("lastName"),
        address: formData.get("address"),
        gender: formData.get("gender"),
        phoneNumber: formData.get("phoneNumber"),
        role: "User", // Default role set to "User"
        dateOfBirth: new Date(formData.get("dateOfBirth")).toISOString(),
        email: formData.get("email"),
        hashPass: formData.get("password"), // In real app, this would be hashed
        createdAt: new Date().toISOString(),
        updatedAt: new Date().toISOString(),
      };

      try {
        // Make API call to register endpoint
        const response = await fetch(`${API()}/api/v1/Auth/register`, {
          method: "POST",
          headers: {
            accept: "*/*",
            "Content-Type": "application/json",
          },
          body: JSON.stringify(registerData),
        });

        if (response.ok) {
          // Handle both JSON and non-JSON responses
          const contentType = response.headers.get("content-type");
          let result;

          try {
            if (contentType && contentType.includes("application/json")) {
              result = await response.json();
            } else {
              const textResult = await response.text();
              console.log("Non-JSON response received:", textResult);

              // Try to parse as JSON in case content-type header is wrong
              try {
                result = JSON.parse(textResult);
              } catch (parseError) {
                console.log(
                  "Failed to parse as JSON, treating as text:",
                  parseError,
                );
                result = { message: textResult };
              }
            }
          } catch (parseError) {
            console.error("Error parsing response:", parseError);
            showToast("Invalid response from server.", "error");
            return;
          }

          // Store registration data in sessionStorage temporarily
          sessionStorage.setItem(
            "lastRegistration",
            JSON.stringify(registerData),
          );

          console.log("Register response:", result);
          showToast("Registration successful!", "success");

          // Switch to login tab after successful registration
          setTimeout(() => {
            switchTab("login");
            document.getElementById("loginEmail").value = registerData.email;
          }, 1500);
        } else {
          const errorData = await response.text();
          console.error("Registration failed:", errorData);
          showToast("Registration failed. Please try again.", "error");
        }
      } catch (error) {
        console.error("Registration error:", error);
        showToast("Registration failed. Please try again.", "error");
      }
    });

  // Restore last login email if exists
  const lastLoginEmail = sessionStorage.getItem("lastLoginEmail");
  if (lastLoginEmail && activeTab === "login") {
    document.getElementById("loginEmail").value = lastLoginEmail;
  }
});
