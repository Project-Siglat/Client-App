let contactData = [];

async function fetchContactData() {
  try {
    const response = await fetch(`${API()}/api/v1/Admin/contact`, {
      method: "GET",
      headers: {
        accept: "*/*",
      },
    });

    if (response.ok) {
      contactData = await response.json();
    } else {
      console.error("Failed to fetch contact data:", response.statusText);
      showToast("Failed to load contact data");
    }
  } catch (error) {
    console.error("Error fetching contact data:", error);
    showToast("Error loading contact data");
  }
}

async function renderContactList() {
  const contactList = document.getElementById("contactList");
  const loadingSpinner = document.getElementById("loadingSpinner");

  // Show loading spinner
  loadingSpinner.classList.remove("hidden");
  contactList.innerHTML =
    '<div id="loadingSpinner" class="flex items-center justify-center py-8"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-[#fe8019]"></div><span class="ml-3 text-[#a89984]">Loading contacts...</span></div>';

  await fetchContactData();

  // Hide loading spinner and clear content
  contactList.innerHTML = "";

  contactData.forEach((contact) => {
    let icon, linkHref, linkText, displayText;

    if (contact.contactType === "email") {
      icon = "📧";
      linkHref = `mailto:${contact.contactInformation}`;
      linkText = contact.contactInformation;
      displayText = linkText;
    } else if (contact.contactType === "phone") {
      icon = "📞";
      linkHref = `tel:${contact.contactInformation}`;
      linkText = contact.contactInformation;
      displayText = linkText;
    } else if (contact.contactType === "facebook") {
      icon = "📘";
      linkHref = `https://facebook.com/${contact.contactInformation}`;
      linkText = contact.contactInformation;
      // Truncate Facebook URL if too long
      displayText =
        linkText.length > 25 ? linkText.substring(0, 25) + "..." : linkText;
    }

    const contactItem = document.createElement("div");
    contactItem.className =
      "mb-4 p-4 border border-[#504945] rounded-lg bg-[#3c3836] hover:bg-[#504945] transition-colors";
    contactItem.innerHTML = `
            <div class="mb-3">
                <div class="text-[#fe8019] text-sm font-medium mb-1">
                    ${contact.label}
                </div>
                <a href="${linkHref}" class="flex items-center gap-2 mb-2 text-[#ebdbb2] hover:text-[#fe8019] no-underline" target="_blank" title="${linkText}">
                    <span class="text-[#fe8019]">${icon}</span>
                    <div class="text-[#ebdbb2] flex-1 overflow-hidden text-ellipsis whitespace-nowrap">
                        ${displayText}
                    </div>
                </a>
            </div>
        `;

    contactList.appendChild(contactItem);
  });
}

function showToast(message) {
  const toast = document.createElement("div");
  toast.className =
    "fixed top-4 right-4 bg-[#b8bb26] text-[#1d2021] px-4 py-2 rounded shadow-lg z-[10000] transition-opacity";
  toast.textContent = message;
  document.body.appendChild(toast);

  setTimeout(() => {
    toast.style.opacity = "0";
    setTimeout(() => document.body.removeChild(toast), 300);
  }, 2000);
}

function showAddContactForm() {
  document.getElementById("addContactForm").classList.remove("hidden");
}

function cancelAddContact() {
  document.getElementById("addContactForm").classList.add("hidden");
  document.getElementById("newContactLabel").value = "";
  document.getElementById("newContactInfo").value = "";
}

function addContact() {
  const label = document.getElementById("newContactLabel").value.trim();
  const type = document.getElementById("newContactType").value;
  const info = document.getElementById("newContactInfo").value.trim();

  if (!label || !info) {
    showToast("Please fill in all fields");
    return;
  }

  const newContact = {
    id: generateId(),
    label: label,
    contactType: type,
    contactInformation: info,
    createdAt: new Date().toISOString(),
    updatedAt: new Date().toISOString(),
  };

  contactData.push(newContact);
  renderContactList();
  cancelAddContact();
  showToast("Contact added successfully!");
}

function generateId() {
  return "xxxx-xxxx-4xxx-yxxx".replace(/[xy]/g, function (c) {
    const r = (Math.random() * 16) | 0;
    const v = c == "x" ? r : (r & 0x3) | 0x8;
    return v.toString(16);
  });
}

function toggleContactModal() {
  console.log("toggleContactModal function triggered");
  const modal = document.getElementById("contactModal");
  console.log("Modal element:", modal);
  console.log("Current classes:", modal.className);

  if (modal.classList.contains("hidden")) {
    modal.classList.remove("hidden");
    modal.style.display = "flex";
    renderContactList();
    console.log("Modal shown");
  } else {
    modal.classList.add("hidden");
    modal.style.display = "none";
    cancelAddContact();
    console.log("Modal hidden");
  }
}
