function redirectAfterFunction(el) {
    alert("doing functiony stuff")
    safeRedirect(el.getAttribute('href'));
}
function safeRedirect(ref) {
    var link = document.createElement("a");
    link.href = ref;
    link.click();
}
