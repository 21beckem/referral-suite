function inIframe() { try {return window.self !== window.top;} catch(e) {return true;} }
function redirectAfterFunction(el) {
    alert("doing functiony stuff")
    safeRedirect(el.getAttribute('href'));
}
function safeRedirect(ref) {
    if (!inIframe()) {
        window.location.href = ref;
        return;
    }
    window.parent.postMessage(JSON.stringify({
        type: 'redirect',
        data: ref,
    }), '*');
}
document.addEventListener(`click`, e => {
    const origin = e.target.closest(`a`);
    
    if (origin) {
        e.preventDefault();
        //console.log(origin.href);
        homeURL = origin.href.substring(0,document.URL.lastIndexOf('/'));         
        if (origin.href == "") {
            homeURL = RealHome;
        }
        safeRedirect(origin.href);
    }
});
