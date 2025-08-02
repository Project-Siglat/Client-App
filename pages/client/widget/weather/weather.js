let isDragging = false;
let startY = 0;
let startTime = 0;
let currentTranslateY = 100;
let drawerHeight = 0;

function toggleWeatherDrawer() {
  const drawer = document.getElementById("weather-drawer");
  const isOpen =
    drawer.style.transform === "translateY(0%)" ||
    drawer.style.transform === "translateY(0px)";

  if (isOpen) {
    drawer.style.transform = "translateY(100%)";
    currentTranslateY = 100;
  } else {
    drawer.style.transform = "translateY(0%)";
    currentTranslateY = 0;
  }
}

function initDrag() {
  const drawer = document.getElementById("weather-drawer");
  const header = document.getElementById("drawer-header");

  drawerHeight = drawer.offsetHeight;

  function startDrag(e) {
    isDragging = true;
    startY = e.type === "mousedown" ? e.clientY : e.touches[0].clientY;
    startTime = Date.now();
    drawer.style.transition = "none";
    header.style.cursor = "grabbing";
  }

  function drag(e) {
    if (!isDragging) return;

    e.preventDefault();
    const currentY = e.type === "mousemove" ? e.clientY : e.touches[0].clientY;
    const deltaY = currentY - startY;
    const deltaPercent = (deltaY / drawerHeight) * 100;

    let newTranslateY = currentTranslateY + deltaPercent;
    newTranslateY = Math.max(0, Math.min(100, newTranslateY));

    drawer.style.transform = `translateY(${newTranslateY}%)`;
  }

  function endDrag(e) {
    if (!isDragging) return;

    isDragging = false;
    header.style.cursor = "grab";
    drawer.style.transition = "transform 0.3s ease-in-out";

    const currentY =
      e.type === "mouseup" ? e.clientY : e.changedTouches[0].clientY;
    const deltaY = currentY - startY;
    const deltaPercent = (deltaY / drawerHeight) * 100;
    const newTranslateY = currentTranslateY + deltaPercent;

    // Calculate velocity (pixels per millisecond)
    const endTime = Date.now();
    const duration = endTime - startTime;
    const velocity = deltaY / Math.max(duration, 1); // Avoid division by zero

    // Check for fast swipe (velocity threshold)
    const velocityThreshold = 0.5; // pixels per millisecond

    if (Math.abs(velocity) > velocityThreshold) {
      // Fast swipe detected - respond immediately based on direction
      if (velocity > 0) {
        // Fast downward swipe - close drawer
        drawer.style.transform = "translateY(100%)";
        currentTranslateY = 100;
      } else {
        // Fast upward swipe - open drawer
        drawer.style.transform = "translateY(0%)";
        currentTranslateY = 0;
      }
    } else {
      // Slow drag - use position-based snapping
      if (newTranslateY < 50) {
        drawer.style.transform = "translateY(0%)";
        currentTranslateY = 0;
      } else {
        drawer.style.transform = "translateY(100%)";
        currentTranslateY = 100;
      }
    }
  }

  // Mouse events
  header.addEventListener("mousedown", startDrag);
  document.addEventListener("mousemove", drag);
  document.addEventListener("mouseup", endDrag);

  // Touch events
  header.addEventListener("touchstart", startDrag, { passive: false });
  document.addEventListener("touchmove", drag, { passive: false });
  document.addEventListener("touchend", endDrag);
}

// Initialize drag functionality when page loads
document.addEventListener("DOMContentLoaded", initDrag);
