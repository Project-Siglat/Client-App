let currentUserData = null;
let isEditMode = false;
let isLoading = false;
let verificationStatus = null;
let isVerificationFormVisible = false;

function showToast(message, type = "error") {
  const toastContainer = document.getElementById("toastContainer");
  const toast = document.getElementById("toast");
  const toastMessage = document.getElementById("toastMessage");

  toastMessage.textContent = message;

  // Set toast styling based on type
  if (type === "error") {
    toast.style.backgroundColor = "#cc241d";
    toast.style.color = "#fbf1c7";
  } else if (type === "success") {
    toast.style.backgroundColor = "#98971a";
    toast.style.color = "#282828";
  } else if (type === "warning") {
    toast.style.backgroundColor = "#d79921";
    toast.style.color = "#282828";
  }

  toastContainer.style.display = "block";

  // Animate in
  setTimeout(() => {
    toast.classList.remove("translate-x-full");
    toast.classList.add("translate-x-0");
  }, 10);

  // Auto hide after 5 seconds
  setTimeout(() => {
    hideToast();
  }, 5000);
}

function hideToast() {
  const toastContainer = document.getElementById("toastContainer");
  const toast = document.getElementById("toast");

  toast.classList.remove("translate-x-0");
  toast.classList.add("translate-x-full");

  setTimeout(() => {
    toastContainer.style.display = "none";
  }, 300);
}

function checkProfileCompleteness() {
  if (!currentUserData) return false;

  const requiredFields = [
    { field: "firstName", name: "First Name" },
    { field: "lastName", name: "Last Name" },
    { field: "phoneNumber", name: "Phone Number" },
    { field: "address", name: "Address" },
    { field: "gender", name: "Gender" },
  ];

  const missingFields = [];

  for (const fieldInfo of requiredFields) {
    const value = currentUserData[fieldInfo.field];
    if (
      !value ||
      value.trim() === "" ||
      value === "Not provided" ||
      value === "Not specified"
    ) {
      missingFields.push(fieldInfo.name);
    }
  }

  return missingFields;
}

async function loadUserData() {
  if (isLoading) return;

  try {
    isLoading = true;
    updateLoadingState(true);

    const authToken = sessionStorage.getItem("authToken");
    if (!authToken) {
      console.error("No auth token found");
      showErrorMessage("Authentication required");
      return;
    }

    const response = await fetch(API() + "/api/v1/IAM", {
      method: "GET",
      headers: {
        accept: "*/*",
        Authorization: `Bearer ${authToken}`,
      },
    });

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }

    const userData = await response.json();
    currentUserData = userData;

    // Update the UI with fetched data
    updateUserDisplay(userData);

    // Load verification status
    await loadVerificationStatus();
  } catch (error) {
    console.error("Error loading user data:", error);
    showErrorMessage("Failed to load user data");
  } finally {
    isLoading = false;
    updateLoadingState(false);
  }
}

async function loadVerificationStatus() {
  try {
    const authToken = sessionStorage.getItem("authToken");
    if (!authToken) {
      console.error("No auth token found");
      return;
    }

    const response = await fetch(API() + "/api/v1/IAM/verified", {
      method: "GET",
      headers: {
        accept: "*/*",
        Authorization: `Bearer ${authToken}`,
      },
    });

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }

    const status = await response.text();
    verificationStatus = status.replace(/"/g, ""); // Remove quotes if present

    // Store status in localStorage
    localStorage.setItem("status", verificationStatus);

    updateVerificationDisplay(verificationStatus);
  } catch (error) {
    console.error("Error loading verification status:", error);
    document.getElementById("verificationStatusDisplay").textContent =
      "Error loading status";
  }
}

