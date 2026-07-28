document.addEventListener("DOMContentLoaded", function () {
  const htcookiebg = document.getElementById("ht-cookie");
  if (!htcookiebg) return;
  const closeButtons = document.querySelectorAll(".ht-cookie-oke, .ht-cookie-close");
  if (!localStorage.getItem("htcookie")) {
	htcookiebg.style.display = "block";
  }
  closeButtons.forEach(button => {
	button.addEventListener("click", function () {
	  localStorage.setItem("htcookie", "true");
	  htcookiebg.style.display = "none";
	});
  });
});