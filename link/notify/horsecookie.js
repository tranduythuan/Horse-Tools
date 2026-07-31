document.addEventListener("DOMContentLoaded", function () {
  const bar = document.getElementById("ht-cookie");
  if (!bar) return;

  // Only show if the visitor has not made a choice yet.
  if (!localStorage.getItem("htcookie")) {
    bar.classList.add("ht-cookie-show");
  }

  // Record the choice both in localStorage (for this notice) and in a plain
  // cookie (so a site owner can gate their own scripts on ht_cookie_consent).
  function choose(value) {
    try { localStorage.setItem("htcookie", value); } catch (e) {}
    document.cookie = "ht_cookie_consent=" + value + ";path=/;max-age=15552000;samesite=Lax";
    bar.classList.remove("ht-cookie-show");
  }

  bar.querySelectorAll(".ht-cookie-oke").forEach(function (b) {
    b.addEventListener("click", function () { choose("accepted"); });
  });
  bar.querySelectorAll(".ht-cookie-no").forEach(function (b) {
    b.addEventListener("click", function () { choose("declined"); });
  });
  bar.querySelectorAll(".ht-cookie-close").forEach(function (b) {
    b.addEventListener("click", function () { choose("dismissed"); });
  });
});
