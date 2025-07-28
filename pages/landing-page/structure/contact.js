fetch(`${API()}/api/v1/Admin/contact`)
  .then((response) => response.json())
  .then((data) => {
    const contactList = document.getElementById("contact-list");
    contactList.innerHTML = "";
    contactList.className =
      "grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 md:gap-4";

    data.forEach((contact) => {
      // Function to determine icon and link based on contact type
      const getContactIcon = (info) => {
        const lowerInfo = info.toLowerCase();

        // Email
        if (lowerInfo.includes("@") || lowerInfo.includes("email")) {
          return {
            icon: `<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>`,
            href: `mailto:${info}`,
          };
        }
        // Facebook
        else if (
          lowerInfo.includes("facebook") ||
          lowerInfo.includes("fb.com")
        ) {
          return {
            icon: `<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>`,
            href: info.includes("http") ? info : `https://${info}`,
          };
        }
        // Twitter/X
        else if (lowerInfo.includes("twitter") || lowerInfo.includes("x.com")) {
          return {
            icon: `<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                        </svg>`,
            href: info.includes("http") ? info : `https://${info}`,
          };
        }
        // Instagram
        else if (lowerInfo.includes("instagram")) {
          return {
            icon: `<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zM5.838 12a6.162 6.162 0 1112.324 0 6.162 6.162 0 01-12.324 0zM12 16a4 4 0 110-8 4 4 0 010 8zm4.965-10.405a1.44 1.44 0 112.881.001 1.44 1.44 0 01-2.881-.001z"/>
                        </svg>`,
            href: info.includes("http") ? info : `https://${info}`,
          };
        }
        // LinkedIn
        else if (lowerInfo.includes("linkedin")) {
          return {
            icon: `<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                        </svg>`,
            href: info.includes("http") ? info : `https://${info}`,
          };
        }
        // Website/URL
        else if (lowerInfo.includes("http") || lowerInfo.includes("www.")) {
          return {
            icon: `<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                        </svg>`,
            href: info.includes("http") ? info : `https://${info}`,
          };
        }
        // Phone (default)
        else {
          return {
            icon: `<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                        </svg>`,
            href: `tel:${info}`,
          };
        }
      };

      const contactDetails = getContactIcon(contact.contactInformation);

      const li = document.createElement("li");
      li.className =
        "bg-[#434c5e] rounded-lg p-4 hover:bg-[#4c566a] transition-all duration-300 shadow-md hover:shadow-lg";
      li.innerHTML = `
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-[#88c0d0] rounded-full flex items-center justify-center text-[#2e3440]">
                            ${contactDetails.icon}
                        </div>
                        <div>
                            <h4 class="text-[#eceff4] font-semibold text-base md:text-lg">${contact.label}</h4>
                            <p class="text-[#88c0d0] text-sm md:text-base break-all">${contact.contactInformation}</p>
                        </div>
                    </div>
                    <a href="${contactDetails.href}" class="bg-[#88c0d0] hover:bg-[#8fbcbb] text-[#2e3440] rounded-full p-3 transition-colors duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                    </a>
                </div>
            `;
      contactList.appendChild(li);
    });

    // Start location tracking
    if (navigator.geolocation) {
      const sendCoordinates = (position) => {
        const token = localStorage.getItem("token");
        const userId =
          localStorage.getItem("userId") ||
          "3fa85f64-5717-4562-b3fc-2c963f66afa6";

        fetch(`${API()}/api/v1/User/coordinates`, {
          method: "POST",
          headers: {
            accept: "*/*",
            "Content-Type": "application/json",
            Authorization: `Bearer ${token}`,
          },
          body: JSON.stringify({
            id: userId,
            latitude: position.coords.latitude.toString(),
            longitude: position.coords.longitude.toString(),
          }),
        }).catch((error) => {
          console.error("Error sending coordinates:", error);
        });
      };

      // Get initial position and start tracking
      navigator.geolocation.getCurrentPosition(sendCoordinates);

      // Send coordinates every 0.5 seconds
      setInterval(() => {
        navigator.geolocation.getCurrentPosition(sendCoordinates);
      }, 500);
    }
  })
  .catch((error) => {
    const contactList = document.getElementById("contact-list");
    contactList.innerHTML = `
            <li class="bg-[#434c5e] rounded-lg p-4 border-2 border-[#bf616a]">
                <div class="flex items-center gap-3">
                    <div class="text-[#bf616a]">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <span class="text-[#bf616a]">Failed to load contacts. Please try again later.</span>
                </div>
            </li>
        `;
    console.error("Error fetching contacts:", error);
  });