function updateVerificationDisplay(status) {
  const statusDisplay = document.getElementById("verificationStatusDisplay");
  const toggleBtn = document.getElementById("toggleVerificationBtn");
  const editBtn = document.getElementById("editProfileBtn");

  statusDisplay.textContent = status.charAt(0).toUpperCase() + status.slice(1);

  // Hide buttons if verified
  if (status === "accepted") {
    if (editBtn) editBtn.style.display = "none";
    if (toggleBtn) toggleBtn.style.display = "none";
  } else {
    if (editBtn) editBtn.style.display = "block";
    if (toggleBtn) toggleBtn.style.display = "inline-block";
  }

  // Apply color coding and update button text
  switch (status) {
    case "verified":
      statusDisplay.style.backgroundColor = "#98971a";
      statusDisplay.style.color = "#282828";
      toggleBtn.textContent = "Re-verify";
      break;
    case "pending":
      statusDisplay.style.backgroundColor = "#d79921";
      statusDisplay.style.color = "#282828";
      toggleBtn.style.display = "none";
      break;
    case "rejected":
      statusDisplay.style.backgroundColor = "#cc241d";
      statusDisplay.style.color = "#fbf1c7";
      toggleBtn.textContent = "Re-verify";
      break;
    case "none":
    default:
      statusDisplay.style.backgroundColor = "#504945";
      statusDisplay.style.color = "#ebdbb2";
      toggleBtn.textContent = "Verify";
      break;
  }
}

function toggleVerificationForm() {
  // Check if profile is complete before allowing verification
  const missingFields = checkProfileCompleteness();

  if (missingFields.length > 0) {
    const fieldsText = missingFields.join(", ");
    showToast(
      `Please complete your profile first. Missing fields: ${fieldsText}`,
      "error",
    );
    return;
  }

  const verificationSection = document.getElementById("verificationSection");
  const toggleBtn = document.getElementById("toggleVerificationBtn");

  isVerificationFormVisible = !isVerificationFormVisible;

  if (isVerificationFormVisible) {
    verificationSection.style.display = "block";
    toggleBtn.textContent = "Hide Form";
  } else {
    verificationSection.style.display = "none";
    // Reset button text based on verification status
    updateVerificationDisplay(verificationStatus);
  }
}
function updateUserDisplay(userData) {
  const fullName = [userData.firstName, userData.middleName, userData.lastName]
    .filter((name) => name && name.trim())
    .join(" ");

  document.getElementById("userName").textContent =
    fullName || "No name provided";
  document.getElementById("userEmail").textContent =
    userData.email || "No email provided";
  document.getElementById("userRoleDisplay").textContent =
    userData.role || "No role assigned";
  document.getElementById("userGenderDisplay").textContent =
    userData.gender || "Not specified";
  document.getElementById("userPhoneDisplay").textContent =
    userData.phoneNumber || "Not provided";
  document.getElementById("userAddressDisplay").textContent =
    userData.address || "Not provided";

  // Format and display member since date
  if (userData.createdAt) {
    const memberSince = new Date(userData.createdAt).toLocaleDateString(
      "en-US",
      {
        year: "numeric",
        month: "long",
      },
    );
    document.getElementById("memberSince").textContent = memberSince;
  } else {
    document.getElementById("memberSince").textContent = "Unknown";
  }
}

function updateLoadingState(loading) {
  const loadingText = loading ? "Loading..." : "";
  if (loading) {
    document.getElementById("userName").textContent = loadingText;
    document.getElementById("userEmail").textContent = loadingText;
    document.getElementById("userRoleDisplay").textContent = loadingText;
    document.getElementById("userGenderDisplay").textContent = loadingText;
    document.getElementById("userPhoneDisplay").textContent = loadingText;
    document.getElementById("userAddressDisplay").textContent = loadingText;
    document.getElementById("memberSince").textContent = loadingText;
    document.getElementById("verificationStatusDisplay").textContent =
      loadingText;
  }
}

function showErrorMessage(message) {
  showToast(message, "error");
}

