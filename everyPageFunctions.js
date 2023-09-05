function inIframe() { try { return window.self !== window.top; } catch (e) { return true; } }
HTMLCollection.prototype.forEach = function (x) {
    return Array.from(this).forEach(x);
}
String.prototype.toTitleCase = function() {
    var splitStr = this.toLowerCase().split(' ');
    for (var i = 0; i < splitStr.length; i++) {
        // You do not need to check if i is larger than splitStr length, as your for does that for you
        // Assign it back to the array
        splitStr[i] = splitStr[i].charAt(0).toUpperCase() + splitStr[i].substring(1);     
    }
    // Directly return the joined string
    return splitStr.join(' '); 
}
Array.prototype.indexOfAll = function (searchItem) {
    let i = this.indexOf(searchItem);
    let indexes = [];
    while (i !== -1) {
        indexes.push(i);
        i = this.indexOf(searchItem, ++i);
    }
    return indexes;
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
async function safeFetch(ref) {
    if (!inIframe()) {
        return await fetch(ref);
    }
    ref = window.parent.makeSafeLink(ref);
    // make link safe
    return await fetch(ref);
}
document.addEventListener('click', e => {
    const origin = e.target.closest('a');
    if (origin) {
        //console.log(origin);
        if (origin.hasAttribute("target") || !origin.hasAttribute("href")) {
            //JSAlert.alert('has target');
            return;
        }
        e.preventDefault();
        safeRedirect(origin.href);
    }
});
function setCookie(cname, cvalue) {
    localStorage.setItem(cname, cvalue);
}
function getCookie(cname) {
    return localStorage.getItem(cname);
}
function getCookieJSON(x) {
    let out = null;
    try {
        out = JSON.parse(getCookie(x));
    } catch (e) { }
    return out;
}
function setCookieJSON(x, y) {
    return setCookie(x, JSON.stringify(y));
}
function _(x) { return document.getElementById(x); }
