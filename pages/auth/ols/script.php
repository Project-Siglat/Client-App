<?php
include "./config/env.php";

$API = $_ENV["API"];

// Get the 'what' parameter from URL
$what = isset($_GET["what"]) ? $_GET["what"] : "login";
$isRegister = $what === "register";
?>

<script>
document.addEventListener("DOMContentLoaded", function () {
  const registerTab = document.getElementById("registerTab");
  const loginTab = document.getElementById("loginTab");
  const registerPanel = document.getElementById("registerPanel");
  const loginPanel = document.getElementById("loginPanel");

  // Toast function
  function showToast(message, type = "success") {
    const toastId = "toast-" + Date.now();
    const bgColor = type === "success" ? "bg-green-600" : "bg-red-600";
    const toast = document.createElement("div");
    toast.id = toastId;
    toast.className = `flex items-center p-4 mb-4 text-white rounded-lg ${bgColor} shadow-lg transform transition-all duration-300 translate-x-full`;
    toast.innerHTML = `
            <div class="ml-3 text-sm font-medium">${message}</div>
            <button type="button" class="ml-auto -mx-1.5 -my-1.5 text-white hover:text-gray-300 rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 inline-flex h-8 w-8" onclick="this.parentElement.style.opacity='0'; setTimeout(() => this.parentElement.remove(), 300);">
                <span class="sr-only">Close</span>
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
            </button>
        `;

    document.getElementById("toastContainer").appendChild(toast);
    setTimeout(() => {
      toast.classList.remove("translate-x-full");
    }, 100);

    setTimeout(() => {
      toast.style.opacity = "0";
      setTimeout(() => {
        toast.remove();
      }, 300);
    }, 5000);
  }

  // Function to update URL parameter
  function updateURLParam(param, value) {
    const url = new URL(window.location);
    url.searchParams.set(param, value);
    window.history.pushState({}, "", url);
  }

  // Function to redirect based on role
  function redirectByRole(role) {
    let redirectUrl;

    switch (role) {
      case "User":
        redirectUrl = "/client";
        break;
      case "Admin":
        redirectUrl = "/siglat";
        break;
      case "Ambulance":
        redirectUrl = "/ambulance";
        break;
      case "BFP":
        redirectUrl = "/bfp";
        break;
      case "PNP":
        redirectUrl = "/pnp";
        break;
      default:
        redirectUrl = "/client";
        break;
    }

    window.location.href = redirectUrl;
  }

  registerTab.addEventListener("click", function () {
    // Update URL parameter
    updateURLParam("what", "register");

    // Update tabs
    registerTab.classList.add(
      "bg-gradient-to-r",
      "from-red-600",
      "to-red-700",
      "text-white",
      "shadow-lg",
    );
    registerTab.classList.remove(
      "text-gray-300",
      "hover:text-white",
      "hover:bg-gray-700/30",
    );
    loginTab.classList.remove(
      "bg-gradient-to-r",
      "from-yellow-600",
      "to-yellow-700",
      "text-white",
      "shadow-lg",
    );
    loginTab.classList.add(
      "text-gray-300",
      "hover:text-white",
      "hover:bg-gray-700/30",
    );

    // Update panels
    registerPanel.classList.remove("hidden");
    loginPanel.classList.add("hidden");

    // Update ARIA
    registerTab.setAttribute("aria-selected", "true");
    loginTab.setAttribute("aria-selected", "false");
  });

  loginTab.addEventListener("click", function () {
    // Update URL parameter
    updateURLParam("what", "login");

    // Update tabs
    loginTab.classList.add(
      "bg-gradient-to-r",
      "from-yellow-600",
      "to-yellow-700",
      "text-white",
      "shadow-lg",
    );
    loginTab.classList.remove(
      "text-gray-300",
      "hover:text-white",
      "hover:bg-gray-700/30",
    );
    registerTab.classList.remove(
      "bg-gradient-to-r",
      "from-red-600",
      "to-red-700",
      "text-white",
      "shadow-lg",
    );
    registerTab.classList.add(
      "text-gray-300",
      "hover:text-white",
      "hover:bg-gray-700/30",
    );

    // Update panels
    loginPanel.classList.remove("hidden");
    registerPanel.classList.add("hidden");

    // Update ARIA
    loginTab.setAttribute("aria-selected", "true");
    registerTab.setAttribute("aria-selected", "false");
  });

  // Handle registration form submission
  document
    .getElementById("registerForm")
    .addEventListener("submit", function (e) {
      e.preventDefault();

      const passwordValue = document.getElementById("password").value;

      const registrationData = {
        id: "00000000-0000-0000-0000-000000000000",
        firstName: document.getElementById("firstName").value,
        middleName: document.getElementById("middleName").value || "",
        lastName: document.getElementById("lastName").value,
        address: document.getElementById("address").value,
        gender: document.getElementById("gender").value,
        phoneNumber: document.getElementById("phoneNumber").value,
        role: "User",
        dateOfBirth: document.getElementById("dateOfBirth").value,
        email: document.getElementById("email").value,
        hashPass: passwordValue,
        HashPass: passwordValue,
        createdAt: new Date().toISOString(),
        updatedAt: new Date().toISOString(),
      };

      fetch("<?php echo $API; ?>/api/v1/Auth/register", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(registrationData),
      })
        .then((response) => {
          // Always get the response text first
          return response.text().then((text) => {
            if (response.ok) {
              // Try to parse as JSON if response is ok
              try {
                return JSON.parse(text);
              } catch (e) {
                // If it's not valid JSON, return the text
                return { message: text };
              }
            } else {
              // For error responses, throw the text as error
              throw new Error(text);
            }
          });
        })
        .then((data) => {
          showToast("Registration successful!", "success");
          document.getElementById("registerForm").reset();

          // Switch to login tab after successful registration
          setTimeout(() => {
            loginTab.click();
          }, 1000);
        })
        .catch((error) => {
          let errorMessage = "Registration failed";

          // Try to parse error message as JSON first
          try {
            const errorResponse = JSON.parse(error.message);
            errorMessage =
              errorResponse.message || errorResponse.title || errorMessage;
          } catch (e) {
            // If not JSON, use the raw error message or a fallback
            errorMessage = error.message || errorMessage;
          }

          showToast(errorMessage, "error");
          console.error("Registration error:", error.message);
        });
    });

  // Handle login form submission
  document.getElementById("loginForm").addEventListener("submit", function (e) {
    e.preventDefault();

    const loginData = {
      email: document.getElementById("loginEmail").value,
      password: document.getElementById("loginPassword").value,
    };

    fetch("<?php echo $API; ?>/api/v1/Auth/login", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(loginData),
    })
      .then((response) => {
        if (response.ok) {
          return response.json();
        }
        throw new Error("Credentials are invalid");
      })
      .then((data) => {
        showToast("Login successful!", "success");

        // Store token and role in session storage if provided in response
        if (data && data.token) {
          sessionStorage.setItem("token", data.token);
          if (data.role) {
            sessionStorage.setItem("role", data.role);
          }
        }

        // Redirect based on role after successful login
        setTimeout(() => {
          const userRole = data.role || "User";
          redirectByRole(userRole);
        }, 1000);
      })
      .catch((error) => {
        showToast("Credentials are invalid", "error");
        console.error("Login error:", error);
      });
  });
});

</script>
