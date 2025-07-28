setInterval(() => {
  const authToken = sessionStorage.getItem("authToken");
  if (!authToken) {
    window.location.href = "/";
  }
}, 500);