function toggleEditMode() {
  isEditMode = true;
  if (isVerificationFormVisible) {
    document.getElementById("verificationSection").style.display = "none";
    isVerificationFormVisible = false;
    // Reset the verify button text based on current verification status
    updateVerificationDisplay(verificationStatus);
  }

  // Hide the verification button when in edit mode
  document.getElementById("toggleVerificationBtn").style.display = "none";

  // Auto show verification section and button when edit mode is false
  if (!isEditMode) {
    document.getElementById("verificationSection").style.display = "block";
    document.getElementById("toggleVerificationBtn").style.display =
      "inline-block";
  }

  // Hide display elements and show edit elements
  document.getElementById("userNameDisplay").style.display = "none";
  document.getElementById("userNameEdit").style.display = "block";
  document.getElementById("userRoleDisplay").style.display = "none";
  document.getElementById("userRoleEdit").style.display = "inline-block";
  document.getElementById("userGenderDisplay").style.display = "none";
  document.getElementById("userGenderEdit").style.display = "inline-block";
  document.getElementById("userPhoneDisplay").style.display = "none";
  document.getElementById("userPhoneEdit").style.display = "inline-block";
  document.getElementById("userAddressDisplay").style.display = "none";
  document.getElementById("userAddressEdit").style.display = "inline-block";

  // Populate edit fields with current data
  if (currentUserData) {
    document.getElementById("firstNameEdit").value =
      currentUserData.firstName || "";
    document.getElementById("middleNameEdit").value =
      currentUserData.middleName || "";
    document.getElementById("lastNameEdit").value =
      currentUserData.lastName || "";
    document.getElementById("userRoleEdit").value = currentUserData.role || "";
    document.getElementById("userGenderEdit").value =
      currentUserData.gender || "";
    document.getElementById("userPhoneEdit").value =
      currentUserData.phoneNumber || "";
    document.getElementById("userAddressEdit").value =
      currentUserData.address || "";
  }

  // Toggle buttons
  document.getElementById("editProfileBtn").style.display = "none";
  document.getElementById("saveProfileBtn").style.display = "block";
  document.getElementById("cancelEditBtn").style.display = "block";
}

function cancelEdit() {
  isEditMode = false;
  // Show the verification button when exiting edit mode
  document.getElementById("toggleVerificationBtn").style.display =
    "inline-block";

  // Show display elements and hide edit elements
  document.getElementById("userNameDisplay").style.display = "block";
  document.getElementById("userNameEdit").style.display = "none";
  document.getElementById("userRoleDisplay").style.display = "inline";
  document.getElementById("userRoleEdit").style.display = "none";
  document.getElementById("userGenderDisplay").style.display = "inline";
  document.getElementById("userGenderEdit").style.display = "none";
  document.getElementById("userPhoneDisplay").style.display = "inline";
  document.getElementById("userPhoneEdit").style.display = "none";
  document.getElementById("userAddressDisplay").style.display = "inline";
  document.getElementById("userAddressEdit").style.display = "none";

  // Toggle buttons
  document.getElementById("editProfileBtn").style.display = "block";
  document.getElementById("saveProfileBtn").style.display = "none";
  document.getElementById("cancelEditBtn").style.display = "none";
}

async function saveUserProfile() {
  if (isLoading) return;

  try {
    isLoading = true;
    const saveBtn = document.getElementById("saveProfileBtn");
    const originalText = saveBtn.textContent;
    saveBtn.textContent = "Saving...";
    saveBtn.disabled = true;

    const authToken = sessionStorage.getItem("authToken");
    if (!authToken) {
      console.error("No auth token found");
      showErrorMessage("Authentication required");
      return;
    }

    if (!currentUserData) {
      console.error("No current user data found");
      showErrorMessage("No user data available");
      return;
    }

    // Get updated values from form
    const updatedData = {
      id: currentUserData.id,
      firstName: document.getElementById("firstNameEdit").value.trim(),
      middleName: document.getElementById("middleNameEdit").value.trim(),
      lastName: document.getElementById("lastNameEdit").value.trim(),
      address: document.getElementById("userAddressEdit").value.trim(),
      gender: document.getElementById("userGenderEdit").value.trim(),
      phoneNumber: document.getElementById("userPhoneEdit").value.trim(),
      role: document.getElementById("userRoleEdit").value.trim(),
      dateOfBirth: currentUserData.dateOfBirth,
      email: currentUserData.email,
      hashPass: currentUserData.hashPass,
      createdAt: currentUserData.createdAt,
      updatedAt: new Date().toISOString(),
    };

    const response = await fetch(API() + "/api/v1/IAM/update", {
      method: "POST",
      headers: {
        accept: "*/*",
        Authorization: `Bearer ${authToken}`,
        "Content-Type": "application/json",
      },
      body: JSON.stringify(updatedData),
    });

    if (!response.ok) {
      const errorData = await response.text();
      throw new Error(
        `HTTP error! status: ${response.status}, message: ${errorData}`,
      );
    }

    // Update current user data and refresh display
    currentUserData = updatedData;
    updateUserDisplay(currentUserData);
    cancelEdit();

    // Show success message (you can replace alert with a better notification system)
    showSuccessMessage("Profile updated successfully!");
  } catch (error) {
    console.error("Error updating user profile:", error);
    showErrorMessage("Error updating profile. Please try again.");
  } finally {
    isLoading = false;
    const saveBtn = document.getElementById("saveProfileBtn");
    saveBtn.textContent = "Save Changes";
    saveBtn.disabled = false;
  }
}

function showSuccessMessage(message) {
  showToast(message, "success");
}

function toggleUserSidebar() {
  const modal = document.getElementById("userSidebar");
  const backdrop = document.getElementById("userSidebarBackdrop");
  const isOpen = modal.classList.contains("translate-x-0");

  if (isOpen) {
    modal.classList.remove("translate-x-0");
    modal.classList.add("translate-x-full");
    if (backdrop) {
      backdrop.style.display = "none";
    }
    // Cancel edit mode when closing sidebar
    if (isEditMode) {
      cancelEdit();
    }
    // Hide verification form when closing sidebar
    if (isVerificationFormVisible) {
      isVerificationFormVisible = false;
      document.getElementById("verificationSection").style.display = "none";
    }
  } else {
    modal.classList.remove("translate-x-full");
    modal.classList.add("translate-x-0");
    if (backdrop) {
      backdrop.style.display = "block";
    }
    // Load user data when sidebar opens
    loadUserData();
  }
}

async function submitVerificationV2() {
  const fileInput = document.getElementById("verificationFileUpload");
  const documentType = document.getElementById("documentTypeSelect").value;
  const errorDiv = document.getElementById("verificationErrorMessage");
  const errorText = document.getElementById("verificationErrorText");

  // Hide error message initially
  errorDiv.style.display = "none";

  if (!fileInput.files[0]) {
    errorText.textContent = "Please select a file to upload";
    errorDiv.style.backgroundColor = "#cc241d";
    errorDiv.style.color = "#fbf1c7";
    errorDiv.style.borderColor = "#fb4934";
    errorDiv.style.display = "block";
    return;
  }

  if (!documentType) {
    errorText.textContent = "Please select a document type";
    errorDiv.style.backgroundColor = "#cc241d";
    errorDiv.style.color = "#fbf1c7";
    errorDiv.style.borderColor = "#fb4934";
    errorDiv.style.display = "block";
    return;
  }

  const submitBtn = document.getElementById("submitVerificationBtn");
  const originalText = submitBtn.textContent;
  submitBtn.textContent = "Submitting...";
  submitBtn.disabled = true;

  try {
    const authToken = sessionStorage.getItem("authToken");
    if (!authToken) {
      console.error("No auth token found");
      errorText.textContent = "Authentication required";
      errorDiv.style.backgroundColor = "#cc241d";
      errorDiv.style.color = "#fbf1c7";
      errorDiv.style.borderColor = "#fb4934";
      errorDiv.style.display = "block";
      return;
    }

    const formData = new FormData();
    formData.append("image", fileInput.files[0]);

    const response = await fetch(
      `${API()}/api/v1/IAM/verify?DocuType=${encodeURIComponent(documentType)}`,
      {
        method: "POST",
        headers: {
          accept: "*/*",
          Authorization: `Bearer ${authToken}`,
        },
        body: formData,
      },
    );

    if (!response.ok) {
      if (response.status === 400) {
        errorText.textContent = "One of your credentials must be complied";
        errorDiv.style.backgroundColor = "#cc241d";
        errorDiv.style.color = "#fbf1c7";
        errorDiv.style.borderColor = "#fb4934";
        errorDiv.style.display = "block";
        return;
      }
      throw new Error(`HTTP error! status: ${response.status}`);
    }

    // Show success message
    errorText.textContent = "Verification document submitted successfully!";
    errorDiv.style.backgroundColor = "#98971a";
    errorDiv.style.color = "#282828";
    errorDiv.style.borderColor = "#98971a";
    errorDiv.style.display = "block";

    // Reload verification status
    await loadVerificationStatus();

    // Clear form and hide it
    fileInput.value = "";
    document.getElementById("documentTypeSelect").value = "";
    toggleVerificationForm(); // Hide the form after successful submission
  } catch (error) {
    console.error("Error submitting verification:", error);
    errorText.textContent =
      "Error submitting verification document. Please try again.";
    errorDiv.style.backgroundColor = "#cc241d";
    errorDiv.style.color = "#fbf1c7";
    errorDiv.style.borderColor = "#fb4934";
    errorDiv.style.display = "block";
  } finally {
    submitBtn.textContent = originalText;
    submitBtn.disabled = false;
  }
}
